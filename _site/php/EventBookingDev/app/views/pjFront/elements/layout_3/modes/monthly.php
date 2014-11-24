<?php
$first_day_of_month = $_GET['year'] . '-01-01';
$number_of_days = date('t', strtotime($_GET['year'] . '-'. $_GET['month'] .'-01'));
$month_arr = __('months', true);
$month = ltrim($_GET['month'], '0');
$year = $_GET['year'];
?>
<div id="phpevtcal_nav_bar" class="phpevtcal-nav-bar">
	<a class="nav-arrow prev" href="javascript:void(0);" rev="prev"></a>
	<label class="month-name"><?php echo $month_arr[$month] . ', ' . $year; ?></label>
	<a class="nav-arrow next" href="javascript:void(0);" rev="next"></a>
</div>

<div id="phpevtcal_month_bar" class="phpevtcal-month-bar">
	<?php
	for($i = 0; $i < 13; $i++){
		if(date('Y-m', strtotime($first_day_of_month)) == $year . '-' . $_GET['month'])
		{
			?>
			<a class="current" href="javascript:void(0);"><span><?php echo date('M', strtotime($first_day_of_month));?></span></a>
			<?php
		}else{
			?>
			<a class="short-month" href="javascript:void(0);" rev="<?php echo date('Y', strtotime($first_day_of_month));?>" rel="<?php echo date('m', strtotime($first_day_of_month));?>"><span><?php echo date('M', strtotime($first_day_of_month));?></span></a>
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
			$events = $event_date_arr[$day_in_month];
			$num_events = count($events);
			for($j = 0; $j < $num_events; $j++)
			{
				$event_title = '&nbsp;';
				if(!empty($events[$j]['end_time'])){
					$event_time = pjUtil::formatTime($events[$j]['end_time'], 'H:i:s', $tpl['option_arr']['o_time_format']);
					$event_title = $event_time . ' - ' . $events[$j]['event_title'];
				}else{
					$event_title = $events[$j]['event_title'];
				}
				if($j == 0)
				{
					?>
					<tr class="has-event">
						<td rowspan="<?php echo $num_events;?>" class="day-num"><?php echo $i;?></td>
						<td rowspan="<?php echo $num_events;?>" class="day-week"><?php echo date('l', strtotime($day_in_month));?></td>
						<td class="start-time"><?php echo !empty($events[$j]['start_time']) ? pjUtil::formatTime($events[$j]['start_time'], 'H:i:s', $tpl['option_arr']['o_time_format']) : '&nbsp;'; ?></td>
						<td>
							<label>
								<?php 
								echo $event_title;
								if(!empty($events[$j]['category']))
								{
									?>,&nbsp;<span><?php echo $events[$j]['category'];?></span><?php
								} 
								?>
							</label>
							<div><?php echo stripslashes($events[$j]['description']); ?></div>
						</td>
					</tr>
					<?php
				}else{
					?>
					<tr class="has-event">
						<td class="start-time"><?php echo !empty($events[$j]['start_time']) ? pjUtil::formatTime($events[$j]['start_time'], 'H:i:s', $tpl['option_arr']['o_time_format']) : '&nbsp;'; ?></td>
						<td>
							<label>
								<?php 
								echo $event_title;
								if(!empty($events[$j]['category']))
								{
									?>,&nbsp;<span><?php echo $events[$j]['category'];?></span><?php
								} 
								?>
							</label>
							<div><?php echo stripslashes($events[$j]['description']); ?></div>
						</td>
					</tr>
					<?php
				}
			}
		}else{
			?>
			<tr <?php echo $day_in_month == date('Y-m-d') ? 'class="current-date"' : null; ?>>
				<td class="day-num"><?php echo $i;?></td>
				<td class="day-week"><?php echo date('l', strtotime($day_in_month));?></td>
				<td class="start-time">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?php
		}
		$day_in_month = date('Y-m-d', strtotime($day_in_month . '+1 day'));
	} 
	?>
</table>