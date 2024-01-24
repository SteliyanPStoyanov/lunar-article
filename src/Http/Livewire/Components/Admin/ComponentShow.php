<?php

namespace Lunar\Article\Http\Livewire\Components\Admin;

class ComponentShow extends AbstractArticle
{
    public  $deleteConfirm;

    /**
     * Called when the component is mounted.
     *
     * @return void
     */
    public function mount()
    {

    }

    /**
     * Delete the product.
     *
     * @return void
     */
    public function delete()
    {
        $this->article->delete();
        $this->notify(__('article::notifications.article.deleted'),'hub.article.index');
    }


    /**
     * Restore the product.
     *
     * @return void
     */
    public function restore()
    {
        $this->article->restore();
        $this->showRestoreConfirm = false;
        $this->notify(__('article::notifications.article.article_restored'));
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('article::livewire.components.admin.show')->layout('adminhub::layouts.base');
    }

    public function deleteForce()
    {
        if($this->deleteConfirm === $this->article->title){
            $this->article->forceDelete();
            $this->notify(__('article::notifications.article.deletedForce'),'hub.article.index');
        }
    }

    protected function getSlotContexts()
    {
        return ['article.all', 'article.show'];
    }
}
