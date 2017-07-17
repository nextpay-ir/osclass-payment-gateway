<?php
    $packs = array();
    if(osc_get_preference("pack_price_1", "payment")!='' && osc_get_preference("pack_price_1", "payment")!='0') {
        $packs[] = osc_get_preference("pack_price_1", "payment");
    }
    if(osc_get_preference("pack_price_2", "payment")!='' && osc_get_preference("pack_price_2", "payment")!='0') {
        $packs[] = osc_get_preference("pack_price_2", "payment");
    }
    if(osc_get_preference("pack_price_3", "payment")!='' && osc_get_preference("pack_price_3", "payment")!='0') {
        $packs[] = osc_get_preference("pack_price_3", "payment");
    }
    @$user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
    $wallet = ModelNextPayPayment::newInstance()->getWallet(osc_logged_user_id());
    if(osc_get_preference('currency', 'payment')=='BTC') {
        $amount = isset($wallet['i_amount'])?$wallet['i_amount']:0;
        $formatted_amount = payment_format_btc($amount);
        $credit_msg = sprintf(__('Credit packs. Your current credit is %s', 'payment'), $formatted_amount);
    } else {
        $amount = isset($wallet['i_amount'])?$wallet['i_amount']:0;
        if($amount!=0) {
            $formatted_amount = $amount .osc_get_preference('currency', 'payment');
            $credit_msg = sprintf(__('Credit packs. Your current credit is %s', 'payment'), $formatted_amount);
        } else {
            $credit_msg = __('Your wallet is empty. Buy some credits.', 'payment');
        }
    }

?>

<h2><?php echo $credit_msg; ?></h2>
<?php $pack_n = 0;
foreach($packs as $pack) { $pack_n++; ?>
    <div class="pack">
        <h3><?php echo sprintf(__('Credit pack #%d', 'payment'), $pack_n); ?></h3>
        <div><label><?php _e("Price", "payment");?>:</label> <?php echo $pack." ".osc_get_preference('currency', 'payment'); ?></div>
        <ul class="payments-ul">
        <?php payment_buttons($pack, sprintf(__("Credit for %s %s", "payment"), $pack, osc_get_preference("currency", "payment")), '301x'.$pack, array('user' => @$user['pk_i_id'], 'itemid' => @$user['pk_i_id'], 'email' => @$user['s_email'])); ?>
        </ul>
    </div>


<?php } ?>
<style>
.pack {
    background: #ddd;
    border-radius: 5px;
    width: 210px;
    padding: 12px;
    font: 15px byekan;
    list-style: none;
    text-align: center;
    float: right;
    margin-right: 8px;
    margin-top: 30px;
}
.pack:hover {
    background: #E3E3E3;
}
li.payment {
    cursor: pointer;
}
</style>
<?php payment_buttons_js(); ?>