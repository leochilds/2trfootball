<?php
$distances = __('distances', true);
ksort($distances);
?>
<div class="stl-store-container">
	<div class="stl-search-container">
		
		<form action="" method="post" name="stl_seach_form" class="stl-form" onsubmit="return false;">
			<?php
			if($tpl['option_arr']['o_use_categories'] == 'Yes')
			{
				?>
				<p class="stl-category-outer">
					<label class="title"><?php __('front_label_category'); ?></label>
					<select name="category_id" class="stl-select">
						<option value="">--<?php __('front_label_choose'); ?>--</option>
						<?php
						foreach ($tpl['category_arr'] as $k => $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo $v['category_title']; ?></option><?php
						}
						?>
					</select>
				</p>
				<?php
			}
			?>
			<p class="stl-address-outer">
				<label class="title"><?php __('front_label_address'); ?></label>
				<input name="address" value="<?php echo $tpl['option_arr']['o_default_address'];?>" class="stl-text" />
				<a href="javascript:void(0);" id="stl_current_location" class="stl-current-location" title="<?php __('front_current_location');?>"></a>
			</p>
			<p class="stl-within">
				<label class="title"><?php __('front_label_within'); ?></label>
				<select name="radius" class="stl-select stl-select-radius stl-r5">
					<?php
					foreach ($distances as $k => $v)
					{
						?><option value="<?php echo $k; ?>" <?php echo $k == 25 ? 'selected="selected"' : null;?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
				<label class="stl-distance-legend"><?php echo $tpl['option_arr']['o_distance'];?></label>
			</p>
			<p class="stl-button-outer">
				<input type="button" value="<?php __('front_button_search'); ?>" name="stl_search_form_search" class="stl-button " />
			</p>
		</form>
	</div>
	
	<div id="stl_search_result" class="stl-search-result">
		<div id="stl_search_addresses"></div>
	</div>
	<div id="stl_search_directions" class="stl-search-directions" style="display: none">
		<div class="stl-directions-menu">
			<a href="#" class="stl-directions-close"><?php __('front_label_close');?></a>
			<a href="#" id="stl_email_menu" class="stl-directions-email"><?php __('front_label_email');?></a>
		</div>
		<div class="stl-search-directions-panel">
			<div id="stl_directions_email" class="stl-directions-email">
				<form action="" method="post" id="stl_send_email_form" name="stl_send_email_form" class="stl-form" onsubmit="return false;">
					<p class="stl-send-direction">
						<label class="title30"><?php __('front_label_email');?></label>
						<input name="stl_email_text" class="stl-email-text stl-text stl-w60p" />
						<input type="button" id="stl_send_email" name="stl_send_email" value="<?php __('front_button_send'); ?>" class="stl-button stl-go-button" />
						<textarea id="stl_directions_html" name="stl_directions_html" class="stl-direction-html"></textarea>
					</p>
				</form>
			</div>
			<div id="stl_search_directions_panel"></div>
		</div>
	</div>
	
	<div id="stl_store_canvas" class="stl-map-container"></div>
</div>

<script type="text/javascript">
	var stivaSTLObj = new stivaSTL({
		zoom_level: <?php echo $tpl['option_arr']['o_zoom_level']; ?>,
		default_address: "<?php echo $tpl['option_arr']['o_default_address']; ?>",
		distance: "<?php echo $tpl['option_arr']['o_distance']; ?>",
		use_categories: "<?php echo $tpl['option_arr']['o_use_categories']; ?>",

		search_form_name: "stl_seach_form",
		search_form_address_name: "stl_seach_form_add",
		search_form_search_name: "stl_search_form_search",
		search_form_address: "address",

		label_opening_time: "<?php echo pjSanitize::clean(__('front_label_opening_times', true, false));?>",
		label_full_address: "<?php echo pjSanitize::clean(__('front_label_full_address', true, false));?>",
		label_directions: "<?php echo pjSanitize::clean(__('front_label_directions', true, false));?>",
		label_close: "<?php echo pjSanitize::clean(__('front_label_close', true, false));?>",
		label_from: "<?php echo pjSanitize::clean(__('front_label_from', true, false));?>",
		label_address: "<?php echo pjSanitize::clean(__('front_label_address', true, false));?>",
		label_go: "<?php echo pjSanitize::clean(__('front_label_go', true, false));?>",
		label_phone: "<?php echo pjSanitize::clean(__('front_label_phone', true, false));?>",
		label_email: "<?php echo pjSanitize::clean(__('front_label_email', true, false));?>",
		label_website: "<?php echo pjSanitize::clean(__('front_label_website', true, false));?>",
		label_not_found: "<?php echo pjSanitize::clean(__('front_label_not_found', true, false));?>",
		label_address_not_found: "<?php echo pjSanitize::clean(__('front_label_address_not_found', true, false));?>",
		label_sent: "<?php echo pjSanitize::clean(__('front_label_sent', true, false));?>",
		label_empty_email: "<?php echo pjSanitize::clean(__('front_label_empty_email', true, false));?>",
		label_invalid_email: "<?php echo pjSanitize::clean(__('front_label_invalid_email', true, false));?>",
		label_geo_not_supported: "<?php echo pjSanitize::clean(__('front_geo_supported', true, false));?>",

		install_url: "<?php echo PJ_INSTALL_URL; ?>",
		generate_xml_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionGenerateXml",
		get_latlng_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionGetLatLng",
		send_email_url: "<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionSendEmail"
	});
</script>