<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Favorite;

class RelationController extends Controller{
    
    public function addProductToFavorites(Request $request){
        $array = ['error' => ''];

        $exists = Favorite::
            where('userId', $request->id)
            ->where('productId', $request->productid)
        ->first();

        if($exists){
            $array['error'] = 'Este item já esta nos seus favoritos.';
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
            ->join('cloth', 'cloth.id', 'favorites.productid')
            ->select('cloth.*', 'favorites.id', 'favorites.productId')
        ->get();

        return $array;
    }

    public function removeFavorite(Request $request){
        $array = ['error' => ''];

        $remove = Favorite::find($request->id)->delete();

        return $array;
    }

}
