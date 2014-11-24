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
	<?php include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php'; ?>
    
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex"><?php __('menuCategories'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionCreate"><?php __('category_create'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('category_update'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(@$titles['AC13'], @$bodies['AC13']);
	?>
	
	<div class="multilang"></div>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>" method="post" id="frmUpdateCategory" class="pj-form form">
		<input type="hidden" name="category_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
			?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('category_name'); ?>:</label>
				<span class="inline_block">
					<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['name']); ?>" />
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
				</span>
			</p>
			<?php
		}
		$child_ids = array();
		foreach ($tpl['child_arr'] as $child)
		{
			$child_ids[] = $child['data']['id'];
		}
		?>
		<p><label class="title"><?php __('category_parent'); ?>:</label>
			<select name="parent_id" id="parent_id" class="pj-form-field">
				<option value="1"><?php __('category_no_parent'); ?></option>
				<?php
				foreach ($tpl['node_arr'] as $node)
				{
					$disabled = NULL;
					if ($node['data']['id'] == $tpl['arr']['id'] || in_array($node['data']['id'], $child_ids))
					{
						$disabled = ' disabled="disabled"';
					}
					?><option value="<?php echo $node['data']['id']; ?>"<?php echo $disabled; ?><?php echo $tpl['arr']['parent_id'] == $node['data']['id'] ? ' selected="selected"' : NULL; ?>><?php echo str_repeat('------', $node['deep']) . " " . $node['data']['name']; ?></option><?php
				}
				?>
			</select>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	</form>
	
	<script type="text/javascript">
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					// Callback, e.g. ajax requests or whatever
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>