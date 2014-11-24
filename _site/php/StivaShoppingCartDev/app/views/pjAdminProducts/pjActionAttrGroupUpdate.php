<div class="multilang b20"></div>

<form action="" method="post" class="clear_both">
	<input type="hidden" name="attr_group_update" value="1" />
	<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>
		<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
			<label class="title"><?php __('product_attr_group_name'); ?></label>
			<span class="inline_block">
				<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['name']); ?>" />
				<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
			</span>
		</p>
		<?php
	}
	?>
</form>