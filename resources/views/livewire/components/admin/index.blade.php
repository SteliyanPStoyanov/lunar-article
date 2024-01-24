<div class="flex-col space-y-4">
    <div class="flex items-center justify-between">
        <strong class="text-lg font-bold md:text-2xl">
            {{ __('article::components.article.index.title') }}
        </strong>
        <div class="text-right">
            <x-hub::button tag="a" href="{{ route('hub.article.create') }}">
                {{ __('article::components.article.index.create_article') }}</x-hub::button>
        </div>
    </div>
    @livewire('article.components.admin.component-table')
</div>
