<?php

namespace Lunar\Article\Http\Livewire\Components\Admin;

use Illuminate\Validation\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Lunar\Article\Models\ArticleCategory;
use Lunar\Facades\DB;
use Lunar\Hub\Http\Livewire\Traits\CanExtendValidation;
use Lunar\Hub\Http\Livewire\Traits\HasImages;
use Lunar\Hub\Http\Livewire\Traits\HasSlots;
use Lunar\Hub\Http\Livewire\Traits\HasUrls;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Hub\Http\Livewire\Traits\SearchesProducts;
use Lunar\Hub\Http\Livewire\Traits\WithAttributes;
use Lunar\Hub\Http\Livewire\Traits\WithLanguages;
use Lunar\Models\Attribute;
use Lunar\Article\Models\Article;

abstract class AbstractArticle extends Component
{
    use CanExtendValidation;
    use HasImages;
    use HasSlots;
    use HasUrls;
    use Notifies;
    use SearchesProducts;
    use WithAttributes;
    use WithFileUploads;
    use WithLanguages;

    /**
     * The current product we are editing.
     */
    public Article $article;

    public ?string $ArticleCategory = null;

    /**
     * Whether to show the delete confirmation modal.
     *
     * @var bool
     */
    public $showDeleteConfirm = false;

    /**
     * Whether to show the delete confirmation modal.
     *
     * @var bool
     */
    public $showRestoreConfirm = false;

    public bool $useNewCategory = false;

    protected function getListeners()
    {
        return array_merge(
            [
                'updatedAttributes',
                'urlSaved' => 'refreshUrls'
            ],
            $this->getHasImagesListeners(),
            $this->getHasSlotsListeners()
        );
    }

    /**
     * Returns any custom validation messages.
     *
     * @return array
     */
    protected function getValidationMessages()
    {
        return array_merge(
            []

        );
    }

    /**
     * Define the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        $baseRules = [
            'article.status' => 'required|string',
            'article.title' => 'required|string',
            'article.description' => 'required|string',
            'article.article_category_id' => 'nullable',
            'ArticleCategory' => 'nullable',
        ];

        return array_merge(
            $baseRules,
            $this->hasImagesValidationRules(),
            $this->hasUrlsValidationRules(!$this->article->id),
            $this->withAttributesValidationRules()
        );
    }

    /**
     * Define the validation attributes.
     *
     * @return array
     */
    protected function validationAttributes()
    {
        return $this->getUrlsValidationAttributes();
    }


    /**
     * Universal method to handle saving the product.
     *
     * @return void|\Symfony\Component\HttpFoundation\Response
     */
    public function save()
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {

                if ($validator->errors()->count()) {
                    $this->notify(
                        __('article::validation.generic'),
                        level: 'error'
                    );
                }
            });
        })->validate();

        $this->validateUrls();
        $isNew = !$this->article->id;

        DB::transaction(function () use ($isNew) {
            if (!empty($this->customName)) {
                $data = $this->prepareAttributeData($this->customName);
            } else {
                $data = $this->prepareAttributeData();
            }

            $this->article->attribute_data = $data;

            $this->article->article_category_id = $this->article->article_category_id ?: null;

            if ($this->ArticleCategory) {
                $ArticleCategory = ArticleCategory::create([
                    'name' => $this->ArticleCategory
                ]);
                $this->article->article_category_id = $ArticleCategory->id;
                $this->ArticleCategory = null;
                $this->useNewCategory = false;
            }

            $this->article->save();
            $this->saveUrls();
            $this->updateImages();
            $this->updateSlots();
            $this->article->refresh();
            $this->dispatchBrowserEvent('remove-images');
            $this->notify(__('article::notifications.article.is_save'));
        });

        if ($isNew) {
            return redirect()->route('hub.article.show', [
                'article' => $this->article->id,
            ]);
        }
    }

    /**
     * Returns the attribute data.
     *
     * @return array
     */
    public function getAttributeDataProperty()
    {
        return $this->article->attribute_data;
    }


    /**
     * Returns all available attributes.
     *
     * @return void
     */
    public function getAvailableAttributesProperty()
    {
        return Attribute::whereAttributeType(Article::class)->orderBy('position')->get();
    }

    public function getArticleCategoryProperty()
    {
        return ArticleCategory::all();
    }

    /**
     * Return the side menu links.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSideMenuProperty()
    {
        $collect = collect([
            [
                'title' => __('article::menu.article.basic-information'),
                'id' => 'basic-information',
                'has_errors' => $this->errorBag->hasAny([
                    'article.title',
                    'article.description',
                ]),
            ],
            [
                'title' => __('article::menu.article.images'),
                'id' => 'images',
                'has_errors' => $this->errorBag->hasAny([
                    'newImages.*',
                ]),
            ],
            [
                'title' => __('article::menu.article.urls'),
                'id' => 'urls',
                'has_errors' => $this->errorBag->hasAny([
                    'urls',
                    'urls.*',
                ]),
            ],
        ])->reject(fn($item) => ($item['hidden'] ?? false));

        if (count($this->getAvailableAttributesProperty()) > 0) {
            $collect->splice(1, 0, [[
                'title' => __('article::menu.article.attributes'),
                'id' => 'attributes',
                'has_errors' => $this->errorBag->hasAny([
                    'attributeMapping.*',
                ]),
            ]]);

        }

        return $collect;
    }


    protected function getHasUrlsModel()
    {
        return $this->article;
    }


    /**
     * Returns the model which has media associated.
     *
     * @return Article
     */
    protected function getMediaModel()
    {
        return $this->article;
    }

    /**
     * Returns the model which has slots associated.
     *
     * @return Article
     */
    protected function getSlotModel()
    {
        return $this->article;
    }


    /**
     * Returns the contexts for any slots.
     *
     * @return array
     */
    protected function getSlotContexts()
    {
        return ['article.all'];
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    abstract public function render();

    public function getRichTextOptionProperty()
    {
        return ['modules' => ['toolbar' => [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [['header' => 1], ['header' => 2]],               // custom button values
            [['list' => 'ordered'], ['list' => 'bullet']],
            [['script' => 'sub'], ['script' => 'super']],      // superscript/subscript
            [['indent' => '-1'], ['indent' => '+1']],          // outdent/indent
            [['direction' => 'rtl']],                         // text direction
            [['size' => ['small', false, 'large', 'huge']]],  // custom dropdown
            [['header' => [1, 2, 3, 4, 5, 6, false]]],

            [['color' => []], ['background' => []]],          // dropdown with defaults from theme
            [['font' => []]],
            [['align' => []]],

            ['clean']

        ]]];
    }
}
