<?php

namespace Lunar\Article\Http\Livewire\Components\Admin;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Hub\Models\SavedSearch;
use Lunar\LivewireTables\Components\Columns\BadgeColumn;
use Lunar\LivewireTables\Components\Columns\TextColumn;
use Lunar\LivewireTables\Components\Filters\CheckboxFilter;
use Lunar\LivewireTables\Components\Filters\SelectFilter;
use Lunar\LivewireTables\Components\Table;
use Lunar\Article\Tabels\ArticleTableBuilder;

class ComponentTable extends Table
{
    use Notifies;

    /**
     * {@inheritDoc}
     */
    protected $tableBuilderBinding = ArticleTableBuilder::class;

    /**
     * {@inheritDoc}
     */
    public bool $searchable = true;

    /**
     * {@inheritDoc}
     */
    public bool $canSaveSearches = true;

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'saveSearch' => 'handleSaveSearch',
    ];

    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $this->tableBuilder->addFilter(
            SelectFilter::make('status')->options(function () {
                $statuses = collect([
                    'published' => __('article::components.article.index.published'),
                    'draft' => __('article::components.article.index.draft'),
                ]);
                return collect([
                    null => __('article::components.article.index.all_statuses'),
                ])->merge($statuses);
            })->query(function ($filters, $query) {
                $value = $filters->get('status');
                if ($value) {
                    $query->whereStatus($value);
                }
            })
        );

        $this->tableBuilder->addFilter(
            CheckboxFilter::make('deleted')->query(function ($filters, $query) {
                $value = $filters->get('deleted');

                if ($value) {
                    $query->onlyTrashed();
                }
            })
        );
        $this->tableBuilder->baseColumns([
            BadgeColumn::make('status', function ($record) {
                $Status = 'article::components.article.index.' . ($record->deleted_at ? 'deleted' : $record->status);
                return __($Status);
            })->states(function ($record) {
                return [
                    'success' => $record->status == 'published' && !$record->deleted_at,
                    'warning' => $record->status == 'draft' && !$record->deleted_at,
                    'danger' => (bool)$record->deleted_at,
                ];
            }),

            TextColumn::make('title', function ($record) {
                return $record->title;
            })->url(function ($record) {
                return route('hub.article.show', $record->id);
            })->heading(
                __('article::tables.headings.title')
            ),
            TextColumn::make('created_at')->sortable(true)->value(function ($record) {
                return $record->created_at?->format('Y/m/d @ H:ia');
            })->heading(
                __('article::tables.headings.created_at')
            ),

        ]);
    }

    /**
     * Remove a saved search record.
     *
     * @param int $id
     * @return void
     */
    public function deleteSavedSearch($id)
    {
        SavedSearch::destroy($id);

        $this->resetSavedSearch();

        $this->notify(
            __('article::notifications.saved_searches.deleted')
        );
    }

    /**
     * Save a search.
     *
     * @return void
     * @throws ValidationException
     */
    public function saveSearch()
    {
        $this->validateOnly('savedSearchName', [
            'savedSearchName' => 'required',
        ]);

        auth()->getUser()->savedSearches()->create([
            'name' => $this->savedSearchName,
            'term' => $this->query,
            'component' => $this->getName(),
            'filters' => $this->filters,
        ]);

        $this->notify( __('article::notifications.article.search_saved'));

        $this->savedSearchName = null;

        $this->emit('savedSearch');
    }

    /**
     * Return the saved searches available to the table.
     */
    public function getSavedSearchesProperty(): Collection
    {

        return auth()->getUser()->savedSearches()->whereComponent(
            $this->getName()
        )->get()->map(function ($savedSearch) {
            return [
                'key' => $savedSearch->id,
                'label' => $savedSearch->name,
                'filters' => $savedSearch->filters,
                'query' => $savedSearch->term,
            ];
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        $filters = $this->filters;
        $query = $this->query;

        if ($this->savedSearch) {
            $search = $this->savedSearches->first(function ($search) {
                return $search['key'] == $this->savedSearch;
            });

            if ($search) {
                $filters = $search['filters'];
                $query = $search['query'];
            }
        }

        return $this->tableBuilder
            ->searchTerm($query)
            ->queryStringFilters($filters)
            ->perPage($this->perPage)
            ->sort(
                $this->sortField ?: 'created_at',
                $this->sortDir ?: 'desc',
            )
            ->getData();
    }
}
