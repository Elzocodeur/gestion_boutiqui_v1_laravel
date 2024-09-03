<?php

namespace App\Repositories;

use App\Models\Article;

class ArticleRepositoryImplemente implements ArticleRepository
{
    public function create(array $data)
    {
        return Article::create($data);
    }

    public function find(int $id)
    {
        return Article::find($id);
    }

    public function update(int $id, array $data)
    {
        $article = $this->find($id);
        $article->update($data);
        return $article;
    }

    public function delete(int $id)
    {
        $article = $this->find($id);
        return $article->delete();
    }

    public function findById(int $id)
    {
        return Article::find($id);
    }

    public function findByEtat(string $etat)
    {
        return Article::where('etat', $etat)->get();
    }

    public function findByLibelle(string $libelle)
    {
        return Article::where('libelle', $libelle)->first();
    }
    public function newQuery()
    {
        return Article::query();
    }
}
