<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Domain\Model;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Comment extends AbstractEntity //give autometic power to Comment class for pageid and unique id.
{
    protected string $authorName = '';

    protected string $commentText = '';

    protected bool $approved = false;

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    public function getCommentText(): string
    {
        return $this->commentText;
    }

    public function setCommentText(string $commentText): void
    {
        $this->commentText = $commentText;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): void
    {
        $this->approved = $approved;
    }
}