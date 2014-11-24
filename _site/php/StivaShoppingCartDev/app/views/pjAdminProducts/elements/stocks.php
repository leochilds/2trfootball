<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 100%">
	<thead>
		<tr>
			<th class="sub"><?php __('product_stock_image'); ?></th>
			<?php
			if (isset($tpl['attr_arr']))
			{
				foreach ($tpl['attr_arr'] as $attr)
				{
					?><th class="sub"><?php echo pjSanitize::html($attr['name']); ?></th><?php
				}
			}
			?>
			<th class="sub w80"><?php __('product_stock_qty'); ?></th>
			<th class="sub w110"><?php __('product_stock_price'); ?></th>
			<th class="sub" style="width: 5%">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if (isset($tpl['stock_arr']) && count($tpl['stock_arr']) > 0)
	{
		foreach ($tpl['stock_arr'] as $stock)
		{
			?>
			<tr>
				<td>
					<?php
					if (!empty($stock['small_path']))
					{
						?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btnImageStock" rel="<?php echo $stock['image_id']; ?>"><img src="<?php echo PJ_INSTALL_URL . $stock['small_path']; ?>" alt="" class="in-stock" /></a><?php
					} else {
						?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnImageStock"><?php __('product_stock_choose_image'); ?></a><?php
					}
					?>
					<span class="boxStockImageId"><span><input type="hidden" name="stock_image_id[<?php echo $stock['id'] ?>]" value="<?php echo $stock['image_id']; ?>" class="required" /></span></span>
				</td>
				<?php
				foreach ($tpl['attr_arr'] as $attr)
				{
					?>
					<td>
						<select name="stock_attribute[<?php echo $stock['id'] ?>][<?php echo $attr['id']; ?>]" class="pj-form-field">
							<option value="">---</option>
							<?php
							foreach ($attr['child'] as $child)
							{
								?><option value="<?php echo $child['id']; ?>"<?php echo isset($stock['attrs'][$attr['id']]) && $stock['attrs'][$attr['id']] == $child['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($child['name']); ?></option><?php
							}
							?>
						</select>
					</td>
					<?php
				}
				?>
				<td><input type="text" name="stock_qty[<?php echo $stock['id'] ?>]" class="pj-form-field w40 align_right" value="<?php echo $stock['qty']; ?>" /></td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="stock_price[<?php echo $stock['id']; ?>]" class="pj-form-field w60 align_right" value="<?php echo $stock['price']; ?>" />
					</span>
				</td>
				<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" rel="<?php echo $stock['id']; ?>" class="pj-table-icon-delete btnDeleteStock"></a></td>
			</tr>
			<?php
		}
	} else {
		mt_srand();
		$index = 'x_' . mt_rand(0, 999999);
		?>
		<tr>
			<td>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnImageStock"><?php __('product_stock_choose_image'); ?></a>
				<span class="boxStockImageId"><span><input type="hidden" name="stock_image_id[<?php echo $index; ?>]" value="" /></span></span>
			</td>
			<?php
			if (isset($tpl['attr_arr']))
			{
				foreach ($tpl['attr_arr'] as $attr)
				{
					?>
					<td>
						<select name="stock_attribute[<?php echo $index; ?>][<?php echo $attr['id']; ?>]" class="pj-form-field">
							<option value="">---</option>
							<?php
							foreach ($attr['child'] as $child)
							{
								?><option value="<?php echo $child['id']; ?>"><?php echo pjSanitize::html($child['name']); ?></option><?php
							}
							?>
						</select>
					</td>
					<?php
				}
			}
			?>
			<td><input type="text" name="stock_qty[<?php echo $index; ?>]" class="pj-form-field w40 align_right" /></td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="stock_price[<?php echo $index; ?>]" class="pj-form-field w60 align_right" />
				</span>
			</td>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-table-icon-delete btnRemoveStock"></a></td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>

<?php if (!empty($tpl['attr_arr'])) : ?>
<div class="h30">
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnStockAdd"><?php __('product_stock_add'); ?></a>
</div>
<?php endif; ?>

<div>
	<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
</div>