<?php

namespace App\Models\Indexes;

use App\Models\Article;
use PDPhilip\ElasticLens\Builder\IndexBuilder;
use PDPhilip\ElasticLens\Builder\IndexField;
use PDPhilip\ElasticLens\IndexModel;
use PDPhilip\Elasticsearch\Schema\Blueprint;

class IndexedArticle extends IndexModel
{
    protected $baseModel = Article::class;

    protected int $buildChunkRate = 0;

    public function fieldMap(): IndexBuilder
    {
        return IndexBuilder::map(Article::class, function (IndexField $field) {});
    }

    //    public function migrationMap(): callable
    //    {
    //        return function (Blueprint $index) {};
    //    }
}
