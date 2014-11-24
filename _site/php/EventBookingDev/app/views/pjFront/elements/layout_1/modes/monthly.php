<?php
$first_day_of_month = $_GET['year'] . '-01-01';
$number_of_days = date('t', strtotime($_GET['year'] . '-'. $_GET['month'] .'-01'));
$month_arr = __('months', true);
$shortmonth_arr = __('short_months', true);
$days_arr = __('days', true);
$month = ltrim($_GET['month'], '0');
$year = $_GET['year'];
?>
<div id="phpevtcal_nav_bar_<?php echo $index;?>" class="phpevtcal-nav-bar">
	<a class="nav-arrow prev" href="javascript:void(0);" rev="prev"></a>
	<label class="month-name"><?php echo $month_arr[$month] . ', ' . $year; ?></label>
	<a class="nav-arrow next" href="javascript:void(0);" rev="next"></a>
</div>

<div id="phpevtcal_month_bar_<?php echo $index;?>" class="phpevtcal-month-bar">
	<?php
	for($i = 0; $i < 13; $i++){
		if(date('Y-m', strtotime($first_day_of_month)) == $year . '-' . $_GET['month'])
		{
			?>
			<a class="current" href="javascript:void(0);"><span><?php echo $shortmonth_arr[date('n', strtotime($first_day_of_month))];?></span></a>
			<?php
		}else{
			?>
			<a class="short-month" href="javascript:void(0);" rev="<?php echo date('Y', strtotime($first_day_of_month));?>" rel="<?php echo date('m', strtotime($first_day_of_month));?>"><span><?php echo $shortmonth_arr[date('n', strtotime($first_day_of_month))];?></span></a>
			<?php
		}
		
		$first_day_of_month = date('Y-m-d', strtotime($first_day_of_month . '+1 month'));
	}
	?>
