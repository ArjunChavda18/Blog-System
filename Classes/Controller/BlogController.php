<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Controller;

use TYPO3\CMS\Core\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use NITSAN\BlogSystem\Domain\Model\Blog;
use TYPO3\CMS\Core\Utility\DebugUtility;
use NITSAN\BlogSystem\Domain\Model\Comment;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use NITSAN\BlogSystem\Domain\Repository\BlogRepository;

class BlogController extends ActionController
{
    protected BlogRepository $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    /**
     * List Action with Filtering and Pagination
     * This action shows a list of blogs.
     * It supports search filters (title, created date, modified date)
     * and page-by-page navigation (pagination) without AJAX reload.
     * It also supports AJAX requests, which only return the blog cards HTML.
     */
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'searchTitle'])]
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'createDate'])]
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'modifyDate'])]
    public function listAction(): ResponseInterface
    {
        // Combine GET parameters (normal page load) and POST parameters (AJAX request)
        // into one array, so both request types can be handled the same way.
        $queryParams = $this->request->getQueryParams();
        $parsedBody  = $this->request->getParsedBody() ?? [];
        $params      = array_merge($queryParams, $parsedBody);

        // Read filter values sent by the user (title search, date filters).
        $searchTitle = $params['searchTitle'] ?? null;
        $createDate  = $params['createDate'] ?? null;
        $modifyDate  = $params['modifyDate'] ?? null;

        // Check if this request is an AJAX call (used for filter-only refresh, no full page reload).
        $isAjax = isset($params['ajax']) && $params['ajax'] === '1';

        // Number of blogs to show on a single page.
        // This value now controls "items per page" for pagination,
        // instead of being a hard database query limit.
        $itemsPerPage = (int)($this->settings['limit'] ?? 3);

        // Sorting order (ASC/DESC) and storage page ID, taken from plugin settings.
        $sorting    = $this->settings['sortOrder'] ?? 'DESC';
        $storagePid = (int)($this->settings['overrideStoragePid'] ?? 0);

        // Check whether the user has applied any filter (title/date search).
        $isFilterApplied = (!empty($searchTitle) && trim($searchTitle) !== '') ||
            (!empty($createDate) && trim($createDate) !== '') ||
            (!empty($modifyDate) && trim($modifyDate) !== '');

        // Fetch blogs from the database.
        // IMPORTANT: we pass "0" as limit here (no limit at DB level),
        // because the Paginator below will handle splitting results into pages.
        // If we limited the query here too, pagination page count would be wrong.
        if ($isFilterApplied) {
            $blogs = $this->blogRepository->findBlogsWithFilters(
                0,
                $sorting,
                $storagePid,
                $searchTitle,
                $createDate,
                $modifyDate
            );
        } else {
            $blogs = $this->blogRepository->findBlogs(0, $sorting, $storagePid);
        }

        // Get the current page number from the request.
        // hasArgument()/getArgument() is used instead of reading $params directly,
        // because Extbase automatically unwraps the plugin namespace
        // (e.g. tx_blogsystem_bloglist[currentPage]) for us.
        $currentPage = $this->request->hasArgument('currentPage')
            ? (int)$this->request->getArgument('currentPage')
            : 1;

        // Safety check: never allow page number below 1.
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        // Create the Paginator: it takes the full blog result set,
        // the current page number, and how many items to show per page.
        // Internally it only pulls the records needed for the current page.
        $paginator = new QueryResultPaginator($blogs, $currentPage, $itemsPerPage);

        // SimplePagination calculates page numbers, next/previous page info, etc.
        // This is used in the template to render page number links.
        $pagination = new SimplePagination($paginator);

        // Get only the blogs that belong to the current page.
        // We assign THIS to the "blogs" variable (not the full $blogs result),
        // so the existing Fluid loop <f:for each="{blogs}"> does not need any change.
        $paginatedBlogs = $paginator->getPaginatedItems();

        // Handle AJAX request: return only the blog cards section (partial HTML),
        // used when filters are applied without a full page reload.
        if ($isAjax) {
            $this->view->assign('blogs', $paginatedBlogs);
            $this->view->assign('paginator', $paginator);
            $this->view->assign('pagination', $pagination);
            $this->view->assign('searchTitle', $searchTitle);
            $this->view->assign('createDate', $createDate);
            $this->view->assign('modifyDate', $modifyDate);

            // Use the Blog/List template for rendering
            $this->view->setTemplate('Blog/List');

            // Render only the BlogItemsContent section with paginated data
            $htmlContent = $this->view->renderSection('BlogItemsContent', [
                'blogs' => $paginatedBlogs,
            ]);

            // Return only the rendered HTML section
            return $this->htmlResponse($htmlContent);
        }

        // Normal (non-AJAX) page load: assign everything the template needs,
        // including pagination data and current filter values
        // (filter values are re-sent so pagination links keep the same filter active).
        $this->view->assignMultiple([
            'blogs'       => $paginatedBlogs,
            'paginator'   => $paginator,
            'pagination'  => $pagination,
            'searchTitle' => $searchTitle,
            'createDate'  => $createDate,
            'modifyDate'  => $modifyDate,
        ]);

        return $this->htmlResponse();
    }

    public function showAction(Blog $blog): ResponseInterface
    {
        $this->view->assign('blog', $blog);
        return $this->htmlResponse();
    }

    public function createCommentAction(Blog $blog, Comment $newComment): ResponseInterface
    {
        $newComment->setApproved(false);
        $blog->addComment($newComment);
        $this->blogRepository->update($blog);

        return $this->redirect('show', null, null, ['blog' => $blog]);
    }

    public function deleteCommentAction(Blog $blog, Comment $comment): ResponseInterface
    {
        $blog->removeComment($comment);
        $this->blogRepository->update($blog);

        return $this->redirect('show', null, null, ['blog' => $blog]);
    }
}