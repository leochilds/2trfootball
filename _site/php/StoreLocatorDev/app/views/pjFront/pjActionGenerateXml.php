<?php
ob_start();
$arr = array();
if(count($tpl['arr']) > 0)
{
	foreach($tpl['arr'] as $k => $v)
	{
		$address = array();
		$address[] = $v['address_country'];
		$address[] = $v['address_state'];
		$address[] = $v['address_city'];
		$address[] = $v['address_content'];
		$address[] = $v['address_zip'];
		$address = array_filter($address, 'strlen');
		$_address = join(", ", $address);
	  
		$v["address"] = $_address;
		$v["img_tag"] = ''; 
		$v['marker_content'] = ''; 
		?>
		<div class="stl-store-item" lang="<?php echo $k?>">
			<?php
			if(!empty($v['image_path']))
			{
				?><img class="stl-store-image" src="<?php echo PJ_INSTALL_URL . $v['image_path'];?>" lang="<?php echo $k?>"/><?php
				$v["img_tag"] = '<img class="stl-store-image" src="' . PJ_INSTALL_URL . $v['image_path'].'" lang="' .$k. '" />';
			} 
			?>
			<div class="stl-store-item-detail">
				<abbr class="stl-store-title" lang="<?php echo $k?>"><?php echo pjSanitize::clean($v['name']);?></abbr>
				<?php
				if(!empty($v['phone']))
				{
					?>
					<div class="stl-item-row"><label><?php __('front_label_phone');?></label><span><?php echo pjSanitize::clean($v['phone']);?></span></div>
					<?php
					$v['phone'] = '<label class="stl-row-tooltip">' . __('front_label_phone', true, false) . ': <span class="stl-content-tooltip">' . $v['phone'] . '</span></label>';
				}
				if(!empty($v['email']))
				{
					$v["email"] = !preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i', $v['email']) ? $v['email'] : '<a href="mailto:'.$v['email'].'">'.$v['email'].'</a>';
					?>
					<div class="stl-item-row"><label><?php __('front_label_email');?></label><span><?php echo $v['email'];?></span></div>
					<?php
					$v["email"] = '<label class="stl-row-tooltip">'.__('front_label_email', true, false).': <span class="stl-content-tooltip">' .$v["email"]. '</span></label>';
				}
				if(!empty($v['website']))
				{
					$v["website"] = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $v['website']);
					?>
					<div class="stl-item-row"><label><?php __('front_label_website');?></label><span><?php echo $v['website'];?></span></div>
					<?php
					$v["website"] = '<label class="stl-row-tooltip">'.__('front_label_website', true, false).': <span class="stl-content-tooltip">' .$v["website"]. '</span></label>';
				}
				if(!empty($v['opening_times']))
				{
					?>
					<div class="stl-item-row"><label><?php __('front_label_opening_times');?></label><span><?php echo pjSanitize::clean($v['opening_times']);?></span></div>
					<?php
				}
				if(!empty($address))
				{
					?>
					<div id="stl_hidden_container_<?php echo $k?>" class="stl-hidden-container">
						<div id="stl_full_address_<?php echo $k?>" class="stl-full-address" lang="<?php echo $k?>"><?php __('front_label_full_address');?></div>
						<div id="stl_directions_<?php echo $k?>" class="stl-directions" lang="<?php echo $k?>"><?php __('front_label_directions');?></div>
					</div>
					<div id="stl_store_address_<?php echo $k?>" class="stl-item-row stl-store-address">
						<label><?php __('front_label_address');?></label>
						<span><?php echo $_address;?></span>
					</div>
					<div id="stl_close_address_<?php echo $k?>" class="stl-close-address" lang="<?php echo $k?>"><?php __('front_label_close');?></div>
					<div id="stl_direction_box_<?php echo $k?>" class="stl-direction-box stl-form" lang="<?php echo $k?>">
						<p>
							<label class="title30"><?php __('front_label_from');?></label>
							<input id="stl_direction_text_<?php echo $k?>" class="stl-direction-text stl-text stl-w50p" name="stl_direction_text_<?php echo $k?>" lang="<?php echo $k?>" />
							<input type="button" value="<?php __('front_label_go');?>" name="stl_direction_go_<?php echo $k?>" class="stl-button stl-go-button" lang="<?php echo $k?>" />
						</p>
						<div id="stl_close_direction_<?php echo $k?>" class="stl-close-direction" lang="<?php echo $k?>"><?php __('front_label_close');?></div>
					</div>
					<?php
					$v["address"] = '<label class="stl-row-tooltip">' .__('front_label_address', true, false).': <span class="stl-content-tooltip">' .$v["address"]. '</span></label>';
				} 
				?>
			</div>
		</div>
		<?php
		$v['marker_content'] = '<div class="stl-google-tooltip">' . $v["img_tag"] . '<div class="stl-detail-tooltip"><h3>' . $v['name'] . '</h3>' . $v['phone'] . $v["email"] . $v["website"] . $v["address"]. '</div></div>';
		
		$arr[$k] = array('name' => $v['name'], 'lat' => $v['lat'], 'lng' => $v['lng'], 'distance' => $v['distance'], 'marker' => $v['marker'], 'marker_content' => $v['marker_content']);
	}
}else{
	?><div class="stl-store-empty"><?php __('front_label_not_found');?></div><?php
}
$ob_store_list = ob_get_contents();
ob_end_clean();
$arr[count($tpl['arr'])] = array('store_list' => '');
if (!empty($ob_store_list))
{
	$arr[count($tpl['arr'])] = array('store_list' => $ob_store_list);
}
pjAppController::jsonResponse($arr);
exit;
?>