</div>
<table class="phpevtcal-event-table">
	<?php
	$day_in_month = $_GET['year'] . '-' . $_GET['month'] . '-01';
	$event_date_arr = $tpl['event_date_arr'];
	for($i = 1; $i <= $number_of_days; $i++)
	{
		if(!empty($event_date_arr[$day_in_month]))
		{
			$now = time() + ((int) $tpl['option_arr']['o_booking_before_hours'] * 60 * 60);
			
			$events = $event_date_arr[$day_in_month];
			$num_events = count($events);
			for($j = 0; $j < $num_events; $j++)
			{
				$event_time = '';
				if($events[$j]['o_show_start_time'] == 'T')
				{
					$event_time .= pjUtil::formatTime(date('H:i:s', $events[$j]['event_start_ts']), 'H:i:s', $tpl['option_arr']['o_time_format']);
				}
				if($events[$j]['o_show_end_time'] == 'T')
				{
					if($event_time == '')
					{
						$event_time .= pjUtil::formatTime(date('H:i:s', $events[$j]['event_end_ts']), 'H:i:s', $tpl['option_arr']['o_time_format']);
					}else{
						$event_time .= '<br/>' . pjUtil::formatTime(date('H:i:s', $events[$j]['event_end_ts']), 'H:i:s', $tpl['option_arr']['o_time_format']);
					}
				}
				
				$event_title = $events[$j]['event_title'];
				
				$event_image_url = '';
				if(!empty($events[$j]['event_thumb']))
				{
					if(is_file(PJ_INSTALL_PATH . $events[$j]['event_thumb']))
					{
						$event_image_url = PJ_INSTALL_URL . $events[$j]['event_thumb'];
					}
				}
				$is_fulled = false;
				if(intval($events[$j]['total_avail']) - intval($events[$j]['total_booked']) == 0)
				{
					$is_fulled = true;
				}
				 
				if($j == 0)
				{
					?>
					<tr class="has-event<?php echo $is_fulled == true ? ' ebcal-fulled-event' : null;?>">
						<td rowspan="<?php echo $num_events;?>" class="day-num"><?php echo $i;?></td>
						<td rowspan="<?php echo $num_events;?>" class="day-week"><?php echo $days_arr[date('w', strtotime($day_in_month))];?></td>
						<td class="start-time"><?php echo $event_time; ?></td>
						<td class="event-desc">
							<label>
								<?php 
								echo $event_title;
								if(isset($_GET['show_cate']) && $_GET['show_cate'] == 'Yes')
								{
									if(!empty($events[$j]['category']))
									{
										?>,&nbsp;<span><?php echo $events[$j]['category'];?></span><?php
									}
								} 
								?>
							</label>
							<?php
							if($tpl['option_arr']['o_display_available_tickets'] == 'Yes')
							{ 
								?><label class="ebcal-available-tickets"><?php echo __('front_label_available_tickets', true) . ': ' . ($events[$j]['total_avail'] - $events[$j]['total_booked']) . '/' . $events[$j]['total_avail'] ?></label><?php
							} 
							?>
							<label class="ebcal-location"><?php echo stripslashes($events[$j]['location'])?></label>
							<div class="ebcal-monthly-desc">
								<?php
								if($event_image_url != '')
								{ 
									$large_url = PJ_INSTALL_URL . $events[$j]['event_img'];
									?><a target="_blank" href="<?php echo $large_url; ?>"><img class="ebcal-event-image" src="<?php echo $event_image_url;?>" /></a><?php
								} 
								echo nl2br(stripslashes($events[$j]['description'])); 
								?>
							</div>
							<?php
							if($now > $events[$j]['event_start_ts'])
							{
								?><span class="ebcal-past-event"><?php __('front_label_past_event'); ?></span><?php
							}else{ 
								if(intval($events[$j]['total_avail']) == intval($events[$j]['total_booked']))
								{
									?><span class="ebcal-full-event"><?php __('front_label_full_event'); ?></span><?php
								}else if(intval($events[$j]['total_avail']) > intval($events[$j]['total_booked'])){
									?>
									<input type="button" class="ebcal-button ebcal-buy-ticket" lang="<?php echo $events[$j]['id']?>" value="<?php __('front_button_buy_ticket');?>">
									<?php
								}
							} 
							?>
						</td>
					</tr>
					<?php
				}else{
					?>
					<tr class="has-event<?php echo $is_fulled == true ? ' ebcal-fulled-event' : null;?>">
						<td class="start-time"><?php echo $event_time; ?></td>
						<td class="event-desc">
							<label>
								<?php 
								echo $event_title;
								if(isset($_GET['show_cate']) && $_GET['show_cate'] == 'Yes')
								{
									if(!empty($events[$j]['category']))
									{
										?>,&nbsp;<span><?php echo $events[$j]['category'];?></span><?php
									}
								} 
								?>
							</label>
							<?php
							if($tpl['option_arr']['o_display_available_tickets'] == 'Yes')
							{ 
								?><label class="ebcal-available-tickets"><?php echo __('front_label_available_tickets', true) . ': ' . ($events[$j]['total_avail'] - $events[$j]['total_booked']) . '/' . $events[$j]['total_avail'] ?></label><?php
							} 
							?>
							<div class="ebcal-monthly-desc">
								<?php
								if($event_image_url != '')
								{ 
									$large_url = PJ_INSTALL_URL . $events[$j]['event_img'];
									?><a target="_blank" href="<?php echo $large_url; ?>"><img class="ebcal-event-image" src="<?php echo $event_image_url;?>" /></a><?php
								} 
								echo nl2br(stripslashes($events[$j]['description'])); 
								?>
							</div>
							<?php
							if($now > $events[$j]['event_start_ts'])
							{
								?><span class="ebcal-past-event"><?php __('front_label_past_event'); ?></span><?php
							}else{ 
								if(intval($events[$j]['total_avail']) == intval($events[$j]['total_booked']))
								{
									?><span class="ebcal-full-event"><?php __('front_label_full_event'); ?></span><?php
								}else if(intval($events[$j]['total_avail']) > intval($events[$j]['total_booked'])){
									?>
									<input type="button" class="ebcal-block ebcal-button ebcal-buy-ticket" lang="<?php echo $events[$j]['id']?>" value="<?php __('front_button_buy_ticket');?>">
									<?php
								}
							} 
							?>
						</td>
					</tr>
					<?php
				}
			}
		}else{
			?>
			<tr <?php echo $day_in_month == date('Y-m-d') ? 'class="current-date"' : null; ?>>
				<td class="day-num"><?php echo $i;?></td>
				<td class="day-week"><?php echo $days_arr[date('w', strtotime($day_in_month))];?></td>
				<td class="start-time">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?php
		}
		$day_in_month = date('Y-m-d', strtotime($day_in_month . '+1 day'));
	} 
	?>
</table>