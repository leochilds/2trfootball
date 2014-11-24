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
} else {
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	$start_date_time = pjUtil::formatDate(date('Y-m-d', $tpl['arr']['event_start_ts']), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', $tpl['arr']['event_start_ts']), 'H:i:s', $tpl['option_arr']['o_time_format']);
	$end_date_time = pjUtil::formatDate(date('Y-m-d', $tpl['arr']['event_end_ts']), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', $tpl['arr']['event_end_ts']), 'H:i:s', $tpl['option_arr']['o_time_format']); 
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionIndex"><?php __('menuEvents'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionCreate"><?php __('lblAddEvent'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblUpdateEvent'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate" method="post" id="frmUpdateEvent" class="form pj-form" enctype="multipart/form-data">
		<input type="hidden" name="event_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<input type="hidden" name="recurring_id" value="<?php echo $tpl['arr']['recurring_id']; ?>" />
		<input type="hidden" id="num_prices" name="num_prices" value="<?php echo count($tpl['price_arr']) == 0 ? 1 : count($tpl['price_arr']);?>" />
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('tabDetails'); ?></a></li>
				<li><a href="#tabs-2"><?php __('tabConfirmation'); ?></a></li>
				<li><a href="#tabs-3"><?php __('tabTerms'); ?></a></li>
				<li><a href="#tabs-4"><?php __('tabTicket'); ?></a></li>
				<li><a href="#tabs-5"><?php __('tabBookings'); ?></a></li>
				<li><a href="#tabs-6"><?php __('tabUsedTickets'); ?></a></li>
				<li><a href="#tabs-7"><?php __('tabInstall'); ?></a></li>
			</ul>
			<div id="tabs-1">
				<?php
				pjUtil::printNotice(__('infoEventTimeTitle', true), __('infoEventTimeDesc', true)); 
				?>	
				<p>
					<label class="title"><?php __('lblStartDateTime'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="event_start_ts" id="start" class="pj-form-field pointer w120 required datetimepick" value="<?php echo $start_date_time;?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						<input type="checkbox" id="o_show_start_time" name="o_show_start_time" class="l20 t10" value="F" <?php echo $tpl['arr']['o_show_start_time'] == 'F' ? 'checked="checked"' : null; ?>/>
						<label for="o_show_start_time"><?php __('lblHideTime');?></label>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblEndDateTime'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="event_end_ts" id="end" class="pj-form-field pointer w120 required datetimepick" value="<?php echo $end_date_time;?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						<input type="checkbox" id="o_show_end_time" name="o_show_end_time" class="l20 t10" value="F" <?php echo $tpl['arr']['o_show_end_time'] == 'F' ? 'checked="checked"' : null; ?>/>
						<label for="o_show_end_time"><?php __('lblHideTime');?></label>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblEventTitle'); ?></label>
					<span class="inline_block">
						<input type="text" name="event_title" id="event_title" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['event_title'])); ?>" class="pj-form-field w400 required" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblCategory'); ?></label>
					<span class="inline_block">
						<select name="category_id" id="category_id" class="pj-form-field w250">
							<option value="">-- <?php __('lblChoose');?> --</option>
							<?php
							foreach($tpl['category_arr'] as $v){
								?><option value="<?php echo $v['id']?>" <?php echo $v['id'] == $tpl['arr']['category_id'] ? 'selected="selected"' : null; ?>><?php echo $v['category']?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblLocation'); ?></label>
					<span class="inline_block">
						<input type="text" name="location" id="location" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['location'])); ?>" class="pj-form-field w400" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblDescription'); ?></label>
					<span class="inline_block">
						<textarea name="description" id="description" class="pj-form-field w450 h100"><?php echo stripslashes($tpl['arr']['description']);?></textarea>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblImage'); ?></label>
					<span class="inline_block">
						<input type="file" name="event_img" id="event_img" class="" />
					</span>
				</p>
				<?php
				if(!empty($tpl['arr']['event_thumb']))
				{
					if(is_file(PJ_INSTALL_PATH . $tpl['arr']['event_thumb']))
					{
						$img_url = PJ_INSTALL_URL . $tpl['arr']['event_thumb'];
						$large_url = PJ_INSTALL_URL . $tpl['arr']['event_img'];
						$img_name = basename($tpl['arr']['event_thumb']);
						?>
						<p id="image_container">
							<label class="title">&nbsp;</label>
							<span class="block float_left r10">
								<a target="_blank" href="<?php echo $large_url;?>"><img class="event_thumb" src="<?php echo $img_url; ?>" /></a>
							</span>
							<span class="block float_left">
								<a href="javascript:void(0);" class="delete-image" rev="<?php echo $tpl['arr']['id']?>"><?php echo strtolower(__('lnkDelete', true)); ?></a>
							</span>
						</p>
						<?php
					}
				} 
				
				pjUtil::printNotice(__('infoEventPriceTitle', true), __('infoEventPriceDesc', true)); 
				?>
				<div id="price_container">
					<?php
					if(count($tpl['price_arr']) > 0)
					{
						$has_bookings = 0;
						if(count($tpl['booking_arr']) > 0)
						{
							$has_bookings = 1;
						}
						for($i = 0; $i < count($tpl['price_arr']); $i++)
						{
							?>
							<p id="price_box_<?php echo $i + 1;?>"  class="price-box">
								<label class="title"><?php echo $i == 0 ? __('lblPrice', true) : '&nbsp;'; ?></label>
								<span class="block overflow">
									<span class="block float_left r10">
										<input type="text" name="title[x_<?php echo $tpl['price_arr'][$i]['id'];?>]" id="title<?php echo $i + 1;?>" value="<?php echo $tpl['price_arr'][$i]['title']?>" class="pj-form-field w120 required" />
									</span>
									<span class="block float_left r10 w130">
										<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
										<input type="text" name="price[x_<?php echo $tpl['price_arr'][$i]['id'];?>]" id="price<?php echo $i + 1;?>" value="<?php echo $tpl['price_arr'][$i]['price'];?>" class="pj-form-field number w60 required" />
									</span>
									<label class="block t10 float_left r5 content"><?php __('lblAvailable'); ?></label>
									<span class="block float_left r10">
										<input type="text" name="available[x_<?php echo $tpl['price_arr'][$i]['id'];?>]" id="available<?php echo $i + 1;?>" value="<?php echo $tpl['price_arr'][$i]['available'];?>" class="pj-form-field field-int w60" />
									</span>
									<?php
									if($has_bookings == 0)
									{ 
										?><input type="button" value="<?php __('btnRemove'); ?>" class="pj-button btn-remove-price" id="ebc_remove_price_<?php echo $i + 1;?>" lang="<?php echo $i + 1;?>" /><?php
									} 
									?>
								</span>

							</p>
							<?php
						} 
					}else{
						?>
						<p class="price-box">
							<label class="title"><?php __('lblPrice'); ?></label>
							<span class="block overflow">
								<span class="block float_left r10">
									<input type="text" name="title[1]" id="title1" value="<?php __('lblRegular');?>" class="pj-form-field w120 required" />
								</span>
								<span class="block float_left r10 w130">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" name="price[1]" id="price1" class="pj-form-field number w60 required" />
								</span>
								<label class="block t10 float_left r5 content"><?php __('lblAvailable'); ?></label>
								<span class="block float_left r10">
									<input type="text" name="available[1]" id="available1" value="5" class="pj-form-field w60 field-int" />
								</span>
							</span>
						</p>
						<?php
					}
					?>
				</div>
				<p>
					<label class="title">&nbsp;</label>
					<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button" id="ebc_add_price" />
				</p>
				
			</div><!-- tab-1 -->
			<div id="tabs-2">
				<?php
				pjUtil::printNotice(__('infoConfirmationTitle', true), __('infoConfirmationBody', true)); 
				?>
				<div class="t10 b10 l5">
					<span class="inline_block pt5"><?php __('lblConfirmationEmail'); ?></span>
					<a class="pj-form-langbar-tip listing-tip" href="#" title="<?php echo nl2br(__('lblConfirmationEmailTip', true)); ?>"></a>
				</div>
				<p>
					<label class="title"><?php __('lblSubject'); ?></label>
					<span class="inline_block">
						<input type="text" name="o_email_confirmation_subject" class="pj-form-field w500" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['o_email_confirmation_subject'])); ?>" />
					</span>
				</p>
				<p>
					<label class="title">
						<?php __('lblMessage'); ?>
						<br/><br/>
						<span class="tokens">
							<?php __('lblAvailabeTokens');?>
						</span>
					</label>
					<span class="inline_block">
						<textarea name="o_email_confirmation" class="pj-form-field" style="width: 560px; height: 400px;"><?php echo htmlspecialchars(stripslashes($tpl['arr']['o_email_confirmation'])); ?></textarea>
					</span>
				</p>
				
				<div class="t10 b10 l5">
					<span class="inline_block pt5"><?php __('lblPaymentEmail'); ?></span>
					<a class="pj-form-langbar-tip listing-tip" href="#" title="<?php echo nl2br(__('lblPaymentEmailTip', true)); ?>"></a>
				</div>
				<p>
					<label class="title"><?php __('lblSubject'); ?></label>
					<span class="inline_block">
						<input type="text" name="o_email_payment_subject" class="pj-form-field w500" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['o_email_payment_subject'])); ?>" />
					</span>
				</p>
				<p>
					<label class="title">
						<?php __('lblMessage'); ?>
						<br/><br/>
						<span class="tokens">
							<?php __('lblAvailabeTokens');?>
						</span>
					</label>
					<span class="inline_block">
						<textarea name="o_email_payment" class="pj-form-field" style="width: 560px; height: 400px;"><?php echo htmlspecialchars(stripslashes($tpl['arr']['o_email_payment'])); ?></textarea>
					</span>
				</p>
					
			</div><!-- tab-2 -->
			<div id="tabs-3">
				<?php
				pjUtil::printNotice(__('infoTermsTitle', true), __('infoTermsBody', true)); 
				?>
				<span class="block t10">
					<textarea name="terms" class="pj-form-field" style="width: 725px; height: 200px;"><?php echo htmlspecialchars(stripslashes($tpl['arr']['terms'])); ?></textarea>
				</span>	
			</div><!-- tab-3 -->
			<div id="tabs-4">
				<?php
				$info_body = __('infoTicketsImageBody', true);
				$info_body = str_replace("[STARTTAG]", "<a href='".PJ_INSTALL_URL."sample-ticket.jpg' target='_blank'>", $info_body);
				$info_body = str_replace("[ENDTAG]", "</a>", $info_body);
				pjUtil::printNotice(__('infoTicketsImageTitle', true), $info_body, false); 
				$ticket_info = "{Name}\n{Email}\n{Ticket}\n";
				if(!empty($tpl['arr']['ticket_info']))
				{
					$ticket_info = htmlspecialchars(stripslashes($tpl['arr']['ticket_info']));
				}
				?>
				<p>
					<label class="title">
						<?php __('lblTicketDetails'); ?>
						<br/><br/>
						<span class="tokens">
							<?php __('lblImageTokens');?>
						</span>
					</label>
					<span class="inline_block">
						<textarea name="ticket_info" class="pj-form-field" style="width: 560px; height: 200px;"><?php echo $ticket_info; ?></textarea>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblTicketImage'); ?></label>
					<span class="inline_block">
						<input type="file" name="ticket_img" id="ticket_img" class="pj-form-field" />
					</span>
				</p>
				<?php
				if (!empty($tpl['arr']['ticket_img']) && is_file(PJ_INSTALL_PATH . $tpl['arr']['ticket_img']))
				{
					 ?>
					 <p>
						<label class="title">&nbsp;</label>
						<span class="inline_block">
							<a href="<?php echo PJ_INSTALL_URL . $tpl['arr']['ticket_img']; ?>?<?php echo rand(9999,99999); ?>" target="_blank"><img class="ticket-img" src="<?php echo PJ_INSTALL_URL . $tpl['arr']['ticket_img'];?>" /></a>
						</span>
					</p>
					 <?php
				}
				?>
				
			</div><!-- tab-4 -->
			<div id="tabs-5">
				<?php
				pjUtil::printNotice(__('infoBookingsTitle', true), __('infoBookingsBody', true)); 
				
				if(count($tpl['booking_arr']) > 0)
				{
					$bk_statuses = __('booking_statuses', true);
					?>
					<table class="tbl-booking">
						<thead>
							<tr>
								<th width="100"><?php __('lblID');?></th>
								<th width="140"><?php __('lblBookingName');?></th>
								<th><?php __('lblBookingEmail');?></th>
								<th width="100"><?php __('lblTickets');?></th>
								<th width="80"><?php __('lblStatus');?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							foreach($tpl['booking_arr'] as $v)
							{
								?>
								<tr class="<?php echo $i % 2 == 0 ? 'even' : 'odd'; ?>">
									<?php
									if($v['booking_status'] == 'pending')
									{ 
										?><td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&id=<?php echo $v['id'];?>"><?php echo $v['unique_id'];?></a></td><?php
									}else{
										?><td><?php echo $v['unique_id'];?></td><?php
									} 
									?>
									<td><?php echo stripslashes($v['customer_name']);?></td>
									<td><?php echo stripslashes($v['customer_email']);?></td>
									<td>
										<?php
										$temp_arr = $tpl['detail_arr'][$v['id']];
										if(count($temp_arr) > 0)
										{
											foreach($temp_arr as $d)
											{
												echo $d['cnt'] . ' x ' . $d['price_title'] . '<br/>';
											}
										}else{
											?>&nbsp;<?php
										}	
										?>
									</td>
									<td><?php echo stripslashes($bk_statuses[$v['booking_status']]);?></td>
								</tr>
								<?php
								$i++;
							} 
							?>
						</tbody>
					</table>
					<?php
				} 
				?>
				<div class="form pj-form t20">
					
					<p class="pj-short">
						<label class="title"><?php __('lblCurrentDateTime');?>:</label>
						<span class="inline_block">
							<label class="content"><?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s'), 'H:i:s', $tpl['option_arr']['o_time_format']);?></label>
						</span>
					</p>
					<p  class="pj-short">
						<label class="title"><?php __('lblTotalBookings');?>:</label>
						<span class="inline_block">
							<label class="content"><?php echo count($tpl['booking_arr']);?></label>
						</span>
					</p>
					<p  class="pj-short">
						<label class="title"><?php __('lblTotalTickets');?>:</label>
						<span class="inline_block">
							<label class="content"><?php echo $tpl['total_tickets'];?></label>
						</span>
					</p>
					<?php
					if(isset($tpl['print_file']))
					{ 
						?>
						<p class="pj-short">
							<label class="title">&nbsp;</label>
							<a target="_blank" href="<?php echo $tpl['print_file'];?>"><input type="button" value="<?php __('btnPrint'); ?>" class="pj-button" /></a>
						</p>
						<?php
					} 
					?>
				</div>
			</div><!-- tab-5 -->
			<div id="tabs-6">
				<?php
				pjUtil::printNotice(__('infoUsedTicketsTitle', true), __('infoUsedTicketsBody', true)); 
				
				if(count($tpl['tickets_arr']) > 0)
				{
					?>
					<table class="tbl-booking">
						<thead>
							<tr>
								<th width="140"><?php __('lblBookingName');?></th>
								<th><?php __('lblBookingEmail');?></th>
								<th width="120"><?php __('lblTicketType');?></th>
								<th width="100"><?php __('lblUsedTickets');?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							foreach($tpl['tickets_arr'] as $v)
							{
								?>
								<tr class="<?php echo $i % 2 == 0 ? 'even' : 'odd'; ?>">
									<td><?php echo stripslashes($v['customer_name']);?></td>
									<td><?php echo stripslashes($v['customer_email']);?></td>
									<td><?php echo $v['price_title'];?></td>
									<td><?php echo stripslashes($v['ticket_id']);?></td>
								</tr>
								<?php
								$i++;
							} 
							?>
						</tbody>
					</table>
					<?php
				} 
				?>
				<div class="form pj-form t20">
					
					<p class="pj-short">
						<label class="title"><?php __('lblCurrentDateTime');?>:</label>
						<span class="inline_block">
							<label class="content"><?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s'), 'H:i:s', $tpl['option_arr']['o_time_format']);?></label>
						</span>
					</p>
					<p  class="pj-short">
						<label class="title"><?php __('lblTotalAvailable');?>:</label>
						<span class="inline_block">
							<label class="content"><?php echo $tpl['arr']['total_avail'];?></label>
						</span>
					</p>
					<p class="pj-short">
						<label class="title"><?php __('lblBookedTickets');?>:</label>
						<span class="inline_block">
							<label class="content"><?php echo $tpl['total_tickets'];?></label>
						</span>
					</p>
					<p class="pj-short">
						<label class="title"><?php __('lblUsedTickets');?>:</label>
						<span class="inline_block">
							<label class="content"><?php echo $tpl['used_tickets'];?></label>
						</span>
					</p>
					<?php
					if(isset($tpl['print_tickets_file']))
					{ 
						?>
						<p class="pj-short">
							<label class="title">&nbsp;</label>
							<a target="_blank" href="<?php echo $tpl['print_tickets_file'];?>"><input type="button" value="<?php __('btnPrint'); ?>" class="pj-button" /></a>
						</p>
						<?php
					} 
					?>
				</div>
			</div><!-- tab-6 -->
			<div id="tabs-7">
				<?php pjUtil::printNotice(NULL, __('lblInstallPhp1Title', true), false, false); ?>
				<p>
					<label class="title"><?php __('opt_o_layout'); ?></label>
					<span class="inline_block">
						<select name="layout" id="layout" class="pj-form-field w150">
							<?php
							$layouts = __('layouts', true);
							foreach($layouts as $k => $v)
							{
								if($k != 'layout_1' && $k != 'layout_2') continue;
								?><option value="<?php echo $k?>"><?php echo $v;?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstall_2a'); ?></p>
				<textarea id="install_step_1" class="pj-form-field w700 textarea_install" style="overflow: auto; height:120px"></textarea>
			</div><!-- tab-6 -->
			
			<div id="button_container">
				<?php
				if($tpl['number_of_events'] > 1)
				{ 
					$text_apply = str_replace('{numevents}', $tpl['number_of_events'], __('lblApplyRecurring', true));
					?>
					<p>
						<label class="title">&nbsp;</label>
						<span class="inline_block">
							<input type="checkbox" class="float_left t5 r10" value="1" id="apply_recurring" name="apply_recurring">
							<label for="apply_recurring" class="apply-recurring"><?php echo $text_apply;?></label>
						</span>
					</p>
					<?php
				} 
				?>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminEvents&action=pjActionIndex';" />
				</p>
			</div>
		</div><!-- Tabs -->
	</form>
	
	<div id="dialogDeleteImage" title="<?php __('lblDeleteImageTitle'); ?>" style="display:none">
		<p><?php __('lblDeleteImageBody'); ?></p>
	</div>
	
	<input type="hidden" name="css_file" id="css_file" value="front_layout_1.css" class="pj-form-field w400" />	
	<div id="clone_step_1" style="display:none;">&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionLoadCss{CSSFile}" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionLoadJs"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&amp;action=pjActionLoad&amp;layout={LAYOUT}&amp;view=list&amp;icons=T&amp;cats=T&amp;event_id=<?php echo $tpl['arr']['id'];?>"&gt;&lt;/script&gt;</div>

	<div id="clone_container" style="display:none;">
		<p id="price_box_{index}" class="price-box">
			<label class="title">&nbsp;</label>
			<span class="block overflow">
				<span class="block float_left r10">
					<input type="text" name="title[{index}]" id="title{index}" class="pj-form-field w120 required" />
				</span>
				<span class="block float_left r10 w130">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="price[{index}]" id="price{index}" class="pj-form-field number w60 required" />
				</span>
				<label class="block t10 float_left r5 content"><?php __('lblAvailable'); ?></label>
				<span class="block float_left r10">
					<input type="text" name="available[{index}]" id="available{index}" value="5" class="pj-form-field {fieldint} w60" />
				</span>
				<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button btn-remove-price" id="ebc_remove_price_{index}" lang="{index}" />
			</span>
		</p>
	</div>
	<div id="clone_container_1" style="display:none;">
		<p id="price_box_{index}" class="price-box">
			<label class="title"><?php __('lblPrice');?></label>
			<span class="block overflow">
				<span class="block float_left r10">
					<input type="text" name="title[]" id="title{index}" class="pj-form-field w120 required" />
				</span>
				<span class="block float_left r10 w130">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="price[]" id="price{index}" class="pj-form-field number w60 required" />
				</span>
				<label class="block t10 float_left r5 content"><?php __('lblAvailable'); ?></label>
				<span class="block float_left r10">
					<input type="text" name="available[]" id="available{index}" value="5" class="pj-form-field {fieldint} w60" />
				</span>
			</span>
		</p>
	</div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.add_time = "<?php __('lnkAddTime'); ?>";
	myLabel.add_start_time = "<?php __('lnkAddStartTime'); ?>";
	myLabel.add_end_time = "<?php __('lnkAddEndTime'); ?>";
	myLabel.lblInvalidPrice = "<?php __('lblInvalidPrice'); ?>";
	myLabel.lblFieldRequired = "<?php __('lblFieldRequired'); ?>";
	</script>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{		
		$tab_id = $_GET['tab_id'];
		//$tab_id = (int) $_GET['tab_id'] - 1;
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
		<script type="text/javascript">
		(function ($) {
			$(function () {
				$("#tabs").tabs("option", "selected", <?php echo $tab_id; ?>);
			});
		})(jQuery);
		</script>
		<?php
	}
}
?>