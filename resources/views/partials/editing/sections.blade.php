<div class="flex justify-between items-center">
    <div class="flex items-center gap-4">
        <a href="{{ route('hub.article.index') }}"
           class="text-gray-600 rounded bg-gray-50 hover:bg-sky-500 hover:text-white"
           title="{{ __('article::catalogue.article.show.back_link_title') }}">
            <x-hub::icon ref="chevron-left"
                         style="solid"
                         class="w-8 h-8"/>
        </a>

        <h1 class="text-xl font-bold md:text-xl">
            @if ($article->id)
                {{ $article->title }}
            @else
                {{ __('article::global.new_article') }}
            @endif
        </h1>
    </div>
    <div>
        <x-hub::model-url :model="$article" :preview="$article->status == 'draft'"/>
    </div>
</div>

<x-hub::layout.bottom-panel>
    <form wire:submit.prevent="save">
        <div class="flex justify-end gap-6">
            @include('article::partials.status-bar')

            <x-hub::button type="submit">
                {{ __('article::catalogue.article.show.save_btn') }}
            </x-hub::button>
        </div>
    </form>
</x-hub::layout.bottom-panel>

<div class="pb-24 mt-8 lg:gap-8 lg:flex lg:items-start">
    <div class="space-y-6 lg:flex-1">
        <div class="space-y-6">
            @foreach ($this->getSlotsByPosition('top') as $slot)
                <div id="{{ $slot->handle }}">
                    <div>
                        @livewire($slot->component, ['slotModel' => $article], key('top-slot-' . $slot->handle))
                    </div>
                </div>
            @endforeach
            <div class="shadow sm:rounded-md">
                <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md sm:p-6">
                    <header>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            {{ __('article::partials.article.basic-information.heading') }}
                        </h3>
                    </header>
                    <x-hub::input.group
                        :label="__('article::inputs.article_category.label')"
                        for="article"
                        :errors="$errors->get('article.article_category_id_id') ?: $errors->get('ArticleCategory')"
                    >
                        <div class="flex items-center space-x-4">
                            <div class="grow">
                                @if($useNewCategory)
                                    <x-hub::input.text wire:model="ArticleCategory"/>
                                @else
                                    <x-hub::input.select id="Category" wire:model="article.article_category_id"
                                                         :error="$errors->first('article.article_category_id')">
                                        <option
                                            value>{{ __('adminhub::components.brands.choose_brand_default_option') }}</option>
                                        @foreach($this->articleCategory as $brand)
                                            <option value="{{ $brand->id }}"
                                                    wire:key="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </x-hub::input.select>
                                @endif
                            </div>
                            <div>
                                @if($useNewCategory)
                                    <x-hub::button theme="gray" type="button"
                                                   wire:click="$set('useNewCategory', false)">
                                        {{ __('adminhub::global.cancel') }}
                                    </x-hub::button>
                                @else
                                    <x-hub::button theme="gray" type="button" wire:click="$set('useNewCategory', true)">
                                        {{ __('adminhub::global.add_new') }}
                                    </x-hub::button>
                                @endif

                            </div>
                        </div>
                    </x-hub::input.group>
                    <div class="space-y-4">
                        <x-hub::input.group
                            :label="__('article::inputs.title.label')"
                            for="title"
                            :errors="$errors->get('article.title') ?: $errors->get('article')">
                            <x-hub::input.text id="title" wire:model="article.title"/>
                        </x-hub::input.group>
                        <x-hub::input.group
                            :label="__('article::inputs.short_description.label')"
                            for="short_description"
                            :errors="$errors->get('article.short_description') ?: $errors->get('article')">
                            <x-hub::input.richtext id="short_description" wire:model.defer="article.short_description"
                                                   :initial-value="$article->description"/>
                        </x-hub::input.group>
                        <x-hub::input.group
                            :label="__('article::inputs.description.label')"
                            for="description"
                            :errors="$errors->get('article.description') ?: $errors->get('article')">
                            <x-hub::input.richtext id="description" wire:model.defer="article.description"
                                                   :initial-value="$article->description"
                                                   :options="$this->richTextOption"/>
                        </x-hub::input.group>
                    </div>
                </div>
            </div>
            <div id="attributes">
                @include('adminhub::partials.attributes')
            </div>
            <div id="images">
                @include('article::partials.image-manager', [ 'existing' => $images, 'wireModel' => 'imageUploadQueue', 'filetypes' => ['image/*'], ])
            </div>
            <div id="urls">
                @include('adminhub::partials.urls')
            </div>
            @foreach ($this->getSlotsByPosition('bottom') as $slot)
                <div id="{{ $slot->handle }}">
                    <div>
                        @livewire($slot->component, ['slotModel' => $article], key('bottom-slot-' . $slot->handle))
                    </div>
                </div>
            @endforeach
            @if ($article->id)
                @if($article->deleted_at)
                    <div class="mb-24 bg-white border border-red-300 rounded shadow">
                        <header class="px-6 py-4 text-red-700 bg-white border-b border-red-300 rounded-t">
                            {{ __('adminhub::inputs.danger_zone.title') }}
                        </header>
                        <div class="p-6 text-sm">
                            <div class="grid items-center grid-cols-12 gap-4">
                                <div class="col-span-12 md:col-span-6">
                                    <strong>{{ __('adminhub::partials.forms.brand_delete_brand') }}</strong>

                                    <p class="text-xs text-gray-600">
                                        {{ __('adminhub::partials.forms.brand_name_delete') }}
                                    </p>
                                </div>
                                <div class="col-span-9 lg:col-span-4">
                                    <x-hub::input.text wire:model="deleteConfirm"/>
                                </div>

                                <div class="col-span-3 text-right lg:col-span-2">
                                    <x-hub::button :disabled="false"
                                                   theme="danger"
                                                   wire:click="deleteForce"
                                                   type="button">
                                        {{ __('adminhub::global.delete') }}
                                    </x-hub::button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div
                    @class(['bg-white border rounded shadow',
                        'border-red-300' => !$article->deleted_at,
                        'border-gray-300' => $article->deleted_at,
                    ]) >
                    <header
                        @class(['px-6 py-4 bg-white border-b rounded-t',
                            'border-red-300 text-red-700' => !$article->deleted_at,
                            'border-gray-300 text-gray-700' => $article->deleted_at,
                        ])>
                        @if($article->deleted_at)
                            {{ __('article::inputs.restore_zone.title') }}
                        @else
                            {{ __('article::inputs.danger_zone.title') }}
                        @endif
                    </header>
                    <div class="p-6 text-sm">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 lg:col-span-8">
                                <strong>
                                    @if($article->deleted_at)
                                        {{ __('article::inputs.restore_zone.label', ['model' => 'article']) }}
                                    @else
                                        {{ __('article::inputs.danger_zone.label', ['model' => 'article']) }}
                                    @endif
                                </strong>

                                <p class="text-xs text-gray-600">
                                    @if($article->deleted_at)
                                        {{ __('article::catalogue.article.show.restore_strapLine') }}
                                    @else
                                        {{ __('article::catalogue.article.show.delete_strapLine') }}
                                    @endif

                                </p>
                            </div>

                            <div class="col-span-6 text-right lg:col-span-4">
                                @if($article->deleted_at)
                                    <x-hub::button :disabled="false" wire:click="$set('showRestoreConfirm', true)"
                                                   type="button" theme="green">
                                        {{ __('article::global.restore') }}
                                    </x-hub::button>
                                @else
                                    <x-hub::button :disabled="false" wire:click="$set('showDeleteConfirm', true)"
                                                   type="button" theme="danger">
                                        {{ __('article::global.delete') }}
                                    </x-hub::button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <x-hub::modal.dialog wire:model="showRestoreConfirm">
                    <x-slot name="title">
                        {{ __('article::catalogue.article.show.restore_title') }}
                    </x-slot>
                    <x-slot name="content">
                        {{ __('article::catalogue.article.show.restore_strapLine') }}
                    </x-slot>
                    <x-slot name="footer">
                        <div class="flex items-center justify-end space-x-4">
                            <x-hub::button theme="gray" type="button"
                                           wire:click.prevent="$set('showRestoreConfirm', false)">
                                {{ __('article::global.cancel') }}
                            </x-hub::button>
                            <x-hub::button wire:click="restore" theme="green">
                                {{ __('article::catalogue.article.show.restore_btn') }}
                            </x-hub::button>
                        </div>
                    </x-slot>
                </x-hub::modal.dialog>
                <x-hub::modal.dialog wire:model="showDeleteConfirm">
                    <x-slot name="title">
                        {{ __('article::catalogue.article.show.delete_title') }}
                    </x-slot>
                    <x-slot name="content">
                        {{ __('article::catalogue.article.show.delete_strapLine') }}
                    </x-slot>
                    <x-slot name="footer">
                        <div class="flex items-center justify-end space-x-4">
                            <x-hub::button theme="gray" type="button"
                                           wire:click.prevent="$set('showDeleteConfirm', false)">
                                {{ __('article::global.cancel') }}
                            </x-hub::button>
                            <x-hub::button wire:click="delete" theme="danger">
                                {{ __('article::catalogue.article.show.delete_btn') }}
                            </x-hub::button>
                        </div>
                    </x-slot>
                </x-hub::modal.dialog>
            @endif
            <div class="pt-12 mt-12 border-t">
                @livewire('article.components.activity-log-feed', ['subject' => $article,])
            </div>
        </div>
    </div>

    <x-hub::layout.page-menu>
        <nav class="space-y-2"
             aria-label="Sidebar"
             x-data="{ activeAnchorLink: '' }"
             x-init="activeAnchorLink = window.location.hash">
            @foreach ($this->getSlotsByPosition('top') as $slot)
                <a href="#{{ $slot->handle }}"
                   @class([
                       'flex items-center gap-2 p-2 rounded text-gray-500',
                       'hover:bg-sky-50 hover:text-sky-700' => empty(
                           $this->getSlotErrorsByHandle($slot->handle)
                       ),
                       'text-red-600 bg-red-50' => !empty(
                           $this->getSlotErrorsByHandle($slot->handle)
                       ),
                   ])
                   aria-current="article"
                   x-data="{ linkId: '#{{ $slot->handle }}' }"
                   :class="{
                       'bg-sky-50 text-sky-700 hover:text-sky-500': linkId === activeAnchorLink
                   }"
                   x-on:click="activeAnchorLink = linkId">
                    @if (!empty($this->getSlotErrorsByHandle($slot->handle)))
                        <x-hub::icon ref="exclamation-circle"
                                     class="w-4 text-red-600"/>
                    @endif

                    <span class="text-sm font-medium">
                        {{ $slot->title }}
                    </span>
                </a>
            @endforeach

            @foreach ($this->sideMenu as $item)
                <a href="#{{ $item['id'] }}"
                   @class([
                       'flex items-center gap-2 p-2 rounded text-gray-500',
                       'hover:bg-sky-50 hover:text-sky-700' => empty($item['has_errors']),
                       'text-red-600 bg-red-50' => !empty($item['has_errors']),
                   ])
                   aria-current="article"
                   x-data="{ linkId: '#{{ $item['id'] }}' }"
                   :class="{
                       'bg-sky-50 text-sky-700 hover:text-sky-500': linkId === activeAnchorLink
                   }"
                   x-on:click="activeAnchorLink = linkId">
                    @if (!empty($item['has_errors']))
                        <x-hub::icon ref="exclamation-circle"
                                     class="w-4 text-red-600"/>
                    @endif

                    <span class="text-sm font-medium">
                        {{ $item['title'] }}
                    </span>
                </a>
            @endforeach

            @foreach ($this->getSlotsByPosition('bottom') as $slot)
                <a href="#{{ $slot->handle }}"
                   @class([
                       'flex items-center gap-2 p-2 rounded text-gray-500',
                       'hover:bg-sky-50 hover:text-sky-700' => empty(
                           $this->getSlotErrorsByHandle($slot->handle)
                       ),
                       'text-red-600 bg-red-50' => !empty(
                           $this->getSlotErrorsByHandle($slot->handle)
                       ),
                   ])
                   aria-current="article"
                   x-data="{ linkId: '#{{ $slot->handle }}' }"
                   :class="{
                       'bg-sky-50 text-sky-700 hover:text-sky-500': linkId === activeAnchorLink
                   }"
                   x-on:click="activeAnchorLink = linkId">
                    @if (!empty($this->getSlotErrorsByHandle($slot->handle)))
                        <x-hub::icon ref="exclamation-circle"
                                     class="w-4 text-red-600"/>
                    @endif

                    <span class="text-sm font-medium">
                        {{ $slot->title }}
                    </span>
                </a>
            @endforeach
        </nav>
    </x-hub::layout.page-menu>
</div>
