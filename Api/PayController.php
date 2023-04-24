<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use \Datetime;

class PayController extends Controller
{
    public function pay_create(Request $request)
    {
        $reference = rand(1000000000000000, 10000000000000000);
        $type = 'DMS';
        $token = rand(100000000, 1000000000);
        $save = 'y';
        $amount = floatval($request->amount)*100;
        $currency = '944';
        $biller = 'BLR0001';
        $description = 'Save_card';
        $template = 'TPL0003';
        $language = $request->lang;

        $callback = 'https://oz21ioidare.ozio.az/pay/pay_result.php?reference='.$reference;
        //$extra = 'user_id='. 1;
        $extra = 'back-url=https://oz21ioidare.ozio.az/pay/pay_result.php?msg=success;fail-url=https://oz21ioidare.ozio.az/pay/pay_result.php?msg=failed';
        $secretKey = 'A89DE2FF83625E4935B3E569265BE152';

        /* $signature = base64_encode(md5("$reference"."$type"."$token"."$save"."$amount"."$currency"."$biller"."$description"."$template"."$language".$callback."$secretKey", true));
        $url = "https://api.pay.yigim.az/payment/create?reference=$reference&type=$type&token=$token&save=$save&amount=$amount&currency=$currency&biller=$biller&description=$description&template=$template&language=$language&callback=$callback";
        */
        $signature = base64_encode(md5("$reference"."$type"."$token"."$save"."$amount"."$currency"."$biller"."$description"."$template"."$language"."$callback"."$extra"."$secretKey", true));
        $url = "https://api.pay.yigim.az/payment/create?reference=$reference&type=$type&token=$token&save=$save&amount=$amount&currency=$currency&biller=$biller&description=$description&template=$template&language=$language&callback=$callback&extra=$extra";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Merchant: CIN0001',
            'X-Signature: '.$signature,
            'X-Type: JSON'
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }
        curl_close($ch);
        $data=json_decode($response,true);
        if($data["url"] && $data["url"]!=""){
            $message = array('status' => '1','create_url'=>$data["url"]."?reference=".$reference, 'reference' => $reference);
        }else{
            $message = array('status' => '0');
        }
        return $message;
    }
    public function pay_result(Request $request)
    {

        echo "pay_result";

    }
    public function delete_card(Request $request)
    {
       $user_id = $request->user_id;
       $py_id = $request->py_id;
       $deleteCard = DB::table('orders_payments')
            ->where('user_id', $user_id)
            ->where('py_id', $py_id)
            ->delete();


       if ($deleteCard) {
           $message = array('status' => '1', 'message' => 'Deleted Card');
           return $message;
       }
   }
    public function pay_execute(Request $request)
    {
        $reference = rand(1000000000000000, 10000000000000000);
        $type = 'DMS';
        $token = $request->token;
        // $save = '';
        $amount = floatval($request->amount)*100;
        $currency = '944';
        $biller = 'BLR0001';
        // $description = 'Save_Card';
        // $template = 'TPL0002';
        // $language = 'az';
        // $callback = 'https://oz21ioidare.ozio.az/pay_result.php';
        $extra = 'back-url=https://oz21ioidare.ozio.az/pay/pay_result.php?msg=success;fail-url=https://oz21ioidare.ozio.az/pay/pay_result.php?msg=failed';
        $secretKey = 'A89DE2FF83625E4935B3E569265BE152';

        $signature = base64_encode(md5($reference.$type.$token.$amount.$currency.$biller.$extra.$secretKey, true));
        //echo 'Signature Degerimiz:   '.$reference.$type.$token.$amount.$currency.$biller.$extra.$secretKey;
        //echo 'Signature nin kendisi:   '.$signature ;
        $url = "https://api.pay.yigim.az/payment/execute?reference=".$reference."&type=".$type."&token=".$token."&amount=".$amount."&currency=".$currency."&biller=".$biller."&extra=".$extra;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Merchant: CIN0001',
            'X-Signature: '.$signature,
            'X-Type: JSON'
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }
        curl_close($ch);
        $data=json_decode($response,true);
        $message = array('status' => '1','data'=>$data);
        return $message;
        //header("Location: http://localhost/yigim_megaplus/charge_sample.php?reference=".$data["reference"]."&amount=".$data["amount"]);

    }
    public function pay_charge(Request $request)
        {

        $reference =$request->reference;
        $amount = floatval($request->amount)*100;
        $secretKey = 'A89DE2FF83625E4935B3E569265BE152';


        $signature = base64_encode(md5("$reference"."$amount"."$secretKey", true));
        $url = "https://api.pay.yigim.az/payment/charge?reference=$reference&amount=$amount";


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Merchant: CIN0001',
            'X-Signature: '.$signature,
            'X-Type: JSON'
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $data=json_decode($response,true);

        if (curl_errno($ch)) {
            echo curl_error($ch);
        }

        curl_close($ch);
        //echo explode(" ",$data["message"])[0];
        $message = array('status' => '1','data'=>$data);
        return $message;
    }
    public function pay_status(Request $request)
    {

        $reference = $request->reference;
               $type = $request->type;
               $url = "https://api.pay.yigim.az/payment/status?reference=".$reference;
               $secretkey = "A89DE2FF83625E4935B3E569265BE152";
               $signature = base64_encode(md5("$reference"."$secretkey", true));
               $ch = curl_init();
               curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
               curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'X-Merchant: CIN0001',
                   'X-Signature:'.$signature,
                   'X-Type: JSON'
               ));
               curl_setopt($ch, CURLOPT_URL, $url);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
               curl_setopt($ch, CURLOPT_TIMEOUT, 10);
               $response = curl_exec($ch);
               curl_close($ch);
               $data=json_decode($response,true);
               //var_dump( $data);

               //echo $data["token"];

               if($type=="quick_pay"){
                   header("Location: http://localhost/yigim_megaplus/kayitli_kart.php?token=".$data["token"]."&amount=".$data["amount"]);
               }else{
                   $message = array('status' => '1', 'message' => 'My All orders', 'order' => $data);
               }
               return $message;

    }
}
?>
