<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Controller;
use Psr\Http\Message\ResponseInterface;
use NITSAN\BlogSystem\Domain\Model\Blog;
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
     */
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'searchTitle'])]
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'createDate'])]
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'modifyDate'])]
    public function listAction(): ResponseInterface
    {
        $queryParams = $this->request->getQueryParams();
        $parsedBody  = $this->request->getParsedBody() ?? [];
        $params      = array_merge($queryParams, $parsedBody);

        $searchTitle = $params['searchTitle'] ?? null;
        $createDate  = $params['createDate'] ?? null;
        $modifyDate  = $params['modifyDate'] ?? null;

        $isAjax = isset($params['ajax']) && $params['ajax'] === '1';

        $itemsPerPage = (int)($this->settings['limit'] ?? 3);
        $sorting    = $this->settings['sortOrder'] ?? 'DESC';
        $storagePid = (int)($this->settings['overrideStoragePid'] ?? 0);

        $isFilterApplied = (!empty($searchTitle) && trim($searchTitle) !== '') ||
            (!empty($createDate) && trim($createDate) !== '') ||
            (!empty($modifyDate) && trim($modifyDate) !== '');

        $blogs = $this->blogRepository->findBlogs(
            0, // Limit
            $sorting,
            $storagePid,
            $isFilterApplied ? $searchTitle : null,
            $isFilterApplied ? $createDate : null,
            $isFilterApplied ? $modifyDate : null
        );

        $currentPage = $this->request->hasArgument('currentPage')
            ? (int)$this->request->getArgument('currentPage')
            : 1;

        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $paginator = new QueryResultPaginator($blogs, $currentPage, $itemsPerPage);
        $pagination = new SimplePagination($paginator);
        $paginatedBlogs = $paginator->getPaginatedItems();

        if ($isAjax) {
            $this->view->assign('blogs', $paginatedBlogs);
            $this->view->assign('paginator', $paginator);
            $this->view->assign('pagination', $pagination);
            $this->view->assign('searchTitle', $searchTitle);
            $this->view->assign('createDate', $createDate);
            $this->view->assign('modifyDate', $modifyDate);

            $this->view->setTemplate('Blog/List');

            // CHANGED: Pass pagination objects into the section as well
            $htmlContent = $this->view->renderSection('BlogItemsContent', [
                'blogs' => $paginatedBlogs,
                'paginator' => $paginator,
                'pagination' => $pagination,
                'searchTitle' => $searchTitle,
                'createDate'  => $createDate,
                'modifyDate'  => $modifyDate,
            ]);

            return $this->htmlResponse($htmlContent);
        }

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