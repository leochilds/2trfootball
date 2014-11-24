<?php
if(!empty($tpl['event_arr']))
{
	foreach($tpl['event_arr'] as $v){
		$event_date = pjUtil::getEventDateTime($v['event_start_ts'], $v['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $v['o_show_start_time'], $v['o_show_end_time']);
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
		<div id="phpevtcal_event_box_<?php echo $v['id']?>" class="phpevtcal-event-box phpevtcal-event-listbox">
			<label class="phpevtcal-event-title"><?php echo stripslashes($v['event_title'])?></label>
			<?php
			if(isset($_GET['show_cate']) && $_GET['show_cate'] == 'Yes')
			{ 
				?>
				<div class="phpevtcal-detail-cate"><?php echo stripslashes($v['category'])?></div>
				<?php
			}
			?>
			<label class="phpevtcal-detail-date"><?php echo $event_date;?></label>
			<label class="phpevtcal-location"><?php echo stripslashes($v['location'])?></label>
			
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
			<div class="phpevtcal-button-pane">
				<?php 
				if($tpl['option_arr']['o_display_available_tickets'] == 'Yes')
				{ 
					?><label class="ebcal-available-tickets"><?php echo __('front_label_available_tickets', true) . ': <span>' . ($v['total_avail'] - $v['total_booked']) . '/' . $v['total_avail'] . '</span>'; ?></label><?php
				} 
				?>
				<?php
				$now = time() + ((int) $tpl['option_arr']['o_booking_before_hours'] * 60 * 60);
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
		</div>
		<?php
	} 
	if(!isset($_GET['event_id']))//installation for all events.
	{
		$page = isset($_GET['page']) ? $_GET['page'] : 1 ;
		$pages = $tpl['pages'];
		if($pages > 1)
		{
			?>
			<div id="phpevtcal_pagination_<?php echo $index;?>" class="phpevtcal-pagination">
				<?php
				if($page < $pages)
				{
					?><a href="javascript:void(0);" class="phpevtcal-paging phpevtcal-paging-next" rev="<?php echo $page + 1;?>"></a><?php
				}else{
					?><a href="javascript:void(0);" class="phpevtcal-paging-next"></a><?php
				}
				if($page > 1)
				{
					?><a href="javascript:void(0);" class="phpevtcal-paging phpevtcal-paging-prev" rev="<?php echo $page - 1;?>"></a><?php
				}else{
					?><a href="javascript:void(0);" class="phpevtcal-paging-prev"></a><?php
				}
				?>
			</div>
			<?php
		}
	}
}else{
	__('front_no_event');
}
?>