<?php
namespace App\Service;

use App\Repository\SearchRepository;

final class SearchService
{
    public function __construct(private SearchRepository $repository)
    {
    }

    public function search(string $term, string $type): array
    {
        $detected = $this->detectType($term);
        return $this->repository->search($type, $detected, $term);
    }

    private function detectType(string $term): string
    {
        $t = trim($term);
        if (preg_match('/^\d{15}$/', $t)) {
            return 'cns_exato';
        }
        if (preg_match('/^\d{2,8}$/', $t)) {
            return 'prontuario_exato';
        }
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $t) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $t)) {
            return 'data';
        }
        if (preg_match('/^\d{11}$/', $t) || preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $t)) {
            return 'cpf';
        }
        return 'nome';
    }
}


