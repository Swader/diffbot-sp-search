<?php

use SitePoint\Helpers\PaginationHelper;
use SitePoint\Helpers\SearchHelper;
use Swader\Diffbot\Diffbot;
use Swader\Diffbot\Entity\Article;

require_once '../vendor/autoload.php';
require_once '../token.php';
require_once '../app/config/templating.php';

$view = new TemplateConfigurator(TemplateConfigurator::TWIG, true);

$vars = ['showResults' => false];

// Get query params from request
parse_str($_SERVER['QUERY_STRING'], $queryParams);

$resultsPerPage = 50;
$pageRange = 9;

if (!isset($queryParams['page'])) {
    $queryParams['page'] = 1;
}

// Check if the search form was submitted
if (isset($queryParams['search'])) {

    $diffbot = new Diffbot(DIFFBOT_TOKEN);

    // Building the search string
    $searchHelper = new SearchHelper();
    $string = (isset($queryParams['q']) && !empty($queryParams['q']))
        ? $queryParams['q']
        : $searchHelper->stringFromParams($queryParams);

    // Basics
    $search = $diffbot
        ->search($string)
        ->setCol('sp_search')
        ->setStart(($queryParams['page'] - 1) * $resultsPerPage)
        ->setNum($resultsPerPage);

//    die($search->buildUrl());

    // Add to template for rendering
    $results = $search->call();
    $info = $search->call(true);

    // Clean up and alter results
    $uniques = [];
    /** @var Article $article */
    foreach ($results as $i => $article) {

        if (in_array($article->getResolvedPageUrl(), $uniques)) {
            $results->offsetUnset($i);
            continue;
        } else {
            $uniques[] = $article->getResolvedPageUrl();
        }

        if (count($article->getImages())) {
            $article->heroImage = $article->getImages()[0];
        } elseif (isset($article->getMeta()['og']['og:image'])) {
            $article->heroImage = $article->getMeta()['og']['og:image'];
        } elseif (isset($article->getData()['icon'])) {
            $article->heroImage = [
                'url' => $article->getData()['icon'],
                'title' => 'Channel icon'
            ];
        } else {
            $article->heroImage = '/apple-touch-icon.png';
        }
    }

    $ph = new PaginationHelper();
    $vars = [
        'showResults' => true,
        'results' => $results,
        'info' => $info,
        'paginationData' => $ph->getPaginationData(
            $queryParams['page'], $resultsPerPage, $pageRange, $results, $info
        )
    ];
}

$view->setAll($vars);
$view->render('home');