<?php

namespace Lunar\Article;

use Lunar\Base\BaseModel;
use Spatie\Image\Manipulations;

class ArticleMediaConversions
{
    public function apply(BaseModel $model)
    {
        $conversions = [
            'large' => [
                'width' => 740,
                'height' => 460,
            ],
            'medium' => [
                'width' => 350,
                'height' => 220,
            ],
        ];

        foreach ($conversions as $key => $conversion) {
            $model->addMediaConversion($key)
                ->fit(
                    Manipulations::FIT_CROP,
                    $conversion['width'],
                    $conversion['height']
                )->keepOriginalImageFormat();
        }
    }
}
