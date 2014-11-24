<?php $index = $_GET['index'];?>
<div class="ebcal-booking-form">
	<div class="ebcal-form-header">
		<div class="event-datetime">
			<?php __('front_label_event_details');?>
		</div>
		<a href="javascript:void(0);" class="ebcal-close-form"></a>
	</div>
	<div class="ebcal-event-detail">
		<label><?php echo stripslashes($tpl['arr']['event_title']) ;?></label>
		<span><?php echo $event_date = pjUtil::getEventDateTime($tpl['arr']['event_start_ts'], $tpl['arr']['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $tpl['arr']['o_show_start_time'], $tpl['arr']['o_show_end_time']) ;?></span>
	</div>
	<div class="ebcal-form-container">
		<form action="" method="post" name="ebcal_booking_form" class="ebcal-form">
			<input type="hidden" name="total_price" value="0" />
			<div class="ebcal-form-header">
				<div class="event-datetime">
					<?php __('front_label_select_tickets');?>
				</div>
			</div>
			<div class="ebcal-form-inner-container">
				<?php
				$front_error = __('front_error', true);
				$front_required = __('front_required', true);
				$cc_types = __('cc_types', true);
				$months = __('months', true);
				ksort($months);
				
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
				} 
				?>
				<p class="ebcal-price-related" style="display: none">
					<label class="title"><?php __('front_label_price'); ?></label>
					<label id="ebcal_price_box_<?php echo $index; ?>" class="content">---</label>
				</p>
				<p class="ebcal-price-related" style="display: none">
					<label class="title"><?php __('front_label_tax'); ?></label>
					<label id="ebcal_tax_box_<?php echo $index; ?>" class="content">---</label>
				</p>
				<p class="ebcal-price-related" style="display: none">
					<label class="title"><?php __('front_label_total_price'); ?></label>
					<label id="ebcal_total_amount_box_<?php echo $index; ?>" class="content">---</label>
				</p>
				<p class="ebcal-price-related" style="display: none">
					<label class="title"><?php __('front_label_deposit'); ?></label>
					<label id="ebcal_deposit_box_<?php echo $index; ?>" class="content">---</label>
				</p>
			</div>
			<div class="ebcal-form-header">
				<div class="event-datetime">
					<?php __('front_label_fill_in');?>
				</div>
			</div>
			<div class="ebcal-form-inner-container">
				<?php
				if (in_array($tpl['option_arr']['o_bf_include_name'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_name');?></label>
						<input type="text" name="customer_name" class="ebcal-field ebc-w300<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['name']); ?>" />
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_email');?></label>
						<input type="text" name="customer_email" class="ebcal-field ebcal-email ebc-w300<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['email']); ?>" />
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_phone');?></label>
						<input type="text" name="customer_phone" class="ebcal-field ebc-w300<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['phone']); ?>" />
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_country');?></label>
						<select name="customer_country" class="ebcal-field ebc-w300<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' ebcal-required' : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['country']); ?>">
							<option value="">---</option>
							<?php
							if (isset($tpl['country_arr']) && is_array($tpl['country_arr']))
							{
								foreach ($tpl['country_arr'] as $v)
								{
									?><option value="<?php echo $v['id']; ?>"<?php echo isset($_POST['customer_country']) && $_POST['customer_country'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['country_title']); ?></option><?php
								}
							}
							?>
						</select>
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_city');?></label>
						<input type="text" name="customer_city" class="ebcal-field ebc-w300<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['customer_city']) ? htmlspecialchars($_POST['customer_city']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['city']); ?>" />
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_state');?></label>
						<input type="text" name="customer_state" class="ebcal-field ebc-w300<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['customer_state']) ? htmlspecialchars($_POST['customer_state']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['state']); ?>" />
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_zip');?></label>
						<input type="text" name="customer_zip" class="ebcal-field ebc-w300<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['customer_zip']) ? htmlspecialchars($_POST['customer_zip']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['zip']); ?>" maxlength="8" />
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_address');?></label>
						<input type="text" name="customer_address" class="ebcal-field ebc-w300<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['customer_address']) ? htmlspecialchars($_POST['customer_address']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['address']); ?>" />
					</p>
					<?php
				}
				if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php __('front_label_notes');?></label>
						<textarea name="customer_notes" class="ebcal-field ebc-w300 ebc-h100<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' ebcal-required' : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['notes']); ?>"><?php echo isset($_POST['customer_notes']) ? htmlspecialchars($_POST['customer_notes']) : NULL; ?></textarea>
					</p>
					<?php
				}
				if ($tpl['option_arr']['o_payment_disable'] == 'No')
				{
					?>
					<p class="ebcal-price-related" style="display: none;">
						<label class="title"><?php __('front_label_payment_method'); ?></label>
						<select name="payment_method" class="ebcal-field ebc-w300 ebcal-required" lang="<?php echo $front_error['payment']; ?>">
							<option value="">---</option>
							<?php
							$payment_methods = __('payment_methods', true);
							foreach ($payment_methods as $k => $v)
							{
								if (@$tpl['option_arr']['o_allow_' . $k] == 'Yes')
								{
									?><option value="<?php echo $k; ?>"<?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v); ?></option><?php
								}
							}
							?>
						</select>
					</p>
					
					<p class="ebcal-bankdata" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank' ? 'block' : 'none'; ?>">
						<label class="title">&nbsp;</label>
						<label class="content ebc-overflow"><?php echo nl2br($tpl['option_arr']['o_bank_account']);?></label>
					</p>
					
					<div class="ebcal-price-related" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
						<p class="ebcal-ccdata" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<label class="title"><?php __('front_label_cc_type'); ?></label>
							<select name="cc_type" class="ebcal-field ebc-w300<?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? ' ebcal-required' : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['cc_type']); ?>">
								<option value="">---</option>
								<?php
								foreach ($cc_types as $k => $v)
								{
									if (isset($_POST['cc_type']) && $_POST['cc_type'] == $k)
									{
										?><option value="<?php echo $k; ?>" selected="selected"><?php echo $v; ?></option><?php
									} else {
										?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
									}
								}
								?>
							</select>
						</p>
						<p class="ebcal-ccdata" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<label class="title"><?php __('front_label_cc_number'); ?></label>
							<input type="text" name="cc_num" class="ebcal-field ebc-w300<?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['cc_num']) ? htmlspecialchars($_POST['cc_num']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['cc_number']); ?>" />
						</p>
						<p class="ebcal-ccdata" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<label class="title"><?php __('front_label_cc_expiration_date'); ?></label>
							<select name="cc_exp_month" class="ebcal-field ebc-w120<?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? ' ebcal-required' : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['cc_exp_month']); ?>">
								<option value="">---</option>
								<?php
								foreach ($months as $key => $val)
								{
									?><option value="<?php echo $key;?>" <?php echo isset($_POST['cc_exp_month']) && $_POST['cc_exp_month'] == $key ? 'selected="selected"' : NULL;?> ><?php echo $val;?></option><?php
								}
								?>
							</select>
							<select name="cc_exp_year" class="ebcal-field ebc-w120<?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? ' ebcal-required' : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['cc_exp_year']); ?>">
								<option value="">---</option>
								<?php
								$y = (int) date('Y');
								for ($i = $y; $i <= $y + 10; $i++)
								{
									?><option value="<?php echo $i; ?>"<?php echo isset($_POST['cc_exp_year']) && (int) $_POST['cc_exp_year'] == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
								}
								?>
							</select>
						</p>
						<p class="ebcal-ccdata" style="display: <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<label class="title"><?php __('front_label_cc_code'); ?></label>
							<input type="text" name="cc_code" class="ebcal-field ebc-w300<?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard' ? ' ebcal-required' : NULL; ?>" value="<?php echo isset($_POST['cc_code']) ? htmlspecialchars($_POST['cc_code']) : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['cc_code']); ?>" />
						</p>
					</div>
					<?php 
				}
				if (in_array($tpl['option_arr']['o_bf_include_captcha'], array(2, 3)))
				{
					?>
					<p>
						<label class="title"><?php echo __('front_label_captcha'); ?></label>
						<span class="ebc-block ebc-float-left">
							<img src="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 999999); ?>" alt="Captcha" />
							<input type="text" name="captcha" maxlength="6" class="ebcal-field ebc-w80<?php echo $tpl['option_arr']['o_bf_include_captcha'] == 3 ? ' ebcal-required' : NULL; ?>" lang="<?php echo htmlspecialchars($front_required['captcha']); ?>" />
						</span>
					</p>
					<?php
				}
				if(!empty($tpl['arr']['terms']))
				{
					?>
					<p>
						<label class="title">&nbsp;</label>
						<span class="ebc-term-content ebc-block ebc-float-left ebc-l120">
							<?php echo nl2br(stripslashes($tpl['arr']['terms']));?>
						</span>
						<span class="ebc-block ebc-float-left ebc-l120 ebc-t10">
							<input type="checkbox" id="ebc_accept_term_<?php echo $index;?>" name="accept_term" class="ebcal-field ebcal-required" <?php echo isset($_POST['accept_term']) ? 'checked="checked"' : NULL; ?> lang="<?php echo htmlspecialchars($front_required['accept_terms']); ?>" />
							<label for="ebc_accept_term_<?php echo $index;?>"><?php __('front_label_accept_terms');?></label>
						</span>
					</p>
					<?php
				}
				?>
				<p class="ebcal-error-container" style="display: none"></p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="button" value="<?php __('front_button_submit'); ?>" name="ebcal_booking_form_submit" class="ebcal-button" />
					<input type="button" value="<?php __('front_button_cancel'); ?>" name="ebcal_booking_form_cancel" class="ebcal-button ebcal-buy-ticket" />
				</p>
			</div>
		</form>
	</div>
</div>