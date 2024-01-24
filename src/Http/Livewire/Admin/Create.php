<?php

namespace Lunar\Article\Http\Livewire\Admin;

use Livewire\Component;

class Create extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('article::livewire.admin.create')
            ->layout('adminhub::layouts.app', [
                'title' =>  __('article::components.article.index.create_article'),
            ]);
    }
}
