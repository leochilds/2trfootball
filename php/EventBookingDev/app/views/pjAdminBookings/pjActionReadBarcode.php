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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('menuBookings'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('lblAddBooking'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionReadBarcode"><?php __('lblBarcodeReader'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoReadBarcodeTitle', true), __('infoReadBarcodeBody', true)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionReadBarcode" method="post" id="frmReadBarcode" class="form pj-form">
		<input type="hidden" name="read_barcode" value="1" />
		
		<div class="barcode-container">
			<label class="block b10"><?php __('lblBarcodeDetails')?></label>
			<span class="block b10 overflow"><input type="text" name="barcode_label" id="barcode_label" value="" class="pj-form-field barcode-field required b10" /></span>
			<span class="block b10 overflow"><input type="submit" value="<?php __('btnCheck'); ?>" class="pj-button" /></span>
			<?php
			if(isset($tpl['ticket_status']))
			{
				$ticket_statuses = __('ticket_statuses', true);
				if($tpl['ticket_status'] == 1)
				{
					?>
					<label class="check-ticket-message"><?php echo $ticket_statuses[$tpl['ticket_status']]; ?></label>
					<?php
				}else{
					?>
					<label class="check-ticket-error"><?php echo $ticket_statuses[$tpl['ticket_status']]; ?></label>
					<?php
				}
			} 
			?>
		</div>
		<br/><br/>
		<?php
		if(isset($tpl['arr']))
		{
			$booking_statuses = __('booking_statuses', true);
			
			$event_date = pjUtil::getEventDateTime($tpl['arr']['event_start_ts'], $tpl['arr']['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $tpl['arr']['o_show_start_time'], $tpl['arr']['o_show_end_time']);
			
			?>
			<p>
				<label class="title"><?php echo ucfirst(__('lblTicket', true)); ?>:</label>
				<span class="block r20 overflow float_left">
					<label class="content"><?php echo stripslashes($tpl['arr']['ticket_id']) . ' / ' . $tpl['arr']['price_title']?></label>
				</span>
				<?php
				if($tpl['ticket_status'] == 1)
				{ 
					?>
					<span class="block t5 pointer overflow">
						<input type="checkbox" <?php echo $tpl['arr']['is_used'] == 'T' ? 'disabled="disabled" checked="checked"' : null; ?> class="r5 pointer" id="use_ticket" lang="<?php echo $tpl['arr']['id'];?>" /><label class="pointer" for="use_ticket"><?php echo __('lblUseTicket');?></label>
					</span>
					<?php
				} 
				?>
			</p>
			<p>
				<label class="title"><?php __('lblEvent'); ?>:</label>
				<span class="inline_block">
					<label class="content"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&id=<?php echo $tpl['arr']['event_id'];?>"><?php echo stripslashes($tpl['arr']['event_title'])?></a></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblDateTime'); ?>:</label>
				<span class="inline_block">
					<label class="content"><?php echo $event_date; ?></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblStatus'); ?>:</label>
				<span class="inline_block">
					<label class="content"><?php echo $booking_statuses[$tpl['arr']['booking_status']]; ?></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblName'); ?>:</label>
				<span class="inline_block">
					<label class="content"><?php echo stripslashes($tpl['arr']['customer_name']); ?></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblBookingEmail'); ?>:</label>
				<span class="inline_block">
					<label class="content"><?php echo stripslashes($tpl['arr']['customer_email']); ?></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblBookingPhone'); ?>:</label>
				<span class="inline_block">
					<label class="content"><?php echo stripslashes($tpl['arr']['customer_phone']); ?></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblBookingTickets'); ?>:</label>
				<span class="inline_block">
					<label class="content overflow">
						<?php
						foreach($tpl['details_arr'] as $v)
						{
							echo $v['cnt'] . ' x ' . $v['price_title'] . '&nbsp;&nbsp;' . pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . '<br/>';
						} 
						?>
					</label>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<label class="content"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&id=<?php echo $tpl['arr']['booking_id'];?>"><?php __('lblEditBooking'); ?></a></label>
				</span>
			</p>
			<?php
		}
		?>
	</form>
	<div id="dialogTicketConfirmation" title="<?php __('lblTicketConfirmationTitle'); ?>" style="display:none">
		<p><?php __('lblTicketConfirmationBody'); ?></p>
	</div>
	<?php
}
?>