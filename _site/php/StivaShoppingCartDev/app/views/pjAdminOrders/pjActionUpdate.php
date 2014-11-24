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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex"><?php __('menuOrders'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjInvoice&amp;action=pjActionInvoices"><?php __('plugin_invoice_menu_invoices'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('order_update'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionUpdate" method="post" class="pj-form form" id="frmUpdateOrder">
		<input type="hidden" name="update_form" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('order_tab_order'); ?></a></li>
				<li><a href="#tabs-2"><?php __('order_tab_client'); ?></a></li>
				<li><a href="#tabs-3"><?php __('order_tab_shipping'); ?></a></li>
			</ul>
			
			<div id="tabs-1">
				<?php pjUtil::printNotice(@$titles['AO10'], @$bodies['AO10']); ?>
				<fieldset class="fieldset white">
					<legend><?php __('order_general'); ?></legend>
					
					<div class="overflow pt5 b5">
						<label class="title">&nbsp;</label>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders" data-id="<?php echo $tpl['arr']['id']; ?>" class="pj-button btn-confirm"><?php __('order_send_confirm'); ?></a>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders" data-id="<?php echo $tpl['arr']['id']; ?>" class="pj-button btn-payment"><?php __('order_send_payment'); ?></a>
					</div>
					
					<div class="float_left">
						<p>
							<label class="title"><?php __('order_created'); ?>:</label>
							<span class="left"><?php echo date($tpl['option_arr']['o_datetime_format'], strtotime($tpl['arr']['created'])); ?></span>
						</p>
						<p>
							<label class="title"><?php __('order_uuid'); ?>:</label>
							<input type="text" name="uuid" id="uuid" class="pj-form-field w100" value="<?php echo pjSanitize::html($tpl['arr']['uuid']); ?>" />
						</p>
						<p>
							<label class="title"><?php __('order_status'); ?>:</label>
							<select name="status" id="status" class="pj-form-field">
								<option value=""><?php __('order_choose'); ?></option>
								<?php
								foreach (__('order_statuses', true) as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['status'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
								}
								?>
							</select>
						</p>
						<p>
							<label class="title"><?php __('order_payment'); ?>:</label>
							<select name="payment_method" id="payment_method" class="pj-form-field">
								<option value=""><?php __('order_choose'); ?></option>
								<?php
								foreach (__('payment_methods', true) as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
								}
								?>
							</select>
						</p>
						<p>
							<label class="title"><?php __('order_voucher'); ?>:</label>
							<input type="text" name="voucher" id="voucher" class="pj-form-field w100" value="<?php echo pjSanitize::html($tpl['arr']['voucher']); ?>" />
						</p>
						<p>
							<label class="title"><?php __('order_shipping_location'); ?></label>
							<select name="tax_id" class="pj-form-field w150">
								<option value=""><?php __('order_choose'); ?></option>
								<?php
								foreach ($tpl['tax_arr'] as $item)
								{
									?><option value="<?php echo $item['id']; ?>"<?php echo $tpl['arr']['tax_id'] != $item['id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($item['location']); ?></option><?php
								}
								?>
							</select>
						</p>
					</div>
					<div class="float_right">
						<p>
							<label class="title"><?php __('order_price'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="price" id="price" class="pj-form-field number w80" value="<?php echo number_format(@$tpl['arr']['price'], 2, ".", ""); ?>" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('order_discount'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="discount" id="discount" class="pj-form-field number w80" value="<?php echo number_format(@$tpl['arr']['discount'], 2, ".", ""); ?>" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('order_insurance'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="insurance" id="insurance" class="pj-form-field number w80" value="<?php echo number_format(@$tpl['arr']['insurance'], 2, ".", ""); ?>" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('order_shipping'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="shipping" id="shipping" class="pj-form-field number w80" value="<?php echo number_format(@$tpl['arr']['shipping'], 2, ".", ""); ?>" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('order_tax'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="tax" id="tax" class="pj-form-field number w80" value="<?php echo number_format(@$tpl['arr']['tax'], 2, ".", ""); ?>" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('order_total'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="total" id="total" class="pj-form-field number w80" value="<?php echo number_format(@$tpl['arr']['total'], 2, ".", ""); ?>" />
							</span>
						</p>
					</div>
					<br class="clear_both" />
					<p>
						<label class="title"><?php __('order_notes'); ?>:</label>
						<textarea name="notes" id="notes" class="pj-form-field w500 h100"><?php echo pjSanitize::html($tpl['arr']['notes']); ?></textarea>
					</p>
					<div class="p">
						<label class="title"><?php __('order_products'); ?>:</label>
						<div id="boxStockProducts"></div>
						
						<div id="dialogStockDelete" title="Delete confirmation" style="display: none">Are you sure you want to delete selected stock?</div>
						<div id="dialogStockEdit" title="Edit Stock" style="display: none"></div>
						<div id="dialogStockAdd" title="Add Stock" style="display: none"></div>
					</div>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
						<input type="button" value="+ Add stock" class="pj-button stock-add" />
						<input type="button" value="Recalculate the price" class="pj-button order-calc" />
					</p>
				</fieldset>
				
				<?php
				if (pjObject::getPlugin('pjInvoice') !== NULL)
				{
					$map = array(
						'completed' => 'paid',
						'pending' => 'not_paid',
						'new' => 'not_paid',
						'cancelled' => 'cancelled'
					);
					?>
					<fieldset class="fieldset white" style="position: static">
						<legend><?php __('order_invoice_details'); ?></legend>
						<input type="button" class="pj-button btnCreateInvoice" value="<?php __('order_create_invoice'); ?>" />
						
						<div id="grid_invoices" class="t10 b10"></div>
					</fieldset>
					<?php
				}
				?>
			</div>
			<div id="tabs-2">
				<?php pjUtil::printNotice(@$titles['AO11'], @$bodies['AO11']); ?>
				<fieldset class="fieldset white">
					<legend><?php __('order_customer'); ?></legend>
					<p>
						<label class="title"><?php __('order_client'); ?>:</label>
						<span class="float_left r5">
							<select name="client_id" id="client_id" class="pj-form-field w200 custom-chosen">
								<option value=""><?php __('order_choose'); ?></option>
								<?php
								foreach ($tpl['client_arr'] as $client)
								{
									?><option value="<?php echo $client['id']; ?>"<?php echo $client['id'] == $tpl['arr']['client_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($client['client_name']); ?></option><?php
								}
								?>
							</select>
						</span>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&action=pjActionUpdate&id=<?php echo $tpl['arr']['client_id']; ?>" class="pj-icon-edit"></a>
					</p>
					<div id="boxClient">
					<?php
					if ($tpl['arr']['client_id'] > 0)
					{
						?>
						<p>
							<label class="title"><?php __('order_email'); ?>:</label>
							<span class="left"><?php echo pjSanitize::html($tpl['arr']['client_email']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('order_phone'); ?>:</label>
							<span class="left"><?php echo pjSanitize::html($tpl['arr']['client_phone']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('order_url'); ?>:</label>
							<span class="left"><?php echo pjSanitize::html($tpl['arr']['client_url']); ?></span>
						</p>
						<?php
					}
					?>
					</div>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</fieldset>
				
				<fieldset class="fieldset white" style="position: static">
					<legend><?php __('order_all_list'); ?></legend>
					<div id="grid_client_orders"></div>
				</fieldset>
			</div>
			<div id="tabs-3">
				<?php pjUtil::printNotice(@$titles['AO12'], @$bodies['AO12']); ?>
				<fieldset class="fieldset white">
					<legend><?php __('client_address_book'); ?></legend>
					<div id="boxAddressBook">
					<?php
					if ($tpl['arr']['client_id'] > 0)
					{
						?>
						<p>
							<label class="title"><?php __('order_address'); ?>:</label>
							<select name="address_id" id="address_id" class="pj-form-field w200">
								<option value=""><?php __('order_choose'); ?></option>
								<?php
								$disabled = ' disabled="disabled"';
								foreach ($tpl['address_arr'] as $address)
								{
									$selected = NULL;
									if ($address['id'] == $tpl['arr']['address_id'])
									{
										$selected = ' selected="selected"';
										$disabled = NULL;
									}
									?><option value="<?php echo $address['id']; ?>"<?php echo $selected; ?>><?php echo pjSanitize::html($address['name']); ?></option><?php
								}
								?>
							</select>
							<input type="button" value="<?php __('order_copy_b'); ?>" class="pj-button btnCopy btnCopyBilling"<?php echo $disabled; ?> />
							<input type="button" value="<?php __('order_copy_s'); ?>" class="pj-button btnCopy btnCopyShipping"<?php echo $disabled; ?> />
						</p>
						<div id="boxAddress">
						<?php
						foreach ($tpl['address_arr'] as $address)
						{
							if ($address['id'] == $tpl['arr']['address_id'])
							{
								$tpl['address_arr'] = $address;
								include dirname(__FILE__) . '/pjActionGetAddress.php';
								break;
							}
						}
						?>
						</div>
						<?php
					}
					?>
					</div>
				</fieldset>
				<fieldset class="fieldset white">
					<legend><?php __('order_billing_details'); ?></legend>
					<div class="float_left w360">
						<p>
							<label class="title"><?php __('order_country'); ?>:</label>
							<select name="b_country_id" id="b_country_id" class="pj-form-field w180 custom-chosen">
								<option value=""><?php __('order_choose'); ?></option>
								<?php
								foreach ($tpl['country_arr'] as $country)
								{
									?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] == $tpl['arr']['b_country_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
								}
								?>
							</select>
						</p>
						<p>
							<label class="title"><?php __('order_state'); ?>:</label>
							<input type="text" name="b_state" id="b_state" class="pj-form-field w180" value="<?php echo pjSanitize::html($tpl['arr']['b_state']); ?>" />
						</p>
					</div>
					<div class="float_right w350">
						<p>
							<label class="title"><?php __('order_city'); ?>:</label>
							<input type="text" name="b_city" id="b_city" class="pj-form-field w160" value="<?php echo pjSanitize::html($tpl['arr']['b_city']); ?>" />
						</p>
						<p>
							<label class="title"><?php __('order_zip'); ?>:</label>
							<input type="text" name="b_zip" id="b_zip" class="pj-form-field w80" value="<?php echo pjSanitize::html($tpl['arr']['b_zip']); ?>" />
						</p>
					</div>
					<br class="clear_both" />
					<p>
						<label class="title"><?php __('order_name'); ?>:</label>
						<input type="text" name="b_name" id="b_name" class="pj-form-field w300" value="<?php echo pjSanitize::html($tpl['arr']['b_name']); ?>" />
					</p>
					<p>
						<label class="title"><?php __('order_address_1'); ?>:</label>
						<input type="text" name="b_address_1" id="b_address_1" class="pj-form-field w500" value="<?php echo pjSanitize::html($tpl['arr']['b_address_1']); ?>" />
					</p>
					<p>
						<label class="title"><?php __('order_address_2'); ?>:</label>
						<input type="text" name="b_address_2" id="b_address_2" class="pj-form-field w500" value="<?php echo pjSanitize::html($tpl['arr']['b_address_2']); ?>" />
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</fieldset>
				
				<fieldset class="fieldset white">
					<legend><?php __('order_shipping_details'); ?></legend>
					<?php
					$isSame = false;
					if ((int) $tpl['arr']['same_as'] === 1)
					{
						$isSame = true;
					}
					?>
					<p>
						<input type="checkbox" name="same_as" id="same_as" value="1"<?php echo $isSame ? ' checked="checked"' : NULL; ?> /> <label for="same_as"><?php __('order_same'); ?></label>
					</p>
					<div class="boxSame" style="display: <?php echo $isSame ? 'none' : NULL; ?>">
						<div class="float_left w360">
							<p>
								<label class="title"><?php __('order_country'); ?>:</label>
								<select name="s_country_id" id="s_country_id" class="pj-form-field w180 custom-chosen">
									<option value=""><?php __('order_choose'); ?></option>
									<?php
									foreach ($tpl['country_arr'] as $country)
									{
										?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] == $tpl['arr']['s_country_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
									}
									?>
								</select>
							</p>
							<p>
								<label class="title"><?php __('order_state'); ?>:</label>
								<input type="text" name="s_state" id="s_state" class="pj-form-field w180" value="<?php echo pjSanitize::html($tpl['arr']['s_state']); ?>" />
							</p>
						</div>
						<div class="float_right w350">
							<p>
								<label class="title"><?php __('order_city'); ?>:</label>
								<input type="text" name="s_city" id="s_city" class="pj-form-field w160" value="<?php echo pjSanitize::html($tpl['arr']['s_city']); ?>" />
							</p>
							<p>
								<label class="title"><?php __('order_zip'); ?>:</label>
								<input type="text" name="s_zip" id="s_zip" class="pj-form-field w80" value="<?php echo pjSanitize::html($tpl['arr']['s_zip']); ?>" />
							</p>
						</div>
						<br class="clear_both" />
						<p>
							<label class="title"><?php __('order_name'); ?>:</label>
							<input type="text" name="s_name" id="s_name" class="pj-form-field w300" value="<?php echo pjSanitize::html($tpl['arr']['s_name']); ?>" />
						</p>
						<p>
							<label class="title"><?php __('order_address_1'); ?>:</label>
							<input type="text" name="s_address_1" id="s_address_1" class="pj-form-field w500" value="<?php echo pjSanitize::html($tpl['arr']['s_address_1']); ?>" />
						</p>
						<p>
							<label class="title"><?php __('order_address_2'); ?>:</label>
							<input type="text" name="s_address_2" id="s_address_2" class="pj-form-field w500" value="<?php echo pjSanitize::html($tpl['arr']['s_address_2']); ?>" />
						</p>
					</div>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					</p>
				</fieldset>
			</div>
		</div>
	</form>
	
	<?php
	if (pjObject::getPlugin('pjInvoice') !== NULL)
	{
		$map = array(
			'completed' => 'paid',
			'pending' => 'not_paid',
			'new' => 'not_paid',
			'cancelled' => 'cancelled'
		);
		?>
		<form action="<?php echo PJ_INSTALL_URL; ?>index.php" method="get" target="_blank" style="display: inline" id="frmCreateInvoice">
			<input type="hidden" name="controller" value="pjInvoice" />
			<input type="hidden" name="action" value="pjActionCreateInvoice" />
			<input type="hidden" name="tmp" value="<?php echo md5(uniqid(rand(), true)); ?>" />
			<input type="hidden" name="uuid" value="<?php echo pjUtil::uuid(); ?>" />
			<input type="hidden" name="order_id" value="<?php echo pjSanitize::html($tpl['arr']['uuid']); ?>" />
			<input type="hidden" name="issue_date" value="<?php echo date('Y-m-d'); ?>" />
			<input type="hidden" name="due_date" value="<?php echo date('Y-m-d'); ?>" />
			<input type="hidden" name="status" value="<?php echo @$map[$tpl['arr']['status']]; ?>" />
			<input type="hidden" name="subtotal" value="<?php echo $tpl['arr']['price'] + $tpl['arr']['insurance'] + $tpl['arr']['shipping']; ?>" />
			<input type="hidden" name="discount" value="<?php echo $tpl['arr']['discount']; ?>" />
			<input type="hidden" name="tax" value="<?php echo $tpl['arr']['tax']; ?>" />
			<input type="hidden" name="shipping" value="<?php echo $tpl['arr']['shipping']; ?>" />
			<input type="hidden" name="total" value="<?php echo $tpl['arr']['total']; ?>" />
			<input type="hidden" name="paid_deposit" value="0.00" />
			<input type="hidden" name="amount_due" value="0.00" />
			<input type="hidden" name="currency" value="<?php echo pjSanitize::html($tpl['option_arr']['o_currency']); ?>" />
			<input type="hidden" name="notes" value="<?php echo pjSanitize::html($tpl['arr']['notes']); ?>" />
			<input type="hidden" name="b_billing_address" value="<?php echo pjSanitize::html($tpl['arr']['b_address_1']); ?>" />
			<input type="hidden" name="b_name" value="<?php echo pjSanitize::html($tpl['arr']['b_name']); ?>" />
			<input type="hidden" name="b_address" value="<?php echo pjSanitize::html($tpl['arr']['b_address_1']); ?>" />
			<input type="hidden" name="b_street_address" value="<?php echo pjSanitize::html($tpl['arr']['b_address_2']); ?>" />
			<input type="hidden" name="b_city" value="<?php echo pjSanitize::html($tpl['arr']['b_city']); ?>" />
			<input type="hidden" name="b_state" value="<?php echo pjSanitize::html($tpl['arr']['b_state']); ?>" />
			<input type="hidden" name="b_zip" value="<?php echo pjSanitize::html($tpl['arr']['b_zip']); ?>" />
			<input type="hidden" name="b_phone" value="<?php echo pjSanitize::html($tpl['arr']['client_phone']); ?>" />
			<input type="hidden" name="b_email" value="<?php echo pjSanitize::html($tpl['arr']['client_email']); ?>" />
			<input type="hidden" name="b_url" value="<?php echo pjSanitize::html($tpl['arr']['client_url']); ?>" />
			<input type="hidden" name="s_shipping_address" value="<?php echo pjSanitize::html((int) $tpl['arr']['same_as'] === 1 ? $tpl['arr']['b_address_1'] : $tpl['arr']['s_address_1']); ?>" />
			<input type="hidden" name="s_name" value="<?php echo pjSanitize::html((int) $tpl['arr']['same_as'] === 1 ? $tpl['arr']['b_name'] : $tpl['arr']['s_name']); ?>" />
			<input type="hidden" name="s_address" value="<?php echo pjSanitize::html((int) $tpl['arr']['same_as'] === 1 ? $tpl['arr']['b_address_1'] : $tpl['arr']['s_address_1']); ?>" />
			<input type="hidden" name="s_street_address" value="<?php echo pjSanitize::html((int) $tpl['arr']['same_as'] === 1 ? $tpl['arr']['b_address_2'] : $tpl['arr']['s_address_2']); ?>" />
			<input type="hidden" name="s_city" value="<?php echo pjSanitize::html((int) $tpl['arr']['same_as'] === 1 ? $tpl['arr']['b_city'] : $tpl['arr']['s_city']); ?>" />
			<input type="hidden" name="s_state" value="<?php echo pjSanitize::html((int) $tpl['arr']['same_as'] === 1 ? $tpl['arr']['b_state'] : $tpl['arr']['s_state']); ?>" />
			<input type="hidden" name="s_zip" value="<?php echo pjSanitize::html((int) $tpl['arr']['same_as'] === 1 ? $tpl['arr']['b_zip'] : $tpl['arr']['s_zip']); ?>" />
			<input type="hidden" name="s_phone" value="<?php echo pjSanitize::html($tpl['arr']['client_phone']); ?>" />
			<input type="hidden" name="s_email" value="<?php echo pjSanitize::html($tpl['arr']['client_email']); ?>" />
			<input type="hidden" name="s_url" value="<?php echo pjSanitize::html($tpl['arr']['client_url']); ?>" />
			<?php
			$items = array();
			if (isset($tpl['os_arr']) && !empty($tpl['os_arr']))
			{
				foreach ($tpl['os_arr'] as $i => $attr)
				{
					$items[$i] = array(
						'name' => $attr['product_name'],
						'description' => str_replace('~:~', ': ', join("; ", $attr['attr'])),
						'qty' => $attr['qty'],
						'unit_price' => $attr['price'],
						'amount' => number_format($attr['qty'] * $attr['price'], 2, ".", "")
					);
					?>
					<input type="hidden" name="items[<?php echo $i; ?>][name]" value="<?php echo pjSanitize::html($items[$i]['name']); ?>" />
					<input type="hidden" name="items[<?php echo $i; ?>][description]" value="<?php echo pjSanitize::html($items[$i]['description']); ?>" />
					<input type="hidden" name="items[<?php echo $i; ?>][qty]" value="<?php echo $items[$i]['qty']; ?>" />
					<input type="hidden" name="items[<?php echo $i; ?>][unit_price]" value="<?php echo $items[$i]['unit_price']; ?>" />
					<input type="hidden" name="items[<?php echo $i; ?>][amount]" value="<?php echo $items[$i]['amount']; ?>" />
					<?php
				}
				?>
				<input type="hidden" name="items[<?php echo $i+1; ?>][name]" value="<?php echo pjSanitize::html(__('order_insurance', true)); ?>" />
				<input type="hidden" name="items[<?php echo $i+1; ?>][description]" value="" />
				<input type="hidden" name="items[<?php echo $i+1; ?>][qty]" value="1" />
				<input type="hidden" name="items[<?php echo $i+1; ?>][unit_price]" value="<?php echo $tpl['arr']['insurance']; ?>" />
				<input type="hidden" name="items[<?php echo $i+1; ?>][amount]" value="<?php echo $tpl['arr']['insurance']; ?>" />
				
				<input type="hidden" name="items[<?php echo $i+2; ?>][name]" value="<?php echo pjSanitize::html(__('order_shipping', true)); ?>" />
				<input type="hidden" name="items[<?php echo $i+2; ?>][description]" value="" />
				<input type="hidden" name="items[<?php echo $i+2; ?>][qty]" value="1" />
				<input type="hidden" name="items[<?php echo $i+2; ?>][unit_price]" value="<?php echo $tpl['arr']['shipping']; ?>" />
				<input type="hidden" name="items[<?php echo $i+2; ?>][amount]" value="<?php echo $tpl['arr']['shipping']; ?>" />
				<?php
			} else {
				$items[0] = array(
					'name' => 'Order payment',
					'description' => '',
					'qty' => 1,
					'unit_price' => $tpl['arr']['total'],
					'amount' => $tpl['arr']['total']
				);
				?>
				<input type="hidden" name="items[0][name]" value="<?php echo pjSanitize::html($items[0]['name']); ?>" />
				<input type="hidden" name="items[0][description]" value="<?php echo pjSanitize::html($items[0]['description']); ?>" />
				<input type="hidden" name="items[0][qty]" value="<?php echo $items[0]['qty']; ?>" />
				<input type="hidden" name="items[0][unit_price]" value="<?php echo $items[0]['unit_price']; ?>" />
				<input type="hidden" name="items[0][amount]" value="<?php echo $items[0]['amount']; ?>" />
				<?php
			}
			?>
		</form>
		<?php
	}
	$statuses = __('plugin_invoice_statuses', true);
	?>
	
	<div id="dialogDeleteAddress" style="display: none" title="<?php __('client_da_title'); ?>"><?php __('client_da_body'); ?></div>
	<div id="boxCloneAddress" style="display: none"><?php include PJ_VIEWS_PATH . 'pjAdminClients/elements/address.php'; ?></div>
	
	<div id="dialogConfirm" title="<?php __('order_confirm_title'); ?>" style="display: none"></div>
	<div id="dialogPayment" title="<?php __('order_payment_title'); ?>" style="display: none"></div>
	
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	var myLabel = myLabel || {};
	myLabel.uuid = "<?php __('order_uuid'); ?>";
	myLabel.client = "<?php __('order_client'); ?>";
	myLabel.created = "<?php __('order_created'); ?>";
	myLabel.status = "<?php __('order_status'); ?>";
	myLabel.total = "<?php __('order_total'); ?>";
	myLabel.statuses = <?php echo pjAppController::jsonEncode(__('order_statuses', true)); ?>;
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('gridDeleteConfirmation'); ?>";

	myLabel.num = "<?php __('plugin_invoice_i_num'); ?>";
	myLabel.order_id = "<?php __('plugin_invoice_i_order_id'); ?>";
	myLabel.issue_date = "<?php __('plugin_invoice_i_issue_date'); ?>";
	myLabel.due_date = "<?php __('plugin_invoice_i_due_date'); ?>";
	myLabel.created = "<?php __('plugin_invoice_i_created'); ?>";
	myLabel.status = "<?php __('plugin_invoice_i_status'); ?>";
	myLabel.total = "<?php __('plugin_invoice_i_total'); ?>";
	myLabel.delete_title = "<?php __('plugin_invoice_i_delete_title'); ?>";
	myLabel.delete_body = "<?php __('plugin_invoice_i_delete_body'); ?>";
	myLabel.paid = "<?php echo $statuses['paid']; ?>";
	myLabel.not_paid = "<?php echo $statuses['not_paid']; ?>";
	myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";
	myLabel.empty_date = "<?php __('gridEmptyDate'); ?>";
	myLabel.invalid_date = "<?php __('gridInvalidDate'); ?>";
	myLabel.empty_datetime = "<?php __('gridEmptyDatetime'); ?>";
	myLabel.invalid_datetime = "<?php __('gridInvalidDatetime'); ?>";
	</script>
	<?php
}
?>