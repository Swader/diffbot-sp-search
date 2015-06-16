<?php

namespace SitePoint\Helpers;

class SearchHelper
{
    protected $strings = [];
    protected $appendStrings = [];

    public function stringFromParams(array $queryParams)
    {
        $this->authorCheck($queryParams);
        $this->keywordCheck($queryParams);
        $this->sortCheck($queryParams);

        if (empty($this->strings)) {
            die("Please provide at least *some* search values!");
        }

        return ((count($this->strings) > 1) ? implode(' AND ',
            $this->strings) : $this->strings[0]) . ' ' . implode(' ',
            $this->appendStrings);
    }

    protected function sortCheck(array $queryParams)
    {
        if (isset($queryParams['sort']) && !empty($queryParams['sort'])) {
            $operator = (isset($queryParams['dir']) && $queryParams['dir'] == 'asc') ? "revsortby:" : "sortby:";
            $this->appendStrings[] = $operator . $queryParams['sort'];
        } else {
            $this->appendStrings[] = "sortby:date";
        }
    }

    protected function authorCheck(array $queryParams)
    {
        if (isset($queryParams['authors']) && !empty($queryParams['authors'])) {

            $authors = array_map(function ($item) {

                $authorFragments = explode(' ', trim($item));

                $string = 'author:'.$authorFragments[0];
                if (count($authorFragments) > 1) {
                    unset($authorFragments[0]);
                    $string .= ' AND author:' . implode(' AND author:', $authorFragments);
                }
                return '('.$string.')';

                //return 'author:"' . trim($item) . '"';
            }, explode(',', $queryParams['authors']));

            $this->strings[] = '(' . ((count($authors) > 1)
                    ? implode(' OR ', $authors)
                    : trim($authors[0], '()')) . ')';
        }
    }

    protected function keywordCheck(array $queryParams)
    {
        $kany = [];
        if (isset($queryParams['keywords_any']) && !empty($queryParams['keywords_any'])) {
            $kany = array_map(function ($item) {
                return trim($item);
            }, explode(',', $queryParams['keywords_any']));
        }

        $kall = [];
        if (isset($queryParams['keywords_all']) && !empty($queryParams['keywords_all'])) {
            $kall = array_map(function ($item) {
                return trim($item);
            }, explode(',', $queryParams['keywords_all']));
        }

        $string = '';
        if (!empty($kany)) {
            $string .= (count($kany) > 1) ? '(' . implode(' OR ',
                    $kany) . ')' : $kany[0];
        }

        if (!empty($kall)) {
            $string .= ' AND ';
            $string .= (count($kall) > 1) ? implode(' AND ', $kall) : $kall[0];
        }

        if (!empty($string)) {
            $this->strings[] = '(' . $string . ')';
        }
    }
}