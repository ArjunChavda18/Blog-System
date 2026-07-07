<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This file is part of the "Blog Management System" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2026 Arjun <arjunchavda@gmail.com>, Nitsan
 */

/**
 * Blog
 */
class Blog extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * image (Multiple images allowed via ObjectStorage)
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $image = null;

    /**
     * @var \DateTime
     */ 
    protected $crdate = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\NITSAN\BlogSystem\Domain\Model\Comment>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $comments = null;

    /**
     * Constructor to initialize ObjectStorages for both images and comments
     */
    public function __construct()
    {
        // Dono ObjectStorage ko ek hi constructor mein initialize kar diya
        $this->image = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->comments = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns the image storage
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image storage
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $image
     * @return void
     */
    public function setImage(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $image)
    {
        $this->image = $image;
    }

    /**
     * Returns the creation date
     *
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Adds a Comment
     *
     * @param \NITSAN\BlogSystem\Domain\Model\Comment $comment
     * @return void
     */
    public function addComment(\NITSAN\BlogSystem\Domain\Model\Comment $comment): void
    {
        $this->comments->attach($comment);
    }

    /**
     * Removes a Comment
     * DockBlock comments
     *
     * @param \NITSAN\BlogSystem\Domain\Model\Comment $commentToRemove
     * @return void
     */
    public function removeComment(\NITSAN\BlogSystem\Domain\Model\Comment $commentToRemove): void
    {
        $this->comments->detach($commentToRemove);
    }

    /**
     * Returns the comments (Only Approved ones for Frontend)
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\NITSAN\BlogSystem\Domain\Model\Comment>
     */
    public function getComments(): ObjectStorage
    {
        // ADMIN APPROVAL LOGIC: 
        $approvedComments = new ObjectStorage();
        
        foreach ($this->comments as $comment) {
            if ($comment->isApproved()) {
                $approvedComments->attach($comment);
            }
        }
        
        return $approvedComments;
    }

    /**
     * Sets the comments
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\NITSAN\BlogSystem\Domain\Model\Comment> $comments
     * @return void
     */
    public function setComments(ObjectStorage $comments): void
    {
        $this->comments = $comments;
    }
}