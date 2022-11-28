<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuyerArticleIndexRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\OrderResource;
use App\Models\Article;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerArticleController extends Controller
{
    public function index(BuyerArticleIndexRequest $request)
    {
        $articles = Article::where('quantity', '>', 0)
            ->when($request->has('name'), function ($q) {
                $q->where('name', \request()->name);
            })
            ->when($request->has('category_id'), function ($q) {
                $q->where('category_id', \request()->category_id);
            })
            ->when($request->has('sortPriceType'), function ($q) {
                $q->orderBy('price', \request()->sortPriceType);
            })
            ->when($request->has('sortDiscountType'), function ($q) {
                $q->orderBy('discount', \request()->sortDiscountType);
            })
            ->get();

        return response(ArticleResource::collection($articles));
    }

    public function buy(Request $request, Article $article)
    {
        if ($article->quantity < $request->quantity) {
            return response(['error' => "There is no item on stock. There is ".$article->quantity." article available.", 'code' => 404], 404);
        }

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'article_id' => $article->id,
            'quantity' => $request->quantity
        ]);

        $article->decrement('quantity', $request->quantity);

        return response(new OrderResource($order));
    }
}
