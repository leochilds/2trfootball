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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex"><?php __('menuClients'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionCreate"><?php __('client_create'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionCreate" method="post" class="pj-form form" id="frmCreateClient">
		<input type="hidden" name="create_form" value="1" />
		<fieldset class="fieldset white">
			<legend><?php __('client_general'); ?></legend>
			<p>
				<label class="title"><?php __('client_email'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
					<input type="text" name="email" id="email" class="pj-form-field email w200" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('client_password'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
					<input type="text" name="password" id="password" class="pj-form-field w200" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('client_client_name'); ?></label>
				<span class="inline_block">
					<input type="text" name="client_name" id="client_name" class="pj-form-field w200" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('client_phone'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
					<input type="text" name="phone" id="phone" class="pj-form-field w200" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('client_url'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-url"></abbr></span>
					<input type="text" name="url" id="url" class="pj-form-field w350" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('client_status'); ?></label>
				<span class="inline_block">
					<select name="status" id="status" class="pj-form-field required">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach (__('u_statarr', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			</p>
		</fieldset>
		<?php pjUtil::printNotice(@$titles['AC10'], @$bodies['AC10']); ?>
		<fieldset class="fieldset white">
			<legend><?php __('client_address_book'); ?></legend>
			<p style="padding-top: 10px !important">
				<label class="title">&nbsp;</label>
				<a href="#" class="pj-button btnAddAddress"><?php __('client_add_address'); ?></a>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			</p>
		</fieldset>
	</form>
	<div id="boxCloneAddress" style="display: none"><?php include dirname(__FILE__) . '/elements/address.php'; ?></div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.email_taken = "<?php __('vr_email_taken'); ?>";
	</script>
	<?php
}
?>