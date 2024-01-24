<?php

namespace Lunar\Article\ActivityLog;

use Lunar\Hub\Base\ActivityLog\AbstractRender;
use Spatie\Activitylog\Models\Activity;

class Restore extends AbstractRender
{
    public function getEvent(): string
    {
        return 'restored';
    }

    public function render(Activity $log)
    {
        return view('article::partials.activity-log.restore', [
            'log' => $log,
        ]);
    }
}
