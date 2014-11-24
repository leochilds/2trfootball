<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
}else{
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	?>
	<div class="dashboard">
		<div class="dashboard_header">
			<div class="left"></div>
			<div class="middle">
				<div class="total-bookings"><div class="header-content"><span><?php echo $tpl['cnt_bookings']; ?></span><label><?php echo strtolower(__('lblTotalBookings', true));?></label></div></div>
				<div class="total-events"><div class="header-content"><span><?php echo $tpl['cnt_events']; ?></span><label><?php echo strtolower(__('lblTotalEvents', true));?></label></div></div>
				<div class="users"><div class="header-content"><span><?php echo $tpl['cnt_users']; ?></span><label><?php echo strtolower(__('lblUsers', true));?></label></div></div>
			</div>
			<div class="right"></div>
		</div>
		<div class="dashboard_box today-events-box">
			<div class="header">
				<div class="left"></div>
				<div class="middle"><span><?php __('lblLatestBookings');?></span></div>
				<div class="right"></div>
			</div>
			<div class="content">
				<div class="dashboard_list">
					<?php
					if(!empty($tpl['latest_bookings']))
					{
						$row_count = count($tpl['latest_bookings']) > 5 ? 4 : count($tpl['latest_bookings']) - 1;
						foreach($tpl['latest_bookings'] as $k => $v)
						{
							?>
							<div class="dashboard_row latest-booking-row <?php echo $k == $row_count ? 'dashboard_last_row' : null; ?>">
								<div class="customer-name b10" >
									<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&id=<?php echo $v['id'];?>"><?php echo pjSanitize::html(stripslashes($v['customer_name']));?></a>
								</div>
								<label><?php echo intval($v['customer_people']);?>&nbsp;<?php echo strtolower(__('lblTickets', true));?></label>
							</div>
							<?php
						}
					} else {
						?>
						<div class="dashboard_row"><div class="customer-name"><?php __('lblNoBookingFound');?></div></div>
						<?php
					}
					?>
				</div>
			</div>
			<div class="footer">
				<div class="left"></div>
				<div class="middle"></div>
				<div class="right"></div>
			</div>
		</div>
		<div class="dashboard_box total-events-box">
			<div class="header">
				<div class="left"></div>
				<div class="middle"><span><?php __('lblUpcomingEvents');?></span></div>
				<div class="right"></div>
			</div>
			<div class="content">
				<div class="dashboard_list">
					<?php
					if(!empty($tpl['upcoming_events']))
					{
						 
						$row_count = count($tpl['upcoming_events']);
						
						foreach($tpl['upcoming_events'] as $k => $v)
						{
							$event_date = pjUtil::getEventDateTime($v['event_start_ts'], $v['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $v['o_show_start_time'], $v['o_show_end_time']);
							?>
							<div class="dashboard_row today-events-row <?php echo $k + 1 == $row_count ? 'dashboard_last_row' : null; ?>">
								<div class="event-title" >
									<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&id=<?php echo $v['id'];?>"><?php echo pjSanitize::html(stripslashes($v['event_title']));?></a>
								</div>
								
								<?php
								if($event_date != null)
								{ 
									?><label><?php echo $event_date;?></label><?php
								} 
								?>									
							</div>
							<?php
						}
					} else {
						?>
						<div class="dashboard_row"><div class="event-title"><?php __('lblNoEventFound');?></div></div>
						<?php
					}
					?>
				</div>
			</div>
			<div class="footer">
				<div class="left"></div>
				<div class="middle"></div>
				<div class="right"></div>
			</div>
		</div>
		<div class="dashboard_box user-box">
			<div class="header">
				<div class="left"></div>
				<div class="middle"><span><?php __('lblUsers');?></span></div>
				<div class="right"></div>
			</div>
			<div class="content">
				<div class="dashboard_list">
					<?php
					if(!empty($tpl['user_arr']))
					{
						$row_count = count($tpl['user_arr']);
						foreach($tpl['user_arr'] as $k => $v)
						{
							?>
							<div class="dashboard_row user-row <?php echo $k + 1 == $row_count ? 'dashboard_last_row' : null; ?>">
								<label><?php echo $v['name']?></label>
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionUpdate&id=<?php echo $v['id'];?>"><?php echo $v['email'];?></a>
								<span class="datetime"><?php echo strtolower(__('lblDashLastLogin', true)); ?>:&nbsp;<?php echo pjUtil::formatDate(date('Y-m-d', strtotime($v['last_login'])), "Y-m-d", $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($v['last_login'])), "H:i:s", $tpl['option_arr']['o_time_format']); ?></span>
							</div>
							<?php
						}
					} else {
						?>
						<div class="dashboard_row"><div class="topic_title"><?php __('lblNoMemberFound');?></div></div>
						<?php
					}
					?>
				</div>
			</div>
			<div class="footer">
				<div class="left"></div>
				<div class="middle"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
	<div class="clear_left t20 overflow">
		<div class="float_left black t30"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo date("F d, Y H:i", strtotime($_SESSION[$controller->defaultUser]['last_login'])); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo $other; ?>
			</div>
			<div class="dashboard_hour"><?php echo $hour; ?></div>
		</div>
	</div>
	<?php
}
?>