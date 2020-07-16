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
 * Listens for Instant Payment Notification from Authorize.net
 *
 * This script waits for Payment notification from Authorize.net,
 * then it sets up the enrolment for that user.
 *
 * @package    enrol_sslcommerz
 * @copyright  2015 Dualcube, Moumita Ray, Parthajeet Chakraborty
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

require ("../../config.php");
require_once ("lib.php");
require_once ("api/Sslcommerz.php");
//require_once($CFG->libdir.'/eventslib.php');
require_once ($CFG->libdir . '/enrollib.php');
require_once ($CFG->libdir . '/filelib.php');
// echo "<pre>"; print_r($_POST); die;
//require_once($CFG->dirroot . '/admin/environment.php');
use Sslcommerz\API\Sslcommerz_API;

require_login();

global $DB, $CFG;

if (empty($_POST) or !empty($_GET))
{
    print_error("Sorry, you can not use the script that way.");
    die;
}
$PAGE->set_pagelayout('admin');
$PAGE->set_url($CFG->wwwroot . '/enrol/sslcommerz/pay_process.php');
echo $OUTPUT->header();
echo $OUTPUT->heading("Your Payment is in Process....");
echo $OUTPUT->heading("Don't Reload or Leave This Page. This Page Will Automatically Redirect You To SSLCommerz Payment Page. ");
echo $OUTPUT->footer();

$store_id = $_POST['store_id'];
$store_password = $_POST['store_password'];
$is_live = $_POST['is_live'];
$enrolperiod = $_POST['enrolperiod'];

$trans_id = $_POST['x_invoice_num'];

$enrolsslcommerz = new stdClass();
$postdata = array_map('utf8_encode', $_POST);
$existing_array = array(
    'transId' => $trans_id
);
$postdata = array_merge($postdata, $existing_array);

$enrolsslcommerz->auth_json = json_encode($postdata);
$enrolsslcommerz->trans_id = $trans_id;
$enrolsslcommerz->timeupdated = time();

$ret1 = $DB->insert_record("enrol_sslcommerz", $enrolsslcommerz, true);

$post_data = array();

$post_data['total_amount'] = $_POST['amount'];
$post_data['currency'] = $_POST['x_currency_code'];
$post_data['tran_id'] = $trans_id;
$post_data['success_url'] = $CFG->wwwroot . '/enrol/sslcommerz/validation.php?id=' . $ret1;
$post_data['fail_url'] = $CFG->wwwroot . '/enrol/sslcommerz/validation.php?id=' . $ret1;
$post_data['cancel_url'] = $CFG->wwwroot . '/enrol/sslcommerz/validation.php?id=' . $ret1;

$post_data['shipping_method'] = 'NO';
$post_data['num_of_item'] = '1';
$post_data['product_name'] = $_POST['x_description'];
$post_data['product_profile'] = 'general';
$post_data['product_category'] = 'course';

# CUSTOMER INFORMATION
$post_data['cus_name'] = "Test Customer";
$post_data['cus_email'] = "test@test.com";
$post_data['cus_add1'] = "Dhaka";
$post_data['cus_add2'] = "Dhaka";
$post_data['cus_city'] = "Dhaka";
$post_data['cus_state'] = "Dhaka";
$post_data['cus_postcode'] = "1000";
$post_data['cus_country'] = "Bangladesh";
$post_data['cus_phone'] = "01711111111";
$post_data['cus_fax'] = "01711111111";

# OPTIONAL PARAMETERS
$post_data['value_a'] = $_POST['x_cust_id'];
$post_data['value_b'] = $_POST['x_fp_sequence'];
$post_data['value_c'] = $enrolperiod;

Sslcommerz_API::requestToSSL($store_id, $store_password, $is_live, $post_data);
exit;

