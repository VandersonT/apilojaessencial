<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; 

use App\Models\User;

class UserController extends Controller
{
    public function getAllUsers(){
        $array = ['error' => ''];

        $array['users'] = User::all();

        return $array;
    }

    public function getUser(Request $request){
        $array = ['error' => ''];

        $foundUser = User::find($request->id);

        if(!$foundUser){
            $array['error'] = 'Não encontramos ninguém com esse id.';
            return $array;
        }

        $user = [
            'name' => $foundUser['name'],
            'email' => $foundUser['email'],
            'photo' => $foundUser['photo'],
            'access' => $foundUser['access'],
        ];

        $array['foundUser'] = $user;

        return $array;
    }

    public function addNewUser(Request $request){
        $array = ['error' => ''];

        $rules = [
            'name' => 'required|max:40',
            'email' => 'required|email:rfc,dns|unique:user,email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->messages();
            return $array;
        }

        $urlPhoto = '';
        if($request->hasFile('photo')){
            if($request->file('photo')->isValid()){
                $photo = $request->file('photo')->store('public');
                $urlPhoto = asset(Storage::url($photo));
            }
        }

        $token = md5(time().rand(0,9999).rand(0,999));

        $hash = password_hash($request->password, PASSWORD_DEFAULT);

        $newUser = new User();
            $newUser->name = $request->name;
            $newUser->email = $request->email;
            $newUser->password = $hash;
            $newUser->photo = $urlPhoto;
            $newUser->access = 0;
            $newUser->token = $token;
        $newUser->save();

        $array['token'] = $token;

        return $array;
    }

    public function loginAction(Request $request){
        $array = ['error' => ''];

        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->messages();
            return $array;
        }

        $user = User::where('email', $request->email)->first();

        if($user){
            if(password_verify($request->password, $user['password'])){
                
                $array['token'] = $user['token'];

            }else{
                $array['error'] = 'E-mail e/ou senha estão incorretos.';
            }
        }else{
            $array['error'] = 'Esta conta não esta registrada no nosso sistema!';
        }

        return $array;
    }

    public function authentication(Request $request){
        $array = ['error' => ''];
        
        $rules = ['currentToken' => 'required'];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->messages();
            return $array;
        }

        $user = User::where('token', $request->currentToken)->first();

        if($user){
            $array['logged'] = true;
            $array['loggedUser'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'photo' => $user['photo'],
                'access' => $user['access'],
            ];
        }else{
            $array['logged'] = false;
        }

        return $array;
    }

    public function editUser(Request $request){
        $array = ['error' => ''];

        $user = User::find($request->id);

        if(!$user){
            $array['error'] = 'Não encontramos ninguém com o id '.$request->id;
            return $array;
        }

        if($request->name){
            $user->name = $request->name;
        }

        if($request->description){
            $user->description = $request->description;
        }

        if($request->password){
            $user->password = $request->password;
        }

        if($request->access){
            $user->access = $request->access;
        }
        $user->save();

        return $array;
    }
}
