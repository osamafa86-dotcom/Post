<?php

namespace App\Livewire\Main\HodHod;

use App\Models\Category;
use App\Models\NewsLetterEmails;
use App\Models\Post;
use Livewire\Component;

class SidebarWidget extends Component
{
    public $author ,$mostReadPost,$randomCategory,$randomPost,$infographicCategoryPost,$characterCategoryPost;
    public $newsletterEmail;

    public function submitNewsletterEmail(){
        $this->validate(['newsletterEmail' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->newsletterEmail]);
        $this->reset('newsletterEmail');
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
    }
    public function mount($mostReadPost,$randomCategory,$randomPost)
    {
        $this->mostReadPost=$mostReadPost;
        $this->randomCategory=$randomCategory;
        $this->randomPost=$randomPost;

        $this->infographicCategoryPost=Post::whereHas('category', function ($q){
            $q->whereHasMorph(
                'relationable',
                [Category::class],
                function ($subQuery) {
                    $subQuery->where('category_title', '=', 'أنفوجراف');
                }
            );
        })->with(['files.file'])->latest()->take(3)->get();
        $this->characterCategoryPost=Post::whereHas('category', function ($q){
            $q->whereHasMorph(
                'relationable',
                [Category::class],
                function ($subQuery) {
                    $subQuery->where('category_title', '=', 'كاريكتير');
                }
            );
        })->with(['files.file'])->latest()->take(3)->get();
    }
    public function render()
    {
        return view('livewire.main.hod-hod.sidebar-widget');
    }
}
