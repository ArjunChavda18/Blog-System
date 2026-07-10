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
     * @param string|null $searchTitle
     * @param string|null $createDate
     * @param string|null $modifyDate
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findBlogs(int $limit, string $sorting, int $storagePid)
    {
        $query = $this->createQuery();

        // overrides query settings to respect storage page or not based on FlexForm configuration
        $querySettings = $query->getQuerySettings();

        // 1. if admin can select page id from flexform then use that page id.
        if ($storagePid > 0) {
            $querySettings->setStoragePageIds([$storagePid]);
            $querySettings->setRespectStoragePage(true);
        } else {
            // if not override storage pid then we will not respect storage page and show all blogs from all pages.
            $querySettings->setRespectStoragePage(false);
        }
        
        $query->setQuerySettings($querySettings);

        // 2. Apply Sorting based on FlexForm configuration (ASC or DESC)
        $order = ($sorting === 'ASC') ? QueryInterface::ORDER_ASCENDING : QueryInterface::ORDER_DESCENDING;
        $query->setOrderings([
            'crdate' => $order // Sort by creation date ('crdate'). Change to 'title' to sort by title instead.
        ]);

        // 3. Apply limit if an admin-provided limit is set
        if ($limit > 0) {
            $query->setLimit($limit);
        }

        return $query->execute();
    }
    public function findBlogsWithFilters(
        int $limit,
        string $sorting,
        int $storagePid,
        ?string $searchTitle = null,
        ?string $createDate = null,
        ?string $modifyDate = null
    ) {
        $query = $this->createQuery();
        
        // FIX 1: Set up the query settings as done in the original function
        $querySettings = $query->getQuerySettings();
        if ($storagePid > 0) {
            $querySettings->setStoragePageIds([$storagePid]);
            $querySettings->setRespectStoragePage(true);
        } else {
            // If no storage PID is provided, do not restrict to a single storage page (show blogs from all pages)
            $querySettings->setRespectStoragePage(false);
        }
        $query->setQuerySettings($querySettings);

        $constraints = [];

        // FIX 2: Strict empty checks — use trim() to ignore whitespace-only values
        // 1. Title filter
        if ($searchTitle !== null && trim($searchTitle) !== '') {
            $constraints[] = $query->like('title', '%' . trim($searchTitle) . '%');
        }

        // 2. Creation Date Filter
        if ($createDate !== null && trim($createDate) !== '') {
            $startTimestamp = strtotime(trim($createDate) . ' 00:00:00');
            $endTimestamp = strtotime(trim($createDate) . ' 23:59:59');
            
            if ($startTimestamp !== false && $endTimestamp !== false) {
                $constraints[] = $query->logicalAnd(
                    $query->greaterThanOrEqual('crdate', $startTimestamp),
                    $query->lessThanOrEqual('crdate', $endTimestamp)
                );
            }
        }

        // 3. Modification Date Filter
        if ($modifyDate !== null && trim($modifyDate) !== '') {
            $startTimestamp = strtotime(trim($modifyDate) . ' 00:00:00');
            $endTimestamp = strtotime(trim($modifyDate) . ' 23:59:59');

            if ($startTimestamp !== false && $endTimestamp !== false) {
                $constraints[] = $query->logicalAnd(
                    $query->greaterThanOrEqual('tstamp', $startTimestamp),
                    $query->lessThanOrEqual('tstamp', $endTimestamp)
                );
            }
        }

        // Apply constraints only when they are present
        if (!empty($constraints)) {
            $query->matching($query->logicalAnd(...$constraints));
        }

        // Apply sorting and limit
        $orderDirection = ($sorting === 'ASC') ? \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING : \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING;
        $query->setOrderings(['crdate' => $orderDirection]);

        if ($limit > 0) {
            $query->setLimit($limit);
        }

        return $query->execute();
    }
}