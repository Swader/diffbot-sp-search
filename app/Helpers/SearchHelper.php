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
        $this->twitterCheck($queryParams);
        $this->dateCheck($queryParams);
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

                $string = 'author:' . $authorFragments[0];
                if (count($authorFragments) > 1) {
                    unset($authorFragments[0]);
                    $string .= ' AND author:' . implode(' AND author:',
                            $authorFragments);
                }

                return '(' . $string . ')';
            }, explode(',', $queryParams['authors']));

            $this->strings[] = '(' . ((count($authors) > 1)
                    ? implode(' OR ', $authors)
                    : trim($authors[0], '()')) . ')';
        }
    }

    protected function twitterCheck(array $queryParams)
    {
        if (isset($queryParams['twitter']) && !empty($queryParams['twitter'])) {

            $twitters = array_map(function ($item) {
                return 'meta.twitter.twitter.creator:"@' . trim($item,
                    '@ ') . '"';
            }, explode(',', $queryParams['twitter']));

            $this->strings[] = '(' . ((count($twitters) > 1)
                    ? implode(' OR ', $twitters)
                    : trim($twitters[0], '()')) . ')';
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
            if (!empty($kany)) {
                $string .= ' AND ';
            }
            $string .= (count($kall) > 1) ? implode(' AND ', $kall) : $kall[0];
        }

        if (!empty($string)) {
            $this->strings[] = '(' . $string . ')';
        }
    }

    protected function dateCheck(array $queryParams)
    {
        if (isset($queryParams['date-from']) && !empty($queryParams['date-from'])) {
            $this->appendStrings[] = 'min:date:' . strtotime($queryParams['date-from']);
        }

        if (isset($queryParams['date-to']) && !empty($queryParams['date-to'])) {
            $this->appendStrings[] = 'max:date:' . strtotime($queryParams['date-to']);
        }
    }
}