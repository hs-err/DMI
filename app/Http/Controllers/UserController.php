<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator= Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required'
        ]);
        $input=$validator->validated();
        $user=User::whereEmail($input['email'])->firstOrNew([], $input);
        if ($user->password==$input['password']) {
            $user->remember_token=uniqid(md5(microtime()));
            $user->saveOrFail();
            return [
                'success'=>true,
                'error'=>null,
                'message'=>'Login success.',
                'data'=>[
                    $user
                ]
            ];
        } else {
            return [
                'success'=>false,
                'error'=>'UnVerifiedPassword',
                'message'=>'Wrong Password.',
                'data'=>null,
            ];
        }
    }
}
