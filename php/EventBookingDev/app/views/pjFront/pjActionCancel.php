<?php
$statuses = __('cancel_statuses', true);

if (isset($tpl['status']))
{
	?><p><?php echo $statuses[$tpl['status']];; ?></p><?php
}else{
	if (isset($_GET['err']))
	{
		?><p><?php echo $statuses[200]; ?></p><?php
	}
	if (isset($tpl['arr']))
	{
		$event_date = pjUtil::getEventDateTime($tpl['arr']['event_start_ts'], $tpl['arr']['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $tpl['arr']['o_show_start_time'], $tpl['arr']['o_show_end_time']);
		?>
		<table style="width: 100%">
			<thead>
				<tr>
					<th colspan="2" style="text-transform: uppercase; text-align: left"><?php __('front_label_cancel_text'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo __('front_label_event_title'); ?>:</td>
					<td><?php echo stripslashes($tpl['arr']['event_title']); ?></td>
				</tr>
				<tr>
					<td><?php echo __('front_label_event_date_time'); ?>:</td>
					<td><?php echo $event_date; ?></td>
				</tr>
				<tr>
					<td><?php echo __('front_label_event_description'); ?>:</td>
					<td><?php echo nl2br(stripslashes($tpl['arr']['event_title'])); ?></td>
				</tr>
				<?php
				if(!empty($tpl['arr']['customer_name']))
				{ 
					?>
					<tr>
						<td><?php __('front_label_name'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['customer_name']); ?></td>
					</tr>
					<?php
				}
				if(!empty($tpl['arr']['customer_email']))
				{ 
					?>
					<tr>
						<td><?php __('front_label_email'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['customer_email']); ?></td>
					</tr>
					<?php
				}
				if(!empty($tpl['arr']['customer_phone']))
				{ 
					?>
					<tr>
						<td><?php __('front_label_phone'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['customer_phone']); ?></td>
					</tr>
					<?php
				}
				if(!empty($tpl['arr']['country_title']))
				{ 
					?>
					<tr>
						<td><?php __('front_label_country'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['country_title']); ?></td>
					</tr>
					<?php
				}
				if(!empty($tpl['arr']['customer_city']))
				{ 
					?>
					<tr>
						<td><?php __('front_label_city'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['customer_city']); ?></td>
					</tr>
					<?php
				}
				if(!empty($tpl['arr']['customer_state']))
				{ 
					?>
					<tr>
						<td><?php __('front_label_state'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['customer_state']); ?></td>
					</tr>
					<?php
				}
				if(!empty($tpl['arr']['customer_zip']))
				{ 
					?>
					<tr>
						<td><?php __('front_label_zip'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['customer_zip']); ?></td>
					</tr>
					<?php
				}
				if(!empty($tpl['arr']['customer_address']))
				{ 
					?>
					<tr>
						<td><?php __('front_label_address'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['customer_address']); ?></td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td>&nbsp;</td>
					<td><?php __('front_label_cancel_confirm'); ?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td>&nbsp;</td>
					<td>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjFront&amp;action=pjActionCancel" method="post">
							<input type="hidden" name="booking_cancel" value="1" />
							<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
							<input type="submit" value="<?php __('front_button_cancel'); ?>" />
						</form>
					</td>
				</tr>
			</tfoot>
		</table>
		<?php
	}
}
?>