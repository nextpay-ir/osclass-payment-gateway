<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    class Nextpay
    {

        public function __construct()
        {
        }

        public static function button($amount = '0.00', $description = '', $itemnumber = '101', $extra_array = null) {
            $extra = payment_prepare_custom($extra_array);
            $extra .= 'concept,'.$description.'|';
            $extra .= 'product,'.$itemnumber.'|';
            $extra .= 'amount,'.$amount;
            $r = rand(0,1000);
            echo '<li class="payment nextpay-btn">';

            $action = payment_url().'payments/nextpay/send.php';
        ?>
            <form action="<?php echo $action; ?>" method="post" id="nextpay_<?php echo $r; ?>">
              <input type="hidden" name="amount" value="<?php echo $amount; ?>" />
              <input type="hidden" name="custom" value="<?php echo $extra; ?>" />
            </form>
            <div class="buttons">
              <div class="right"><a id="button-confirm" class="button" onclick="$('#nextpay_<?php echo $r; ?>').submit();"><span><img src='<?php echo payment_url(); ?>payments/nextpay/images/nextpaybtn.png' border='0' /></span></a></div>
            </div>
        <?php


            echo '</li>';
        }

    }
?>