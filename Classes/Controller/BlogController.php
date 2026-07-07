<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Controller;

use Psr\Http\Message\ResponseInterface;
use NITSAN\BlogSystem\Domain\Model\Blog;
use NITSAN\BlogSystem\Domain\Model\Comment;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use NITSAN\BlogSystem\Domain\Repository\BlogRepository;

class BlogController extends ActionController
{
    protected BlogRepository $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public function listAction(): ResponseInterface
    {
        $limit = (int)($this->settings['limit'] ?? 0);
        $sorting = $this->settings['sortOrder'] ?? 'DESC';
        $storagePid = (int)($this->settings['overrideStoragePid'] ?? 0);
        // $datepicker = $this->settings['datepicker'] ?? '';

        $blogs = $this->blogRepository->findBlogs(
            $limit,
            $sorting,
            $storagePid,
            // $datepicker
        );

        $this->view->assign('blogs', $blogs);

        return $this->htmlResponse();
    }

    /**
     * Single View Action for Blog Details
     * 
     * @param \NITSAN\BlogSystem\Domain\Model\Blog $blog
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(Blog $blog): ResponseInterface
    {
        $this->view->assign('blog', $blog);

        return $this->htmlResponse();
    }
    
    /**
     * Action to save a new comment from Frontend
     * 
     * @param \NITSAN\BlogSystem\Domain\Model\Blog $blog //Docblock mein type hinting
     * @param \NITSAN\BlogSystem\Domain\Model\Comment $newComment
     */
    public function createCommentAction(
        \NITSAN\BlogSystem\Domain\Model\Blog $blog, //Typo hinting
        \NITSAN\BlogSystem\Domain\Model\Comment $newComment
    ): ResponseInterface{  // ResponseInterface return type hinting it means return only valid http response
        $newComment->setApproved(false);

        // 2. Blog object ke andar is comment ko attach karna
        $blog->addComment($newComment);

        // 3. Blog Repository se database update karna
        $this->blogRepository->update($blog);
        return $this->redirect('show', null, null, ['blog' => $blog]);
    }
    /**
     * Action to delete a comment
     * 
     * @param \NITSAN\BlogSystem\Domain\Model\Blog $blog
     * @param \NITSAN\BlogSystem\Domain\Model\Comment $comment
     * @return \Psr\Http\Message\ResponseInterface
     */
        public function deleteCommentAction(Blog $blog, Comment $comment): ResponseInterface
    {
        $blog->removeComment($comment); // Yahan chalta hai wo removeComment wala function!
        $this->blogRepository->update($blog);
        return $this->redirect('show', null, null, ['blog' => $blog]);
    }
}