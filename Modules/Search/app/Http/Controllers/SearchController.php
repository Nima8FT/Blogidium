<?php

namespace Modules\Search\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Modules\Article\Models\Article as ArticleModel;
use Modules\Search\Http\Requests\SearchRequest;

class SearchController extends Controller
{
    public function search(SearchRequest $request)
    {
        $search = $request->validated();

//        search base on milisearch
//        for use this search uncomment searchable in model Modules\Article\Models\Article
//        and comment indexable in model App\Models\Article
//        and uncomment the code below is here
        //        return ArticleModel::search($search['search'])->get();

//        search base on elasticsearch
//        for use this search comment searchable in model Modules\Article\Models\Article
//        and uncomment indexable in model App\Models\Article
//        and uncomment the code below is here
//        default we use elasticsearch
        return Article::search($search['search']);
    }
}
