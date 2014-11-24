<?php
pjUtil::printNotice(__('plugin_gallery_resize_title', true), __('plugin_gallery_resize_body', true));
if (isset($_GET['query_string']))
{
	parse_str($_GET['query_string'], $output);
}
?>
<div id="pj-crop-control" class="overflow">
	<div class="float_left">
	<input type="button" value="<?php __('plugin_gallery_original'); ?>" class="pj-button btn-original" />
	<input type="button" value="<?php __('plugin_gallery_thumb'); ?>" class="pj-button btn-thumb" />
	<input type="button" value="<?php __('plugin_gallery_preview'); ?>" class="pj-button btn-preview" />
	<div class="t15 h25">
		<?php
		printf("%u x %u, %s", $tpl['arr']['source_width'], $tpl['arr']['source_height'], pjUtil::formatSize($tpl['arr']['source_size']));
		?> <a href="#" class="btn-recreate pj-button"><?php __('plugin_gallery_recreate'); ?></a>
	</div>
	</div>
	<form action="<?php echo PJ_INSTALL_URL; ?>index.php" method="get" class="float_right inline">
	<?php
	if (isset($output))
	{
		foreach ($output as $key => $value)
		{
			?><input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" /><?php
		}
	}
	?>
		<input type="submit" value="<?php __('plugin_gallery_btn_back'); ?>" class="pj-button" />
	</form>
</div>

<div id="pj-crop-image" class="b10 t10" style="border: solid 1px #bbb; padding: 10px; height: 555px; width: 718px; overflow: auto">
	
</div>

<form action="<?php echo PJ_INSTALL_URL; ?>" method="post" class="form pj-form" id="frmMetaInfo">
	<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
	<input type="hidden" name="src" value="" />
	<input type="hidden" name="dst" value="" />
	<input type="hidden" name="x" value="" />
	<input type="hidden" name="x2" value="" />
	<input type="hidden" name="y" value="" />
	<input type="hidden" name="y2" value="" />
	<input type="hidden" name="w" value="" />
	<input type="hidden" name="h" value="" />
	<input type="button" value="<?php __('plugin_gallery_btn_save'); ?>" class="pj-button btn-save" />
</form>

<script type="text/javascript">
var pjGallery = {
	small_path: "<?php echo $tpl['arr']['small_path']; ?>",
	medium_path: "<?php echo $tpl['arr']['medium_path']; ?>",
	large_path: "<?php echo $tpl['arr']['large_path']; ?>",
	source_path: "<?php echo $tpl['arr']['source_path']; ?>",
	source_width: <?php echo (int) $tpl['arr']['source_width']; ?>,
	source_height: <?php echo (int) $tpl['arr']['source_height']; ?>,
	source_size: <?php echo (int) $tpl['arr']['source_size']; ?>,
	small_width: <?php echo (int) $tpl['imageSizes']['small'][0]; ?>,
	small_height: <?php echo (int) $tpl['imageSizes']['small'][1]; ?>,
	medium_width: <?php echo (int) $tpl['imageSizes']['medium'][0]; ?>,
	medium_height: <?php echo (int) $tpl['imageSizes']['medium'][1]; ?>
};
</script>