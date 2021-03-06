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

        $array['users'] = User::
            select('name', 'email', 'photo', 'access')
        ->get();

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

        if($request->name == '' || $request->email == '' || $request->password == '' || $request->confirmPassword == ''){
            $array['error'] = 'Não envie campos vazios.';
            return $array;
        }

        if($request->password != $request->confirmPassword){
            $array['error'] = 'A senha e a confirmação não coincidem.';
            return $array;
        }

        $urlPhoto = url('/media/no-picture.png');

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

        if($request->email == '' || $request->password == ''){
            $array['error'] = 'Não envie campos vazios.';
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

    public function updateInfoProfile(Request $request){
        $array = ['error' => ''];

        /*Validation*/

        if(!$request->id){
            $array['error'] = 'Precisamos do id do perfil para atualizarmos.';
            return $array; 
        }

        if($request->newPassword && !$request->currentPassword){
            $array['error'] = 'Não é possivel alterar a senha sem enviar a atual.';
            return $array;
        }

        $user = User::find($request->id);

        if(!$user){
            $array['error'] = 'Não encontramos ninguém com o id '.$request->id;
            return $array;
        }


        if($request->newPassword){
            if(password_verify($request->currentPassword, $user['password'])){
                $hash = password_hash($request->newPassword, PASSWORD_DEFAULT);
                $user->password = $hash;
            }else{
                $array['error'] = 'senha errada';
                return $array;
            }
        }


        if($request->name){
            $user->name = $request->name;
        }

        if($request->email){
            $user->email = $request->email;
            
        }

        if($request->access){
            $user->access = $request->access;
        }
        $user->save();
        /***/

        return $array;
    }
    
    public function updatePhoto(Request $request){
        $array = ['error' => ''];

        if($request->hasFile('file')){
            $pathName = md5(time().rand(0,1000)).'.jpg';
            move_uploaded_file($_FILES['file']['tmp_name'], 'media/'.$pathName);

            $user = User::find($request->id);
                $user->photo = url('/media').'/'.$pathName;
            $user->save();
        }

        return $array;
    }
}
