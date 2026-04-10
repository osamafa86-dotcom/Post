<?php

namespace App\Livewire\Main\Tayqan;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SharePost extends Component
{
    public $title, $description, $image,$share_url,$componentID;

    public function mount($title = null, $description = null, $image = null, $share_url = null)
    {
        $this->componentID=$this->__id;
        $this->title = $title;
        $this->description = $description;
        $this->share_url = $share_url;
        $this->image = config('app.url') . $image;
    }

    public function share($type='facebook')
    {
        $url=null;
        if($type=='facebook'){
            $url='https://www.facebook.com/sharer/sharer.php?u='.$this->share_url;
        }elseif ($type=='twitter'){
            $url='https://twitter.com/intent/tweet?url='.$this->share_url;
        }elseif ($type=='whatsapp'){
            $url='https://wa.me/?text='.$this->share_url;
        }elseif ($type=='telegram'){
            $url='https://t.me/share/url?url='.$this->share_url;
        }
        $this->dispatch("updateMetaData.$this->componentID", ['title' => $this->title, 'description' => $this->description, 'image' => $this->image,'share_url'=>$url]);
    }

    #[Layout('components.layouts.main.tayqan.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tayqan.share-post');
    }


}
