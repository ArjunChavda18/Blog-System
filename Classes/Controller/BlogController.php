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
    
    /**
     * List Action with Filtering Options
     * 
     * @param string|null $searchTitle
     * @param string|null $createDate
     * @param string|null $modifyDate
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(
        string $searchTitle = null, 
        string $createDate = null, 
        string $modifyDate = null
    ): ResponseInterface
    {
        $limit = (int)($this->settings['limit'] ?? 0);
        $sorting = $this->settings['sortOrder'] ?? 'DESC';
        $storagePid = (int)($this->settings['overrideStoragePid'] ?? 0);

        // Check karte hain ki kya user ne kisi bhi filter mein kuch daala hai?
        $isFilterApplied = ($searchTitle !== null && trim($searchTitle) !== '') || 
            ($createDate !== null && trim($createDate) !== '') || 
            ($modifyDate !== null && trim($modifyDate) !== '');

        if ($isFilterApplied) {
            // Case 1: Agar filter lagaya hai, toh advanced function chalega
            $blogs = $this->blogRepository->findBlogsWithFilters(
                $limit,
                $sorting,
                $storagePid,
                $searchTitle,
                $createDate,
                $modifyDate
            );
        } else {
            // Case 2: Agar koi filter nahi hai, toh aapka purana findBlogs chalega
            $blogs = $this->blogRepository->findBlogs(
                $limit,
                $sorting,
                $storagePid
            );
        }
        if ($isFilterApplied && count($blogs) === 0) {
            $this->addFlashMessage(
                'No blogs found matching your filter criteria!', // Message Body
                'Filter Alert',                                  // Message Title
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::INFO // TYPO3 Standard Alert Type
            );
        }
    
        $this->view->assign('blogs', $blogs);
        $this->view->assign('searchTitle', $searchTitle);
        $this->view->assign('createDate', $createDate);
        $this->view->assign('modifyDate', $modifyDate);

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