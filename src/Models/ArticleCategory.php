<?php

namespace Lunar\Article\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\BaseModel;
use Lunar\Base\Casts\AsAttributeData;

class ArticleCategory extends BaseModel
{
    protected $table = 'article_category';

    /**
     * Define which attributes should be
     * fillable during mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'attribute_data',
        'name',
    ];

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attribute_data' => AsAttributeData::class,
    ];

        /**
     * Return the product relationship.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
