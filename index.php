<?php

$request ['merchantId'] = ""; //PUT YOUR MERCHANT ID HERE
$request ['merchantTransactionId'] = "MT".time();
$request ['merchantUserId'] = "MUID".time();
$request ['amount'] = 10000;
$request ['redirectUrl'] = 'http://localhost/phonepay/responce.php';
$request['redirectMode'] = 'POST';
$request ['callbackUrl'] = 'http://localhost/phonepay/responce.php';
$request ['mobileNumber'] = "9999999999";
$request11 ['type'] = "PAY_PAGE";
$request ['paymentInstrument'] = $request11;

$salt=""; //PUT YOUR SALT HERE
$requestJson = json_encode($request);
$base = base64_encode($requestJson);
$hasbValue = base64_encode($requestJson). "/pg/v1/pay" . $salt;
$hashRequest = hash('sha256', $hasbValue);
$hashFinalRequest = $hashRequest."###1";

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-preprod.phonepe.com/apis/hermes/pg/v1/pay",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => '{"request":"'.$base.'"}',
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "X-VERIFY: ".$hashFinalRequest,
    "accept: application/json"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
} 
else {
  $res = json_decode($response);
  if($res->success)
  {
    header('Location:'.$res->data->instrumentResponse->redirectInfo->url);
  }
  else
  {
    echo $response;
  }
}

?>
