<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for Blogs
 */
class BlogRepository extends Repository
{
    /**
     * Custom query to filter blogs based on FlexForm settings
     *
     * @param int $limit
     * @param string $sorting
     * @param int $storagePid
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findBlogs(int $limit, string $sorting, int $storagePid)
    {
        $query = $this->createQuery();

        // Query settings ko overwrite karna taaki rules change ho sakein
        $querySettings = $query->getQuerySettings();

        // 1. Agar admin ne FlexForm mein Storage PID (pages) select kiya hai, toh wahi use karein
        if ($storagePid > 0) {
            $querySettings->setStoragePageIds([$storagePid]);
            $querySettings->setRespectStoragePage(true);
        } else {
            // Agar override nahi kiya, toh standard TYPO3 behavior chalega
            $querySettings->setRespectStoragePage(false);
        }
        
        $query->setQuerySettings($querySettings);

        // 2. Sorting Apply karna (ASC ya DESC)
        $order = ($sorting === 'ASC') ? QueryInterface::ORDER_ASCENDING : QueryInterface::ORDER_DESCENDING;
        $query->setOrderings([
            'crdate' => $order // Yahan 'crdate' (creation date) ya 'title' par sorting laga sakte hain
        ]);

        // 3. Limit Apply karna (Agar admin ne input field mein limit daali hai)
        if ($limit > 0) {
            $query->setLimit($limit);
        }

        return $query->execute();
    }
}