<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

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
    public function findBlogs(
        int $limit,
        string $sorting,
        int $storagePid,
        ?string $searchTitle = null,
        ?string $createDate = null,
        ?string $modifyDate = null
    ): QueryResultInterface {
        $query = $this->createQuery();

        // 1. Query Settings (Storage PID handling)
        $querySettings = $query->getQuerySettings();
        if ($storagePid > 0) {
            $querySettings->setStoragePageIds([$storagePid]);
            $querySettings->setRespectStoragePage(true);
        } else {
            $querySettings->setRespectStoragePage(false);
        }
        $query->setQuerySettings($querySettings);

        $constraints = [];

        // 2. Title Filter
        if ($searchTitle !== null && trim($searchTitle) !== '') {
            $constraints[] = $query->like('title', '%' . trim($searchTitle) . '%');
        }

        // 3. Creation Date Filter
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

        // 4. Modification Date Filter
        // Note: Extbase standard variables rely on 'tstamp' or custom model mappings
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

        // Apply constraints if any exist
        if (!empty($constraints)) {
            $query->matching($query->logicalAnd(...$constraints));
        }

        // 5. Apply Sorting
        $order = ($sorting === 'ASC') ? QueryInterface::ORDER_ASCENDING : QueryInterface::ORDER_DESCENDING;
        $query->setOrderings(['crdate' => $order]);

        // 6. Apply Limit
        if ($limit > 0) {
            $query->setLimit($limit);
        }

        return $query->execute();
    }
}