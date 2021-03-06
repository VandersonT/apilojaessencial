<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; 

use App\Models\Cloth;
use App\Models\Images;
use App\Models\Comment;

class ClothesController extends Controller{
    
    public function getAllClothes(){
        $array = ['error' => ''];

        $array['products'] = Cloth::get();
        
        $array['images'] = Images::get();

        return $array;
    }

    public function getCloth(Request $request){
        $array = ['error' => ''];

        $product = Cloth::find($request->id);

        if(empty($product)){
            $array['error'] = 'Não encontramos nenhum produto com o id '.$request->id;
            return $array;
        }

        $array['product'] = $product;
        $array['images'] = Images::where('cloth_id', $request->id)->get();

        $array['comments'] = Comment::
            where('productId', $request->id)
            ->join('user', 'user.id', 'comments.userId')
            ->select('comments.*', 'user.name')
        ->get();

        return $array;
    }

    public function clothesWithFilter(Request $request){
        //if(!empty($urlData['order'])){}

        $array = ['error' => ''];

        $urlData = $request->query();

        #OrderBy
        if(empty($urlData['order'])){
            $urlData['order'] = 'Normal';
        }

        #product
        if(empty($urlData['p1'])){
            $urlData['p1'] = '';
        }
        if(empty($urlData['p2'])){
            $urlData['p2'] = '';
        }
        if(empty($urlData['p3'])){
            $urlData['p3'] = '';
        }
        if(empty($urlData['p4'])){
            $urlData['p4'] = '';
        }
        if(empty($urlData['p5'])){
            $urlData['p5'] = '';
        }
        if(empty($urlData['p1']) && empty($urlData['p2']) && empty($urlData['p3']) && empty($urlData['p4']) && empty($urlData['p5'])){
            $urlData['p1'] = 'calça';
            $urlData['p2'] = 'sapato';
            $urlData['p3'] = 'blusa';
            $urlData['p4'] = 'camisa';
            $urlData['p5'] = 'jaqueta';
        }

        #Age
        if(empty($urlData['age1'])){
            $urlData['age1'] = '';
        }
        if(empty($urlData['age2'])){
            $urlData['age2'] = '';
        }
        if(empty($urlData['age3'])){
            $urlData['age3'] = '';
        }
        if(empty($urlData['age4'])){
            $urlData['age4'] = '';
        }
        if(empty($urlData['age1']) && empty($urlData['age2']) && empty($urlData['age3']) && empty($urlData['age4'])){
            $urlData['age1'] = 'bebês';
            $urlData['age2'] = 'crianças';
            $urlData['age3'] = 'adolecentes';
            $urlData['age4'] = 'adultos';
        }

        switch($urlData['order']){
            case 'Normal':
                $array['products'] = Cloth::
                    where(function($query) use ($urlData){
                        $query->where('cloth.type', $urlData['p1'])
                        ->orWhere('cloth.type', '=' ,$urlData['p2'])
                        ->orWhere('cloth.type', '=' ,$urlData['p3'])
                        ->orWhere('cloth.type', '=' ,$urlData['p4'])
                        ->orWhere('cloth.type', '=' ,$urlData['p5']);
                    })
                    ->where(function($query) use ($urlData){
                        $query->where('cloth.age', $urlData['age1'])
                        ->orWhere('cloth.age', '=' ,$urlData['age2'])
                        ->orWhere('cloth.age', '=' ,$urlData['age3'])
                        ->orWhere('cloth.age', '=' ,$urlData['age4']);
                    })
                    ->where(function ($query) use ($urlData) {
                        $query->where('name', 'like' ,'%'.$urlData['search'].'%')
                        ->orWhere('description', 'like' ,'%'.$urlData['search'].'%');
                    })

                    ->where(function ($query) use ($urlData) {
                        if(!empty($urlData['gender'])){
                            if($urlData['gender'] != 'todos'){
                                $query->where('sex', $urlData['gender']);
                            }
                        }
                    })

                ->get();
                break;
            case 'Menor Preço':
                $array['products'] = Cloth::
                    where(function($query) use ($urlData){
                        $query->where('cloth.type', $urlData['p1'])
                        ->orWhere('cloth.type', '=' ,$urlData['p2'])
                        ->orWhere('cloth.type', '=' ,$urlData['p3'])
                        ->orWhere('cloth.type', '=' ,$urlData['p4'])
                        ->orWhere('cloth.type', '=' ,$urlData['p5']);
                    })
                    ->where(function($query) use ($urlData){
                        $query->where('cloth.age', $urlData['age1'])
                        ->orWhere('cloth.age', '=' ,$urlData['age2'])
                        ->orWhere('cloth.age', '=' ,$urlData['age3'])
                        ->orWhere('cloth.age', '=' ,$urlData['age4']);
                    })
                    ->where(function ($query) use ($urlData) {
                        $query->where('name', 'like' ,'%'.$urlData['search'].'%')
                        ->orWhere('description', 'like' ,'%'.$urlData['search'].'%');
                    })
                    ->where(function ($query) use ($urlData) {
                        if(!empty($urlData['gender'])){
                            if($urlData['gender'] != 'todos'){
                                $query->where('sex', $urlData['gender']);
                            }
                        }
                    })
                    ->orderBy('cloth.price')
                ->get();
                break;
            case 'Maior Preço':
                $array['products'] = Cloth::
                    where(function($query) use ($urlData){
                        $query->where('cloth.type', $urlData['p1'])
                        ->orWhere('cloth.type', '=' ,$urlData['p2'])
                        ->orWhere('cloth.type', '=' ,$urlData['p3'])
                        ->orWhere('cloth.type', '=' ,$urlData['p4'])
                        ->orWhere('cloth.type', '=' ,$urlData['p5']);
                    })
                    ->where(function($query) use ($urlData){
                        $query->where('cloth.age', $urlData['age1'])
                        ->orWhere('cloth.age', '=' ,$urlData['age2'])
                        ->orWhere('cloth.age', '=' ,$urlData['age3'])
                        ->orWhere('cloth.age', '=' ,$urlData['age4']);
                    })
                    ->where(function ($query) use ($urlData) {
                        $query->where('name', 'like' ,'%'.$urlData['search'].'%')
                        ->orWhere('description', 'like' ,'%'.$urlData['search'].'%');
                    })
                    ->where(function ($query) use ($urlData) {
                        if(!empty($urlData['gender'])){
                            if($urlData['gender'] != 'todos'){
                                $query->where('sex', $urlData['gender']);
                            }
                        }
                    })
                    ->orderByDesc('cloth.price')
                ->get();
                break;
            default:
                $array['error'] = "No campo 'order' deve esta, 'Normal', 'Menor Preço' ou 'Maior Preço'.";
                return $array;
                break;
        }

        return $array;
    }

