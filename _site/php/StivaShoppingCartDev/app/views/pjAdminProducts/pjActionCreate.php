<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 1:
			pjUtil::printNotice($status[1]);
			break;
		case 2:
			pjUtil::printNotice($status[2]);
			break;
	}
} else {
	$info = __('info', true);
	
	?>
	<style type="text/css">
	.mce-tinymce{
		float: left;
	}
	</style>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts"><?php __('product_list'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate"><?php __('product_create'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionStock"><?php __('product_stock_tab'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate" method="post" id="frmCreateProduct" class="form pj-form frmProduct" enctype="multipart/form-data">
		<input type="hidden" name="product_create" value="1" />
	    
		<?php pjUtil::printNotice($info['product_details_add_title'], $info['product_details_add_body']); ?>
		<div class="multilang"></div>
		
		<p><label class="title"><?php __('product_status'); ?></label><select name="status" id="status" class="pj-form-field w200">
			<?php
			foreach (__('product_statuses', true) as $k => $v)
			{
				?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
			}
			?>
		</select></p>
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('product_name'); ?></label>
				<span class="inline_block">
					<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
				</span>
			</p>
			<?php
		}
		?>
		<p><label class="title"><?php __('product_category'); ?></label>
			<select name="category_id[]" id="category_id" class="pj-form-field" multiple="multiple" size="5">
			<?php
			foreach ($tpl['category_arr'] as $category)
			{
				?><option value="<?php echo $category['data']['id']; ?>"><?php echo str_repeat("-----", $category['deep']) . " " .pjSanitize::html($category['data']['name']); ?></option><?php
			}
			?>
			</select>
		</p>
		<p>
			<label class="title"><?php __('product_sku'); ?></label>
			<span class="inline_block"><input type="text" name="sku" id="sku" class="pj-form-field w300" /></span>
		</p>
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('product_short_desc'); ?></label>
				<span class="inline_block">
					<textarea name="i18n[<?php echo $v['id']; ?>][short_desc]" class="pj-form-field w500 h100<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"></textarea>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
				</span>
			</p>
			<?php
		}
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('product_full_desc'); ?></label>
				<span class="block overflow">
					<textarea name="i18n[<?php echo $v['id']; ?>][full_desc]" class="pj-form-field w500 h200 selector-full-desc<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"></textarea>
					<span class="pj-multilang-input float_right"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<br class="clear_both" />
				</span>
			</p>
			<?php
		}
		?>
		<p><label class="title"><?php __('product_is_featured'); ?></label><span class="left"><input type="checkbox" name="is_featured" id="is_featured" value="1" /></span></p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	</form>
	
	<script type="text/javascript">
	var pjLocale = pjLocale || {};
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
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