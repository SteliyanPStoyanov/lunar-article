<?php

namespace Lunar\Article\Http\Livewire\Components\Admin;

use Livewire\Component;

class ComponentIndex extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('article::livewire.components.admin.index')
            ->layout('adminhub::layouts.base');
    }
}
