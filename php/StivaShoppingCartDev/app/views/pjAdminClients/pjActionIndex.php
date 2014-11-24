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
	$u_statarr = __('u_statarr', true);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex"><?php __('menuClients'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionCreate"><?php __('client_create'); ?></a></li>
		</ul>
	</div>
	
	<div class="b10">
		<form action="" method="get" class="pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('client_client_name'); ?>";
	myLabel.email = "<?php __('client_email'); ?>";
	myLabel.last_order = "<?php __('client_last_order'); ?>";
	myLabel.orders = "<?php __('client_orders'); ?>";
	myLabel.status = "<?php __('client_status'); ?>";
	myLabel.active = "<?php echo $u_statarr['T']; ?>";
	myLabel.inactive = "<?php echo $u_statarr['F']; ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('gridDeleteConfirmation'); ?>";
	</script>
	<?php
}
?>