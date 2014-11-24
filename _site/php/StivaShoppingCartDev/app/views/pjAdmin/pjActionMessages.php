<?php
$months = __('months', true);
$days = __('days', true);
$product_statuses = __('product_statuses', true);
?>
if (jQuery_1_8_2.datagrid !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.datagrid.messages, {
		empty_result: "<?php __('gridEmptyResult'); ?>",
		choose_action: "<?php __('gridChooseAction'); ?>",
		goto_page: "<?php __('gridGotoPage'); ?>",
		total_items: "<?php __('gridTotalItems'); ?>",
		items_per_page: "<?php __('gridItemsPerPage'); ?>",
		prev_page: "<?php __('gridPrevPage'); ?>",
		prev: "<?php __('gridPrev'); ?>",
		next_page: "<?php __('gridNextPage'); ?>",
		next: "<?php __('gridNext'); ?>",
		month_names: ['<?php echo $months[1]; ?>', '<?php echo $months[2]; ?>', '<?php echo $months[3]; ?>', '<?php echo $months[4]; ?>', '<?php echo $months[5]; ?>', '<?php echo $months[6]; ?>', '<?php echo $months[7]; ?>', '<?php echo $months[8]; ?>', '<?php echo $months[9]; ?>', '<?php echo $months[10]; ?>', '<?php echo $months[11]; ?>', '<?php echo $months[12]; ?>'],
		day_names: ['<?php echo $days[1]; ?>', '<?php echo $days[2]; ?>', '<?php echo $days[3]; ?>', '<?php echo $days[4]; ?>', '<?php echo $days[5]; ?>', '<?php echo $days[6]; ?>', '<?php echo $days[0]; ?>'],
		delete_title: "<?php __('gridDeleteConfirmation'); ?>",
		delete_text: "<?php __('gridConfirmationTitle'); ?>",
		action_title: "<?php __('gridActionTitle'); ?>",
		btn_ok: "<?php __('gridBtnOk'); ?>",
		btn_cancel: "<?php __('gridBtnCancel'); ?>",
		btn_delete: "<?php __('gridBtnDelete'); ?>",
		pr_status_1: "<?php echo $product_statuses[1]; ?>",
		pr_status_2: "<?php echo $product_statuses[2]; ?>"
	});
}

if (jQuery_1_8_2.multilang !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.multilang.messages, {
		tooltip: "<?php __('multilangTooltip'); ?>"
	});
}

if (jQuery_1_8_2.gallery !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.gallery.messages, {
		alt: "<?php __('plugin_gallery_alt'); ?>",
		btn_delete: "<?php __('plugin_gallery_btn_delete'); ?>",
		btn_cancel: "<?php __('plugin_gallery_btn_cancel'); ?>",
		btn_save: "<?php __('plugin_gallery_btn_save'); ?>",
		btn_set_watermark: "<?php __('plugin_gallery_btn_set_watermark'); ?>",
		btn_clear_current: "<?php __('plugin_gallery_btn_clear_current'); ?>",
		btn_compress: "<?php __('plugin_gallery_btn_compress'); ?>",
		btn_recreate: "<?php __('plugin_gallery_btn_recreate'); ?>",
		compress_note: "<?php __('plugin_gallery_compression_note'); ?>",
		compression: "<?php __('plugin_gallery_compression'); ?>",
		delete_all: "<?php __('plugin_gallery_delete_all'); ?>",
		delete_confirmation: "<?php __('plugin_gallery_delete_confirmation'); ?>",
		delete_confirmation_single: "<?php __('plugin_gallery_confirmation_single'); ?>",
		delete_confirmation_multi: "<?php __('plugin_gallery_confirmation_multi'); ?>",
		edit: "<?php __('plugin_gallery_edit'); ?>",
		empty_result: "<?php __('plugin_gallery_empty_result'); ?>",
		erase: "<?php __('plugin_gallery_delete'); ?>",
		image_settings: "<?php __('plugin_gallery_image_settings'); ?>",
		move: "<?php __('plugin_gallery_move'); ?>",
		originals: "<?php __('plugin_gallery_originals'); ?>",
		photos: "<?php __('plugin_gallery_photos'); ?>",
		position: "<?php __('plugin_gallery_position'); ?>",
		resize: "<?php __('plugin_gallery_resize'); ?>",
		rotate: "<?php __('plugin_gallery_rotate'); ?>",
		thumbs: "<?php __('plugin_gallery_thumbs'); ?>",
		upload: "<?php __('plugin_gallery_upload'); ?>",
		watermark: "<?php __('plugin_gallery_watermark'); ?>",
		watermark_position: "<?php __('plugin_gallery_watermark_position'); ?>",
		watermark_positions: {
			tl: "<?php __('plugin_gallery_top_left'); ?>",
			tr: "<?php __('plugin_gallery_top_right'); ?>",
			tc: "<?php __('plugin_gallery_top_center'); ?>",
			bl: "<?php __('plugin_gallery_bottom_left'); ?>",
			br: "<?php __('plugin_gallery_bottom_right'); ?>",
			bc: "<?php __('plugin_gallery_bottom_center'); ?>",
			cl: "<?php __('plugin_gallery_center_left'); ?>",
			cr: "<?php __('plugin_gallery_center_right'); ?>",
			cc: "<?php __('plugin_gallery_center_center'); ?>"
		}
	});
}