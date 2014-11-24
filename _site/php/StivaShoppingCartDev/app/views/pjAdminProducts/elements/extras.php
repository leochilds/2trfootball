<br class="clear_right" />
<div id="boxExtras" class="b10 t10">
<?php include dirname(__FILE__) . '/extras_only.php'; ?>
</div>

<div class="h30">
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnAddExtra"><?php __('product_extra_add'); ?></a>
	<?php __('product_attr_or'); ?>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnCopyExtra"><?php __('product_extra_copy'); ?></a>
</div>
<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />