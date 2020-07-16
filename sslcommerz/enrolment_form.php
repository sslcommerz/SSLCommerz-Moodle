<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Authorize.net enrolment plugin - enrolment form.
 *
 * @package    enrol_sslcommerz
 * @copyright  2015 Dualcube, Moumita Ray, Parthajeet Chakraborty
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$loginid = $this->get_config('loginid');
$transactionkey = $this->get_config('transactionkey');
$clientkey = $this->get_config('clientkey');
$store_id = $this->get_config('store_id');
$store_pass = $this->get_config('store_pass');
$enrolperiod = $this->get_config('enrolperiod');

$auth_modess = $this->get_config('checkproductionmode');

if ($auth_modess == 1)
{
    $api_env = "yes";
}
elseif ($auth_modess == 0)
{
    $api_env = "no";
}

$amount = $cost;
$description = $coursefullname;

$invoice = date('YmdHis');
$_SESSION['sequence'] = $sequence = rand(1, 1000);
$_SESSION['timestamp'] = $timestamp = time();

?>
<!-- Load the jQuery library from the Google CDN -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js">
</script>
<!-- Load the Accept.js CDN -->
<script type="text/javascript"
    src="<?php echo $s_path; ?>"
    charset="utf-8">
</script>

<div align="center">
<p>This course requires a payment for entry.</p>
<p><b><?php echo $instancename; ?></b></p>
<p><b><?php echo get_string("cost") . ": {$instance->currency} {$localisedcost}"; ?></b></p>

<p>&nbsp;</p>
<p><img alt="SSLCommerz" src="<?php echo $CFG->wwwroot; ?>/enrol/sslcommerz/pix/ssltitle.png" /></p>
<p>&nbsp;</p>

<div class="popup">
<div class="popuptext" id="myPopup">
    <form id="paymentForm"
        method="POST"
        action="<?php echo $CFG->wwwroot; ?>/enrol/sslcommerz/pay_process.php"
    >
	    <input type="hidden" name="dataValue" id="dataValue" />
        <input type="hidden" name="dataDesc" id="dataDescriptor" />

		<input type="hidden" name="amount" value="<?php echo $amount; ?>" />

		<input type="hidden" name="x_currency_code" value="<?php echo $instance->currency; ?>" />

        <input type="hidden" name="loginkey" value="<?php echo $loginid; ?>" />
        <input type="hidden" name="transactionkey" value="<?php echo $transactionkey; ?>" />
        <input type="hidden" name="clientkey" value="<?php echo $clientkey; ?>" />
        
		<input type="hidden" name="x_cust_id" value="<?php echo $instance->courseid . '-' . $USER->id . '-' . $instance->id . '-' . $context->id; ?>">
		<input type="hidden" name="x_description" value="<?php echo $description; ?>" />
		<input type="hidden" name="x_invoice_num" value="<?php echo $invoice; ?>" />
		<input type="hidden" name="x_fp_sequence" value="<?php echo $sequence; ?>" />
		<input type="hidden" name="x_fp_timestamp" value="<?php echo $timestamp; ?>" />
		<input type="hidden" name="x_email_customer" value="true" >
		
		<input type="hidden" name="store_id" value="<?php echo $store_id; ?>" >
		<input type="hidden" name="store_password" value="<?php echo $store_pass; ?>" >
		<input type="hidden" name="is_live" value="<?php echo $api_env; ?>" >
		<input type="hidden" name="enrolperiod" value="<?php echo $enrolperiod; ?>" >
		
		<input type="checkbox" name="terms" required> <label for="termchk">By clicking Proceed, you agreed to our <a href="#" target="new">Terms &amp; Condition</a></label><br>

		 <input type="submit" id="pay_btns" name="pay" value="Proceed to Pay">
	</form>
</div>
</div>

<p>
	
</p>
</div>
