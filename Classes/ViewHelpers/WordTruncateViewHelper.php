<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class WordTruncateViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        // Hum do arguments register kar rahe hain: content aur limit
        $this->registerArgument('content', 'string', 'The blog description text', true);
        $this->registerArgument('limit', 'integer', 'Number of words to display', false, 5);
    }

    public function render(): string
    {
        $content = $this->arguments['content'];
        $limit = $this->arguments['limit'];

        // 1. Saare HTML tags saaf karein
        $plainText = strip_tags($content);

        // 2. Text ko words ke array mein todein
        $words = explode(' ', $plainText);

        // 3. Agar total words limit se zyada hain, toh cut karein aur '...' jodein
        if (count($words) > $limit) {
            $words = array_slice($words, 0, $limit);
            return implode(' ', $words) . '...';
        }

        return implode(' ', $words);
    }
}