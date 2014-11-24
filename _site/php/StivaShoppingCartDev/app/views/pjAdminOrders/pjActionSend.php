<form action="" method="post" class="form pj-form">
	<input type="hidden" name="form_send" value="1" />
	<input type="hidden" name="to" value="<?php echo @$tpl['arr']['to']; ?>" />
	<input type="hidden" name="from" value="<?php echo @$tpl['arr']['from']; ?>" />
	<p><label><?php __('order_send_subject'); ?></label></p>
	<p><input type="text" name="subject" class="pj-form-field w550 required" value="<?php echo pjSanitize::html(@$tpl['arr']['subject']); ?>" /></p>
	<p><label><?php __('order_send_body'); ?></label></p>
	<p><textarea name="body" class="pj-form-field w550 h300 required"><?php echo pjSanitize::html(@$tpl['arr']['body']); ?></textarea></p>
</form>