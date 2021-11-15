<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Favorite;
use App\Models\Kart;

class RelationController extends Controller{
    
    public function addProductToFavorites(Request $request){
        $array = ['error' => ''];

        $exists = Favorite::
            where('userId', $request->id)
            ->where('productId', $request->productid)
        ->first();

        if($exists){
            $array['error'] = 'Este item já está nos seus favoritos.';
            return $array;
        }

        $newFavorite = new Favorite();
            $newFavorite->userId = $request->id;
            $newFavorite->productId = $request->productid;
        $newFavorite->save();

        return $array;
    }

    public function getUserFavorites(Request $request){
        $array = ['error' => ''];

        $array['favorites'] = Favorite::
            where('userId', $request->id)
            ->join('cloth', 'cloth.id', 'favorites.productId')
            ->select('cloth.*', 'favorites.id', 'favorites.productId')
        ->get();

        return $array;
    }

    public function removeFavorite(Request $request){
        $array = ['error' => ''];

        $remove = Favorite::find($request->id)->delete();

        return $array;
    }

    public function addProductToKart(Request $request){
        $array = ['error' => ''];

        $exists = Kart::
            where('userId', $request->id)
            ->where('productId', $request->productid)
        ->first();

        if($exists){
            $array['error'] = 'Este item já está no seu carrinho.';
            return $array;
        }

        $newProduct = new Kart();
            $newProduct->userId = $request->id;
            $newProduct->productId = $request->productid;
            $newProduct->amountWanted = 1;
        $newProduct->save();

        return $array;
    }

    public function getUserKart(Request $request){
        $array = ['error' => ''];

        $array['kart'] = Kart::
            where('userId', $request->id)
            ->join('cloth', 'cloth.id', 'kart.productId')
            ->select('cloth.*', 'kart.id', 'kart.productId', 'kart.amountWanted')
        ->get();

        return $array;
    }

    public function editUserKart(Request $request){
        $array = ['error' => ''];

        $id = $request->input('id');
        $newAmount = $request->input('newAmount');

        if(!($id && $newAmount)){
            $array['error'] = 'Não envie campos vazios';
            return $array;
        }

        $editKart = Kart::find($request->id);

        if(!$editKart){
            $array['error'] = 'O id enviado não foi encontrado';
            return $array;
        }

        $editKart->amountWanted = $request->newAmount;
        $editKart->save();

        return $array;
    }

    public function deleteUserKart(Request $request){
        $array = ['error' => ''];

        $remove = Kart::find($request->id)->delete();

        return $array;
    }

}
