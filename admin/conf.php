<?php

    if(Params::getParam('plugin_action')=='done') {
        osc_set_preference('default_premium_cost', Params::getParam("default_premium_cost") ? Params::getParam("default_premium_cost") : '1.0', 'payment', 'STRING');
        osc_set_preference('allow_premium', Params::getParam("allow_premium") ? Params::getParam("allow_premium") : '0', 'payment', 'BOOLEAN');
        osc_set_preference('default_publish_cost', Params::getParam("default_premium_cost") ? Params::getParam("default_publish_cost") : '1.0', 'payment', 'STRING');
        osc_set_preference('pay_per_post', Params::getParam("pay_per_post") ? Params::getParam("pay_per_post") : '0', 'payment', 'BOOLEAN');
        osc_set_preference('premium_days', Params::getParam("premium_days") ? Params::getParam("premium_days") : '7', 'payment', 'INTEGER');
        osc_set_preference('currency', Params::getParam("currency") ? Params::getParam("currency") : 'USD', 'payment', 'STRING');
        osc_set_preference('pack_price_1', Params::getParam("pack_price_1"), 'payment', 'STRING');
        osc_set_preference('pack_price_2', Params::getParam("pack_price_2"), 'payment', 'STRING');
        osc_set_preference('pack_price_3', Params::getParam("pack_price_3"), 'payment', 'STRING');

        osc_set_preference('nextpay_apikey', payment_crypt(Params::getParam("nextpay_apikey")), 'payment', 'STRING');
        osc_set_preference('nextpay_enabled', Params::getParam("nextpay_enabled") ? Params::getParam("nextpay_enabled") : '0', 'payment', 'BOOLEAN');

        // HACK : This will make possible use of the flash messages ;)
        ob_get_clean();
        osc_add_flash_ok_message(__('Congratulations, the plugin is now configured', 'payment'), 'admin');
        osc_redirect_to(osc_route_admin_url('payment-admin-conf'));
    }
?>

<script type="text/javascript" >
    $(document).ready(function(){
        $("#dialog-nextpay").dialog({
            autoOpen: false,
            modal: true,
            width: '90%',
            title: 'راهنمای نکست پی'
        });
    });
</script>
<?php if(PAYMENT_CRYPT_KEY=='randompasswordchangethis') {
    echo '<div style="text-align:center; font-size:22px; background-color:#dd0000;"><p>' . sprintf(__('Please, change the crypt key (PAYMENT_CRYPT_KEY) in %s', 'payment'), payment_path().'index.php') . '</p></div>';
}; ?>
<div id="general-setting">
    <div id="general-settings">
        <h2 class="render-title"><?php _e('Payments settings', 'payment'); ?></h2>
        <ul id="error_list"></ul>
        <form name="payment_form" action="<?php echo osc_admin_base_url(true); ?>" method="post">
            <input type="hidden" name="page" value="plugins" />
            <input type="hidden" name="action" value="renderplugin" />
            <input type="hidden" name="route" value="payment-admin-conf" />
            <input type="hidden" name="plugin_action" value="done" />
            <fieldset>
                <div class="form-horizontal">
                    <div class="form-row">
                        <div class="form-label"><?php _e('Premium ads', 'payment'); ?></div>
                        <div class="form-controls">
                            <div class="form-label-checkbox">
                                <label>
                                    <input type="checkbox" <?php echo (osc_get_preference('allow_premium', 'payment') ? 'checked="true"' : ''); ?> name="allow_premium" value="1" />
                                    <?php _e('Allow premium ads', 'payment'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Default premium cost', 'payment'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="default_premium_cost" value="<?php echo osc_get_preference('default_premium_cost', 'payment'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Premium days', 'payment'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="premium_days" value="<?php echo osc_get_preference('premium_days', 'payment'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Publish fee', 'payment'); ?></div>
                        <div class="form-controls">
                            <div class="form-label-checkbox">
                                <label>
                                    <input type="checkbox" <?php echo (osc_get_preference('pay_per_post', 'payment') ? 'checked="true"' : ''); ?> name="pay_per_post" value="1" />
                                    <?php _e('Pay per post ads', 'payment'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Default publish cost', 'payment'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="default_publish_cost" value="<?php echo osc_get_preference('default_publish_cost', 'payment'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Default currency', 'payment'); ?></div>
                        <div class="form-controls">
                            <select name="currency" id="currency">
                                <option value="ریال" <?php if(osc_get_preference('currency', 'payment')=="ریال") { echo 'selected="selected"';}; ?> >ریال</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <span class="help-box">
                            <?php _e("You could specify up to 3 'packs' that users can buy, so they don't need to pay each time they publish an ad. The credit from the pack will be stored for later uses.",'payment'); ?>
                        </span>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php echo sprintf(__('Price of pack #%d', 'payment'), '1'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="pack_price_1" value="<?php echo osc_get_preference('pack_price_1', 'payment'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php echo sprintf(__('Price of pack #%d', 'payment'), '2'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="pack_price_2" value="<?php echo osc_get_preference('pack_price_2', 'payment'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php echo sprintf(__('Price of pack #%d', 'payment'), '3'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="pack_price_3" value="<?php echo osc_get_preference('pack_price_3', 'payment'); ?>" /></div>
                    </div>


                    <h2 class="render-title separate-top">تنظیمات نکست پی <span><a href="javascript:void(0);" onclick="$('#dialog-nextpay').dialog('open');" ><?php _e('help', 'payment'); ?></a></span> <span style="font-size: 0.5em" ><a href="javascript:void(0);" onclick="$('.nextpay').toggle();" ><?php _e('Show options', 'payment'); ?></a></span></h2>
                    <div class="form-row nextpay hide">
                        <div class="form-label">فعال کردن نکست پی</div>
                        <div class="form-controls">
                            <div class="form-label-checkbox">
                                <label>
                                    <input type="checkbox" <?php echo (osc_get_preference('nextpay_enabled', 'payment') ? 'checked="true"' : ''); ?> name="nextpay_enabled" value="1" />
                                   فعال کردن نکست پی برای انجام پرداخت
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row nextpay hide">
                        <div class="form-label">کلید مجوز دهی</div>
                        <div class="form-controls"><input type="text" class="xlarge" name="nextpay_apikey" value="<?php echo payment_decrypt(osc_get_preference('nextpay_apikey', 'payment')); ?>" /></div>
                    </div>




                    <div class="clear"></div>
                    <div class="form-actions">
                        <input type="submit" id="save_changes" value="<?php echo osc_esc_html( __('Save changes') ); ?>" class="btn btn-submit" />
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<form id="dialog-nextpay" method="get" action="#" class="has-form-actions hide">
    <div class="form-horizontal">
        <div class="form-row">
    با استفاده از درگاه نکست پی قادر هستید قابلیت پرداخت وجه را به سایت خود بیافزایید.<br>
برای فعال کردن به بخش تنظیمات درگاه نکست پی مراجعه کرده و کد api دریافتی از سایت نکست پی را وارد نماید.<br>


        </div>
        <div class="form-actions">
            <div class="wrapper">
                <a class="btn" href="javascript:void(0);" onclick="$('#dialog-nextpay').dialog('close');"><?php _e('Cancel'); ?></a>
            </div>
        </div>
    </div>
</form>