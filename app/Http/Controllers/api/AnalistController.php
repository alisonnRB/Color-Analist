<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnalistController extends Controller
{
    protected $image;
    public function index(Request $request)
    {
        $this->image = $request->file('image');
        
    }

}
