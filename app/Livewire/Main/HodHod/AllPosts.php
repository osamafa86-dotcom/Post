<?php

namespace App\Livewire\Main\HodHod;

use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\NewsLetterEmails;
use App\Models\Post;
use App\Models\Tag;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AllPosts extends Component
{
    use WithPagination;

    public $search, $mostReadPost, $randomCategory, $randomPost, $newsletterEmail, $tagsSearch, $title;

    #[Url(keep: true)]
    public $category_id;
    #[Url(keep: true)]
    public $tag_id;
    public $categorySearch;

    public function mount($id = null)
    {
        $this->category_id = request()->query('category_id');
        if ($this->category_id) {
            $this->categorySearch[] = $this->category_id;
            $this->title = Category::find($this->category_id)?->category_title;
        }
        $this->tag_id = request()->query('tag_id');
        if ($this->tag_id) {
            $this->tagsSearch[] = $this->tag_id;
            $this->title = Tag::find($this->tag_id)?->tag_name;
        }
        $withRelations = ['category.relationable', 'tags.relationable', 'author.relationable', 'files.file'];
        $this->mostReadPost = Post::with($withRelations)
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->withSum('views as views_sum', 'views_number')
            ->orderByRaw('COALESCE(views_sum, 0) DESC')
            ->orderByDesc('publish_date')
            ->take(3)
            ->get();
        $this->randomCategory = Category::with('files.file')->inRandomOrder()
            ->take(4)
            ->get();

        $this->randomPost = Post::with($withRelations)
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->inRandomOrder()
            ->take(4)
            ->get();
    }

    public function submitNewsletterEmail()
    {
        $this->validate(['newsletterEmail' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->newsletterEmail]);
        $this->reset('newsletterEmail');
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
    }

    public function resetSearch()
    {
        $this->reset(['search', 'searchDateForm', 'searchDateTo', 'categorySearch']);
    }

    #[Computed]
    public function posts()
    {
        return Post::where('publish_status', '=', PublishEnum::PUBLISHED->value)
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->categorySearch, function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->whereIn('relationable_id', (array)$this->categorySearch);
                });
            })
            ->when($this->tagsSearch, function ($q) {
                $q->whereHas('tags', function ($q) {
                    $q->whereIn('relationable_id', (array)$this->tagsSearch);
                });
            })
            ->with(['category.relationable', 'author.relationable', 'files.file'])->latest('publish_date')->paginate(14);
    }

    #[Computed]
    public function categories()
    {
        return Category::all();
    }

    #[Layout('components.layouts.main.hodhod.main')]
    public function render()
    {
        return view('livewire.main.hod-hod.all-posts');
    }
}
