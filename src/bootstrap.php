<?php

require_once __DIR__ . '/core/functions.php';

require_once __DIR__ . '/Support/config.php';
require_once __DIR__ . '/Support/Database.php';

require_once __DIR__ . '/Repository/SearchRepository.php';
require_once __DIR__ . '/Service/SearchService.php';
require_once __DIR__ . '/Controller/SearchController.php';

use App\Support\Database;
use App\Repository\SearchRepository;
use App\Service\SearchService;

$config = require __DIR__ . '/Support/config.php';
$pdo = Database::makePdo($config['db']);

$repository = new SearchRepository($pdo);
$searchService = new SearchService($repository);

$container = [
    'searchService' => $searchService,
];


