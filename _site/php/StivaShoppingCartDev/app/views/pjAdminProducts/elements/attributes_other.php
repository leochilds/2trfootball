<div id="dialogCopyAttr" style="display: none" title="<?php __('product_attr_copy_title'); ?>"></div>
<div id="dialogAttrGroupDelete" style="display: none" title="<?php __('product_attr_group_delete'); ?>"><?php __('product_attr_group_delete_body'); ?></div>
<div id="dialogAttrDelete" style="display: none" title="<?php __('product_attr_erase'); ?>"><?php __('product_attr_delete_body'); ?></div>

<div id="boxAddAttr" style="display: none">
	<div class="attrBoxRowItems">
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>
		<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
			<span class="inline_block">
				<input type="text" name="i18n[<?php echo $v['id']; ?>][attr_item][{INDEX}][{X}]" class="pj-form-field w80<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
				<a href="#" class="pj-icon-delete align_top btnAttrRemove"></a>
			</span>
		</p>
		<?php
	}
	?>
	</div>
</div>

<div id="boxAddAttribute" style="display: none">
	<div class="attrBox">
		<input type="hidden" name="attr[{INDEX}]" value="1" />
		<div class="attrBoxRow">
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
			?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('product_attr_group_name'); ?></label>
				<span class="inline_block">
					<input type="text" name="i18n[<?php echo $v['id']; ?>][attr_group][{INDEX}]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
					<a href="#" class="pj-icon-delete align_top btnAttrGroupRemove"></a>
				</span>
			</p>
			<?php
		}
		?>
		</div>
		<div class="attrBoxRow">
			<label class="title"><?php __('product_attr_name'); ?></label>
			<div class="attrBoxRowStick">
				<div class="attrBoxRowItems">
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][attr_item][{INDEX}][{X}]" class="pj-form-field w80<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
							<a href="#" class="pj-icon-delete align_top btnAttrRemove"></a>
						</span>
					</p>
					<?php
				}
				?>
				</div>
			</div>
		</div>
		<div>
			<label class="title">&nbsp;</label>
			<a href="#" class="pj-button btnAddAttr" rel="{INDEX}"><?php __('product_attr_create'); ?></a>
		</div>
	</div>
</div>