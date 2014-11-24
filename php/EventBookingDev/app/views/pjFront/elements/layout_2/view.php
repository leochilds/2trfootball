<div class="ebcal-header">
	<div class="ebcal-menu">
		<a class="ebcal-back" href="#"><?php __('front_button_back');?></a>
	</div>
</div>
<?php
$event_date = pjUtil::getEventDateTimeNOL($tpl['arr']['event_start_ts'], $tpl['arr']['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $tpl['arr']['o_show_start_time'], $tpl['arr']['o_show_end_time']);

$event_image_url = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/layout_2/no-img.png';
if(!empty($tpl['arr']['event_medium']))
{
	if(is_file(PJ_INSTALL_PATH . $tpl['arr']['event_img']))
	{
		$event_image_url = PJ_INSTALL_URL . $tpl['arr']['event_img'];
	}
}
?>
<div class="ebcal-event-detail">
	<div class="ebcal-event-detail-inner">
		<div class="ebcal-title"><?php echo stripslashes($tpl['arr']['event_title']); ?></div>
		<div class="ebcal-detail">
			<?php
			if(isset($_GET['show_cate']) && $_GET['show_cate'] == 'Yes' && !empty($tpl['arr']['category']))
			{ 
				?><div class="ebcal-category"><?php echo stripslashes($tpl['arr']['category']); ?></div><?php
			}
			?>
			<label class="ebcal-date"><?php echo $event_date; ?></label>
			<label class="ebcal-location"><?php echo stripslashes($tpl['arr']['location'])?></label>
			<?php 
			if($tpl['option_arr']['o_display_available_tickets'] == 'Yes')
			{ 
				?><label class="ebcal-ticket"><?php echo __('front_label_available_tickets', true) . ': <span>' . ($tpl['arr']['total_avail'] - $tpl['arr']['total_booked']) . '/' . $tpl['arr']['total_avail'] . '</span>'; ?></label><?php
			} 
			?>
		</div>
		<div class="ebcal-event-content">
			<div class="left-column">
				<div class="desc">
					<?php echo pjSanitize::html($tpl['arr']['description']); ?>
				</div>
				<?php
				$now = time() + ((int) $tpl['option_arr']['o_booking_before_hours'] * 60 * 60);
				if($now > $tpl['arr']['event_start_ts'])
				{
					?><span class="ebcal-past-event"><?php __('front_label_past_event'); ?></span><?php
				}else{
					if(intval($tpl['arr']['total_avail']) == intval($tpl['arr']['total_booked']))
					{
						?><span class="ebcal-full-event"><?php __('front_label_full_event'); ?></span><?php
					}else{
						?>
						<div class="price">
							<form action="" method="post" name="ebcal_detail_form" class="ebcal-form">
								<?php
								if(count($tpl['price_arr']) > 0)
								{
									foreach($tpl['price_arr'] as $v)
									{
										?>
										<p>
											<label class="title"><?php echo stripslashes($v['title']);?></label>
											<select name="price_<?php echo $v['id']; ?>" lang="<?php echo $v['price'];?>" class="ebcal-field ebcal-price ebc-w50">
												<?php
												$max = intval($v['available']) - intval($v['cnt_booked']);
												$max = (int) $max < 1 ? 0 : $max;
												foreach (range(0, $max) as $i)
												{
													if (isset($_POST['price_' . $v['id']]) && $_POST['price_' . $v['id']] == $i)
													{
														?><option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?></option><?php
													} else {
														?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
													}
												}
												?>
											</select> x <?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?>
										</p>
										<?php
									}
									?>
									<p>
										<label class="title">&nbsp;</label>
										<input type="button" class="ebcal-button ebcal-buy-ticket" data-id="<?php echo $tpl['arr']['id']?>" value="<?php __('front_button_buy_ticket');?>">
									</p>
									<div class="ebcal-buy-ticket-notes"><?php __('front_buy_ticket_notes');?></div>
									<?php
								} 
								?>
							</form>
						</div>
						<?php
					}
				} 
				?>
			</div>
			<div class="image"><img src="<?php echo $event_image_url;?>" /></div>
		</div>
	</div>
</div>