<?php
$index = $_GET['index'];
foreach($tpl['event_arr'] as $v){
	$event_title = '';
	if($v['o_show_start_time'] == 'T')
	{
		$event_title = pjUtil::formatTime(date('H:i:s', $v['event_start_ts']), 'H:i:s', $tpl['option_arr']['o_time_format']) . ', ' . $v['event_title'];
	}else{
		$event_title = $v['event_title'];
	}
	$event_date = pjUtil::getEventDateTime($v['event_start_ts'], $v['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $v['o_show_start_time'], $v['o_show_end_time']);
	$now = time() + ((int) $tpl['option_arr']['o_booking_before_hours'] * 60 * 60);
	$event_image_url = '';
	if(!empty($v['event_thumb']))
	{
		if(is_file(PJ_INSTALL_PATH . $v['event_thumb']))
		{
			$event_image_url = PJ_INSTALL_URL . $v['event_thumb'];
		}
	}
	$is_fulled = false;
	if(intval($v['total_avail']) - intval($v['total_booked']) == 0)
	{
		$is_fulled = true;
	}
	?>
	<div id="phpevtcal_event_box_<?php echo $index; ?>_<?php echo $v['id']?>" class="phpevtcal-event-box">
		<div class="phpevtcal-detail-heading">
			<label class="phpevtcal-detail-date<?php echo $is_fulled == true ? ' ebcal-fulled-event' : null;?>"><?php echo $event_date;?></label>
			<a class="phpevtcal-detail-close" href="javascript:void(0);" rev="<?php echo $v['id']?>"></a>
		</div>
		<label class="phpevtcal-event-title"><?php echo stripslashes($event_title)?></label>
		<?php
		if(isset($_GET['show_cate']) && $_GET['show_cate'] == 'Yes')
		{ 
			?><div class="phpevtcal-detail-cate"><?php echo stripslashes($v['category'])?></div><?php
		}
		if($tpl['option_arr']['o_display_available_tickets'] == 'Yes')
		{ 
			?><label class="ebcal-available-tickets"><?php echo __('front_label_available_tickets', true) . ': ' . ($v['total_avail'] - $v['total_booked']) . '/' . $v['total_avail'] ?></label><?php
		} 
		?>
		<div class="phpevtcal-detail-location"><?php echo stripslashes($v['location'])?></div>
		<div class="phpevtcal-detail-desc">
			<?php
			if($event_image_url != '')
			{
				$large_url = PJ_INSTALL_URL . $v['event_img'];
				?><a target="_blank" href="<?php echo $large_url; ?>"><img class="ebcal-event-image" src="<?php echo $event_image_url;?>" /></a><?php
			} 
			echo nl2br(stripslashes($v['description']));
			?>
		</div>
		<?php
		if($now > $v['event_start_ts'])
		{
			?><span class="ebcal-past-event"><?php __('front_label_past_event'); ?></span><?php
		}else{ 
			if(intval($v['total_avail']) == intval($v['total_booked']))
			{
				?><span class="ebcal-full-event"><?php __('front_label_full_event'); ?></span><?php
			}else if(intval($v['total_avail']) > intval($v['total_booked'])){
				?>
				<input type="button" class="ebcal-button ebcal-buy-ticket" lang="<?php echo $v['id']?>" value="<?php __('front_button_buy_ticket');?>">
				<?php
			}
		} 
		?>
	</div>
	<?php
} 
?>