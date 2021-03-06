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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex"><?php __('menuCategories'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionCreate"><?php __('lblAddCategory'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblUpdateCategory'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoEditCategoryTitle', true), __('infoEditCategoryDesc', true)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionUpdate" method="post" id="frmUpdateCategory" class="form pj-form">
		<input type="hidden" name="category_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		
		<p>
			<label class="title"><?php __('lblCategory'); ?></label>
			<span class="inline_block">
				<input type="text" name="category" id="category" class="pj-form-field w300 required" value="<?php echo stripslashes($tpl['arr']['category'])?>" />
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCategories&action=pjActionIndex';" />
		</p>
		
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.same_category = "<?php echo __('lblSameCatgory', true); ?>";
	</script>
	<?php
}
?>