<?php

namespace Lunar\Article\Http\Livewire\Admin;

use Livewire\Component;
use Lunar\Article\Models\Article;

class Show extends Component
{
    /**
     * The Product we are currently editing.
     */
    public Article $article;

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {

        return view('article::livewire.admin.show')
            ->layout('adminhub::layouts.app', [
                'title' => __('article::components.article.index.edit_article'),
            ]);
    }
}
