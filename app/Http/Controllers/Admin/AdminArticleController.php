<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminArticleStoreRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use function response;

class AdminArticleController extends Controller
{
    public function store(AdminArticleStoreRequest $request)
    {
        $imagePath = $this->saveImage($request->image);

        $article = Article::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $imagePath,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'discount' => $request->discount
        ]);

        return response(new ArticleResource($article));
    }

    public function index()
    {
        $articles = Article::all();

        return response(ArticleResource::collection($articles));
    }

    public function show(Article $article)
    {
        return response(new ArticleResource($article));
    }

    public function update(Request $request, Article $article)
    {
        $article->update($request->all());

        return response(new ArticleResource($article));
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return response(['success' => "Article deleted", 'code' => 200], 200);
    }

    protected function saveImage($image)
    {
        $imageName = time() . '.' . $image->extension();
        $image->move(storage_path('app/public/images'), $imageName);

        return '/storage/images/' . $imageName;
    }
}
