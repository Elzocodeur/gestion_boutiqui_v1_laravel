<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use Exception;
use Illuminate\Support\Facades\DB;



class ArticleServicesImplemente implements ArticleService
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function createArticle(array $data)
    {
        return $this->articleRepository->create($data);
    }

    public function updateArticle(int $id, array $data)
    {
        return $this->articleRepository->update($id, $data);
    }

    public function deleteArticle(int $id)
    {
        return $this->articleRepository->delete($id);
    }

    public function getArticleById(int $id)
    {
        return $this->articleRepository->findById($id);
    }

    public function filterArticlesByEtat(?string $etat = null)
    {
        $query = $this->articleRepository->newQuery();

        if ($etat === 'oui') {
            $query->where('quantite', '>', 0);
        } elseif ($etat === 'non') {
            $query->where('quantite', '=', 0);
        }

        return $query->get();
    }
    public function updateStock(array $articleData)
    {
        $notFoundArticles = [];
        DB::beginTransaction();

        try {
            foreach ($articleData as $articleId => $quantity) {
                $article = $this->articleRepository->find($articleId);

                if ($article) {
                    $article->quantite += $quantity;
                    $this->articleRepository->update($articleId, ['quantite' => $article->quantite]);
                } else {
                    $notFoundArticles[] = ['id' => $articleId, 'quantite' => $quantity];
                }
            }

            DB::commit();

            return [
                'updatedArticles' => $articleData,
                'notFoundArticles' => $notFoundArticles
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Erreur lors de la mise à jour du stock: ' . $e->getMessage());
        }
    }

    public function updateStockById(int $id, int $quantity)
    {
        $article = $this->articleRepository->find($id);

        if (!$article) {
            throw new Exception('Article non trouvé');
        }

        $article->quantite = $quantity;
        return $this->articleRepository->update($id, ['quantite' => $quantity]);
    }



    public function getByLibelle(string $libelle)
    {
        $article = $this->articleRepository->findByLibelle($libelle);

        if (!$article) {
            throw new Exception('Article non trouvé');
        }

        return $article;
    }
}
