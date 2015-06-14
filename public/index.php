<?php

use SitePoint\Helpers\PaginationHelper;
use SitePoint\Helpers\SearchHelper;
use Swader\Diffbot\Diffbot;

require_once '../vendor/autoload.php';
require_once '../token.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/../template/twig');
$twig = new Twig_Environment($loader
//   , array('cache' => __DIR__ . '/../cache',)
   , array('cache' => false, 'debug' => true)
);

$function = new Twig_SimpleFunction('qprw', function (array $replacements) {
    parse_str($_SERVER['QUERY_STRING'], $qp);
    foreach ($replacements as $k => $v) {
        $qp[$k] = $v;
    }
    return '?'.http_build_query($qp);
});
$twig->addFunction($function);

$vars = [];

// Get query params from request
parse_str($_SERVER['QUERY_STRING'], $queryParams);

$resultsPerPage = 20;
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
        ->setNum($resultsPerPage)
    ;

    // Add to template for rendering
    $results = $search->call();
    $info = $search->call(true);

    $ph = new PaginationHelper();
    $vars = [
        'results' => $results,
        'info' => $info,
        'paginationData' => $ph->getPaginationData(
            $queryParams['page'], $resultsPerPage, $pageRange, $results, $info
        )
    ];

}

echo $twig->render('home.twig', $vars);