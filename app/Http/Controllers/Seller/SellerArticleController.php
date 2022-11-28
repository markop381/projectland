<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use function response;

class SellerArticleController extends Controller
{
    public function update(Request $request, Article $article)
    {
        $article->update($request->only('quantity', 'discount'));

        return response(new ArticleResource($article));
    }
}
