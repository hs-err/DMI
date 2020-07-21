<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generate;
use Illuminate\Support\Facades\Auth;

class GenerateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($uuid)
    {
        $generate = Generate::findOrFail($uuid);
        return view('edit')->with([
            'server_data' => json_encode(json_decode($generate->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'server_id' => $generate->id
        ]);
    }

    public function save($id, Request $request)
    {
        $server = Generate::findOrFail($id);

        $server->data = json_encode(json_decode($request->input('data')));
        $server->saveOrFail();

        return $this->index($id);
    }

    public function create()
    {
        $server = new Generate();
        $server->user_id = Auth::id();
        $server->save();
        return redirect(route('edit', $server->id));
    }
}
