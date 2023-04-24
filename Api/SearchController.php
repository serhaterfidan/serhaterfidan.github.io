<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $ean_code = $request->ean_code;
        if ($ean_code == NULL) {
            $message = array('status' => '0', 'message' => 'Not able to scan any barcode');
            return $message;
        }



        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $prod = DB::table('store_products')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->where([
                ['product_varient.ean', 'like', "%$ean_code%"],
                ['store_products.stock', '>', 0],
                ['store_products.price', '>=', 0],
                ['store_products.price', '<=', 5000],
                ['store_products.store_id', '=', $store_id],
                ['product.hide', '=', 0]
            ])
            ->first();

        $d = Carbon::Now();
        $deal = DB::table('deal_product')
            ->where('varient_id', $prod->varient_id)
            ->where('store_id', $store_id)
            ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
            ->where('deal_product.valid_to', '>', $d->toDateString())
            ->first();

        if ($deal) {
            $prod->price = round($deal->deal_price, 2);
        } else {
            $sp = DB::table('store_products')
                ->where('varient_id', $prod->varient_id)
                ->where('store_id', $store_id)
                ->first();
            $prod->price = round($sp->price, 2);
        }

        if ($request->user_id != NULL) {
            $wishlist = DB::table('wishlist')
                ->where('varient_id', $prod->varient_id)
                ->where('user_id', $request->user_id)
                ->first();
            $cart = DB::table('store_orders')
                ->where('varient_id', $prod->varient_id)
                ->where('store_approval', $request->user_id)
                ->where('order_cart_id', 'incart')
                ->where('store_id', $store_id)
                ->first();

            if ($wishlist) {
                $prod->isFavourite = 'true';
            } else {
                $prod->isFavourite = 'false';
            }

            if ($cart) {
                $prod->cart_qty = $cart->qty;
            } else {
                $prod->cart_qty = 0;
            }
        } else {
            $prod->isFavourite = 'false';
            $prod->cart_qty = 0;
        }

        $getrating = DB::table('product_rating')
            ->whereNull('deletedAt')
            ->where('varient_id', $prod->varient_id)
            ->where('store_id', $store_id)
            ->get();

        if (count($getrating) > 0) {
            $countrating = DB::table('product_rating')
                ->where('varient_id', $prod->varient_id)
                ->where('store_id', $store_id)
                ->count();
            $rating = DB::table('product_rating')
                ->where('varient_id', $prod->varient_id)
                ->where('store_id', $store_id)
                ->avg('rating');
            $prod->avgrating = $rating;
            $prod->countrating = $countrating;
        } else {
            $prod->avgrating = 0;
            $prod->countrating = 0;
        }

        if ($prod->mrp != 0) {
            $discountper = 100 - (($prod->price * 100) / $prod->mrp);
            $prod->discountper = round($discountper, 2);
        } else {
            $prod->discountper = 0;
        }

        if ($prod) {
            $cat_id = $prod->cat_id;
            $prodsssss = DB::table('store_products')
                ->whereNull('store_products.deletedAt')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->where('product.cat_id', $cat_id)
                ->where('store_products.store_id', $store_id)
                ->where('store_products.price', '!=', NULL)
                ->where('product.hide', 0)
                ->where('product.approved', 1)
                ->paginate(5);

            $prodsd = $prodsssss->unique('product_id');
            $prod1 = NULL;

            foreach ($prodsd as $store) {
                $prod1[] = $store;
            }

            if ($prod1 != NULL) {
                $result = array();
                $o = 0;
                $p = 0;
                $q = 0;
                $z = 0;

                foreach ($prod1 as $prods) {
                    $a = 0;

                    $d = Carbon::Now();
                    $deal = DB::table('deal_product')
                        ->where('varient_id', $prods->varient_id)
                        ->where('store_id', $store_id)
                        ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
                        ->where('deal_product.valid_to', '>', $d->toDateString())
                        ->first();

                    if ($deal) {
                        $prods->price = round($deal->deal_price, 2);

                    } else {
                        $sp = DB::table('store_products')
                            ->where('varient_id', $prods->varient_id)
                            ->where('store_id', $store_id)
                            ->first();
                        $prods->price = round($sp->price, 2);

                    }

                    if ($request->user_id != NULL) {
                        $wishlist = DB::table('wishlist')
                            ->where('varient_id', $prods->varient_id)
                            ->where('user_id', $request->user_id)
                            ->first();
                        $cart = DB::table('store_orders')
                            ->where('varient_id', $prods->varient_id)
                            ->where('store_approval', $request->user_id)
                            ->where('order_cart_id', 'incart')
                            ->where('store_id', $store_id)
                            ->first();

                        if ($wishlist) {
                            $prods->isFavourite = 'true';
                        } else {
                            $prods->isFavourite = 'false';
                        }
                        if ($cart) {
                            $prods->cart_qty = $cart->qty;
                        } else {
                            $prods->cart_qty = 0;
                        }
                    } else {
                        $prods->isFavourite = 'false';
                        $prods->cart_qty = 0;
                    }

                    $getrating = DB::table('product_rating')
                        ->whereNull('deletedAt')
                        ->where('varient_id', $prods->varient_id)
                        ->where('store_id', $store_id)
                        ->get();

                    if (count($getrating) > 0) {
                        $countrating = DB::table('product_rating')
                            ->where('varient_id', $prods->varient_id)
                            ->where('store_id', $store_id)
                            ->count();
                        $rating = DB::table('product_rating')
                            ->where('varient_id', $prods->varient_id)
                            ->where('store_id', $store_id)
                            ->avg('rating');
                        $prods->avgrating = $rating;
                        $prods->countrating = $countrating;
                    } else {
                        $prods->avgrating = 0;
                        $prods->countrating = 0;
                    }

                    if ($prods->mrp != 0) {
                        $discountper = 100 - (($prods->price * 100) / $prods->mrp);
                        $prods->discountper = round($discountper, 2);
                    } else {
                        $prods->discountper = 0;
                    }

                    array_push($result, $prods);

                    $app = json_decode($prods->product_id);
                    $apps = array($app);
                    $app = DB::table('store_products')
                        ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                        ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                        ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'deal_product.deal_price', 'deal_product.valid_from', 'deal_product.valid_to')
                        ->whereNull('store_products.deletedAt')
                        ->where('store_products.store_id', $store_id)
                        ->whereIn('product_varient.product_id', $apps)
                        ->where('store_products.price', '!=', NULL)
                        ->where('product_varient.approved', 1)
                        ->get();
                    $app = $app->unique('varient_id');
                    $images = DB::table('product_images')
                        ->whereNull('deletedAt')
                        ->select('image')
                        ->whereIn('product_id', $apps)
                        ->orderBy('type', 'DESC')
                        ->get();

                    if (count($images) > 0) {
                        $result[$q]->images = $images;
                        $q++;
                    } else {
                        $images = DB::table('product')
                            ->whereNull('deletedAt')
                            ->select('product_image as image')
                            ->whereIn('product_id', $apps)
                            ->get();

                        $result[$q]->images = $images;
                        $q++;

                    }

                    $tag = DB::table('tags')
                        ->whereNull('deletedAt')
                        ->whereIn('product_id', $apps)
                        ->get();
                    $result[$p]->tags = $tag;
                    $p++;

                    foreach ($app as $aa) {
                        $d = Carbon::Now();
                        $deal = DB::table('deal_product')
                            ->where('varient_id', $aa->varient_id)
                            ->where('store_id', $store_id)
                            ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
                            ->where('deal_product.valid_to', '>', $d->toDateString())
                            ->first();

                        if ($deal) {
                            $app[$a]->price = round($deal->deal_price, 2);

                        } else {
                            $sp = DB::table('store_products')
                                ->where('varient_id', $aa->varient_id)
                                ->where('store_id', $store_id)
                                ->first();
                            $app[$a]->price = round($sp->price, 2);

                        }
                        if ($request->user_id != NULL) {
                            $wishlist = DB::table('wishlist')
                                ->where('varient_id', $aa->varient_id)
                                ->where('user_id', $request->user_id)
                                ->first();
                            $cart = DB::table('store_orders')
                                ->where('varient_id', $aa->varient_id)
                                ->where('store_approval', $request->user_id)
                                ->where('order_cart_id', 'incart')
                                ->where('store_id', $store_id)
                                ->first();


                            if ($wishlist) {
                                $app[$a]->isFavourite = 'true';
                            } else {
                                $app[$a]->isFavourite = 'false';
                            }
                            if ($cart) {
                                $app[$a]->cart_qty = $cart->qty;
                            } else {
                                $app[$a]->cart_qty = 0;
                            }

                        } else {
                            $app[$a]->isFavourite = 'false';
                            $app[$a]->cart_qty = 0;
                        }

                        $getrating = DB::table('product_rating')
                            ->whereNull('deletedAt')
                            ->where('varient_id', $aa->varient_id)
                            ->where('store_id', $store_id)
                            ->get();

                        if (count($getrating) > 0) {
                            $countrating = DB::table('product_rating')
                                ->where('varient_id', $aa->varient_id)
                                ->where('store_id', $store_id)
                                ->count();
                            $rating = DB::table('product_rating')
                                ->where('varient_id', $aa->varient_id)
                                ->where('store_id', $store_id)
                                ->avg('rating');
                            $app[$a]->avgrating = $rating;
                            $app[$a]->countrating = $countrating;
                        } else {
                            $app[$a]->avgrating = 0;
                            $app[$a]->countrating = 0;
                        }

                        if ($aa->mrp != 0) {
                            $discountper = 100 - (($aa->price * 100) / $aa->mrp);
                            $app[$a]->discountper = round($discountper, 2);
                        } else {
                            $app[$a]->discountper = 0;
                        }

                        $a++;
                    }

                    $result[$z]->varients = $app;
                    $z++;
                }
            }

            $result = array();
            $i = 0;
            $j = 0;
            $m = 0;

            array_push($result, $prod);
            $p_id = $prod->product_id;

            $app = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'deal_product.deal_price', 'deal_product.valid_from', 'deal_product.valid_to')
                ->whereNull('store_products.deletedAt')
                ->where('store_products.store_id', $store_id)
                ->where('product_varient.product_id', $p_id)
                ->where('store_products.price', '!=', NULL)
                ->where('product_varient.approved', 1)
                ->get();
            $app = $app->unique('varient_id');
            $images = DB::table('product_images')
                ->whereNull('deletedAt')
                ->select('image')
                ->where('product_id', $p_id)
                ->orderBy('type', 'DESC')
                ->get();

            if (count($images) > 0) {
                $result[$m]->images = $images;
                $m++;
            } else {
                $images = DB::table('product')
                    ->whereNull('deletedAt')
                    ->select('product_image as image')
                    ->where('product_id', $p_id)
                    ->get();

                $result[$m]->images = $images;
                $m++;

            }

            $tag = DB::table('tags')
                ->whereNull('deletedAt')
                ->where('product_id', $p_id)
                ->get();
            $result[$j]->tags = $tag;
            $j++;
            foreach ($app as $aas) {
                $d = Carbon::Now();
                $deal = DB::table('deal_product')
                    ->where('varient_id', $aas->varient_id)
                    ->where('store_id', $store_id)
                    ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
                    ->where('deal_product.valid_to', '>', $d->toDateString())
                    ->first();

                if ($deal) {
                    $aas->price = round($deal->deal_price, 2);

                } else {
                    $sp = DB::table('store_products')
                        ->where('varient_id', $aas->varient_id)
                        ->where('store_id', $store_id)
                        ->first();
                    $aas->price = round($sp->price, 2);

                }
                if ($request->user_id != NULL) {
                    $wishlist = DB::table('wishlist')
                        ->where('varient_id', $aas->varient_id)
                        ->where('user_id', $request->user_id)
                        ->first();
                    $cart = DB::table('store_orders')
                        ->where('varient_id', $aas->varient_id)
                        ->where('store_approval', $request->user_id)
                        ->where('order_cart_id', 'incart')
                        ->where('store_id', $store_id)
                        ->first();


                    if ($wishlist) {
                        $aas->isFavourite = 'true';
                    } else {
                        $aas->isFavourite = 'false';
                    }
                    if ($cart) {
                        $aas->cart_qty = $cart->qty;
                    } else {
                        $aas->cart_qty = 0;
                    }

                } else {
                    $aas->isFavourite = 'false';
                    $aas->cart_qty = 0;
                }
                $getrating = DB::table('product_rating')
                    ->whereNull('deletedAt')
                    ->where('varient_id', $aas->varient_id)
                    ->where('store_id', $store_id)
                    ->get();
                if (count($getrating) > 0) {
                    $countrating = DB::table('product_rating')
                        ->where('varient_id', $aas->varient_id)
                        ->where('store_id', $store_id)
                        ->count();
                    $rating = DB::table('product_rating')
                        ->where('varient_id', $aas->varient_id)
                        ->where('store_id', $store_id)
                        ->avg('rating');
                    $aas->avgrating = $rating;
                    $aas->countrating = $countrating;
                } else {
                    $aas->avgrating = 0;
                    $aas->countrating = 0;
                }

                if ($aas->mrp != 0) {
                    $discountper = 100 - ($aas->price * 100) / $aas->mrp;
                    $aas->discountper = round($discountper, 2);
                } else {
                    $aas->discountper = 0;
                }
                $aasss[] = $aas;
            }
            $result[$i]->varients = $aasss;
            $i++;



            // AKTİF DİLDE VERİYİ GETİRMEK
            $lang = strtoupper($request->header('langCode'));
            $array = class2Array($prod1);
            foreach ($array as $key => $value){

                $array[$key]['currency'] = getCurrencyData($request->store_id);
                $array[$key]['unit'] = lngUnit($value['unit'], $lang);
                $array[$key]['product_name'] = tPC($value['product_name'], $lang);
                $array[$key]['description'] = tPC($value['description'], $lang);

                $array[$key]['varients'][0]['description'] = tPC($value['varients'][0]['description'], $lang);
                $array[$key]['varients'][0]['unit'] = lngUnit($value['varients'][0]['unit'], $lang);
            }
            $similarPs = array2Class($array);
            // AKTİF DİLDE VERİYİ GETİRMEK

            // AKTİF DİLDE VERİYİ GETİRMEK
            $lang = strtoupper($request->header('langCode'));
            $array2 = class2Array($prod);

            $array2['currency'] = getCurrencyData($request->store_id);
            $array2['unit'] = lngUnit($array2['unit'], $lang);
            $array2['product_name'] = tPC($array2['product_name'], $lang);
            $array2['description'] = tPC($array2['description'], $lang);

            $array2['varients'][0]['unit'] = lngUnit($array2['varients'][0]['unit'], $lang);
            $array2['varients'][0]['description'] = tPC($array2['varients'][0]['description'], $lang);

            $pDetails = array2Class($array2);
            // AKTİF DİLDE VERİYİ GETİRMEK



            $data = array('detail' => $pDetails, 'similar_product' => $similarPs);
            $message = array('status' => '1', 'message' => 'Products Detail', 'data' => $data);

            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Product not found');
            return $message;
        }






    }


    public function searchbystore(Request $request){

        $filterData = generateFilterCases($request);

        // Arama değişkenleri
        $keyword = $request->keyword;
        $userID = $request->user_id;

        // Ürünleri Getirmek
        $products = DB::table('store_products')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('product_rating', 'store_products.varient_id', '=', 'product_rating.varient_id')
            ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            ->join('store', 'store_products.store_id', '=', 'store.id')
            ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'store_products.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw("100-((store_products.price*100)/store_products.mrp) as discountper"), DB::raw("sum(IFNULL(product_rating.rating,0))/count(IFNULL(product_rating.rating,0)) as avgrating"))
            ->groupBy('store_products.store_id', 'store_products.stock', 'store_products.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', 'product_rating.rating')
            ->havingBetween('avgrating', [$filterData->avgRating->min, $filterData->avgRating->max])
            ->havingBetween('discountper', [$filterData->discountPer->min, $filterData->discountPer->max])
            ->whereNull('store_products.deletedAt')
            ->where(class2Array($filterData->whereCase))
            ->orderBy($filterData->orderBy->case, $filterData->orderBy->way)
            ->paginate(50);

        // Son Aramalara Eklemek
        if ($userID != NULL) {
            updateUserLastSearch($userID, $keyword);
        }

        // Ürün Gereksinimlerini Eklemek
        $products = generateProductRequirements($products, $request);

        if ($products->total() > 0){
            $returnProducts = $products->unique('product_id');
            $message = array('status' => '1', 'message' => 'Products found', 'data' => $returnProducts);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Products not found');
            return $message;
        }

    }

    public function trensearchproducts(Request $request)
    {
        $store_id = $request->store_id;
        $prodsssss = DB::table('trending_search')
            ->whereNull('trending_search.deletedAt')
            ->join('store_products', 'trending_search.varient_id', '=', 'store_products.varient_id')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            ->join('store', 'store_products.store_id', '=', 'store.id')
            ->select('store_products.stock', 'product.isSpecial', 'store_products.store_id', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type')
            ->groupBy('store_products.stock', 'store_products.store_id', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type')
            ->where('store_products.store_id', $store_id)
            ->get();

        if (count($prodsssss) > 0) {


            $prodsssss = generateTranslatedProductItem($prodsssss, $request);
            $message = array('status' => '1', 'message' => 'Products found', 'data' => $prodsssss);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Products not found');
            return $message;
        }
    }

    public function recentsearch(Request $request)
    {

        $user_id = $request->user_id;
        $prodsssss = DB::table('recent_search')
            ->whereNull('deletedAt')
            ->where('user_id', $user_id)
            ->get();

        if (count($prodsssss) > 0) {


            $message = array('status' => '1', 'message' => 'Recent Search found', 'data' => $prodsssss);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Products not found');
            return $message;
        }

    }

}