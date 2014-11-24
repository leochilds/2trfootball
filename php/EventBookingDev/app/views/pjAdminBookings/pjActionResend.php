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
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend&id=<?php echo $_GET['id'];?>"><?php __('lblResendConfirmation'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoResendTitle', true), __('infoResendBody', true)); 
	?>
	
	<input type="button" value="<?php __('btnBack'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionUpdate&id=<?php echo $_GET['id'];?>';" />
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend" method="post" id="frmResendConfirm" class="form pj-form">
		<input type="hidden" name="resend_email" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['booking_arr']['id'];?>" />
		<input type="hidden" name="to" value="<?php echo $tpl['booking_arr']['customer_email'];?>" />
		<fieldset class="overflow b20 t20">
			<legend><?php __('lblConfirmationEmail'); ?></legend>
			<p>
				<label class="title130"><?php __('lblSubject'); ?></label>
				<span class="inline_block">
					<input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars(stripslashes($tpl['event_arr']['o_email_confirmation_subject'])); ?>" class="pj-form-field w400 required" />
				</span>
			</p>
			<p>
				<label class="title130"><?php __('lblMessage'); ?></label>
				<span class="inline_block">
					<textarea name="message" class="pj-form-field" style="width: 500px; height: 300px;"><?php echo htmlspecialchars(stripslashes($tpl['event_arr']['o_email_confirmation'])); ?></textarea>
				</span>
			</p>
			<p>
				<label class="title130">&nbsp;</label>
				<input type="submit" value="<?php __('btnSend'); ?>" class="pj-button" />
			</p>
		</fieldset>
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend" method="post" id="frmResendPayment" class="form pj-form">
		<input type="hidden" name="resend_email" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['booking_arr']['id'];?>" />
		<input type="hidden" name="to" value="<?php echo $tpl['booking_arr']['customer_email'];?>" />
		<fieldset class="overflow b10">
			<legend><?php __('lblPaymentEmail'); ?></legend>
			<p>
				<label class="title130"><?php __('lblSubject'); ?></label>
				<span class="inline_block">
					<input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars(stripslashes($tpl['event_arr']['o_email_payment_subject'])); ?>" class="pj-form-field w400 required" />
				</span>
			</p>
			<p>
				<label class="title130"><?php __('lblMessage'); ?></label>
				<span class="inline_block">
					<textarea name="message" class="pj-form-field" style="width: 500px; height: 300px;"><?php echo htmlspecialchars(stripslashes($tpl['event_arr']['o_email_payment'])); ?></textarea>
				</span>
			</p>
			<p>
				<label class="title130">&nbsp;</label>
				<input type="submit" value="<?php __('btnSend'); ?>" class="pj-button" />
			</p>
		</fieldset>
	</form>
	<?php
}
?>