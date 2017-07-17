<?php
/*
Plugin Name: افزونه پرداخت نکست پی
Plugin URI: http://www.faosclass.com/
Description: سیستم پرداخت برای ویژه سازی آگهی ها
Version: 3.0.1
Author: نکست پی
Author URI: https://www.nextpay.ir/
Plugin update URI: https://github.com/nextpay-ir/osclass/plugins.php?name=faosclass_payment
*/

    define('PAYMENT_CRYPT_KEY', 'qwer254gfdrt56fg');
    // PAYMENT STATUS
    define('PAYMENT_FAILED', -2);
    define('PAYMENT_COMPLETED', 0);
    define('PAYMENT_PENDING', -1);
    define('PAYMENT_ALREADY_PAID', -49);

    // load necessary functions
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'ModelNextPayPayment.php';
    // Load different methods of payments

    if(osc_get_preference('nextpay_enabled', 'payment')==1) {
        require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'payments/nextpay/Nextpay.php';
    }

    /**
    * Create tables and variables on t_preference and t_pages
    */
    function payment_install() {
        ModelNextPayPayment::newInstance()->install();
    }

    /**
    * Clean up all the tables and preferences
    */
    function payment_uninstall() {
        ModelNextPayPayment::newInstance()->uninstall();
    }

    /**
    * Create a menu on the admin panel
    */
    function payment_admin_menu() {
        osc_add_admin_submenu_divider('plugins',  __('Payment plugin', 'payment'), 'payment_divider', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Payment options', 'payment'), osc_route_admin_url('payment-admin-conf'), 'payment_settings', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Categories fees', 'payment'), osc_route_admin_url('payment-admin-prices'), 'payment_help', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Premium Log', 'payment'), osc_route_admin_url('payment-premium-log'), 'premium_log', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Publish Log', 'payment'), osc_route_admin_url('payment-publish-log'), 'publish_log', 'administrator');
        osc_add_admin_submenu_page('plugins', __('payment + Log', 'payment'), osc_route_admin_url('payment-log'), 'payment_log', 'administrator');
    }

    /**
     * Load payment's js library
     */
    function payment_load_lib() {
        if(Params::getParam('page')=='custom') {
            osc_enqueue_style('payment-plugin', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'style.css');
        }
    }

    /**
     * Redirect to payment page after publishing an item
     *
     * @param integer $item
     */
    function payment_publish($item) {
        // Need to pay to publish ?
        if(osc_get_preference('pay_per_post', 'payment')==1) {
            $category_fee = ModelNextPayPayment::newInstance()->getPublishPrice($item['fk_i_category_id']);
            payment_send_email($item, $category_fee);
            if($category_fee>0) {
                // Catch and re-set FlashMessages
                osc_resend_flash_messages();
                $mItems = new ItemActions(false);
                $mItems->disable($item['pk_i_id']);
                ModelNextPayPayment::newInstance()->createItem($item['pk_i_id'],0);
                osc_redirect_to(osc_route_url('payment-publish', array('itemId' => $item['pk_i_id'])));
            } else {
                // PRICE IS ZERO
                ModelNextPayPayment::newInstance()->createItem($item['pk_i_id'], 1);
            }
        } else {
            // NO NEED TO PAY PUBLISH FEE
            payment_send_email($item, 0);
            if(osc_get_preference('allow_premium', 'payment')==1) {
                $premium_fee = ModelNextPayPayment::newInstance()->getPremiumPrice($item['fk_i_category_id']);
                if($premium_fee>0) {
                    osc_redirect_to(osc_route_url('payment-premium', array('itemId' => $item['pk_i_id'])));
                }
            }
        }
    }

    /**
     * Create a new menu option on users' dashboards
     */
    function payment_user_menu() {
        echo '<li class="opt_payment" ><a href="'.osc_route_url('payment-user-pay').'" >'.__("User Pay Log", "payment") . '</a></li>' ;
        echo '<li class="opt_payment" ><a href="'.osc_route_url('payment-user-menu').'" >'.__("Listings payment status", "payment").'</a></li>' ;
        if((osc_get_preference('pack_price_1', 'payment')!='' && osc_get_preference('pack_price_1', 'payment')!='0')
            || (osc_get_preference('pack_price_2', 'payment')!='' && osc_get_preference('pack_price_2', 'payment')!='0')
            || (osc_get_preference('pack_price_3', 'payment')!='' && osc_get_preference('pack_price_3', 'payment')!='0')) {
                echo '<li class="opt_payment_pack" ><a href="'.osc_route_url('payment-user-pack').'" >'.__("Buy credit for payments", "payment").'</a></li>' ;
        }
    }

    /**
     * Executed hourly with cron to clean up the expired-premium ads
     */
    function payment_cron() {
        ModelNextPayPayment::newInstance()->purgeExpired();
    }

    /**
     * Executed when an item is manually set to NO-premium to clean up it on the plugin's table
     *
     * @param integer $id
     */
    function payment_premium_off($id) {
        ModelNextPayPayment::newInstance()->premiumOff($id);
    }

    /**
     * Executed before editing an item
     *
     * @param array $item
     */
    function payment_before_edit($item) {
        // avoid category changes once the item is paid
        if((osc_get_preference('pay_per_post', 'payment') == '1' && ModelNextPayPayment::newInstance()->publishFeeIsPaid($item['pk_i_id']))|| (osc_get_preference('allow_premium','payment') == '1' && ModelNextPayPayment::newInstance()->premiumFeeIsPaid($item['pk_i_id']))) {
            $cat[0] = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);
            View::newInstance()->_exportVariableToView('categories', $cat);
        }
    }


    /**
     * Executed before showing an item
     *
     * @param array $item
     */
    function payment_show_item($item) {
        if(osc_get_preference("pay_per_post", "payment")=="1" && !ModelNextPayPayment::newInstance()->publishFeeIsPaid($item['pk_i_id']) ) {
            payment_publish($item);
        };
    };

    function payment_item_delete($itemId) {
        ModelNextPayPayment::newInstance()->deleteItem($itemId);
    }

    function payment_configure_link() {
        osc_redirect_to(osc_route_admin_url('payment-admin-conf'));
    }

    function payment_update_version() {
        ModelNextPayPayment::newInstance()->versionUpdate();
    }


    /**
     * ADD ROUTES (VERSION 3.2+)
     */
    osc_add_route('payment-admin-conf', 'payment/admin/conf', 'payment/admin/conf', osc_plugin_folder(__FILE__).'admin/conf.php');
    osc_add_route('payment-admin-prices', 'payment/admin/prices', 'payment/admin/prices', osc_plugin_folder(__FILE__).'admin/conf_prices.php');
    osc_add_route('payment-publish', 'payment/publish/([0-9]+)', 'payment/publish/{itemId}', osc_plugin_folder(__FILE__).'user/payperpublish.php');
    osc_add_route('payment-premium', 'payment/premium/([0-9]+)', 'payment/premium/{itemId}', osc_plugin_folder(__FILE__).'user/makepremium.php');
    osc_add_route('payment-user-menu', 'payment/menu', 'payment/menu', osc_plugin_folder(__FILE__).'user/menu.php', true);
    osc_add_route('payment-user-pack', 'payment/pack', 'payment/pack', osc_plugin_folder(__FILE__).'user/pack.php', true);
    osc_add_route('payment-wallet', 'payment/wallet/([^\/]+)/([^\/]+)/([^\/]+)', 'payment/wallet/{a}/{extra}/{desc}', osc_plugin_folder(__FILE__).'/user/wallet.php', true);

    osc_add_route('payment-premium-log', 'payment/admin/premium-log', 'payment/admin/premium-log', osc_plugin_folder(__FILE__).'admin/premium_log.php');
    osc_add_route('payment-publish-log', 'payment/admin/publish-log', 'payment/admin/publish-log', osc_plugin_folder(__FILE__).'admin/publish_log.php');
    osc_add_route('payment-log', 'payment/admin/payment-log', 'payment/admin/payment-log', osc_plugin_folder(__FILE__).'admin/payment_log.php');
    osc_add_route('nextpay-admin-conf', 'payments/nextpay/conf', 'payments/nextpay/conf', osc_plugin_folder(__FILE__).'payments/nextpay/conf.php');
    osc_add_route('payment-user-pay', 'payment/user-pay', 'payment/user-pay', osc_plugin_folder(__FILE__).'user/user_pay_log.php', true);


    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'payment_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'payment_configure_link');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'payment_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_enable", 'payment_update_version');

    osc_add_hook('admin_menu_init', 'payment_admin_menu');

    osc_add_hook('init', 'payment_load_lib');
    osc_add_hook('posted_item', 'payment_publish', 10);
    osc_add_hook('user_menu', 'payment_user_menu');
    osc_add_hook('cron_hourly', 'payment_cron');
    osc_add_hook('item_premium_off', 'payment_premium_off');
    osc_add_hook('before_item_edit', 'payment_before_edit');
    osc_add_hook('show_item', 'payment_show_item');
    osc_add_hook('delete_item', 'payment_item_delete');

?>
