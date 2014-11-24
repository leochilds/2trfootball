<?php
if(!isset($_GET['event_id']))
{ 
	?>
	<div class="ebcal-header">
		<div class="ebcal-menu">
			<a class="ebcal-menu-item<?php echo $_GET['period'] == 'all' || $_GET['period'] == '' ? ' focus' : null; ?>" rev="all" href="#"><?php __('front_all');?></a>
			<a class="ebcal-menu-item<?php echo $_GET['period'] == 'today' ? ' focus' : null; ?>" rev="today" href="#"><?php __('front_today');?></a>
			<a class="ebcal-menu-item<?php echo $_GET['period'] == 'tomorrow' ? ' focus' : null; ?>" rev="tomorrow" href="#"><?php __('front_tomorrow');?></a>
			<a class="ebcal-menu-item<?php echo $_GET['period'] == 'weekend' ? ' focus' : null; ?>" rev="weekend" href="#"><?php __('front_this_weekend');?></a>
			<a class="ebcal-menu-item<?php echo $_GET['period'] == 'next7days' ? ' focus' : null; ?>" rev="next7days" href="#"><?php __('front_next_7_days');?></a>
			<a class="ebcal-menu-item<?php echo $_GET['period'] == 'next30days' ? ' focus' : null; ?>" rev="next30days" href="#"><?php __('front_next_30_days');?></a>
			<a class="ebcal-menu-item<?php echo $_GET['period'] == 'all' || $_GET['period'] == '' ? ' focus' : null; ?>" rev="all" href="#"><?php __('front_all_events');?></a>
		</div>
	</div>
	<?php
} 
?>

<div class="ebcal-event-list">
	<?php
	if(!empty($tpl['event_arr']))
	{
		foreach($tpl['event_arr'] as $v)
		{
			$event_date = pjUtil::getEventDateTime($v['event_start_ts'], $v['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $v['o_show_start_time'], $v['o_show_end_time']);
			
			$event_image_url = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/layout_2/no-img.png';
			if(!empty($v['event_medium']))
			{
				if(is_file(PJ_INSTALL_PATH . $v['event_medium']))
				{
					$event_image_url = PJ_INSTALL_URL . $v['event_medium'];
				}
			}
			?>
			<div class="ebcal-event-box" data-id="<?php echo $v['id'];?>">
				<div class="ebcal-img"><img src="<?php echo $event_image_url;?>" alt="<?php echo stripslashes($v['event_title'])?>" /></div>
				<div class="ebcal-detail">
					<?php
					if(isset($_GET['show_cate']) && $_GET['show_cate'] == 'Yes')
					{ 
						?><div class="ebcal-category"><?php echo stripslashes($v['category']); ?></div><?php
					}
					?>
					<div class="ebcal-title"><?php echo stripslashes($v['event_title']); ?></div>
					<label class="ebcal-date"><?php echo $event_date; ?></label>
					<label class="ebcal-location"><?php echo stripslashes($v['location'])?></label>
					<?php 
					if($tpl['option_arr']['o_display_available_tickets'] == 'Yes')
					{ 
						?><label class="ebcal-ticket"><?php echo __('front_label_available_tickets', true) . ': <span>' . ($v['total_avail'] - $v['total_booked']) . '/' . $v['total_avail'] . '</span>'; ?></label><?php
					} 
					?>
				</div>
				<div id="ebcal_overlay_<?php echo $index; ?>_<?php echo $v['id']; ?>" class="ebcal-event-overlay">
					<div class="ebcal-overlay-inner"><?php echo pjUtil::truncateDescription($v['description'], 200, ' '); ?></div>
					<input type="button" class="ebcal-button ebcal-view-details" data-id="<?php echo $v['id']?>" value="<?php __('front_view_details');?>">
				</div>
			</div>
			<?php
		}
	}else{
		?><div class="ebcal-event-notfound"><?php __('front_no_event', false, true);?></div><?php
	}
	?>
</div>
<?php
if(!empty($tpl['event_arr']))
{
	include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_2/paginator.php';
} 
?>