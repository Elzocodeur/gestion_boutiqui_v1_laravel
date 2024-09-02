<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Titre de votre API",
 *         version="1.0.0",
 *         description="Description de votre API",
 *         @OA\Contact(
 *             email="support@votreapi.com"
 *         ),
 *         @OA\License(
 *             name="Licence de votre API",
 *             url="URL de la licence"
 *         )
 *     )
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
