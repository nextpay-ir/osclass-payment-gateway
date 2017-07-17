<?php
$conn   = getConnection();
$paypal_log = $conn->osc_dbFetchResults("SELECT * FROM %st_payments_log", DB_TABLE_PREFIX);
?>
 <link href="<?php echo osc_current_admin_theme_styles_url('datatables.css') ; ?>" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo osc_current_admin_theme_js_url('jquery.dataTables.js') ; ?>"></script>
        <script type="text/javascript" src="<?php echo osc_current_admin_theme_js_url('datatables.pagination.js') ; ?>"></script>
        <script type="text/javascript" src="<?php echo osc_current_admin_theme_js_url('datatables.extend.js') ; ?>"></script>
<div class="dataTables_wrapper">
                    <? if (osc_version() < '240') {?>
                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="datatables_list">
                   <? echo osc_version(); } else {?>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="datatables_list">
                    <? } ?>  
                        <thead>
                            <tr>
                                <th class="sorting"><?php _e('ID', 'payment'); ?></th>
                                <th ><?php _e('Description', 'payment'); ?></th>
                                <th class="sorting"><?php _e('Date', 'payment'); ?></th>
                                <th ><?php _e('Code', 'payment'); ?></th>
                                <th ><?php _e('Amount', 'payment'); ?></th>
                                <th ><?php _e('Email', 'payment'); ?></th>
                                <th ><?php _e('UserID', 'payment'); ?></th>
                                <th ><?php _e('ItemID', 'payment'); ?></th>
                                <th ><?php _e('Source', 'payment'); ?></th>
                                <th ><?php _e('Product type', 'payment'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $odd = 1;
                                foreach($paypal_log as $logs) {
                                    if($odd==1) {
                                        $odd_even = "odd";
                                        $odd = 0;
                                    } else {
                                        $odd_even = "even";
                                        $odd = 1;
                                    }
				
				                 $amount+=$logs['i_amount'];
                            ?>
                            
                            <tr class="<?php echo $odd_even;?>">
                            	<td><?php echo $logs['pk_i_id']; ?></td>
                            	<td><?php echo $logs['s_concept']; ?></td>
                            	<td><?php echo osc_format_date($logs['dt_date'],"l j F Y h:i:s A"); ?></td>
                            	<td><?php echo $logs['s_code']; ?></td>
                                <td><?php echo $logs['i_amount']; echo $logs['s_currency_code']; ?></td>
                            	<td><?php echo $logs['s_email']; ?></td>
                            	<td><?php echo $logs['fk_i_user_id']; ?></td>
                            	<td><?php echo $logs['fk_i_item_id']; ?></td>
                            	<td><?php if ($logs['s_source']=='NEXTPAY') echo "نکست پی";
                                          else echo $logs['s_source'];
				?></td>
                            	<td><?php if ($logs['i_product_type']=='201') echo _e('Premium Ads', 'payment');
											else if ($logs['i_product_type']=='301') echo _e('Pack', 'payment');
											else if ($logs['i_product_type']=='101') echo _e('Publish Ads','payment');
											else if ($logs['i_product_type']=='501') echo _e('WALLET PAY','payment');
								 ?></td>
                            </tr>
                            <?php }?>
                            <tr>
                                <td>&#1580;&#1605;&#1593; &#1662;&#1585;&#1583;&#1575;&#1582;&#1578;&#1610; &#1607;&#1575;</td>
                                <td colspan="9" style="text-align:center;"><?php echo $amount; ?> &#1585;&#1610;&#1575;&#1604; </td>
                            </tr>
                        </tbody>
                    </table>
                    
</div>
