<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('menuBookings'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('lblAddBooking'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionReadBarcode"><?php __('lblBarcodeReader'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('lblUpdateBooking'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate" method="post" id="frmUpdateBooking" class="form pj-form">
		<input type="hidden" name="booking_update" value="1" />
		<input type="hidden" name="unique_id" value="<?php echo $tpl['arr']['unique_id']; ?>" />
		<input type="hidden" id="booking_id" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<fieldset class="overflow b10">
			<legend><?php __('lblAdministration'); ?></legend>
			<div class="overflow float_left w350">
				<p>
					<label class="title130"><?php __('lblBookingDateTime'); ?></label>
					<span class="inline_block">
						<label class="content"><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($tpl['arr']['created'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['arr']['created'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></label>
					</span>
				</p>
				<p>
					<label class="title130"><?php __('lblIpAddress'); ?></label>
					<span class="inline_block">
						<label class="content"><?php echo !empty($tpl['arr']['customer_ip']) ? $tpl['arr']['customer_ip'] : '--';?></label>
					</span>
				</p>
				<p>
					<label class="title130"><?php __('lblBookingID'); ?></label>
					<span class="inline_block">
						<label class="content"><?php echo !empty($tpl['arr']['unique_id']) ? $tpl['arr']['unique_id'] : '--';?></label>
					</span>
				</p>
			</div>
			<div class="overflow float_left w350">
				<p>
					<label class="title"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend&id=<?php echo $tpl['arr']['id'];?>"><?php echo __('lblResendConfirmation', true); ?></a></label>
				</p>
				<p>
					<label class="title"><a target="_blank" href="<?php echo PJ_INSTALL_URL . PJ_UPLOAD_PATH . '/tickets/pdfs/p_' . $tpl['arr']['unique_id'] . '.pdf'; ?>"><?php echo __('lblPrintTickets', true); ?></a></label>
				</p>
			</div>
		</fieldset>
		<fieldset class="overflow float_left w340 r10 b10">
			<legend><?php __('lblReservationDetails'); ?></legend>
			
			<p style="overflow: visible">
				<label class="title80"><?php __('lblBookingEvent'); ?></label>
				<span class="inline_block">
					<select name="event_id" id="event_id" class="pj-form-field w250 required">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						if (isset($tpl['event_arr']) && count($tpl['event_arr']) > 0)
						{
							foreach ($tpl['event_arr'] as $v)
							{
								$event_title = 	$v['event_title'] . ' | ' . pjUtil::getEventDateTime($v['event_start_ts'], $v['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $v['o_show_start_time'], $v['o_show_end_time']);
								
								if($tpl['arr']['event_id'] == $v['id'])
								{
									?><option value="<?php echo $v['id']; ?>" selected="selected"><?php echo stripslashes($event_title); ?></option><?php
								}else{
									?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($event_title); ?></option><?php
								}
							}
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title80"><?php __('lblBookingStatus'); ?></label>
				<span class="inline_block">
					<select name="booking_status" id="booking_status" class="pj-form-field w220 required">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach (__('booking_statuses', true) as $k => $v)
						{
							if($tpl['arr']['booking_status'] == $k)
							{
								?><option value="<?php echo $k; ?>" selected="selected"><?php echo stripslashes($v); ?></option><?php
							}else{
								?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
							}
						}
						?>
					</select>
				</span>
			</p>
			<div id="price_container">
			
			</div>
			
			<p>
				<label class="title80">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			</p>
		</fieldset>
		<fieldset class="overflow w345 b10">
			<legend><?php __('lblAmount'); ?></legend>
			<p>
				<label class="title130"><?php __('lblBookingPayment'); ?></label>
				<span class="inline_block">
					<select name="payment_method" id="payment_method" class="pj-form-field w150">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach (__('payment_methods', true) as $k => $v)
						{
							if($k != 'worldpay')
							{
								if($tpl['arr']['payment_method'] == $k)
								{
									?><option value="<?php echo $k; ?>" selected="selected"><?php echo $v; ?></option><?php
								}else{
									?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
								}
							}
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title130"><?php __('lblBookingPrice'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="booking_price" id="booking_price" class="pj-form-field number w80" />
				</span>
			</p>
			<p>
				<label class="title130"><?php __('lblBookingTax'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="booking_tax" id="booking_tax" class="pj-form-field number w80" />
				</span>
			</p>
			<p>
				<label class="title130"><?php __('lblBookingTotal'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="booking_total" id="booking_total" class="pj-form-field number w80" />
				</span>
			</p>
			<p>
				<label class="title130"><?php __('lblBookingDeposit'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="booking_deposit" id="booking_deposit" class="pj-form-field number w80" />
				</span>
			</p>
			<p class="ebcCC" style="display: <?php echo isset($tpl['arr']['payment_method']) && $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
				<label class="title130"><?php __('lblBookingCCType'); ?></label>
				<span class="inline_block">
					<select name="cc_type" class="pj-form-field w200">
						<option value="">---</option>
						<?php
						foreach (__('cc_types', true) as $k => $v)
						{
							if($tpl['arr']['cc_type'] == $k)
							{
								?><option value="<?php echo $k; ?>" selected="selected"><?php echo $v; ?></option><?php
							}else{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
						}
						?>
					</select>
				</span>
			</p>
			<p class="ebcCC" style="display: <?php echo isset($tpl['arr']['payment_method']) && $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
				<label class="title130"><?php __('lblBookingCCNum'); ?></label>
				<span class="inline_block">
					<input type="text" name="cc_num" id="cc_num" value="<?php echo htmlspecialchars($tpl['arr']['cc_num'])?>" class="pj-form-field w180 digits" />
				</span>
			</p>
			<p class="ebcCC" style="display: <?php echo isset($tpl['arr']['payment_method']) && $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
				<label class="title130"><?php __('lblBookingCCCode'); ?></label>
				<span class="inline_block">
					<input type="text" name="cc_code" id="cc_code" value="<?php echo htmlspecialchars($tpl['arr']['cc_code'])?>" class="pj-form-field w180 digits" />
				</span>
			</p>
			<p class="ebcCC" style="display: <?php echo isset($tpl['arr']['payment_method']) && $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
				<label class="title130"><?php __('lblBookingCCExp'); ?></label>
				<span class="inline_block">
					<input type="text" name="cc_exp" id="cc_exp" value="<?php echo htmlspecialchars($tpl['arr']['cc_exp'])?>" class="pj-form-field w180" />
				</span>
			</p>
			<p>
				<label class="title130">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			</p>
		</fieldset>
		
		<fieldset class="overflow">
			<legend><?php __('lblClientDetails'); ?></legend>
			<p>
				<label class="title80"><?php __('lblBookingName'); ?></label>
				<span class="inline_block">
					<input type="text" name="customer_name" id="customer_name" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['customer_name'])); ?>" class="pj-form-field w200 required" />
				</span>
			</p>
			<p>
				<label class="title80"><?php __('lblBookingEmail'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
					<input type="text" name="customer_email" id="customer_email" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['customer_email'])); ?>" class="pj-form-field email w200" />
				</span>
			</p>
			<p>
				<label class="title80"><?php __('lblBookingPhone'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
					<input type="text" name="customer_phone" id="customer_phone" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['customer_phone'])); ?>" class="pj-form-field w200" />
				</span>
			</p>
			<p>
				<label class="title80"><?php __('lblBookingAddress'); ?></label>
				<span class="inline_block">
					<input type="text" name="customer_address" id="customer_address" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['customer_address'])); ?>" class="pj-form-field w300" />
				</span>
			</p>
			<p>
				<label class="title80"><?php __('lblBookingCity'); ?></label>
				<span class="inline_block">
					<input type="text" name="customer_city" id="customer_city" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['customer_city'])); ?>" class="pj-form-field w300" />
				</span>
			</p>
			
			<p>
				<label class="title80"><?php __('lblBookingState'); ?></label>
				<span class="inline_block">
					<input type="text" name="customer_state" id="customer_state" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['customer_state'])); ?>" class="pj-form-field w300" />
				</span>
			</p>
			<p>
				<label class="title80"><?php __('lblBookingCountry'); ?></label>
				<span class="inline_block">
					<select id="customer_country" name="customer_country" class="pj-form-field w400">
						<option value="">---</option>
						<?php
						foreach ($tpl['country_arr'] as $k => $v)
						{
							if($tpl['arr']['customer_country'] == $v['id'])
							{
								?><option value="<?php echo $v['id']; ?>" selected="selected"><?php echo $v['country_title']; ?></option><?php
							}else{
								?><option value="<?php echo $v['id']; ?>"><?php echo $v['country_title']; ?></option><?php
							}
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title80"><?php __('lblBookingZip'); ?></label>
				<span class="inline_block">
					<input type="text" name="customer_zip" id="customer_zip" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['customer_zip'])); ?>" class="pj-form-field w100" />
				</span>
			</p>
			<p>
				<label class="title80"><?php __('lblBookingNotes'); ?></label>
				<span class="inline_block">
					<textarea name="customer_notes" id="customer_notes" class="pj-form-field w500 h80"><?php echo htmlspecialchars(stripslashes($tpl['arr']['customer_notes'])); ?></textarea>
				</span>
			</p>
			<p>
				<label class="title80">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			</p>
		</fieldset>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.tax = <?php echo $tpl['option_arr']['o_tax_payment'] ?>;
	myLabel.deposit = <?php echo $tpl['option_arr']['o_deposit_payment'] ?>;
	myLabel.price_at_least = "<?php echo __('lblAtLeastPrice', true); ?>";
	</script>
	<?php
}
?>