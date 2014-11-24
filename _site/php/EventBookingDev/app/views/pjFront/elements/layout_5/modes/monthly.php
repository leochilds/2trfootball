<?php
$first_day_of_month = $_GET['year'] . '-01-01';
$number_of_days = date('t', strtotime($_GET['year'] . '-'. $_GET['month'] .'-01'));
$month_arr = __('months', true);
$month = ltrim($_GET['month'], '0');
$year = $_GET['year'];
?>
<div class="phpevtcal-header-container">
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
</div>
<table class="phpevtcal-event-table" border="0" cellpadding="0" cellspacing="0">
	<?php
	$day_in_month = $_GET['year'] . '-' . $_GET['month'] . '-01';
	$event_date_arr = $tpl['event_date_arr'];
	
	for($i = 1; $i <= $number_of_days; $i++)
	{
		if(!empty($event_date_arr[$day_in_month]))
		{
			$events = $event_date_arr[$day_in_month];
			$num_events = count($events);
			
			?>
			<tr class="has-event-top">
				<td class="day-left"></td>
				<td class="day-num"></td>
				<td class="day-separator"></td>
				<td class="day-week"></td>
				<td class="day-content"></td>
				<td class="day-right"></td>
			</tr>
			<tr class="has-event-middle">
				<td class="day-left"></td>
				<td class="day-num"><span><?php echo $i;?></span></td>
				<td class="day-separator"></td>
				<td class="day-week"><?php echo date('l', strtotime($day_in_month));?></td>
				<td class="day-content">
					<?php
					for($j = 0; $j < $num_events; $j++)
					{
						$event_title = '&nbsp;';
						if(!empty($events[$j]['end_time'])){
							$event_time = pjUtil::formatTime($events[$j]['start_time'], 'H:i:s', $tpl['option_arr']['o_time_format']);
							if(!empty($events[$j]['category'])){
								$event_title = $event_time . ' - ' . $events[$j]['event_title'] . ', <span>' . $events[$j]['category'] . '</span>';
							}else{	
								$event_title = $event_time . ' - ' . $events[$j]['event_title'];
							}
						}else{
							$event_title = $events[$j]['event_title'];
						} 
						$has_line = 1;
						if($num_events == 1)
						{
							$has_line = 0;
						}else{
							if($j == $num_events - 1){
								$has_line = 0;
							}
						}
						?>
						<div class="phpevtcal-monthly-eventbox <?php echo ($has_line == 1) ? 'phpevtcal-line' : null;?>">
							<label><?php echo $event_title; ?></label>
							<div><?php echo stripslashes($events[$j]['description']); ?></div>
						</div>
						<?php 
					}	
					?>
				</td>
				<td class="day-right"></td>
			</tr>
			<tr class="has-event-bottom">
				<td class="day-left"></td>
				<td class="day-num"></td>
				<td class="day-separator"></td>
				<td class="day-week"></td>
				<td class="day-content"></td>
				<td class="day-right"></td>
			</tr>
			
			<?php
				
		}else{
			if($day_in_month == date('Y-m-d'))
			{
				?>
				<tr class="current-top">
					<td class="day-left"></td>
					<td class="day-num"></td>
					<td class="day-separator"></td>
					<td class="day-week"></td>
					<td class="day-content"></td>
					<td class="day-right"></td>
				</tr>
				<tr class="current-middle">
					<td class="day-left">&nbsp;</td>
					<td class="day-num"><span><?php echo $i;?></span></td>
					<td class="day-separator">&nbsp;</td>
					<td class="day-week"><?php echo date('l', strtotime($day_in_month));?></td>
					<td class="day-content">&nbsp;</td>
					<td class="day-right">&nbsp;</td>
				</tr>
				<tr class="current-bottom">
					<td class="day-left"></td>
					<td class="day-num"></td>
					<td class="day-separator"></td>
					<td class="day-week"></td>
					<td class="day-content"></td>
					<td class="day-right"></td>
				</tr>
				<?php
			}else{
				?>
				<tr class="normal-top">
					<td class="day-left"></td>
					<td class="day-num"></td>
					<td class="day-separator"></td>
					<td class="day-week"></td>
					<td class="day-content"></td>
					<td class="day-right"></td>
				</tr>
				<tr class="normal-middle">
					<td class="day-left"></td>
					<td class="day-num"><span><?php echo $i;?></span></td>
					<td class="day-separator"></td>
					<td class="day-week"><?php echo date('l', strtotime($day_in_month));?></td>
					<td class="day-content">&nbsp;</td>
					<td class="day-right">&nbsp;</td>
				</tr>
				<tr class="normal-bottom">
					<td class="day-left"></td>
					<td class="day-num"></td>
					<td class="day-separator"></td>
					<td class="day-week"></td>
					<td class="day-content"></td>
					<td class="day-right"></td>
				</tr>
				<?php
			}
		}
		$day_in_month = date('Y-m-d', strtotime($day_in_month . '+1 day'));
	} 
	?>
</table>