<?php
$conn   = getConnection();
$payment_log = $conn->osc_dbFetchResults("SELECT * FROM %st_payments_log WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, osc_logged_user_id());
$wallet = $conn->osc_dbFetchresult("SELECT * FROM %st_payments_wallet WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, osc_logged_user_id());
$amount = isset($wallet['i_amount'])?$wallet['i_amount']:0;
?>

            <div class="content user_account">
                <h1>
                    <span class="price">موجودي حساب شما : <?PHP echo $amount; ?> ريال</span>
                    <strong><?php _e('User Pay Log', 'payment') ; ?></strong>
                </h1>
                    <table border="0" width="100%">
                        <thead>
                            <tr bgcolor="#61707B" style="color: #ffffff; font-size: 10px;">
                                <th align="center" style="width: 230px;"><?php _e('ID', 'payment'); ?></th>
                                <th align="center" style="width: 230px;"><?php _e('Date', 'payment'); ?></th>
                                <th align="center" style="width: 230px;"><?php _e('Code', 'payment'); ?></th>
                                <th align="center" style="width: 230px;"><?php _e('Amount', 'payment'); ?></th>
                                <th align="center" style="width: 230px;"><?php _e('Email', 'payment'); ?></th>
                                <th align="center" style="width: 230px;"><?php _e('ItemID', 'payment'); ?></th>
                                <th align="center" style="width: 230px;"><?php _e('Source', 'payment'); ?></th>
                                <th align="center" style="width: 230px;"><?php _e('Product type', 'payment'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $odd = 1;
                                foreach($payment_log as $kye=>$logs) {
                                  $kye++;
                                    if($odd==1) {
                                        $odd_even = "odd";
                                        $odd = 0;
                                    } else {
                                        $odd_even = "even";
                                        $odd = 1;
                                    }
                            ?>

                            <tr bgcolor="#ededf2" onmouseout="this.style.backgroundColor='#ededf2'; this.style.color='#292929';" onmouseover="this.style.backgroundColor='#464545'; this.style.color='#ffffff';" style=" font-size: 10px;background-color: rgb(237, 237, 242); color: rgb(41, 41, 41);">
                            	<td align="center"><?php echo $kye ?></td>
                            	<td align="center"><?php echo osc_format_date($logs['dt_date'],"l j F Y h:i:s A"); ?></td>
                            	<td align="center"><?php echo $logs['s_code']; ?></td>
                                <td align="center"><?php echo $logs['i_amount']; echo $logs['s_currency_code']; ?></td>
                            	<td align="center"><?php echo $logs['s_email']; ?></td>
                            	<td align="center"><?php echo $logs['fk_i_item_id']; ?></td>
                            	<td align="center"><?php if ($logs['s_source']=='NEXTPAY') echo "نکست پی";
							  else echo$logs['s_source']ک
								 ?></td>
                            	<td align="center"><?php if ($logs['i_product_type']=='201') echo _e('Premium Ads', 'payment');
											else if ($logs['i_product_type']=='301') echo _e('Pack', 'payment');
											else if ($logs['i_product_type']=='101') echo _e('Publish Ads','payment');
											else if ($logs['i_product_type']=='501') echo _e('WALLET PAY','payment');
								 ?></td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
            </div>