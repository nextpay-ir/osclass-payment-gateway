<?php
define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
require_once ABS_PATH . 'oc-load.php';
require_once 'nextpay_payment.php';


    $return = $_POST['custom'];
    $Amount = $_POST['amount']/10; // Tomman | تومان
    $return .= "|".$Amount;
    $api_key = payment_decrypt(osc_get_preference('nextpay_apikey', 'payment'));   //Require
    $order_id = time();


    $callBackUrl = osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'get.php?rpl=' . $return;
    
    
    $parameters = array(
        'api_key' 	=> $api_key,
        'amount' 	=> $Amount,
        'callback_uri' 	=> $callBackUrl,
        'order_id' 	=> $order_id
    );

    try {
        $nextpay = new Nextpay_Payment($parameters);
        $nextpay->setDefaultVerify(Type_Verify::SoapClient);
        $result = $nextpay->token();
        $code = intval($result->code);
        if($code == -1){
            $nextpay->send($result->trans_id);
        }
        else
        {
	    
            $message = ' شماره خطا: '.$code.'<br />';
            $message .='<br>'.$nextpay->code_error($code);
	    osc_add_flash_ok_message($message);
	    payment_js_redirect_to(osc_route_url('payment-user-menu'));
            exit();
        }
    }catch (Exception $e) {
	  osc_add_flash_ok_message(' سیستم ارتباطی  soap   دچار مشکل میباشد' . $e->getMessage());
	  payment_js_redirect_to(osc_route_url('payment-user-menu'));
    }

?>