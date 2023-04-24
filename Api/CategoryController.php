<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use \Datetime;


class CategoryController extends Controller
{
    public function product_images(Request $request)
    {
        $prod_id = $request->product_id;

        $images = DB::table('product_images')
            ->whereNull('deletedAt')
            ->where('product_id', $prod_id)
            ->orderBy('type', 'DESC')
            ->get();

        if (count($images) > 0) {
            $message = array('status' => '1', 'message' => 'Product Images', 'data' => $images);
            return $message;
        } else {
            $prod_id = $request->product_id;

            $images = DB::table('product')
                ->whereNull('deletedAt')
                ->where('product_id', $prod_id)
                ->get();
            if (count($images) > 0) {
                $message = array('status' => '1', 'message' => 'Product Images', 'data' => $images);
                return $message;
            } else {
                $message = array('status' => '0', 'message' => 'no images found', 'data' => $images);
                return $message;
            }
        }

    }

    public function getneareststore(Request $request)
    {
        date_default_timezone_set('Asia/Baku');
                $lat = $request->lat;
                $lng = $request->lng;
                $msg="";
                $nearbystore=null;
                $now = new DateTime();
                $clock=$now->format('H:i');
                        $nearbystoreGet2 = DB::table('store')
                            ->join('store_products', 'store.id', '=', 'store_products.store_id')
                            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                            ->select('store.phone_number', 'store.tips','product.isSpecial', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id', DB::raw("6371 * acos(cos(radians(" . $lat . "))
                                    * cos(radians(store.lat))
                                    * cos(radians(store.lng) - radians(" . $lng . "))
                                    + sin(radians(" . $lat . "))
                                    * sin(radians(store.lat))) AS distance"))
                            ->groupBy('store.phone_number', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id')
                            ->orderBy('distance')
                            /* ->where('store.del_range','>=', 'distance') */
                            ->where('store.store_closing_time','>=', $clock)
                            ->where('store.store_status', 1)
                            ->get();
                            if(count($nearbystoreGet2)>0){
                                $nearbystore = $nearbystoreGet2[0];
                            }
                        /* $nearbystore = DB::table('store')
                            ->join('store_products', 'store.id', '=', 'store_products.store_id')
                            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                            ->select('store.phone_number', 'product.isSpecial', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id', DB::raw("6371 * acos(cos(radians(" . $lat . "))
                                    * cos(radians(store.lat))
                                    * cos(radians(store.lng) - radians(" . $lng . "))
                                    + sin(radians(" . $lat . "))
                                    * sin(radians(store.lat))) AS distance"))
                            ->groupBy('store.phone_number', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id')
                            ->orderBy('distance')
                            ->where('store.store_closing_time','>=', $clock)
                            ->where('store.store_status', 1)
                            ->first(); */
                            if($nearbystore && $nearbystore->del_range>=$nearbystore->distance){
                                //BANA EN YAKIN OLAN MAĞAZA GETİR
                                //SATICI MESAFESİ İÇİNDE OLAN MAĞAZA
                                $msg="Yakın mağaza bulundu açık ve mesafe içinde.";
                            }else{
                                //TÜM MAĞAZALAR KAPALI İSE BANA EN YAKIN OLANI GETİR
                                /* $nearbystore = DB::table('store')
                                ->join('store_products', 'store.id', '=', 'store_products.store_id')
                                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                                ->select('store.phone_number', 'product.isSpecial', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id', DB::raw("6371 * acos(cos(radians(" . $lat . "))
                                        * cos(radians(store.lat))
                                        * cos(radians(store.lng) - radians(" . $lng . "))
                                        + sin(radians(" . $lat . "))
                                        * sin(radians(store.lat))) AS distance"))
                                ->groupBy('store.phone_number', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id')
                                ->orderBy('distance')
                                //İLK YAPTIĞIMDA BU ALTTA Kİ SATIR YOKTU İLK TESTLERDE
                                ->where('store.store_closing_time','>=', $clock)
                                ->where('store.store_status', 1)
                                ->first(); */
                                $nearbystoreGet = DB::table('store')
                                    ->join('store_products', 'store.id', '=', 'store_products.store_id')
                                    ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                                    ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                                    ->select('store.phone_number', 'store.tips','product.isSpecial', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id', DB::raw("6371 * acos(cos(radians(" . $lat . "))
                                            * cos(radians(store.lat))
                                            * cos(radians(store.lng) - radians(" . $lng . "))
                                            + sin(radians(" . $lat . "))
                                            * sin(radians(store.lat))) AS distance"))
                                    ->groupBy('store.phone_number', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id')
                                    ->orderBy('distance')
                                    /* ->where('store.del_range','>=', 'distance') */
                                    //->where('store.store_closing_time','>=', $clock)
                                    ->where('store.store_status', 1)
                                    ->get();
                                if($nearbystore){
                                    //KAPALI AMA BANA EN YAKIN OLAN MAĞAZA GETİR
                                    if($nearbystore->del_range>=$nearbystore->distance){
                                        //KAPALI AMA SATICI MESAFESİ İÇİNDE OLAN MAĞAZA
                                        $msg="Kapalı en yakın mağaza";
                                    }else{
                                        //KAPALI AMA BANA DA UZAK
                                        $msg="Yakın mağaza bulunmadı";
                                        $dataTest="";
                                        for ($index=0; $index < count($nearbystoreGet2); $index++) {
                                            if($nearbystoreGet2[$index]->id!=$nearbystore->id){
                                                if($nearbystoreGet2[$index] && $nearbystoreGet2[$index]->del_range>=$nearbystoreGet2[$index]->distance){
                                                    $nearbystore=$nearbystoreGet2[$index];
                                                    $dataTest="OK";
                                                    break;
                                                }
                                            }
                                        }
                                        if($dataTest==""){
                                            $msg="2.Sefer Yakın mağaza bulunmadı";
                                            for ($index=0; $index < count($nearbystoreGet); $index++) {
                                                if($nearbystoreGet[$index]->id!=$nearbystore->id){
                                                    if($nearbystoreGet[$index] && $nearbystoreGet[$index]->del_range>=$nearbystoreGet[$index]->distance){
                                                        $nearbystore=$nearbystoreGet[$index];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    if(count($nearbystoreGet)>0){
                                        $nearbystore = $nearbystoreGet[0];
                                    }

                                    /* $nearbystore = DB::table('store')
                                    ->join('store_products', 'store.id', '=', 'store_products.store_id')
                                    ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                                    ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                                    ->select('store.phone_number', 'product.isSpecial', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id', DB::raw("6371 * acos(cos(radians(" . $lat . "))
                                            * cos(radians(store.lat))
                                            * cos(radians(store.lng) - radians(" . $lng . "))
                                            + sin(radians(" . $lat . "))
                                            * sin(radians(store.lat))) AS distance"))
                                    ->groupBy('store.phone_number', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id')
                                    ->orderBy('distance')
                                    //İLK YAPTIĞIMDA BU ALTTA Kİ SATIR YOKTU İLK TESTLERDE
                                    //->where('store.store_closing_time','>=', $clock)
                                    ->where('store.store_status', 1)
                                    ->first(); */

                                    if($nearbystore){
                                        //KAPALI AMA BANA EN YAKIN OLAN MAĞAZA GETİR
                                        if($nearbystore->del_range>=$nearbystore->distance){
                                            //KAPALI AMA SATICI MESAFESİ İÇİNDE OLAN MAĞAZA
                                            $msg="last if Kapalı en yakın mağaza";
                                        }else{
                                            //KAPALI AMA BANA DA UZAK
                                            $msg="last if Yakın mağaza bulunmadı";
                                            for ($index=0; $index < count($nearbystoreGet); $index++) {
                                                if($nearbystoreGet[$index]->id!=$nearbystore->id){
                                                    if($nearbystoreGet[$index] && $nearbystoreGet[$index]->del_range>=$nearbystoreGet[$index]->distance){
                                                        $nearbystore=$nearbystoreGet[$index];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        if ($nearbystore) {
                            $o_t = date('Y-m-d h:i a', strtotime($nearbystore->store_opening_time));
                            $c_t = date('Y-m-d h:i a', strtotime($nearbystore->store_closing_time));
                            //$o_t = date('Y-m-d')." ".$nearbystore->store_opening_time;
                            //$c_t = date('Y-m-d')." ".$nearbystore->store_closing_time;
                            $nearbystore->store_opening_time = $o_t;
                            $nearbystore->store_closing_time = $c_t;

                            if ($nearbystore->del_range >= $nearbystore->distance) {
                                if ($nearbystore->store_status == 1) {
                                    $message = array('status' => '1','insert_msg'=>$msg, 'message' => 'Store Found at your location', 'data' => $nearbystore);
                                    return $message;
                                } else {
                                    $message = array('status' => '2','insert_msg'=>$msg, 'message' => 'Store Is Closed', 'data' => $nearbystore);
                                    return $message;
                                }
                            } else {
                                $cities = DB::table('city')
                                    ->join('store', 'city.city_name', '=', 'store.city')
                                    ->join('store_products', 'store.id', '=', 'store_products.store_id')
                                    ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                                    ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                                    ->select('city.city_name')
                                    ->whereNull('city.deletedAt')
                                    ->groupBy('city.city_name')
                                    ->get();
                                $city_name="";

                                foreach ($cities as $city) {
                                    $c_name[] = $city->city_name;
                                    $city_name = implode(',', $c_name);
                                }

                                $message = array('status' => '0','insert_msg'=>$msg, 'message' => 'We are not delivering your area. we service in only');
                                return $message;
                            }
                        } else {
                            $cities = DB::table('city')
                            ->join('store', 'city.city_name', '=', 'store.city')
                            ->whereNull('city.deletedAt')
                            ->select('city.city_name')
                            ->groupBy('city.city_name')
                            ->get();
                            $city_name="";
                            foreach ($cities as $city) {
                                $c_name[] = $city->city_name;
                                $city_name = implode(',', $c_name);
                            }

                            $message = array('status' => '0','insert_msg'=>$msg, 'message' => 'We are not delivering your area. we service in only' );
                            return $message;
            }
        
        /*$lat = $request->lat;
        $lng = $request->lng;
        $nearbystore = DB::table('store')
            ->join('store_products', 'store.id', '=', 'store_products.store_id')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->select('store.phone_number', 'product.isSpecial', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id', DB::raw("6371 * acos(cos(radians(" . $lat . "))
                    * cos(radians(store.lat))
                    * cos(radians(store.lng) - radians(" . $lng . "))
                    + sin(radians(" . $lat . "))
                    * sin(radians(store.lat))) AS distance"))
            ->groupBy('store.phone_number', 'store.store_opening_time', 'store.store_closing_time', 'store.del_range', 'store.device_id', 'store.id', 'store.store_name', 'store.store_status', 'store.inactive_reason', 'store.lat', 'store.lng', 'store.store_opening_time', 'store.store_closing_time', 'store.city', 'store.city_id')
            ->orderBy('distance')
            ->where('store.store_status', 1)
            ->first();
        if ($nearbystore) {
            $o_t = date('Y-m-d h:i a', strtotime($nearbystore->store_opening_time));
            $c_t = date('Y-m-d h:i a', strtotime($nearbystore->store_closing_time));
            $nearbystore->store_opening_time = $o_t;
            $nearbystore->store_closing_time = $c_t;
            if ($nearbystore->del_range >= $nearbystore->distance) {
                if ($nearbystore->store_status == 1) {
                    $message = array('status' => '1', 'message' => 'Store Found at your location', 'data' => $nearbystore);
                    return $message;
                } else {
                    $message = array('status' => '2', 'message' => 'Store Is Closed', 'data' => $nearbystore);
                    return $message;
                }
            } else {
                $cities = DB::table('city')
                    ->join('store', 'city.city_name', '=', 'store.city')
                    ->join('store_products', 'store.id', '=', 'store_products.store_id')
                    ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                    ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                    ->select('city.city_name')
                    ->whereNull('city.deletedAt')
                    ->groupBy('city.city_name')
                    ->get();
                foreach ($cities as $city) {
                    $c_name[] = $city->city_name;
                    $city_name = implode(',', $c_name);
                }

                $message = array('status' => '0', 'message' => 'We are not delivering your area. we service in ' . $city_name . ' only');
                return $message;
            }
        } else {
            $cities = DB::table('city')
                ->join('store', 'city.city_name', '=', 'store.city')
                ->whereNull('city.deletedAt')
                ->select('city.city_name')
                ->groupBy('city.city_name')
                ->get();

            foreach ($cities as $city) {
                $c_name[] = $city->city_name;
                $city_name = implode(',', $c_name);
            }

            $message = array('status' => '0', 'message' => 'We are not delivering your area. we service in ' . $city_name . ' only');
            return $message;
        }*/
    }

    public function oneapi(Request $request){

        $time = Carbon::Now();
        $storeID = $request->store_id;
        $lang = strtoupper($request->header('langCode'));

        // İlk Banner
        $banner = DB::table('store_banner')->join('categories', 'store_banner.cat_id', '=', 'categories.cat_id')->select('store_banner.*', 'categories.title')->whereNull('store_banner.deletedAt')->where([['store_id', '=', $storeID]])->get();
        $banner = generateTranslatedBannerItem($banner, $request);

        // İkinci Banner
        $secondBanner = DB::table('sec_banner')->whereNull('deletedAt')->where([['store_id', '=', $storeID]])->get();
        $secondBanner = generateTranslatedSecondBannerItem($secondBanner, $request);

        // Kategoriler
        $categories = DB::table('categories')->join('categories as cat', 'categories.cat_id', '=', 'cat.parent')->join('product', 'cat.cat_id', '=', 'product.cat_id')->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')->select('categories.title', 'product.isSpecial', 'categories.cat_id', 'categories.image', 'store_products.store_id', 'categories.description', DB::raw("MIN(store_products.price) as stfrom"), DB::raw('COUNT(cat.cat_id) as subcat_count'))->groupBy('categories.title', 'categories.cat_id', 'categories.image', 'store_products.store_id', 'categories.description', 'store_products.price')->whereNull('store_products.deletedAt')->whereNull('categories.deletedAt')->where([['categories.level', '=', 0],['store_products.store_id', '=', $storeID]])->get();
        $categories = generateCategoryTranslate($categories, $request);

        // İndirimli Ürünler
        $discountedProductsFilter = generateFilterCasesForHome(array2Class([
            'keyword' => NULL,
            'byname' => NULL,
            'min_price' => NULL,
            'max_price' => NULL,
            'stock' => 'in',
            'min_discount' => 1,
            'max_discount' => 100,
        ]), $storeID);
        $discountedProducts = DB::table('store_products')
            ->whereNull('store_products.deletedAt')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('product_rating', 'store_products.varient_id', '=', 'product_rating.varient_id')
            ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            ->join('store', 'store_products.store_id', '=', 'store.id')
            ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'store_products.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw("100-((store_products.price*100)/store_products.mrp) as discountper"), DB::raw("sum(IFNULL(product_rating.rating,0))/count(IFNULL(product_rating.rating,0)) as avgrating"))
            ->groupBy('store_products.store_id', 'store_products.stock', 'store_products.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', 'product_rating.rating')
            ->havingBetween('avgrating', [$discountedProductsFilter->avgRating->min, $discountedProductsFilter->avgRating->max])
            ->havingBetween('discountper', [$discountedProductsFilter->discountPer->min, $discountedProductsFilter->discountPer->max])
            ->where(class2Array($discountedProductsFilter->whereCase))
			->whereNull('store_products.deletedAt')
            ->where('product.hide', 0)
            ->inRandomOrder()
            ->limit(4)
            ->get();
        $discountedProducts = generateProductRequirements($discountedProducts, $request);
        $discountedProducts = $discountedProducts->unique('varient_id');


        // Recent Selling
        $recentselling = [];
        /*
        $recentselling = DB::table('store_products')
            ->whereNull('store_products.deletedAt')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('store_orders', 'product_varient.varient_id', '=', 'store_orders.varient_id')
            ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
            ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('count(store_orders.varient_id) as count'))
            ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type')
            ->where('store_products.store_id', $storeID)
            ->orderByRaw('RAND()')
            ->where('deal_product.deal_price', NULL)
            ->where('product.hide', 0)
            ->where('store_products.price', '!=', NULL)
            ->where('store_products.stock', '>', 0)
            ->limit(6)
            ->get();
        $recentselling = generateProductRequirements($recentselling, $request);
        $recentselling = $recentselling->unique('product_id');
        */

        // Top Selling
        $topSelling  = [];

        /*$dealProducts = DB::table('deal_product')
            ->join('store_products', 'deal_product.varient_id', '=', 'store_products.varient_id')
            ->join('store', 'deal_product.store_id', '=', 'store.id')
            ->join('product_varient', 'deal_product.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('product_rating', 'deal_product.varient_id', '=', 'product_rating.varient_id')
            ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'deal_product.deal_price as price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('100-((store_products.price*100)/store_products.mrp) as discountper'), DB::raw('sum(IFNULL(product_rating.rating,0))/count(IFNULL(product_rating.rating,0)) as avgrating'), 'deal_product.valid_to', 'deal_product.valid_from')
            ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'deal_product.deal_price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', 'product_rating.rating', 'deal_product.valid_to', 'deal_product.valid_from')
            ->whereNull('store_products.deletedAt')
            ->whereDate('deal_product.valid_from', '<=', $time->toDateString())
            ->where('deal_product.valid_to', '>', $time->toDateString())
            ->where('store_products.price', '!=', NULL)
            ->where('store_products.stock', '>', 0)
            ->where('product.hide', 0)
            ->where('deal_product.store_id', $storeID)
            ->limit(6)
            ->get();
        $dealProducts = generateProductRequirements($dealProducts, $request);
        $dealProducts = $dealProducts->unique('varient_id');*/

        // Spotlight
        $spotlight = DB::table('spotlight')
            ->whereNull('spotlight.deletedAt')
            ->join('store_products', 'spotlight.p_id', '=', 'store_products.p_id')
            ->join('product_varient', 'spotlight.p_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('store_orders', 'product_varient.varient_id', '=', 'store_orders.varient_id')
            ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
            ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            ->select('store_products.store_id', 'spotlight.deletedAt', 'product.isSpecial', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('count(store_orders.varient_id) as count'))
            ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type')
            ->whereNull('store_products.deletedAt')
            ->where('spotlight.store_id', $storeID)
            ->orderByRaw('RAND()')
            ->where('product.hide', 0)
            ->where('store_products.price', '!=', NULL)
            ->where('store_products.stock', '>', 0)
            ->limit(6)
            ->get();
        $spotlight = generateProductRequirements($spotlight, $request);
        $spotlight = $spotlight->unique('product_id');

        // Category
        $homeCategories = DB::table('categories')
            ->whereNull('categories.deletedAt')
            ->join('product', 'categories.cat_id', '=', 'product.cat_id')
            ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
            ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
            ->select('categories.title', 'product.isSpecial', 'categories.cat_id', 'categories.description')
            ->groupBy('categories.title', 'categories.cat_id', 'categories.description')
            ->where('store_products.store_id', $storeID)
            ->whereNull('store_products.deletedAt')
            ->where('categories.status', 2)
            ->get();
        foreach ($homeCategories as $homeCategory){
            $homeCategory->title = tPC($homeCategory->title, $lang);
            $homeCategory->cat_title = $homeCategory->title;
            $homeCategory->description = tPC($homeCategory->description, $lang);

            $categoryProducts = DB::table('store_products')
                ->whereNull('store_products.deletedAt')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('store_orders', 'product_varient.varient_id', '=', 'store_orders.varient_id')
                ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('count(store_orders.varient_id) as count'))
                ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type')
                ->whereNull('store_products.deletedAt')
                ->where('store_products.store_id', $storeID)
                ->orderByRaw('RAND()')
                ->where('product.hide', 0)
                ->where('store_products.store_id', $storeID)
                ->where('product.cat_id', $homeCategory->cat_id)
                ->where('store_products.price', '!=', NULL)
				->whereNull('store_products.deletedAt')
				->where('store_products.price', '>', 0)
                ->where('store_products.stock', '>', 0)
                ->limit(6)
                ->get();
            $categoryProducts = generateProductRequirements($categoryProducts, $request);
            $categoryProducts = $categoryProducts->unique('product_id');

            $homeCategory->products = $categoryProducts;
        }
        $homeCategories = $homeCategories->unique('cat_id');


        $message = array(
            'status' => '1',
            'message' => 'Homepage data',
            'banner' => $banner,
            'second_banner' => $secondBanner,
            'top_cat' => $categories,
            'whatsnew' => $discountedProducts,
            'topselling' => $topSelling,
            //'dealproduct' => $dealProducts,
            'spotlight' => $spotlight,
            'recentselling' => $recentselling,
            'category' => $homeCategories,
        );

        return $message;
    }

    public function cate(Request $request)
    {
        $store_id = $request->store_id;
        $filter1 = $request->byname;
        if ($filter1 != NULL) {
            $filter = $filter1;
        } else {
            $filter = 'asc';
        }
        $filter2 = $request->latest;

        if ($filter2 != NULL) {
            $filter3 = $filter2;
        } else {
            $filter3 = 'asc';
        }

        $cats = DB::table('categories')
            ->join('categories as cat', 'categories.cat_id', '=', 'cat.parent')
            ->join('product', 'cat.cat_id', '=', 'product.cat_id')
            ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
            ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
            ->select('categories.title', 'categories.cat_id', 'categories.image', 'store_products.store_id', 'categories.description', DB::raw("MIN(store_products.price) as stfrom"), DB::raw('COUNT(cat.cat_id) as subcat_count'))
            ->groupBy('categories.title', 'categories.cat_id', 'categories.image', 'store_products.store_id', 'categories.description', 'store_products.price')
            ->whereNull('categories.deletedAt')
			->whereNull('store_products.deletedAt')
            ->where('categories.level', 0)
            ->where('store_products.store_id', $store_id)
            ->orderBy('categories.title', $filter)
            ->orderBy('categories.cat_id', $filter3)
            ->get();

        $prodsd = $cats->unique('cat_id');

        $cat = NULL;

        foreach ($prodsd as $store) {
            $catss = DB::table('categories')
                ->whereNull('categories.deletedAt')
                ->join('product', 'categories.cat_id', '=', 'product.cat_id')
                ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
                ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
                ->select('categories.title', 'categories.cat_id', 'categories.image', 'store_products.store_id', 'categories.description', DB::raw("MIN(store_products.price) as stfrom"))
                ->groupBy('categories.title', 'categories.cat_id', 'categories.image', 'store_products.store_id', 'categories.description', 'store_products.price')
                ->where('categories.parent', $store->cat_id)
                ->where('categories.level', 1)
				->whereNull('store_products.deletedAt')
                ->paginate(10);
            $pros = $catss->unique('cat_id');

            $cateeees = NULL;

            foreach ($pros as $prossss) {
                $cateeees[] = $prossss;
            }

            $store->subcat_count = ($pros) ? count($cateeees) : 0;
            $cat[] = $store;
        }

        if ($cat != NULL) {
            $result = array();
            $i = 0;

            foreach ($cat as $cats) {
                array_push($result, $cats);

                $app = json_decode($cats->cat_id);
                $apps = array($app);
                $app = DB::table('categories')
                    ->join('product', 'categories.cat_id', '=', 'product.cat_id')
                    ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
                    ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
                    ->select('categories.title', 'categories.cat_id', 'categories.image', 'store_products.store_id', 'categories.description', DB::raw("MIN(store_products.price) as stfrom"))
                    ->groupBy('categories.title', 'categories.cat_id', 'categories.image', 'store_products.store_id', 'categories.description', 'store_products.price')
                    ->whereIn('categories.parent', $apps)
                    ->whereNull('categories.deletedAt')
                    ->where('categories.level', 1)
                    ->get();

                $pro = $app->unique('cat_id');

                $cateeee = NULL;
                foreach ($pro as $prossss) {

                    $cateeee[] = $prossss;

                }
                $result[$i]->subcategory = $cateeee;
                $i++;
            }


            // AKTİF DİLDE VERİYİ GETİRMEK
            $lang = strtoupper($request->header('langCode'));
            $array = class2Array($cat);
            foreach ($array as $key => $value){
                $array[$key]['title'] = tPC($value['title'], $lang);
                $array[$key]['description'] = tPC($value['description'], $lang);
                $array2 = class2Array($value['subcategory']);
                if (count($array2) > 0){
                    foreach ($array2 as $keyTwo => $valueTwo){
                        $valueTranslatedTwo = tPC($valueTwo['title'], $lang);
                        $valueTranslatedDescTwo = tPC($valueTwo['description'], $lang);
                        $array[$key]['subcategory'][$keyTwo]['title'] = $valueTranslatedTwo;
                        $array[$key]['subcategory'][$keyTwo]['description'] = $valueTranslatedDescTwo;
                    }
                }
            }
            $class = array2Class($array);
            // AKTİF DİLDE VERİYİ GETİRMEK



            $message = array('status' => '1', 'message' => 'data found', 'data' => $class);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Category not found');
            return $message;
        }
    }

    public function top_cat_prduct(Request $request)
    {
        $store_id = $request->store_id;

        $topcat = DB::table('store_products')
            ->whereNull('store_products.deletedAt')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('store_orders', 'product_varient.varient_id', '=', 'store_orders.varient_id')
            ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
            ->join('store', 'store_products.store_id', '=', 'store.id')
            ->join('categories', 'product.cat_id', '=', 'categories.cat_id')
            ->select('categories.title', 'product.isSpecial', 'categories.cat_id', DB::raw("count('orders.order_id') as count"))
            ->groupBy('categories.title', 'categories.cat_id')
            ->where('store_products.price', '!=', NULL)
			->whereNull('store_products.deletedAt')
            ->where('product.hide', 0)
            ->where('store.id', $store_id)
            ->orderBy('count', 'desc')
            ->limit(4)
            ->get();

        $topsix = NULL;

        foreach ($topcat as $store) {
            $topsix[] = $store;
        }

        if ($topsix != NULL) {
            $result = array();
            $i = 0;

            foreach ($topsix as $cats) {
                array_push($result, $cats);

                $app = json_decode($cats->cat_id);
                $apps = array($app);
                $app = DB::table('store_products')
                    ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                    ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                    ->whereIn('product.cat_id', $apps)
                    ->whereNull('store_products.deletedAt')
                    ->where('store_products.store_id', $store_id)
                    ->where('store_products.price', '!=', NULL)
					->whereNull('store_products.deletedAt')
                    ->where('product.hide', 0)
                    ->where('product.approved', 1)
                    ->limit(6)
                    ->get();

                $result[$i]->products = $app;
                $i++;
                $res = array();
                $j = 0;
                $k = 0;
                $m = 0;

                foreach ($app as $appss) {
                    array_push($res, $appss);
                    $c = array($appss->product_id);
                    $app1 = DB::table('store_products')
                        ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                        ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                        ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'deal_product.deal_price', 'deal_product.valid_from', 'deal_product.valid_to')
                        ->where('store_products.store_id', $store_id)
                        ->whereNull('store_products.deletedAt')
                        ->whereIn('product_varient.product_id', $c)
                        ->where('store_products.price', '!=', NULL)
						->whereNull('store_products.deletedAt')
                        ->where('product_varient.approved', 1)
                        ->get();
                    $images = DB::table('product_images')
                        ->whereNull('deletedAt')
                        ->select('image')
                        ->whereIn('product_id', $c)
                        ->orderBy('type', 'DESC')
                        ->get();
                    if (count($images) > 0) {
                        $result[$m]->images = $images;
                        $m++;
                    } else {
                        $images = DB::table('product')
                            ->whereNull('deletedAt')
                            ->select('product_image as image')
                            ->whereIn('product_id', $c)
                            ->get();

                        $result[$m]->images = $images;
                        $m++;

                    }
                    $tag = DB::table('tags')
                        ->whereNull('deletedAt')
                        ->whereIn('product_id', $c)
                        ->get();

                    if (count($tag) > 0) {
                        $res[$k]->tags = $tag;
                        $k++;
                    } else {
                        $res[$k]->tags = [];
                        $k++;
                    }

                    $res[$j]->varients = $app1;
                    $j++;

                }

            }

            $message = array('status' => '1', 'message' => 'data found', 'data' => $topsix);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Nothing in Top Categories');
            return $message;
        }

    }

    public function catego(Request $request)
    {
        $store_id = $request->store_id;
        $cat_id = $request->cat_id;
        $cat = DB::table('categories')
            ->whereNull('categories.deletedAt')
            ->join('product', 'categories.cat_id', '=', 'product.cat_id')
            ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
            ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
            ->select('categories.title', 'product.isSpecial', 'categories.cat_id', 'categories.image', 'categories.description')
            ->groupBy('categories.title', 'categories.cat_id', 'categories.image', 'categories.description')
            ->where('store_products.store_id', $store_id)
			->whereNull('store_products.deletedAt')
            ->where('categories.parent', $cat_id)
            ->get();

        foreach ($cat as $cats) {
            $prodsssss = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->select('product.product_id', 'product.isSpecial',)
                ->groupBy('product.product_id')
                ->where('product.cat_id', $cats->cat_id)
                ->where('store_products.store_id', $store_id)
                ->where('store_products.price', '!=', NULL)
				->whereNull('store_products.deletedAt')
                ->where('product.hide', 0)
                ->where('product.approved', 1)
                ->count();

            $cats->prod_count = $prodsssss;

            // AKTİF DİLDE VERİYİ GETİRMEK
            $lang = strtoupper($request->header('langCode'));
            $array = class2Array($cats);

            $array['title'] = tPC($array['title'], $lang);
            $array['description'] = tPC($array['description'], $lang);

            $class = array2Class($array);
            // AKTİF DİLDE VERİYİ GETİRMEK

            $catss[] = $class;
        }

        if (count($cat) > 0) {


            $message = array('status' => '1', 'message' => 'data found', 'data' => $catss);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'data not found');
            return $message;
        }
    }

    public function all_catego_sub(Request $request)
    {
        $store_id = $request->store_id;
        $cat_id = $request->cat_id;

       



        $cat = DB::table('categories')
            ->whereNull('categories.deletedAt')
            ->join('product', 'categories.cat_id', '=', 'product.cat_id')
            ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
            ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
            ->select('categories.title', 'product.isSpecial', 'categories.cat_id', 'categories.image', 'categories.description')
            ->groupBy('categories.title', 'categories.cat_id', 'categories.image', 'categories.description')
            ->whereColumn('store_products.price', "!=","store_products.mrp")
            ->where('store_products.store_id', $store_id)
	    ->whereNull('store_products.deletedAt')
            ->get();

        foreach ($cat as $cats) {
            $prodsssss = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->select('product.product_id', 'product.isSpecial',)
                ->groupBy('product.product_id')
                ->where('product.cat_id', $cats->cat_id)
                ->where('store_products.store_id', $store_id)
                ->where('store_products.price', '!=', NULL)
				->whereNull('store_products.deletedAt')
                ->where('product.hide', 0)
                ->where('product.approved', 1)
                ->count();

            $cats->prod_count = $prodsssss;

            // AKTİF DİLDE VERİYİ GETİRMEK
            $lang = strtoupper($request->header('langCode'));
            $array = class2Array($cats);

            $array['title'] = tPC($array['title'], $lang);
            $array['description'] = tPC($array['description'], $lang);

            $class = array2Class($array);
            // AKTİF DİLDE VERİYİ GETİRMEK

            $catss[] = $class;
        }

        if (count($cat) > 0) {


            $message = array('status' => '1', 'message' => 'data found', 'data' => $catss);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'data not found');
            return $message;
        }
    }
        
    public function catego_sub(Request $request)
    {
        $store_id = $request->store_id;
        $cat_id = $request->cat_id;

        $selectedCat = DB::table('categories')->where([['cat_id', '=', $cat_id]])->first();



        $cat = DB::table('categories')
            ->whereNull('categories.deletedAt')
            ->join('product', 'categories.cat_id', '=', 'product.cat_id')
            ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
            ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
            ->select('categories.title', 'store_products.deletedAt','product_varient.varient_id','product.isSpecial', 'categories.cat_id', 'categories.image', 'categories.description')
            ->groupBy('categories.title', 'categories.cat_id', 'categories.image', 'categories.description')
            ->where('store_products.store_id', $store_id)
            ->whereNull('store_products.deletedAt')
            ->where('categories.parent', $selectedCat->parent)
            ->get();

        foreach ($cat as $cats) {
            $prodsssss = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->select('product.product_id', 'product.isSpecial',)
                ->groupBy('product.product_id')
                ->where('product.cat_id', $cats->cat_id)
                ->where('store_products.store_id', $store_id)
                ->where('store_products.price', '!=', NULL)
				->whereNull('store_products.deletedAt')
                ->where('product.hide', 0)
                ->whereNull('store_products.deletedAt')
                ->where('product.approved', 1)
                ->count();

            $cats->prod_count = $prodsssss;

            // AKTİF DİLDE VERİYİ GETİRMEK
            $lang = strtoupper($request->header('langCode'));
            $array = class2Array($cats);

            $array['title'] = tPC($array['title'], $lang);
            $array['description'] = tPC($array['description'], $lang);

            $class = array2Class($array);
            // AKTİF DİLDE VERİYİ GETİRMEK

            $catss[] = $class;
        }

        if (count($cat) > 0) {


            $message = array('status' => '1', 'message' => 'data found', 'data' => $catss);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'data not found');
            return $message;
        }
    }

    public function cat_product(Request $request){

        $productsFilter = generateFilterCases($request);
        
        $products = DB::table('store_products')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('product_rating', 'store_products.varient_id', '=', 'product_rating.varient_id')
            ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            ->join('store', 'store_products.store_id', '=', 'store.id')
            ->select('store_products.store_id', 'product.cat_id','product.isSpecial', 'store_products.stock', 'store_products.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw("100-((store_products.price*100)/store_products.mrp) as discountper"), DB::raw("sum(IFNULL(product_rating.rating,0))/count(IFNULL(product_rating.rating,0)) as avgrating"))
            ->groupBy('store_products.store_id', 'store_products.stock', 'store_products.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', 'product_rating.rating')
            ->havingBetween('avgrating', [$productsFilter->avgRating->min, $productsFilter->avgRating->max])
            ->havingBetween('discountper', [$productsFilter->discountPer->min, $productsFilter->discountPer->max])
            ->where(class2Array($productsFilter->whereCase))
            ->where('product.cat_id', $request->cat_id)
            ->where('product.hide', 0)
            ->whereNull('store_products.deletedAt')

            ->orderBy('discountper', 'DESC')
            ->orderBy($productsFilter->orderBy->case, $productsFilter->orderBy->way)
            ->offset(($request->page-1)*10)
            ->limit(10)
            ->get();
        $products = generateProductRequirements($products, $request);
        $products = $products->unique('varient_id');

        if (count($products) > 0){
            $message = array('status' => '1', 'message' => 'Products found', 'data' => $products);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Products not found');
            return $message;
        }

    }

    public function varient(Request $request)
    {
        $prod_id = $request->product_id;
        $store_id = $request->store_id;
        $varient = DB::table('store_products')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'deal_product.deal_price', 'deal_product.valid_from', 'deal_product.valid_to')
            ->whereNull('store_products.deletedAt')
            ->where('product_id', $prod_id)
            ->where('store_products.price', '!=', NULL)
			->whereNull('store_products.deletedAt')
            ->where('store_products.store_id', $store_id)
            ->where('product_varient.approved', 1)
            ->get();

        if (count($varient) > 0) {
            // AKTİF DİLDE VERİYİ GETİRMEK
            $lang = strtoupper($request->header('langCode'));
            $array = class2Array($varient);

            $array[0]['unit'] = lngUnit($array[0]['unit'], $lang);
            $array[0]['description'] = tPC($array[0]['description'], $lang);

            $class = array2Class($array);
            // AKTİF DİLDE VERİYİ GETİRMEK

            $message = array('status' => '1', 'message' => 'varients', 'data' => $class);

            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Varients not found');

            return $message;
        }
    }

    public function dealproduct(Request $request)
    {
        $d = Carbon::Now();
        $store_id = $request->store_id;
        $filter1 = $request->byname;
        $filter1 = $request->byname;
        $price1 = DB::table('deal_product')
            ->join('store_products', 'deal_product.varient_id', '=', 'store_products.varient_id')
            ->join('store', 'deal_product.store_id', '=', 'store.id')
            ->join('product_varient', 'deal_product.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('product_rating', 'store_products.varient_id', '=', 'product_rating.varient_id')
            ->select('deal_product.deal_price', 'product.isSpecial',)
            ->groupBy('deal_product.deal_price')
            ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
            ->where('deal_product.valid_to', '>', $d->toDateString())
            ->where('store_products.price', '!=', NULL)
			->whereNull('store_products.deletedAt')
            ->where('product.hide', 0)
            ->where('deal_product.store_id', $store_id)
            ->orderBy('deal_product.deal_price', 'desc')
            ->first();

        if ($price1) {
            $price = $price1->deal_price;
        } else {
            $message = array('status' => '0', 'message' => 'Products not found');
            return $message;
        }

        $min_rating = $request->min_rating;
        if ($min_rating == NULL) {
            $min_rating = 0;
        }

        $max_rating = $request->max_rating;
        if ($max_rating == NULL) {
            $max_rating = 5;
        }

        $min_price = $request->min_price;
        if ($min_price == NULL) {
            $min_price = 0;
        }

        $max_price = $request->max_price;
        if ($max_price == NULL) {
            $max_price = 2500;
        }

        $stock = $request->stock;
        if ($stock == 'out') {
            $stock = "<";
            $by = 1;
        } elseif ($stock == 'in') {
            $stock = ">";
            $by = 0;
        } else {
            $stock = "!=";
            $by = NULL;
        }

        $min_discount = $request->min_discount;
        if ($min_discount == NULL) {
            $min_discount = 0;
        }

        $max_discount = $request->max_discount;
        if ($max_discount == NULL) {
            $max_discount = 100;
        }

        if ($filter1 != NULL) {
            $deal_pssss = DB::table('deal_product')
                ->whereNull('deal_product.deletedAt')
                ->join('store_products', 'deal_product.varient_id', '=', 'store_products.varient_id')
                ->join('store', 'deal_product.store_id', '=', 'store.id')
                ->join('product_varient', 'deal_product.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('product_rating', 'deal_product.varient_id', '=', 'product_rating.varient_id')
                ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'deal_product.deal_price as price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('100-((store_products.price*100)/store_products.mrp) as discountper'), DB::raw('sum(IFNULL(product_rating.rating,0))/count(IFNULL(product_rating.rating,0)) as avgrating'), 'deal_product.valid_to', 'deal_product.valid_from')
                ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'deal_product.deal_price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', 'product_rating.rating', 'deal_product.valid_to', 'deal_product.valid_from')
                ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
                ->where('deal_product.valid_to', '>', $d->toDateString())
                ->where('store_products.price', '!=', NULL)
				->whereNull('store_products.deletedAt')
                ->where('product.hide', 0)
                ->whereBetween('store_products.price', [$min_price, $max_price])
                ->havingBetween('avgrating', [$min_rating, $max_rating])
                ->havingBetween('discountper', [$min_discount, $max_discount])
                ->where('deal_product.store_id', $store_id)
                ->where('store_products.stock', $stock, $by)
                ->orderBy('product.product_name', $filter1)
                ->paginate(10);
        } else {
            $deal_pssss = DB::table('deal_product')
                ->whereNull('deal_product.deletedAt')
                ->join('store_products', 'deal_product.varient_id', '=', 'store_products.varient_id')
                ->join('store', 'deal_product.store_id', '=', 'store.id')
                ->join('product_varient', 'deal_product.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('product_rating', 'deal_product.varient_id', '=', 'product_rating.varient_id')
                ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'deal_product.deal_price as price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('100-((store_products.price*100)/store_products.mrp) as discountper'), DB::raw('sum(IFNULL(product_rating.rating,0))/count(IFNULL(product_rating.rating,0)) as avgrating'), 'deal_product.valid_to', 'deal_product.valid_from')
                ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'deal_product.deal_price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', 'product_rating.rating', 'deal_product.valid_to', 'deal_product.valid_from')
                ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
                ->where('deal_product.valid_to', '>', $d->toDateString())
                ->where('store_products.price', '!=', NULL)
				->whereNull('store_products.deletedAt')
                ->where('product.hide', 0)
                ->whereBetween('store_products.price', [$min_price, $max_price])
                ->havingBetween('avgrating', [$min_rating, $max_rating])
                ->havingBetween('discountper', [$min_discount, $max_discount])
                ->orderBy('deal_product.deal_id', 'desc')
                ->where('deal_product.store_id', $store_id)
                ->where('store_products.stock', $stock, $by)
                ->paginate(10);
        }
        $deal_p = $deal_pssss->unique('varient_id');

        $deal_products = NULL;

        foreach ($deal_p as $store) {
            $deal_products[] = $store;
        }

        if ($deal_products != NULL) {
            $result = array();
            $i = 0;
            $j = 0;
            $k = 0;
            $l = 0;
            $m = 0;
            foreach ($deal_products as $deal_ps) {
                $a = 0;

                if ($request->user_id != NULL) {
                    $wishlist = DB::table('wishlist')
                        ->where('varient_id', $deal_ps->varient_id)
                        ->where('user_id', $request->user_id)
                        ->first();
                    $cart = DB::table('store_orders')
                        ->where('varient_id', $deal_ps->varient_id)
                        ->where('store_approval', $request->user_id)
                        ->where('order_cart_id', 'incart')
                        ->where('store_id', $store_id)
                        ->first();

                    if ($wishlist) {
                        $deal_ps->isFavourite = 'true';
                    } else {
                        $deal_ps->isFavourite = 'false';
                    }
                    if ($cart) {
                        $deal_ps->cart_qty = $cart->qty;
                    } else {
                        $deal_ps->cart_qty = 0;
                    }
                } else {
                    $deal_ps->isFavourite = 'false';
                    $deal_ps->cart_qty = 0;
                }

                $getrating = DB::table('product_rating')
                    ->whereNull('deletedAt')
                    ->where('varient_id', $deal_ps->varient_id)
                    ->where('store_id', $store_id)
                    ->get();

                if (count($getrating) > 0) {
                    $countrating = DB::table('product_rating')
                        ->where('varient_id', $deal_ps->varient_id)
                        ->where('store_id', $store_id)
                        ->count();
                    $rating = DB::table('product_rating')
                        ->where('varient_id', $deal_ps->varient_id)
                        ->where('store_id', $store_id)
                        ->avg('rating');
                    $deal_ps->avgrating = $rating;
                    $deal_ps->countrating = $countrating;
                } else {
                    $deal_ps->avgrating = 0;
                    $deal_ps->countrating = 0;
                }

                if ($deal_ps->mrp != 0) {
                    $discountper = 100 - (($deal_ps->price * 100) / $deal_ps->mrp);
                    $deal_ps->discountper = round($discountper, 2);
                } else {
                    $deal_ps->discountper = 0;
                }

                $deal_ps->maxprice = $price;
                array_push($result, $deal_ps);
                $c = array($deal_ps->product_id);
                $app1 = DB::table('store_products')
                    ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                    ->join('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                    ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'deal_product.deal_price', 'deal_product.valid_from', 'deal_product.valid_to')
                    ->whereNull('store_products.deletedAt')
                    ->where('store_products.store_id', $store_id)
                    ->whereIn('product_varient.product_id', $c)
                    ->where('store_products.price', '!=', NULL)
					->whereNull('store_products.deletedAt')
                    ->where('product_varient.approved', 1)
                    ->get();
                $images = DB::table('product_images')
                    ->whereNull('deletedAt')
                    ->select('image')
                    ->whereIn('product_id', $c)
                    ->orderBy('type', 'DESC')
                    ->get();

                if (count($images) > 0) {
                    $result[$m]->images = $images;
                    $m++;
                } else {
                    $images = DB::table('product')
                        ->whereNull('deletedAt')
                        ->select('product_image as image')
                        ->whereIn('product_id', $c)
                        ->get();

                    $result[$m]->images = $images;
                    $m++;
                }

                $tag = DB::table('tags')
                    ->whereNull('deletedAt')
                    ->whereIn('product_id', $c)
                    ->get();

                if (count($tag) > 0) {
                    $result[$k]->tags = $tag;
                    $k++;
                } else {
                    $result[$k]->tags = [];
                    $k++;
                }

                foreach ($app1 as $aa) {
                    $d = Carbon::Now();
                    $deal = DB::table('deal_product')
                        ->where('varient_id', $aa->varient_id)
                        ->where('store_id', $store_id)
                        ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
                        ->where('deal_product.valid_to', '>', $d->toDateString())
                        ->first();

                    if ($deal) {
                        $app1[$a]->price = round($deal->deal_price, 2);

                    } else {
                        $sp = DB::table('store_products')
                            ->where('varient_id', $aa->varient_id)
                            ->where('store_id', $store_id)
                            ->first();
                        $app1[$a]->price = round($sp->price, 2);

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
                            $app1[$a]->isFavourite = 'true';
                        } else {
                            $app1[$a]->isFavourite = 'false';
                        }
                        if ($cart) {
                            $app1[$a]->cart_qty = $cart->qty;
                        } else {
                            $app1[$a]->cart_qty = 0;
                        }
                    } else {
                        $app1[$a]->isFavourite = 'false';
                        $app1[$a]->cart_qty = 0;
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
                        $app1[$a]->avgrating = $rating;
                        $app1[$a]->countrating = $countrating;
                    } else {
                        $app1[$a]->avgrating = 0;
                        $app1[$a]->countrating = 0;
                    }

                    if ($aa->mrp != 0) {
                        $discountper = 100 - (($aa->price * 100) / $aa->mrp);
                        $app1[$a]->discountper = round($discountper, 2);
                    } else {
                        $app1[$a]->discountper = 0;
                    }

                    $app1[$a]->maxprice = $price;
                    $a++;
                }
                $result[$l]->varients = $app1;
                $l++;

                $val_to = $deal_ps->valid_to;
                $diff_in_minutes = $d->diffInMinutes($val_to);
                $totalDuration = $d->diff($val_to)->format('%H:%I:%S');
                $result[$i]->timediff = $diff_in_minutes;
                $i++;
                $result[$j]->hoursmin = $totalDuration;
                $j++;
            }

            $deal_products = generateTranslatedProductItem($deal_products, $request);
            $message = array('status' => '1', 'message' => 'Products found', 'data' => $deal_products);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Products not found');
            return $message;
        }
    }

    public function top_cat(Request $request)
    {
        $store_id = $request->store_id;

        $topcat = DB::table('store_products')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('store_orders', 'product_varient.varient_id', '=', 'store_orders.varient_id')
            ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
            ->join('store', 'store_products.store_id', '=', 'store.id')
            ->join('categories', 'product.cat_id', '=', 'categories.cat_id')
            ->select('store.del_range', 'product.isSpecial', 'store.id', 'categories.title', 'categories.image', 'categories.description', 'categories.cat_id', DB::raw("count('orders.order_id') as count"))
            ->groupBy('store.del_range', 'store.id', 'categories.title', 'categories.image', 'categories.description', 'categories.cat_id')
            ->whereNull('store_products.deletedAt')
            ->where('store_products.price', '!=', NULL)
            ->where('product.hide', 0)
            ->where('store.id', $store_id)
            ->orderBy('count', 'desc')
            ->get();

        $topsix = NULL;

        foreach ($topcat as $store) {
            // AKTİF DİLDE VERİYİ GETİRMEK
            $lang = strtoupper($request->header('langCode'));
            $array = class2Array($store);

            $array['title'] = tPC($array['title'], $lang);
            $array['description'] = tPC($array['description'], $lang);

            $class = array2Class($array);
            // AKTİF DİLDE VERİYİ GETİRMEK
            $topsix[] = $class;
        }

        if ($topsix != NULL) {
            $message = array('status' => '1', 'message' => 'Top Categories', 'data' => $topsix);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Nothing in Top Categories');
            return $message;
        }

    }

    public function tag_product(Request $request)
    {
        $tag = ucfirst($request->tag_name);
        $store_id = $request->store_id;
        $filter1 = $request->byname;
        $filter1 = $request->byname;
        $price1 = DB::table('tags')
            ->join('product', 'tags.product_id', '=', 'product.product_id')
            ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
            ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
            ->select('store_products.price', 'product.isSpecial', 'store_products.stock')
            ->where('tags.tag', $tag)
            ->where('store_products.store_id', $store_id)
            ->where('store_products.price', '!=', NULL)
			->whereNull('store_products.deletedAt')
            ->where('product.hide', 0)
            ->where('product.approved', 1)
            ->orderBy('store_products.price', 'desc')
            ->first();

        if ($price1) {
            $price = $price1->price;
        } else {
            $message = array('status' => '0', 'message' => 'Products not found');
            return $message;
        }

        $min_rating = $request->min_rating;
        if ($min_rating == NULL) {
            $min_rating = 0;
        }

        $max_rating = $request->max_rating;
        if ($max_rating == NULL) {
            $max_rating = 5;
        }

        $min_price = $request->min_price;
        if ($min_price == NULL) {
            $min_price = 0;
        }

        $max_price = $request->max_price;
        if ($max_price == NULL) {
            $max_price = $price;
        }

        $stock = $request->stock;
        if ($stock == 'out') {
            $stock = "<";
            $by = 1;

        } elseif ($stock == 'all') {
            $stock = "!=";
            $by = NULL;
        } else {
            $stock = ">";
            $by = 0;
        }

        $min_discount = $request->min_discount;
        if ($min_discount == NULL) {
            $min_discount = 0;
        }

        $max_discount = $request->max_discount;
        if ($max_discount == NULL) {
            $max_discount = 100;
        }

        if ($filter1 != NULL) {
            $prodsssss = DB::table('tags')
                ->whereNull('tags.deletedAt')
                ->join('product', 'tags.product_id', '=', 'product.product_id')
                ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
                ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
                ->Leftjoin('product_rating', 'product_varient.varient_id', '=', 'product_rating.varient_id')
                ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('100-((store_products.price*100)/store_products.mrp) as discountper'), DB::raw('sum(IFNULL(product_rating.rating,0))/count(IFNULL(product_rating.rating,0)) as avgrating'))
                ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', 'product_rating.rating')
                ->where('tags.tag', $tag)
                ->where('store_products.store_id', $store_id)
                ->where('store_products.price', '!=', NULL)
				->whereNull('store_products.deletedAt')
                ->where('product.hide', 0)
                ->where('product.approved', 1)
                ->whereBetween('store_products.price', [$min_price, $max_price])
                ->havingBetween('avgrating', [$min_rating, $max_rating])
                ->havingBetween('discountper', [$min_discount, $max_discount])
                ->where('store_products.stock', $stock, $by)
                ->orderBy('product.product_name', $filter1)
                ->paginate(10);
        } else {
            $prodsssss = DB::table('tags')
                ->whereNull('tags.deletedAt')
                ->join('product', 'tags.product_id', '=', 'product.product_id')
                ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
                ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
                ->Leftjoin('product_rating', 'product_varient.varient_id', '=', 'product_rating.varient_id')
                ->select('store_products.store_id', 'product.isSpecial', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('100-((store_products.price*100)/store_products.mrp) as discountper'), DB::raw('sum(IFNULL(product_rating.rating,0))/count(IFNULL(product_rating.rating,0)) as avgrating'))
                ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', 'product_rating.rating')
                ->where('tags.tag', $tag)
                ->where('store_products.store_id', $store_id)
                ->where('store_products.price', '!=', NULL)
				->whereNull('store_products.deletedAt')
                ->where('product.hide', 0)
                ->where('product.approved', 1)
                ->whereBetween('store_products.price', [$min_price, $max_price])
                ->havingBetween('avgrating', [$min_rating, $max_rating])
                ->havingBetween('discountper', [$min_discount, $max_discount])
                ->where('store_products.stock', $stock, $by)
                ->paginate(10);
        }

        $prodsd = $prodsssss->unique('product_id');
        $prod = NULL;

        foreach ($prodsd as $store) {
            $prod[] = $store;
        }

        if ($prod != NULL) {
            $result = array();
            $i = 0;
            $j = 0;
            $m = 0;
            foreach ($prod as $prods) {
                $a = 0;

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
                $prods->maxprice = $price;

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
					->whereNull('store_products.deletedAt')
                    ->where('product_varient.approved', 1)
                    ->get();
                $images = DB::table('product_images')
                    ->whereNull('deletedAt')
                    ->select('image')
                    ->whereIn('product_id', $apps)
                    ->orderBy('type', 'DESC')
                    ->get();
                if (count($images) > 0) {
                    $result[$m]->images = $images;
                    $m++;
                } else {
                    $images = DB::table('product')
                        ->whereNull('deletedAt')
                        ->select('product_image as image')
                        ->whereIn('product_id', $apps)
                        ->get();

                    $result[$m]->images = $images;
                    $m++;

                }
                $tag = DB::table('tags')
                    ->whereNull('deletedAt')
                    ->whereIn('product_id', $apps)
                    ->get();
                $result[$j]->tags = $tag;
                $j++;

                foreach ($app as $aa) {
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
                    $app[$a]->maxprice = $price;
                    $a++;
                }
                $result[$i]->varients = $app;
                $i++;

            }

            $message = array('status' => '1', 'message' => 'Products found', 'data' => $prod);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Products not found');
            return $message;
        }
    }

    public function banner_var(Request $request)
    {
        $prod_id = $request->varient_id;
        $store_id = $request->store_id;
        $prod = DB::table('store_products')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->Leftjoin('store_orders', 'product_varient.varient_id', '=', 'store_orders.varient_id')
            ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
            ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            ->select('product.cat_id', 'product.isSpecial', 'store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type', DB::raw('count(store_orders.varient_id) as count'))
            ->groupBy('product.cat_id', 'store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.type')
            ->where('store_products.store_id', $store_id)
            ->where('store_products.varient_id', $prod_id)
            ->where('deal_product.deal_price', NULL)
			->whereNull('store_products.deletedAt')
            ->where('product.hide', 0)
            ->where('store_products.price', '!=', NULL)
            ->first();
        if (!$prod) {
            $message = array('status' => '0', 'message' => 'Product not found');
            return $message;
        }
        if ($request->user_id != NULL) {
            $wishlist = DB::table('wishlist')
                ->where('varient_id', $prod_id)
                ->where('user_id', $request->user_id)
                ->first();
            $cart = DB::table('store_orders')
                ->where('varient_id', $prod_id)
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
            ->where('varient_id', $prod_id)
            ->where('store_id', $store_id)
            ->get();

        if (count($getrating) > 0) {
            $countrating = DB::table('product_rating')
                ->where('varient_id', $prod_id)
                ->where('store_id', $store_id)
                ->count();
            $rating = DB::table('product_rating')
                ->where('varient_id', $prod_id)
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
				->whereNull('store_products.deletedAt')
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

            if ($request->user_id != NULL) {
                $wishlist = DB::table('wishlist')
                    ->where('varient_id', $app[$i]->varient_id)
                    ->where('user_id', $request->user_id)
                    ->first();
                $cart = DB::table('store_orders')
                    ->where('varient_id', $app[$i]->varient_id)
                    ->where('store_approval', $request->user_id)
                    ->where('order_cart_id', 'incart')
                    ->where('store_id', $store_id)
                    ->first();

                if ($wishlist) {
                    $app[$i]->isFavourite = 'true';
                } else {
                    $app[$i]->isFavourite = 'false';
                }
                if ($cart) {
                    $app[$i]->cart_qty = $cart->qty;
                } else {
                    $app[$i]->cart_qty = 0;
                }
            } else {
                $app[$i]->isFavourite = 'false';
                $app[$i]->cart_qty = 0;
            }

            $getrating = DB::table('product_rating')
                ->whereNull('deletedAt')
                ->where('varient_id', $app[$i]->varient_id)
                ->where('store_id', $store_id)
                ->get();

            if (count($getrating) > 0) {
                $countrating = DB::table('product_rating')
                    ->where('varient_id', $app[$i]->varient_id)
                    ->where('store_id', $store_id)
                    ->count();
                $rating = DB::table('product_rating')
                    ->where('varient_id', $app[$i]->varient_id)
                    ->where('store_id', $store_id)
                    ->avg('rating');
                $app[$i]->avgrating = $rating;
                $app[$i]->countrating = $countrating;
            } else {
                $app[$i]->avgrating = 0;
                $app[$i]->countrating = 0;
            }

            if ($app[$i]->mrp != 0) {
                $discountper = ($app[$i]->price * 100) / $app[$i]->mrp;
                $app[$i]->discountper = round($discountper, 2);
            } else {
                $app[$i]->discountper = 0;
            }
            $result[$i]->varients = $app;

            $i++;

            // AKTİF DİLDE VERİYİ GETİRMEK
            $arrayData = class2Array($prod);
            $lang = strtoupper($request->header('langCode'));

            $arrayData['currency'] = getCurrencyData($arrayData['store_id']);
            $arrayData['unit'] = lngUnit($arrayData['unit'], $lang);
            $arrayData['product_name'] = tPC($arrayData['product_name'], $lang);
            $arrayData['description'] = tPC($arrayData['description'], $lang);
            foreach ($arrayData['varients'] as $value){
                $arrayData['varients'][0]['currency'] = getCurrencyData($value['store_id']);
                $arrayData['varients'][0]['description'] = tPC($value['description'], $lang);
                $arrayData['varients'][0]['unit'] = lngUnit($value['unit'], $lang);
            }

            // AKTİF DİLDE VERİYİ GETİRMEK
            $prod = array2Class($arrayData);

            $prod1 = generateTranslatedProductItemWithVarients($prod1, $request);
            $data = array('detail' => $prod, 'similar_product' => $prod1);

            $message = array('status' => '1', 'message' => 'Products Detail', 'data' => $data);

            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Product not found');

            return $message;
        }
    }

    public function product_det(Request $request)
    {
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $prod = DB::table('store_products')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->where('product.product_id', $product_id)
            ->where('store_products.store_id', $store_id)
            ->where('store_products.price', '!=', NULL)
			->whereNull('store_products.deletedAt')
            ->where('product.hide', 0)
            ->where('product.approved', 1)
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
				->whereNull('store_products.deletedAt')
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
				->whereNull('store_products.deletedAt')
			
                ->where('product.hide', 0)
                ->where('product.approved', 1)
				->inRandomOrder()
                ->paginate(12);

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
						->whereNull('store_products.deletedAt')
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
}