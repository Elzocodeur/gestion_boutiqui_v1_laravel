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
    use RestResponseTrait;

    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }


            /**
     * @OA\Get(
     *     path="/api/v1/articles",
     *     tags={"Articles"},
     *     summary="Obtenir la liste des articles",
     *     description="Retourne la liste des articles avec des filtres optionnels.",
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Inclure des relations",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="disponible",
     *         in="query",
     *         description="Filtrer les articles par disponibilité (oui/non)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des articles retournée avec succès",
     *         @OA\JsonContent(type="array", @OA\Items(ref="ArticleResource"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */

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
     * @OA\Post(
     *     path="/api/v1articles",
     *     tags={"Articles"},
     *     summary="Créer un nouvel article",
     *     description="Ajoute un nouvel article à la base de données.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="StoreArticleRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article créé avec succès",
     *         @OA\JsonContent(ref="ArticleResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"Bearer":{}}}
     * )
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
     * @OA\Get(
     *     path="/api/v1/articles/{id}",
     *     tags={"Articles"},
     *     summary="Afficher un article par ID",
     *     description="Retourne un article spécifique par son ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article retourné avec succès",
     *         @OA\JsonContent(ref="ArticleResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function show($id){
        $article = Article::find($id);
        $this->authorize('view', $article);
        return new ArticleResource($article);
    }

        /**
     * @OA\Put(
     *     path="/api/v1/articles/{id}",
     *     tags={"Articles"},
     *     summary="Mettre à jour un article",
     *     description="Met à jour un article existant.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="UpdateArticleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article mis à jour avec succès",
     *         @OA\JsonContent(ref="ArticleResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"Bearer":{}}}
     * )
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

        /**
     * @OA\Post(
     *     path="/api/v1/articles/stock",
     *     tags={"Articles"},
     *     summary="Mettre à jour les stocks des articles",
     *     description="Met à jour les quantités de stock pour plusieurs articles.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="articles", type="array", @OA\Items(ref="UpdateStockRequest")))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stocks mis à jour avec succès",
     *         @OA\JsonContent(type="object", @OA\Property(property="updatedArticles", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="quantite", type="integer"))), @OA\Property(property="notFoundArticles", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="quantite", type="integer"))))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
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

        /**
     * @OA\Patch(
     *     path="/api/v1/articles/{id}",
     *     tags={"Articles"},
     *     summary="Mettre à jour le stock d'un article par ID",
     *     description="Met à jour la quantité de stock d'un article spécifique.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="UpdateStockRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock mis à jour avec succès",
     *         @OA\JsonContent(ref="ArticleResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
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
     * @OA\Delete(
     *     path="/api/v1/articles/{id}",
     *     tags={"Articles"},
     *     summary="Supprimer un article",
     *     description="Supprime un article en utilisant la suppression douce (soft delete).",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Article supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"Bearer":{}}}
     * )
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
