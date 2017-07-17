<?php
define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
require_once ABS_PATH . 'oc-load.php';
require_once 'nextpay_payment.php';

$api_key = payment_decrypt(osc_get_preference('nextpay_apikey', 'payment'));   //Required
$rpl = $_GET['rpl'];
$data = payment_get_custom($rpl);
$Amount = $data['amount']/10; // Tomman | تومان
$trans_id = isset($_POST['trans_id']) ? $_POST['trans_id'] : false ;
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : false ;
$currency = osc_get_preference('currency', 'payment');

$nextpay = new Nextpay_Payment();

if (!is_string($trans_id) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $trans_id) !== 1)) {
    $message = ' شماره خطا: -34 <br />';
    $message .='<br>'.$nextpay->code_error(-34);
} else {
    $parameters = array
    (
        'api_key'	=> $api_key,
        'order_id'	=> $order_id,
        'trans_id' 	=> $trans_id,
        'amount'	=> $Amount
    );
    try {
	$result = intval($nextpay->verify_request($parameters));
	if($result == 0){
	    $exists = ModelNextPayPayment::newInstance()->getPaymentByCode($trans_id, 'NEXTPAY');
	    if(isset($exists['pk_i_id'])) {
		$message = 'قبلا پرداخت شده است.';
	    }elseif (!$exists['pk_i_id']) {
		$product_type = explode('x', $data['product']);
		$payment_id = ModelNextPayPayment::newInstance()->saveLog(
								  $data['concept'], //concept
								  $trans_id, // transaction code
								  $data['amount'], //amount
								  $currency, //currency
								  $data['email'], // payer's email
								  $data['user'], //user
								  $data['itemid'], //item
								  $product_type[0], //product type
								  'NEXTPAY'); //source
		if ($product_type[0] == '101') {
		    ModelNextPayPayment::newInstance()->payPublishFee($product_type[2], $payment_id);
		} else if ($product_type[0] == '201') {
		    ModelNextPayPayment::newInstance()->payPremiumFee($product_type[2], $payment_id);
		} else {
		    ModelNextPayPayment::newInstance()->addWallet($data['user'], $data['amount']);
		}
		$message = 'پرداخت انجام گرديد<br/>شماره رسيد پرداخت: ' . $trans_id;
	    }
	} else {
	    $message = ' شماره خطا: '.$result.'<br />';
            $message .='<br>'.$nextpay->code_error($result);
	}	
    }catch (Exception $e) {
	  osc_add_flash_ok_message(' سیستم ارتباطی  soap   دچار مشکل میباشد' . $e->getMessage());
	  payment_js_redirect_to(osc_route_url('payment-user-menu'));
    }
}

osc_add_flash_ok_message($message);
payment_js_redirect_to(osc_route_url('payment-user-menu'));

?>