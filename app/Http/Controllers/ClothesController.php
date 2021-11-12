<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; 

use App\Models\Cloth;
use App\Models\Images;

class ClothesController extends Controller{
    
    public function getAllClothes(){
        $array = ['error' => ''];

        $array['products'] = Cloth::get();
        
        $array['images'] = Images::get();;

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

        return $array;
    }

    public function clothesWithFilter(Request $request){
        //if(!empty($urlData['order'])){}

        $array = ['error' => ''];

        $urlData = $request->query();

        if(empty($urlData['order'])){
            $urlData['order'] = 'Normal';
        }

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
                    ->where(function ($query) use ($urlData) {
                        $query->where('name', 'like' ,'%'.$urlData['search'].'%')
                        ->orWhere('description', 'like' ,'%'.$urlData['search'].'%');
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
                    ->where(function ($query) use ($urlData) {
                        $query->where('name', 'like' ,'%'.$urlData['search'].'%')
                        ->orWhere('description', 'like' ,'%'.$urlData['search'].'%');
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
                    ->where(function ($query) use ($urlData) {
                        $query->where('name', 'like' ,'%'.$urlData['search'].'%')
                        ->orWhere('description', 'like' ,'%'.$urlData['search'].'%');
                    })
                    ->orderByDesc('cloth.price')
                ->get();
                break;
            default:
                $array['error'] = "No campo 'order' deve esta, 'Normal', 'Menor Preço' ou 'Maior Preço'.";
                return $array;
                break;
        }

        

        /*$data = Cloth::
            where(function($query) use ($filter){
                $query->where('cloth.level', $filter['levels'][0])
                ->orWhere('cloth.level', $filter['levels'][1])
                ->orWhere('cloth.level', $filter['levels'][2])
                ->orWhere('cloth.level', $filter['levels'][3]);
            })
        ->get();*/

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
            if($request->file('cover')->isValid()){
                $cover = $request->file('cover')->store('public');
                $urlCover = asset(Storage::url($cover));
            }
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
            $cloth->cover = $urlCover;
            $cloth->age = $request->age;
            $cloth->sex = $request->sex;
        $cloth->save();

        if(!empty($request->allFiles()['images'])){
            for($i = 0; $i < count($request->allFiles()['images']); $i++){
        
                $file = $request->allFiles()['images'][$i]->store('public');
    
                $url = url('/').Storage::url($file);

                $productImage = new Images();
                    $productImage->cloth_id = $cloth->id;
                    $productImage->url = $url;
                $productImage->save();

                unset($productImage);
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
