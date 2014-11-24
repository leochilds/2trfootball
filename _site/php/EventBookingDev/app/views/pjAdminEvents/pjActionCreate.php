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
	if(isset($_GET['id']))
	{
		$start_date_time = pjUtil::formatDate(date('Y-m-d', $tpl['arr']['event_start_ts']), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', $tpl['arr']['event_start_ts']), 'H:i:s', $tpl['option_arr']['o_time_format']);
		$end_date_time = pjUtil::formatDate(date('Y-m-d', $tpl['arr']['event_end_ts']), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', $tpl['arr']['event_end_ts']), 'H:i:s', $tpl['option_arr']['o_time_format']);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionIndex"><?php __('menuEvents'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionCreate"><?php __('lblAddEvent'); ?></a></li>
		</ul>
	</div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionCreate" method="post" id="frmCreateEvent" class="pj-form form" enctype="multipart/form-data">
		<input type="hidden" name="event_create" value="1" />
		<input type="hidden" id="time_flag" name="time_flag" value="0" />
		<input type="hidden" id="num_prices" name="num_prices" value="1" />
		<input type="hidden" id="copy" name="copy" value="<?php echo isset($_GET['id']) ? $_GET['id'] : 0;?>" />
		<input type="hidden" id="copy_image" name="copy_image" value="1" />
		<?php
		pjUtil::printNotice(__('infoEventTimeTitle', true), __('infoEventTimeDesc', true)); 
		?>
		<p>
			<label class="title"><?php __('lblStartDateTime'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-after">
				<input type="text" name="event_start_ts" id="start" class="pj-form-field pointer w120 required datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				<input type="checkbox" id="o_show_start_time" name="o_show_start_time" class="l20 t10"  value="F"/>
				<label for="o_show_start_time"><?php __('lblHideTime');?></label>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblEndDateTime'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-after">
				<input type="text" name="event_end_ts" id="end" class="pj-form-field pointer w120 required datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				<input type="checkbox" id="o_show_end_time" name="o_show_end_time" class="l20 t10" value="F"/>
				<label for="o_show_end_time"><?php __('lblHideTime');?></label>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblEventTitle'); ?></label>
			<span class="inline_block">
				<input type="text" name="event_title" id="event_title" value="<?php echo isset($_GET['id']) ? htmlspecialchars(stripslashes($tpl['arr']['event_title'])) : null; ?>" class="pj-form-field w400 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCategory'); ?></label>
			<span class="inline_block">
				<select name="category_id" id="category_id" class="pj-form-field w250">
					<option value="">-- <?php __('lblChoose');?> --</option>
					<?php
					foreach($tpl['category_arr'] as $v){
						?><option value="<?php echo $v['id']?>"<?php echo isset($_GET['id']) ? ($tpl['arr']['category_id'] == $v['id'] ? ' selected="selected"' : null) : null?>><?php echo $v['category']?></option><?php
					} 
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblLocation'); ?></label>
			<span class="inline_block">
				<input type="text" name="location" id="location" value="<?php echo isset($_GET['id']) ? htmlspecialchars(stripslashes($tpl['arr']['location'])) : null; ?>" class="pj-form-field w400" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblDescription'); ?></label>
			<span class="inline_block">
				<textarea name="description" id="description" class="pj-form-field w450 h100"><?php echo isset($_GET['id']) ? stripslashes($tpl['arr']['description']) : null;?></textarea>
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
						<a href="javascript:void(0);" class="delete-image"><?php echo strtolower(__('lnkDelete', true)); ?></a>
					</span>
				</p>
				<?php
			}
		} 
		?>
		<?php
		pjUtil::printNotice(__('infoEventPriceTitle', true), __('infoEventPriceDesc', true)); 
		?>
		<div id="price_container">
			<?php
			if(isset($tpl['price_arr']) && count($tpl['price_arr']) > 0)
			{
				for($i = 0; $i < count($tpl['price_arr']); $i++)
				{
					?>
					<p id="price_box_<?php echo $i + 1;?>"  class="price-box">
						<label class="title"><?php echo $i == 0 ? __('lblPrice', true) : '&nbsp;'; ?></label>
						<span class="block overflow">
							<span class="block float_left r10">
								<input type="text" name="title[<?php echo $i + 1;?>]" id="title<?php echo $i + 1;?>" value="<?php echo $tpl['price_arr'][$i]['title']?>" class="pj-form-field w120 required" />
							</span>
							<span class="block float_left r10 w130">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="price[<?php echo $i + 1;?>]" id="price<?php echo $i + 1;?>" value="<?php echo $tpl['price_arr'][$i]['price'];?>" class="pj-form-field number w60 required" />
							</span>
							<label class="block t10 float_left r5 content"><?php __('lblAvailable'); ?></label>
							<span class="block float_left r10">
								<input type="text" name="available[<?php echo $i + 1;?>]" id="available<?php echo $i + 1;?>" value="<?php echo $tpl['price_arr'][$i]['available'];?>" class="pj-form-field field-int w60" />
							</span>
							<?php
							if($i > 0)
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
							<input type="text" name="available[1]" id="available1" value="5" class="pj-form-field field-int w60" />
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
		<p>
			<label class="title"><?php __('lblRepeat'); ?></label>
			<?php
			$repeat_arr = __('repeatarr', true); 
			?>
			<select name="repeat" id="repeat" class="pj-form-field">
				<option value="none">-- <?php echo $repeat_arr['none'];?> --</option>
				<option value="daily"><?php echo $repeat_arr['daily'];?></option>
				<option value="weekly"><?php echo $repeat_arr['weekly'];?></option>
				<option value="monthly"><?php echo $repeat_arr['monthly'];?></option>
				<option value="quarterly"><?php echo $repeat_arr['quarterly'];?></option>
				<option value="yearly"><?php echo $repeat_arr['yearly'];?></option>
				<option value="custom"><?php echo $repeat_arr['custom'];?></option>
			</select>
		</p>
		<div id="repeat_box" style="display:none;">
			<p id="repeat_daily" style="display:none;">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<?php __('lblRepeatEveryDay');?>
				</span>
			</p>
			<p id="repeat_weekly" style="display:none;">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<?php __('lblRepeatEveryWeek');?>
				</span>
			</p>
			<div id="repeat_monthly" style="display:none;">
				<p>
					<label class="title"><?php __('lblOn');?></label>
					<span class="inline_block">
						<select id="repeat-monthly-date" name="repeat-monthly-date" class="pj-form-field">
							<?php
							$monthly_date = __('monthly_date', true); 
							ksort($monthly_date);
							foreach($monthly_date as $k => $v){
								?><option value="<?php echo $k;?>"><?php echo $v;?></option><?php
							}
							?>
						</select>
					</span>
					<span class="inline_block">
						<?php __('lblOfTheMonth');?>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblOrEach');?></label>
					<span class="inline_block">
						<select id="repeat-monthly-each" name="repeat-monthly-each" class="pj-form-field">
							<?php
							$monthly_each = __('monthly_each', true); 
							?>
							<option value="first"><?php echo $monthly_each['first'];?></option>
							<option value="second"><?php echo $monthly_each['second'];?></option>
							<option value="third"><?php echo $monthly_each['third'];?></option>
							<option value="fourth"><?php echo $monthly_each['fourth'];?></option>
						</select>
					</span>
					<span class="inline_block">
						<select id="repeat-monthly-day" name="repeat-monthly-day" class="pj-form-field">
							<?php
							$day_names = __('days', true); 
							ksort($day_names);
							foreach($day_names as $k => $v){
								?><option value="<?php echo $v;?>"><?php echo substr($v, 0, 3);?></option><?php
							}
							?>
						</select>
					</span>
					<span class="inline_block">
						<?php __('lblOfTheMonth');?>
					</span>
				</p>
			</div>
			<p id="repeat_quarterly" style="display:none;">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<?php __('lblRepeatEveryQuarter');?>
				</span>
			</p>
			<p id="repeat_yearly" style="display:none;">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<?php __('lblRepeatEveryYear');?>
				</span>
			</p>
			<p id="repeat_custom" style="display:none;">
				<label class="title"><?php __('lblEach');?></label>
				<span class="inline_block">
					<input type="text" name="repeat-custom-days" id="repeat-custom-days" class="pj-form-field w50" />
				</span>
				<span class="inline_block">
					<?php __('lblDays');?>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblEndRecurringOn'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-after float_left r10">
					<input type="text" name="end_repeat_date" id="end_repeat_date" class="pj-form-field pointer w80 datepick" value="" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
				<label class="content r10"><?php echo strtolower(__('lblOrRepeat', true)); ?></label>
				<span class="inline_block float_left r10">
					<input type="text" name="end_repeat_times" id="end_repeat_times" class="pj-form-field w50" />
				</span>
				<label class="content r10"><?php __('lblTimes');?></label>
			</p>
		</div>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminEvents&action=pjActionIndex';" />
		</p>
	</form>
	
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
	
	<script type="text/javascript">
	
	var myLabel = myLabel || {};
	myLabel.add_time = "<?php __('lnkAddTime'); ?>";
	myLabel.add_start_time = "<?php __('lnkAddStartTime'); ?>";
	myLabel.add_end_time = "<?php __('lnkAddEndTime'); ?>";
	myLabel.lblInvalidPrice = "<?php __('lblInvalidPrice'); ?>";
	myLabel.lblFieldRequired = "<?php __('lblFieldRequired'); ?>";
	</script>
	<?php
}
?>