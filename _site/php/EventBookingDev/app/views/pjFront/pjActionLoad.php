<?php
$default_view = 'monthly';
$show_header = 1;
$show_icons = 1;
$show_cats = 1;
if($tpl['option_arr']['o_enable_monthly_view'] == 'No' && $tpl['option_arr']['o_enable_list_view'] == 'No')
{
	$show_header = 0;
	$default_view = 'calendar';
}
if(isset($_GET['view'])){
	$default_view = $_GET['view'];
}
if(isset($_GET['icons'])){
	if($_GET['icons'] == 'T')
	{
		$show_icons = 0;
	}else{
		$show_icons = 1;
	}
}
if(isset($_GET['cats'])){
	if($_GET['cats'] == 'T')
	{
		$show_cats = 0;
	}else{
		$show_cats = 1;
	}
}
if($show_icons == 0 && $show_cats == 0)
{
	$show_header = 0;
}

mt_srand();
$index = mt_rand(1, 9999);

switch ($_GET['layout']) {
	case 'layout_1':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/index.php';
		break;
	case 'layout_2':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_2/index.php';
		break;
	case 'layout_3':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_3/index.php';
		break;
	case 'layout_4':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_4/index.php';
		break;
	case 'layout_5':
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_5/index.php';
		break;
	default:
		include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/index.php';
		break;
}
$font_messages = __('front_message', true);
$font_errors  = __('front_error', true);
?>

<script type="text/javascript">
var phpevtcalOpj_<?php echo $index; ?> = new PhpEvtCal({
	index: "<?php echo $index; ?>",
	layout: "<?php echo $_GET['layout']; ?>",
	category_id: "<?php echo isset($_GET['cid']) ? $_GET['cid'] : 0; ?>",
	default_view: "<?php echo $default_view; ?>",
	enable_categories: "<?php echo $show_cats == 1 ? 'Yes' : 'No'; ?>",
	enable_monthly_view: "<?php echo $tpl['option_arr']['o_enable_monthly_view']; ?>",
	enable_list_view: "<?php echo $tpl['option_arr']['o_enable_list_view']; ?>",
	event_title_position: "<?php echo $tpl['option_arr']['o_event_title_position'];?>",
	show_header: "<?php echo $show_header;?>",
	display_events: "<?php echo $tpl['option_arr']['o_display_events'];?>",
	currency: "<?php echo $tpl['option_arr']['o_currency'];?>",
	tax: "<?php echo $tpl['option_arr']['o_tax_payment'];?>",
	deposit: "<?php echo $tpl['option_arr']['o_deposit_payment'];?>",
	one_event: "<?php echo isset($_GET['event_id']) ? $_GET['event_id'] : 0; ?>",
			
	container_event_detail: "phpevtcal_event_detail_<?php echo $index; ?>",
	container_price: "ebcal_price_box_<?php echo $index; ?>",
	container_tax: "ebcal_tax_box_<?php echo $index; ?>",
	container_deposit: "ebcal_deposit_box_<?php echo $index; ?>",
	container_total_amount: "ebcal_total_amount_box_<?php echo $index; ?>",
	container_message: "ebcal-message-container_<?php echo $index; ?>",

	detail_form_name: "ebcal_detail_form",
	booking_form_name: "ebcal_booking_form",
	booking_form_submit_name: "ebcal_booking_form_submit",
	booking_form_cancel_name: "ebcal_booking_form_cancel",
	booking_form_payment_method: "payment_method",

	booking_summary_name: "ebcal_booking_summary",
	booking_summary_submit_name: "ebcal_booking_summary_submit",
	booking_summary_cancel_name: "ebcal_booking_summary_cancel",
	
	class_close_form: "ebcal-close-form",
	class_name_price: "ebcal-price",
	
	load_events_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionLoadEvents&index=<?php echo $index; ?>",
	load_event_detail_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionLoadEventDetail&index=<?php echo $index; ?>",
	load_view_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionView&index=<?php echo $index; ?>",
	load_booking_form_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionLoadBookingForm&index=<?php echo $index; ?>",
	check_captcha_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionCheckCaptcha",
	load_booking_summary_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionLoadBookingSummary&index=<?php echo $index; ?>",
	load_booking_save_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionBookingSave",
	get_payment_form_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionGetPaymentForm",
	thankyou_url: "<?php echo $tpl['option_arr']['o_thankyou_page']; ?>",

	message_1: "<?php echo $font_messages[1]; ?>",
	message_2: "<?php echo $font_messages[2]; ?>",
	message_3: "<?php echo $font_messages[3]; ?>",
	message_4: "<?php echo $font_messages[4]; ?>",
	message_5: "<?php echo $font_messages[5]; ?>",
	message_6: "<?php echo $font_messages[6]; ?>",
	message_7: "<?php echo $font_messages[7]; ?>",
	message_9: "<?php echo $font_messages[9]; ?>",

	validation: {
		error_title: "<?php echo $font_errors['title']; ?>",
		error_email: "<?php echo $font_errors['email']; ?>",
		error_captcha: "<?php echo $font_errors['captcha']; ?>",
		error_payment: "<?php echo $font_errors['payment']; ?>",
		error_max: "<?php echo $font_errors['max']; ?>",
		error_min: "<?php echo $font_errors['min']; ?>"
	},
	payment: {
		paypal: "ebcal_paypal_form",
		authorize: "ebcal_authorize_form"
	},
	
	cc_data_wrapper: "ebcal-ccdata",
	cc_data_names: ["cc_type", "cc_num", "cc_exp_month", "cc_exp_year", "cc_code"],
	cc_data_flag: true,

	bank_data_wrapper: "ebcal-bankdata",
	
	current_month: "<?php echo date('m');?>",
	current_year: "<?php echo date('Y');?>"
});
</script>