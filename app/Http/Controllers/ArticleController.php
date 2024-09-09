<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Exception;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateStockRequest;



class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Article::class);
        try {
            $articles = $this->articleService->filterArticlesByEtat($request->input('disponible'));
            return response()->json($articles, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreArticleRequest $request)
    {
        $this->authorize('create', Article::class);
        $article = $this->articleService->createArticle($request->validated());
        return response()->json($article);
    }

    // public function update(UpdateArticleRequest $request, $id)
    // {
    //     $this->authorize('update', Article::class);
    //     $article = $this->articleService->updateArticle($id, $request->validated());
    //     return response()->json($article);
    // }

    public function update(UpdateArticleRequest $request, $id)
{
    // $this->authorize('update'); // Passer l'article à la politique
    $article = Article::findOrFail($id); // Trouver l'article par son ID

    $article = $this->articleService->updateArticle($id, $request->validated());
    return response()->json($article);
}


    public function destroy($id)
    {

        $this->authorize('delete', Article::class);
        $this->articleService->deleteArticle($id);
        return response()->json(['message' => 'Article supprimé']);
    }

    public function show($id)
    {

        $this->authorize('view', Article::class);
        $article = $this->articleService->getArticleById($id);
        return response()->json($article);
    }



    public function updateStock(Request $request)
    {

        $this->authorize('updateStock', Article::class);
        $articleData = $request->input('articles'); // Array of ['id' => 'quantity']

        try {
            $result = $this->articleService->updateStock($articleData);

            return response()->json([
                'status' => 'success',
                'data' => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStockById(UpdateStockRequest $request, $id): JsonResponse
    {
        $this->authorize('updateStock', Article::class);

        try {
            $article = $this->articleService->updateStockById($id, $request->input('qteStock'));

            return response()->json([
                'status' => 'success',
                'data' => $article,
                'message' => 'Quantité de stock mise à jour'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function getByLibelle(Request $request)
    {
        $this->authorize('viewAny', Article::class);

        $request->validate([
            'libelle' => 'required|string',
        ]);

        try {
            $article = $this->articleService->getByLibelle($request->input('libelle'));

            return response()->json([
                'status' => 'success',
                'data' => $article
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
