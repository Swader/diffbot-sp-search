<?php

namespace SitePoint\Helpers;

use Swader\Diffbot\Entity\EntityIterator;
use Swader\Diffbot\Entity\SearchInfo;

class PaginationHelper
{
    public function getPaginationData(
        $currentPage,
        $itemsPerPage,
        $pageRange,
        EntityIterator $res,
        SearchInfo $searchInfo
    ) {

        $paginationData = [];

        $paginationData['pageCount'] = !count($res)
            ? 0
            : ceil($searchInfo->getHits() / $itemsPerPage);

        $paginationData['currentPage'] = ($paginationData['pageCount'] < $currentPage)
            ? $paginationData['pageCount']
            : $currentPage;

        $paginationData['pageRange'] = ($pageRange > $paginationData['pageCount'])
            ? $paginationData['pageCount']
            : $pageRange;

        $delta = ceil($paginationData['pageRange'] / 2);

        if ($paginationData['currentPage'] - $delta > $paginationData['pageCount'] - $paginationData['pageRange']) {
            $pages = range($paginationData['pageCount'] - $paginationData['pageRange'] + 1,
                $paginationData['pageCount']);
        } else {
            if ($paginationData['currentPage'] - $delta < 0) {
                $delta = $paginationData['currentPage'];
            }
            $offset = $paginationData['currentPage'] - $delta;
            $pages = range($offset + 1, $offset + $paginationData['pageRange']);
        }

        $paginationData['pagesInRange'] = $pages;

        $proximity = floor($paginationData['pageRange'] / 2);

        $paginationData['startPage'] = $paginationData['currentPage'] - $proximity;
        $paginationData['endPage'] = $paginationData['currentPage'] + $proximity;

        if ($paginationData['startPage'] < 1) {
            $paginationData['endPage'] = min($paginationData['endPage'] + (1 - $paginationData['startPage']),
                $paginationData['pageCount']);
            $paginationData['startPage'] = 1;
        }

        if ($paginationData['endPage'] > $paginationData['pageCount']) {
            $paginationData['startPage'] = max($paginationData['startPage'] - ($paginationData['endPage'] - $paginationData['pageCount']),
                1);
            $paginationData['endPage'] = $paginationData['pageCount'];
        }

        $paginationData['previousPage'] = $paginationData['currentPage'] > 1;
        $paginationData['nextPage'] = $paginationData['currentPage'] < $paginationData['pageCount'];

        return $paginationData;

    }
}