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
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionIndex"><?php __('menuEvents'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionCreate"><?php __('lblAddEvent'); ?></a></li>
		</ul>
	</div>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<?php
		$filter = __('filter', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all">All</a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="T"><?php echo $filter['active']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="F"><?php echo $filter['inactive']; ?></a>
		</div>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";

	var myLabel = myLabel || {};
	myLabel.eventdate = "<?php __('lblEventDate'); ?>";
	myLabel.eventtime = "<?php __('lblEventTime'); ?>";
	myLabel.eventtitle = "<?php __('lblEventTitle'); ?>";
	myLabel.bookings = "<?php __('lblEventBookings'); ?>";
	myLabel.tickets = "<?php __('lblEventTickets'); ?>";
	myLabel.copy = "<?php __('lblCopyEvent'); ?>";
	myLabel.revert_status = "<?php __('revert_status'); ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.active = "<?php __('ebc_active'); ?>";
	myLabel.inactive = "<?php __('ebc_inactive'); ?>";
	myLabel.delete_selected = "<?php __('ebc_delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('ebc_delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	</script>
	<?php
}
?>