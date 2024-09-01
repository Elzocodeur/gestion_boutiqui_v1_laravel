<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use App\Enums\StatusResponseEnum;
use Exception;
use App\Http\Requests\UpdateStockRequest;


class ArticleController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }

    use RestResponseTrait;


    public function index(Request $request)
{
    $this->authorize('viewAny', Article::class);
    $include = $request->has('include') ? [$request->input('include')] : [];

    // Start a query using QueryBuilder for advanced filtering and including related models
    $query = QueryBuilder::for(Article::class)
        ->allowedFilters(['libelle'])
        ->allowedIncludes(['related_models']);

    // Check for the 'disponible' query parameter and filter based on it
    if ($request->has('disponible')) {
        $disponible = $request->input('disponible');

        if ($disponible === 'oui') {
            // Only articles with quantity greater than 0
            $query->where('quantite', '>', 0);
        } elseif ($disponible === 'non') {
            // Only articles with quantity equal to 0
            $query->where('quantite', '=', 0);
        }
    }

    // Execute the query and get the filtered articles
    $articles = $query->get();

    // Return the articles as a resource collection
    return new ArticleCollection($articles);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $this->authorize('create', Article::class);
        try {
            $articleData = $request->only('libelle','reference', 'prix', 'quantite');
            $article = Article::create($articleData);

            return $this->sendResponse(new ArticleResource($article));
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);

        }
    }
        /**
     * Display the specified resource.
     */
    public function show($id){
        $article = Article::find($id);
        $this->authorize('view', $article);
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        $this->authorize('update', $article);
        try {
            $articleData = $request->only('libelle','reference', 'prix', 'quantite');
            $article->update($articleData);

            return $this->sendResponse(new ArticleResource($article));
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);

        }
    }

    public function updateStock(Request $request)
    {
        $this->authorize('updateStock', Article::class);
        $articleData = $request->input('articles'); // Assuming the input is an array of ['id' => 'quantity']

        $notFoundArticles = [];
        DB::beginTransaction();

        try {
            foreach ($articleData as $articleId => $quantity) {
                $article = Article::find($articleId);

                if ($article) {
                    $article->quantite += $quantity;
                    $article->save();
                } else {
                    $notFoundArticles[] = ['id' => $articleId, 'quantite' => $quantity];
                }
            }

            DB::commit();

            return $this->sendResponse([
                'updatedArticles' => $articleData,
                'notFoundArticles' => $notFoundArticles
            ], StatusResponseEnum::SUCCESS, 200);

        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }


    public function updateStockById(UpdateStockRequest $request, $id): JsonResponse
    {
        $this->authorize('updateStock', Article::class);
        $article = Article::find($id);

        if (!$article) {
            return $this->sendResponse(['error' => 'Article non trouvé'], StatusResponseEnum::ECHEC, 404);
        }

        $article->quantite = $request->input('qteStock');
        $article->save();

        return $this->sendResponse($article, StatusResponseEnum::SUCCESS, 200, 'Quantité de stock mise à jour');
    }

    /**
     * Remove the specified resource from storage using soft delete.
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        try {
            $article->delete();

            return $this->sendResponse(null, StatusResponseEnum::SUCCESS, 204);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);

        }
    }




    // afficher un article par son libelle
    public function getByLibelle(Request $request)
    {
        $this->authorize('viewAny', Article::class);
        // Validation de la requête pour s'assurer que 'libelle' est présent et est une chaîne
        $request->validate([
            'libelle' => 'required|string',
        ]);

        // Recherche de l'article par libelle
        $libelle = $request->input('libelle');
        $article = Article::where('libelle', $libelle)->first();

        // Vérification si l'article existe
        if ($article) {
            return response()->json([
                'status' => 'success',
                'data' => $article
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Article non trouvé'
            ], 404);
        }
    }




}
