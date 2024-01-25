<?php

namespace Lunar\Article\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lunar\Base\BaseModel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Lunar\Base\Casts\AsAttributeData;
use Lunar\Base\Traits\HasMacros;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\HasTranslations;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Base\Traits\Searchable;
use Lunar\Article\ArticleMediaConversions;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;

/**
 * @property int $id
 * @property string $status
 * @property array $attribute_data
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class Article extends BaseModel implements SpatieHasMedia
{
    use HasMacros;
    use InteractsWithMedia;
    use HasTranslations;
    use HasUrls;
    use LogsActivity;
    use Searchable;
    use SoftDeletes;

    /**
     * Define which attributes should be
     * fillable during mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'attribute_data',
        'status',
        'title',
        'description',
        'short_description',
        'article_category_id',
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
     * Return the product images relation.
     *
     * @return MorphMany
     */
    public function images()
    {
        return $this->media()->where('collection_name', 'images');
    }

    /**
     * Apply the status scope.
     *
     * @param string $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, $status)
    {
        return $query->whereStatus($status);
    }

    /**
     * Return the brand relationship.
     *
     * @return BelongsTo
     */
    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    /**
     * @param Media|null $media
     * @return void
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {

        $conversionClasses = [ArticleMediaConversions::class];


        foreach ($conversionClasses as $classname) {
            app($classname)->apply($this);
        }

        // Add a conversion that the hub uses...
        $this->addMediaConversion('small')
            ->fit(Manipulations::FIT_FILL, 300, 300)
            ->sharpen(10)
            ->keepOriginalImageFormat();
    }

        /**
     * Relationship for thumbnail.
     */
    public function thumbnail(): MorphOne
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('custom_properties->primary', true);
    }
}
