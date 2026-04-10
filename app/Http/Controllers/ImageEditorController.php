<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ImageEditorController extends Controller
{

    public function index($id){
        $selectedFile = Files::where('id', $id)->value('path');
        $image = asset('storage').'/'.$selectedFile;

        return view('image-editor', compact('image'));
    }
}
