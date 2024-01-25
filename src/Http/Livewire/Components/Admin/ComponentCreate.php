<?php

namespace Lunar\Article\Http\Livewire\Components\Admin;

use Lunar\Article\Models\Article;

class ComponentCreate extends AbstractArticle
{

    /**
     * Called when the component is mounted.
     *
     * @return void
     */
    public function mount()
    {
        $this->article = new Article([
            'status' => 'draft',
            'title' => 'draft',
        ]);

    }


    public function updatedArticleTitle()
    {
        $this->customName = [
            [
                "data" => $this->article->title,
                "type" => "Lunar\FieldTypes\Text",
                "handle" => "name"
            ]
        ];

    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('article::livewire.components.admin.create')
            ->layout('adminhub::layouts.base');
    }


    protected function getSlotContexts()
    {
        return ['article.all', 'article.create'];
    }
}
