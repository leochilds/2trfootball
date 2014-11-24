<?php
$index = $_GET['index']; 
?>
<div class="ebcal-header">
	<div class="ebcal-menu">
		<a class="ebcal-back" rev="<?php echo $tpl['arr']['id'];?>" href="#"><?php __('front_button_back');?></a>
	</div>
</div>
	
<div class="ebcal-form-container">
	<div class="ebcal-form-inner">
		<div class="ebcal-title">
			<label><?php echo stripslashes($tpl['arr']['event_title']) ;?></label>
			<span><?php echo $event_date = pjUtil::getEventDateTime($tpl['arr']['event_start_ts'], $tpl['arr']['event_end_ts'], $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format'], $tpl['arr']['o_show_start_time'], $tpl['arr']['o_show_end_time']) ;?></span>
		</div>
		
		<form action="" method="post" name="ebcal_booking_summary" class="ebcal-form" style="width: auto">
			<div class="ebcal-price-container">
				<?php
				$cc_types = __('cc_types', true);
				$months = __('months', true); ksort($months);
				$payment_methods = __('payment_methods', true);
				$font_messages = __('front_message', true);
				?>
				<p>
					<label class="title"><?php __('front_label_price'); ?></label>
					<label class="content"><?php echo pjUtil::formatCurrencySign(number_format($tpl['amount']['price'], 2, '.', ','), $tpl['option_arr']['o_currency']); ?></label>
				</p>
				<p>
					<label class="title"><?php __('front_label_tax'); ?></label>
					<label class="content"><?php echo pjUtil::formatCurrencySign(number_format($tpl['amount']['tax'], 2, '.', ','), $tpl['option_arr']['o_currency']); ?></label>
				</p>
				<p>
					<label class="title"><?php __('front_label_total_price'); ?></label>
					<label class="content"><?php echo pjUtil::formatCurrencySign(number_format($tpl['amount']['total'], 2, '.', ','), $tpl['option_arr']['o_currency']); ?></label>
				</p>
				<p>
					<label class="title"><?php __('front_label_deposit'); ?></label>
					<label class="content"><?php echo pjUtil::formatCurrencySign(number_format($tpl['amount']['deposit'], 2, '.', ','), $tpl['option_arr']['o_currency']); ?></label>
				</p>
			</div>
			<div class="ebcal-form-inner-container">
				<?php
				if (in_array($tpl['option_arr']['o_bf_include_name'], array(2, 3)) && isset($_POST['customer_name']))
				{
					if(!empty($_POST['customer_name']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_name'); ?></label>
							<label class="content"><?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : NULL; ?></label>
						</p>
						<?php
					}
				}
				if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)) && isset($_POST['customer_email']))
				{
					if(!empty($_POST['customer_email']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_email'); ?></label>
							<label class="content"><?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : NULL; ?></label>
						</p>
						<?php
					}
				}
				if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)) && isset($_POST['customer_phone']))
				{
					if(!empty($_POST['customer_phone']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_phone'); ?></label>
							<label class="content"><?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : NULL; ?></label>
						</p>
						<?php
					}
				}
				if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)) && isset($_POST['customer_country']))
				{
					if(!empty($_POST['customer_country']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_country'); ?></label>
							<label class="content">
							<?php
							if (isset($tpl['country_arr']) && is_array($tpl['country_arr']))
							{
								foreach ($tpl['country_arr'] as $v)
								{
									if (isset($_POST['customer_country']) && $_POST['customer_country'] == $v['id'])
									{
										echo htmlspecialchars($v['country_title']);
										break;
									}
								}
							}
							?>
							</label>
						</p>
						<?php
					}
				}
				if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)) && isset($_POST['customer_city']))
				{
					if(!empty($_POST['customer_city']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_city'); ?></label>
							<label class="content"><?php echo isset($_POST['customer_city']) ? htmlspecialchars($_POST['customer_city']) : NULL; ?></label>
						</p>
						<?php
					}
				}
				if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)) && isset($_POST['customer_state']))
				{
					if(!empty($_POST['customer_state']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_state'); ?></label>
							<label class="content"><?php echo isset($_POST['customer_state']) ? htmlspecialchars($_POST['customer_state']) : NULL; ?></label>
						</p>
						<?php
					}
				}
				if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)) && isset($_POST['customer_zip']))
				{
					if(!empty($_POST['customer_zip']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_zip'); ?></label>
							<label class="content"><?php echo isset($_POST['customer_zip']) ? htmlspecialchars($_POST['customer_zip']) : NULL; ?></label>
						</p>
						<?php
					}
				}
				if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3)) && isset($_POST['customer_address']))
				{
					if(!empty($_POST['customer_zip']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_address'); ?></label>
							<label class="content"><?php echo isset($_POST['customer_address']) ? htmlspecialchars($_POST['customer_address']) : NULL; ?></label>
						</p>
						<?php
					}
				}
				if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)) && isset($_POST['customer_notes']))
				{
					if(!empty($_POST['customer_zip']))
					{
						?>
						<p>
							<label class="title"><?php __('front_label_notes'); ?></label>
							<label class="content"><?php echo isset($_POST['customer_notes']) ? nl2br(htmlspecialchars($_POST['customer_notes'])) : NULL; ?></label>
						</p>
						<?php
					}
				}
				if ($tpl['option_arr']['o_payment_disable'] == 'No')
				{
					if (isset($tpl['amount']['price']) && (float) $tpl['amount']['price'] > 0)
					{
						?>
						<p>
							<label class="title"><?php __('front_label_payment_method'); ?></label>
							<label class="content"><?php echo isset($_POST['payment_method']) ? $payment_methods[$_POST['payment_method']] : NULL; ?></label>
						</p>
						<?php
						if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard')
						{
							?>
							<p>
								<label class="title"><?php __('front_label_cc_type'); ?></label>
								<label class="content"><?php echo $cc_types[$_POST['cc_type']]; ?></label>
							</p>
							<p>
								<label class="title"><?php __('front_label_cc_number'); ?></label>
								<label class="content"><?php echo isset($_POST['cc_num']) ? $_POST['cc_num'] : NULL; ?></label>
							</p>
							<p>
								<label class="title"><?php __('front_label_cc_expiration_date'); ?></label>
								<label class="content"><?php echo isset($_POST['cc_exp_year']) ? $_POST['cc_exp_year'] : NULL; ?>-<?php echo isset($_POST['cc_exp_month']) ? $_POST['cc_exp_month'] : NULL;?></label>
							</p>
							<p>
								<label class="title"><?php __('front_label_cc_code'); ?></label>
								<label class="content"><?php echo isset($_POST['cc_code']) ? $_POST['cc_code'] : NULL; ?></label>
							</p>
							<?php
						}
						if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank')
						{
							?>
							<p>
								<label class="title">&nbsp;</label>
								<label class="content ebc-overflow"><?php echo nl2br($tpl['option_arr']['o_bank_account']);?></label>
							</p>
							<?php
						}
					}
				}
				?>
			</div>
			<p class="ebcal-error-container" style="display: none"></p>
			<p id="ebcal-message-container_<?php echo $index;?>" style="display:none;"><?php echo $font_messages[5]; ?></p>
			
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" name="ebcal_booking_summary_submit" class="ebcal-button ebc-float-left" data-id="<?php echo $tpl['arr']['id']?>" value="<?php __('front_button_submit');?>">&nbsp;
				<input type="button" name="ebcal_booking_summary_cancel" class="ebcal-button ebcal-grey-button" data-id="<?php echo $tpl['arr']['id']?>" value="<?php __('front_button_cancel');?>">
			</p>
		</form>
	</div>
</div>