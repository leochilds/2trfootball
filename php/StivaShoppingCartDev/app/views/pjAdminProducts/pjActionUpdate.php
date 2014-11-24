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
		case 9:
			pjUtil::printNotice($status[9]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$errors = __('errors', true);
		$titles = __('titles', true);
		pjUtil::printNotice(@$errors[$_GET['err']], @$titles[$_GET['err']]);
	}
	$info = __('info', true);
	?>
    <style type="text/css">
    .mce-tinymce{
		float: left;
	}
	.pj-status{
		width: 83px !important;
	}
	.pj-status-1{
		background-position: 70px 3px !important;
	}
	.extraBox .pj-multilang-input img {
		vertical-align: baseline;
	}
	</style>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionUpdate" method="post" id="frmUpdateProduct" class="form pj-form frmProduct" enctype="multipart/form-data">
		<input type="hidden" name="product_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<input type="hidden" name="tab" value="<?php echo isset($_GET['tab']) ? (int) $_GET['tab'] : 0; ?>" />
	    
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('product_details'); ?></a></li>
				<li><a href="#tabs-2"><?php __('product_digital'); ?></a></li>
				<li><a href="#tabs-3"><?php __('product_attr'); ?></a></li>
				<li><a href="#tabs-4"><?php __('product_photos'); ?></a></li>
				<li><a href="#tabs-5"><?php __('product_stock'); ?></a></li>
				<li><a href="#tabs-6"><?php __('product_extras'); ?></a></li>
				<li><a href="#tabs-7"><?php __('product_similar'); ?></a></li>
				<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionGetHistory&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('product_history'); ?></a></li>
			</ul>
			<div id="tabs-1">
				<?php pjUtil::printNotice($info['product_details_title'], $info['product_details_body']); ?>
				<div class="multilang"></div>
				
				<p><label class="title"><?php __('product_status'); ?></label><select name="status" id="status" class="pj-form-field w200">
					<?php
					foreach (__('product_statuses', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['status'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
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
							<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['name']); ?>" />
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
						?><option value="<?php echo $category['data']['id']; ?>"<?php echo in_array($category['data']['id'], $tpl['pc_arr']) ? ' selected="selected"' : NULL; ?>><?php echo str_repeat("-----", $category['deep']) . " " . pjSanitize::html($category['data']['name']); ?></option><?php
					}
					?>
					</select>
				</p>
				<p>
					<label class="title"><?php __('product_sku'); ?></label>
					<span class="inline_block"><input type="text" name="sku" id="sku" class="pj-form-field w300" value="<?php echo pjSanitize::html($tpl['arr']['sku']); ?>" /></span>
				</p>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('product_short_desc'); ?></label>
						<span class="inline_block">
							<textarea name="i18n[<?php echo $v['id']; ?>][short_desc]" class="pj-form-field w500 h100<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"><?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['short_desc']); ?></textarea>
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
							<textarea name="i18n[<?php echo $v['id']; ?>][full_desc]" class="pj-form-field w500 h200 selector-full-desc<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"><?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['full_desc']); ?></textarea>
							<span class="pj-multilang-input float_right"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							<br class="clear_both" />
						</span>
					</p>
					<?php
				}
				?>
				<p><label class="title"><?php __('product_is_featured'); ?></label><span class="left"><input type="checkbox" name="is_featured" id="is_featured" value="1"<?php echo (int) $tpl['arr']['is_featured'] === 1 ? ' checked="checked"' : NULL; ?> /></span></p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				</p>
			</div>
			<div id="tabs-2">
				<?php pjUtil::printNotice($info['product_digital_title'], $info['product_digital_body']); ?>
				<p><label class="title"><?php __('product_is_digital'); ?></label><span class="left"><input type="checkbox" name="is_digital" id="is_digital" value="1"<?php echo (int) $tpl['arr']['is_digital'] === 1 ? ' checked="checked"' : NULL; ?> /></span></p>
				<div id="boxDigital">
					<?php
					if (!empty($tpl['arr']['digital_file']))
					{
						?>
						<p>
							<label class="title"><?php __('product_file'); ?></label>
							<a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&amp;action=pjActionOpenDigital&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php echo pjSanitize::html($tpl['arr']['digital_name']); ?></a>
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete align_middle btnDigitalDelete" rel="<?php echo $tpl['arr']['id']; ?>"></a>
						</p>
						<?php
					} else {
						?>
						<p>
							<label class="title">&nbsp;</label>
							<label class="r5"><input type="radio" name="digital_choose" value="1" checked="checked" /> <?php __('product_file_1'); ?></label>
							<label><input type="radio" name="digital_choose" value="2" /> <?php __('product_file_2'); ?></label>
						</p>
						<p class="digitalFile"><label class="title"><?php __('product_file_1'); ?></label><input type="file" name="digital_file" /></p>
						<p class="digitalPath" style="display: none"><label class="title"><?php __('product_file_2'); ?></label><input type="text" name="digital_file" class="pj-form-field w300" maxlength="255" /></p>
						<?php
					}
					?>
				</div>
				<p><label class="title"><?php __('product_digital_expire'); ?></label>
					<?php
					$h = $m = NULL;
					if (!empty($tpl['arr']['digital_expire']))
					{
						list($h, $m,) = explode(":", $tpl['arr']['digital_expire']);
					}
					?>
					<?php echo pjTime::factory()->prop('selected', $h)->attr('name', 'hour')->attr('id', 'hour')->attr('class', 'pj-form-field')->hour(); ?>
					<?php echo pjTime::factory()->prop('selected', $m)->attr('name', 'minute')->attr('id', 'minute')->attr('class', 'pj-form-field')->prop('step', 5)->minute(); ?>
					HH:MM
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				</p>
			</div>
			<div id="tabs-3">
				<?php pjUtil::printNotice($info['product_attr_title'], $info['product_attr_body']); ?>
				<div class="multilang"></div>
				<?php include_once dirname(__FILE__) . '/elements/attributes.php'; ?>
			</div>
			<div id="tabs-4">
				<?php pjUtil::printNotice($info['product_photos_title'], $info['product_photos_body']); ?>
				<div id="gallery"></div>
			</div>
			<div id="tabs-5">
			<?php pjUtil::printNotice($info['product_stock_title'], $info['product_stock_body']); ?>
			<?php include_once dirname(__FILE__) . '/elements/stocks.php'; ?>
			</div>
			<div id="tabs-6">
				<?php pjUtil::printNotice($info['product_extras_title'], $info['product_extras_body']); ?>
				<div class="multilang"></div>
				<?php include_once dirname(__FILE__) . '/elements/extras.php'; ?>
			</div>
			<div id="tabs-7">
				<?php pjUtil::printNotice($info['product_similar_title'], $info['product_similar_body']); ?>
				<p>
					<input type="text" name="similar_id" id="similar_id" class="pj-form-field w400" placeholder="<?php __('btnSearch'); ?>" />
				</p>
				<div id="boxSimilar"></div>
			</div>
		</div>
	</form>
	
	<div id="dialogDeleteDigital" style="display: none" title="<?php __('product_digital_delete_title'); ?>">
	<?php __('product_digital_delete_desc'); ?>
	</div>
	<?php
	include_once dirname(__FILE__) . '/elements/attributes_other.php';
	include_once dirname(__FILE__) . '/elements/stocks_other.php';
	include_once dirname(__FILE__) . '/elements/extras_other.php';
	?>
	<script type="text/javascript">
	var myGallery = myGallery || {};
	myGallery.foreign_id = "<?php echo $tpl['arr']['id']; ?>";
	myGallery.hash = "";
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

	var pjGrid = pjGrid || {};
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.sku = "<?php __('product_sku'); ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	</script>
	<?php
}
?>