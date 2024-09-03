<?php
namespace App\Repositories;

interface ArticleRepository
{
    public function create(array $data);
    public function find(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findById(int $id);
    public function findByEtat(string $etat);
    public function findByLibelle(string $libelle);

    public function newQuery();

}

