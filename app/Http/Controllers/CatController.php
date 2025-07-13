<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CatController extends Controller
{
    public function getCats()
{
    $catImages = [];
    for ($i = 0; $i < 10; $i++) {
        $catImages[] = "https://cataas.com/cat?unique=" . uniqid();
    }
    return response()->json($catImages);
}

}
