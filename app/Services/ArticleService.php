<?php
namespace App\Services;

interface ArticleService
{
    public function createArticle(array $data);
    public function updateArticle(int $id, array $data);
    public function deleteArticle(int $id);
    public function getArticleById(int $id);
    public function filterArticlesByEtat(string $etat);
    public function updateStock(array $articleData);
    public function updateStockById(int $id, int $quantity);
    public function getByLibelle(string $libelle);
    
}
