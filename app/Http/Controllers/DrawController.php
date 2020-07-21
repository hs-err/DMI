<?php

namespace App\Http\Controllers;

use App\Generate;

class DrawController extends Controller
{
    public function draw($uuid)
    {
        $generate = Generate::findOrFail($uuid);
        return response($generate->generate(), 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'Private');
    }
    public function add()
    {
        $generate=new Generate();
        $generate->saveOrFail();
        return true;
    }
}