    public function addNewCloth(Request $request){
        $array = ['error' => ''];

        if($request->name == '' || $request->description == '' || $request->price == '' || $request->price == '' || $request->type == '' || $request->size == '' || $request->amount == '' || $request->info == '' || $request->cover == '' || $request->age == '' || $request->sex == ''){
            $array['error'] = 'Não envie campos vazios';
            return $array;
        }

        #Search for main image and send to server
        if($request->hasFile('cover')){
            $pathName = md5(time().rand(0,1000)).'.jpg';
            move_uploaded_file($_FILES['cover']['tmp_name'], 'media/'.$pathName);
        }else{
            $array['error'] = 'É obrigatorio o envio da foto principal do produto.';
            return $array;
        }

        $cloth = new Cloth();
            $cloth->name = $request->name;
            $cloth->description = $request->description;
            $cloth->price = $request->price;
            $cloth->type = $request->type;
            $cloth->size = $request->size;
            $cloth->amount = $request->amount;
            $cloth->info = $request->info;
            $cloth->cover = url('/media').'/'.$pathName;
            $cloth->age = $request->age;
            $cloth->sex = $request->sex;
        $cloth->save();

        if(!empty($request->allFiles()['images'])){
            for($i = 0; $i < count($_FILES['images']['name']); $i++){
                $pathName = md5(time().rand(0,1000)).'.jpg';
                move_uploaded_file($_FILES['images']['tmp_name'][$i], 'media/'.$pathName);
                $productImage = new Images();
                    $productImage->cloth_id = $cloth->id;
                    $productImage->url = url('/media').'/'.$pathName;
                $productImage->save();
            }
        }

        return $array;
    }

    public function editCloth(Request $request){
        $array = ['error' => ''];

        $rules = [
            'name' => 'min: 3|max:40',
            'description' => 'min: 3',
            'price' => 'min: 3',
            'type' => 'min: 3',
            'size' => 'min: 3',
            'amount' => 'min: 3',
            'info' => 'min: 3',
            'cover' => 'min: 3'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->messages();
            return $array;
        }

        $product = Cloth::find($request->id);

        if($product){
            if($request->name){
                $product->name = $request->name;
            }
            if($request->description){
                $product->description = $request->description;
            }
            if($request->price){
                $product->price = $request->price;
            }
            if($request->type){
                $product->type = $request->type;
            }
            if($request->size){
                $product->size = $request->size;
            }
            if($request->amount){
                $product->amount = $request->amount;
            }
            if($request->info){
                $product->info = $request->info;
            }
            $product->save();

        }else{
            $array['error'] = 'Não encontramos o produto id '.$request->id.', logo, não será possivel edita-lo.';
        }

        return $array;
    }

    public function deleteClothImage(Request $request){
        $array = ['error' => ''];

        $productImage = Images::find($request->id);

        if(!$productImage){
            $array['error'] = 'Não foi possivel deletar o id '.$request->id.', pois, ele não existe.';
            return $array;
        }

        $productImage->delete();

        return $array;
    }

    public function deleteCloth(Request $request){
        $array = ['error' => ''];

        $product = Cloth::find($request->id);

        if(!$product){
            $array['error'] = 'Não foi possivel deletar o id '.$request->id.', pois, ele não existe.';
            return $array;
        }

        $product->delete();
        $productImage = Images::where('cloth_id', $request->id)->delete();

        return $array;
    }

}
