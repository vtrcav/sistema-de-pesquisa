<?php
// Ponto de entrada leve: delega para o controller
ini_set('memory_limit', '128M');

require_once __DIR__ . '/../src/bootstrap.php';

use App\Controller\SearchController;

$controller = new SearchController($container['searchService']);
$controller->search();