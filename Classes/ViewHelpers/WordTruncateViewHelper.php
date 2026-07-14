<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class WordTruncateViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        // we are registering two arguments: content and limit
        $this->registerArgument('content', 'string', 'The blog description text', true);
        $this->registerArgument('limit', 'integer', 'Number of words to display', false, 5);
    }

    public function render(): string
    {
        $content = $this->arguments['content'];
        $limit = $this->arguments['limit'];

        // 1. remove html tags from the content to get plain text
        $plainText = strip_tags($content);

        // 2. join text into words and check if it exceeds the limit
        $words = explode(' ', $plainText);

        // 3. if total words exceed the limit, slice the array and append "..."
        if (count($words) > $limit) {
            $words = array_slice($words, 0, $limit);
            return implode(' ', $words) . '...';
        }

        return implode(' ', $words);
    }
}