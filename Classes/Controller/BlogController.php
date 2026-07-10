<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Controller;

use TYPO3\CMS\Core\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use NITSAN\BlogSystem\Domain\Model\Blog;
use TYPO3\CMS\Core\Utility\DebugUtility;
use NITSAN\BlogSystem\Domain\Model\Comment;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use NITSAN\BlogSystem\Domain\Repository\BlogRepository;

class BlogController extends ActionController
{
    protected BlogRepository $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    /**
     * List Action with Filtering Options
     * @return ResponseInterface
     */
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'searchTitle'])]
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'createDate'])]
    #[\TYPO3\CMS\Extbase\Annotation\IgnoreValidation(['argumentName' => 'modifyDate'])]
    public function listAction(): ResponseInterface
    {
        // Merge GET (normal request) and POST (AJAX request) parameters
        $queryParams = $this->request->getQueryParams();
        $parsedBody  = $this->request->getParsedBody() ?? [];
        $params      = array_merge($queryParams, $parsedBody);

        $searchTitle = $params['searchTitle'] ?? null;
        $createDate  = $params['createDate'] ?? null;
        $modifyDate  = $params['modifyDate'] ?? null;
        $isAjax      = isset($params['ajax']) && $params['ajax'] === '1';

        $limit      = (int)($this->settings['limit'] ?? 0);
        $sorting    = $this->settings['sortOrder'] ?? 'DESC';
        $storagePid = (int)($this->settings['overrideStoragePid'] ?? 0);

        // Check whether any filter value is provided
        $isFilterApplied = (!empty($searchTitle) && trim($searchTitle) !== '') ||
            (!empty($createDate) && trim($createDate) !== '') ||
            (!empty($modifyDate) && trim($modifyDate) !== '');

        if ($isFilterApplied) {
            $blogs = $this->blogRepository->findBlogsWithFilters(
                $limit,
                $sorting,
                $storagePid,
                $searchTitle,
                $createDate,
                $modifyDate
            );
        } else {
            $blogs = $this->blogRepository->findBlogs($limit, $sorting, $storagePid);
        }

        // Handle AJAX request
        if ($isAjax) {

            // Assign filtered blogs to the Fluid view
            $this->view->assign('blogs', $blogs);

            // Use the Blog/List template for rendering
            $this->view->setTemplate('Blog/List');

            // Render only the BlogItemsContent section with filtered data
            $htmlContent = $this->view->renderSection('BlogItemsContent', [
                'blogs' => $blogs
            ]);

            // Return only the rendered HTML section
            return $this->htmlResponse($htmlContent);
        }

        // Assign blogs for the initial page load
        $this->view->assign('blogs', $blogs);

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