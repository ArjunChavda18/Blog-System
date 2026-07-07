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
    public function findBlogsWithFilters(
        int $limit,
        string $sorting,
        int $storagePid,
        ?string $searchTitle = null,
        ?string $createDate = null,
        ?string $modifyDate = null
    ) {
        $query = $this->createQuery();
        
        //  FIX 1: Query Settings ko setup karna jo aapke purane function mein tha
        $querySettings = $query->getQuerySettings();
        if ($storagePid > 0) {
            $querySettings->setStoragePageIds([$storagePid]);
            $querySettings->setRespectStoragePage(true);
        } else {
            // Agar storage PID nahi hai, toh strict page boundary hatao (Toh hi saare blogs dikhenge)
            $querySettings->setRespectStoragePage(false);
        }
        $query->setQuerySettings($querySettings);

        $constraints = [];

        //  FIX 2: Strict Empty Checks (trim use karke space check karna)
        // 1. Title Filter
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

        // Constraints ko apply karna tabhi jab wo khaali na hon
        if (!empty($constraints)) {
            $query->matching($query->logicalAnd(...$constraints));
        }

        // Sorting & Limit Apply Karna
        $orderDirection = ($sorting === 'ASC') ? \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING : \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING;
        $query->setOrderings(['crdate' => $orderDirection]);

        if ($limit > 0) {
            $query->setLimit($limit);
        }

        return $query->execute();
    }
}