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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('menuBookings'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('lblAddBooking'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionReadBarcode"><?php __('lblBarcodeReader'); ?></a></li>
		</ul>
	</div>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		<?php
		$bs = __('booking_statuses', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all">All</a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="confirmed"><?php echo $bs['confirmed']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="pending"><?php echo $bs['pending']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="cancelled"><?php echo $bs['cancelled']; ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	<div class="pj-form-filter-advanced" style="display: none">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="overflow float_left w340">
				<p>
					<label class="title"><?php __('lblID'); ?></label>
					<input type="text" name="unique_id" id="unique_id" class="pj-form-field w150" />
				</p>
				<p>
					<label class="title"><?php __('lblBookingName'); ?></label>
					<input type="text" name="customer_name" id="customer_name" class="pj-form-field w150" />
				</p>
				<p>
					<label class="title"><?php __('lblBookingEmail'); ?></label>
					<input type="text" name="customer_email" id="customer_email" class="pj-form-field w150" />
				</p>
			</div>
			<div class="overflow">
				<p class="w340">
					<label class="title"><?php __('lblNumberOfTickets'); ?></label>
					<label class="float_left block r5 t5"><?php __('lblFrom');?></label><input type="text" name="from_ticket" id="from_ticket" class="pj-form-field w50 r10 float_left" />
					<label class="float_left block r5 t5"><?php __('lblTo');?></label><input type="text" name="to_ticket" id="to_ticket" class="pj-form-field w50 float_left" />
				</p>
				<p class="w340">
					<label class="title"><?php __('lblTotalPrice'); ?></label>
					<label class="float_left block r5 t5"><?php __('lblFrom');?></label><input type="text" name="from_price" id="from_price" class="pj-form-field w50 r10 float_left" />
					<label class="float_left block r5 t5"><?php __('lblTo');?></label><input type="text" name="to_price" id="to_price" class="pj-form-field w50 float_left" />
				</p>
			</div>
			<div class="overflow float_left w680">
				<p style="overflow: visible">
					<label class="title"><?php __('lblBookingEvent'); ?></label>
					<span class="inline_block">
						<select name="event_id" id="event_id" class="pj-form-field w400">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							if (isset($tpl['event_arr']) && count($tpl['event_arr']) > 0)
							{
								foreach ($tpl['event_arr'] as $v)
								{
									$event_title = 	$v['event_title'] . ' | ' . pjUtil::getEventDateTime($v['event_start_ts'], $v['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $v['o_show_start_time'], $v['o_show_end_time']);
									
									?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($event_title); ?></option><?php
									
								}
							}
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
					<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
				</p>
			</div>
		</form>
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['event_id']) && (int) $_GET['event_id'] > 0)
	{
		?>pjGrid.queryString += "&event_id=<?php echo (int) $_GET['event_id']; ?>";<?php
	}
	$statuses = __('booking_statuses', true);
	?>
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblBookingName'); ?>";
	myLabel.eventdate = "<?php __('lblEventDate'); ?>";
	myLabel.tickets = "<?php __('lblBookingTickets'); ?>";
	myLabel.price = "<?php __('lblBookingPrice'); ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.delete_selected = "<?php __('ebc_delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('ebc_delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.resend = "<?php __('lblResendConfirmation'); ?>";
	myLabel.print_tickets = "<?php __('lblPrintTickets');?>";
	myLabel.ticket_url = "<?php echo PJ_INSTALL_URL . PJ_UPLOAD_PATH . '/tickets/pdfs/p_';?>";
	myLabel.pending = "<?php echo $statuses['pending']; ?>";
	myLabel.confirmed = "<?php echo $statuses['confirmed']; ?>";
	myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";
	</script>
	<?php
}
?>