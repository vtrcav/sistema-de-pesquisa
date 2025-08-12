<?php
namespace App\Controller;

use App\Service\SearchService;

final class SearchController
{
    public function __construct(private SearchService $service)
    {
    }

    public function search(): void
    {
        $p = isset($_GET['p']) ? trim((string)$_GET['p']) : '';
        $t = isset($_GET['t']) ? trim((string)$_GET['t']) : '';

        if ($p === '' || $t === '') {
            header('Location: https://servicos.hnr.ma/arquivo');
            exit;
        }

        $validTypes = ['atendimento', 'internacao', 'agendamento'];
        if (!in_array($t, $validTypes, true)) {
            header('Location: index.php');
            exit;
        }

        $results = $this->service->search($p, $t);

        $viewData = [
            'searchTerm' => $p,
            'type' => $t,
            'results' => $results,
        ];

        $this->render(__DIR__ . '/../View/templates/search_results.php', $viewData);
    }

    private function render(string $templateFile, array $viewData): void
    {
        // Variáveis disponíveis no template: $viewData
        require __DIR__ . '/../View/templates/header.php';
        require $templateFile;
        require __DIR__ . '/../View/templates/footer.php';
    }
}


