<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
pjUtil::printNotice(@$titles['AOR07'], @$bodies['AOR07']);
?>
<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 10px">
	<thead>
		<tr>
			<?php
			if (isset($tpl['attr_arr']) && !empty($tpl['attr_arr']))
			{
				foreach ($tpl['attr_arr'] as $attr)
				{
					?><th><?php echo pjSanitize::html($attr['name']); ?></th><?php
				}
			}
			?>
			<th class="align_center"><?php __('order_p_qty'); ?></th>
			<th class="align_center"><?php __('order_current_stock'); ?></th>
			<th><?php __('order_unit_price'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if (isset($tpl['stock_arr']))
	{
		$cnt = isset($tpl['attr_arr']) ? count($tpl['attr_arr']) : 0;
		$xtras = !empty($tpl['extra_arr']);
		foreach ($tpl['stock_arr'] as $k => $stock)
		{
			if ((int) $stock['qty'] === 0)
			{
				continue;
			}
			?>
			<tr class="<?php echo $k % 2 !== 0 ? 'pj-table-row-even' : 'pj-table-row-odd'; ?>">
				<?php
				if (isset($tpl['attr_arr']) && !empty($tpl['attr_arr']))
				{
					foreach ($tpl['attr_arr'] as $attr)
					{
						?>
						<td>
						<?php
						foreach ($attr['child'] as $child)
						{
							if (isset($stock['attrs'][$attr['id']]) && $stock['attrs'][$attr['id']] == $child['id'])
							{
								echo pjSanitize::html($child['name']);
								break;
							}
						}
						?>
						</td>
						<?php
					}
				}
				?>
				<td class="align_center"><input type="text" name="qty[<?php echo $stock['id']; ?>]" value="0" class="pj-form-field w60" data-max="<?php echo $stock['qty']; ?>" readonly="readonly" /></td>
				<td class="align_center"><input type="hidden" name="current_qty[<?php echo $stock['id']; ?>]" value="<?php echo $stock['qty']; ?>" /><?php echo $stock['qty']; ?></td>
				<td><input type="hidden" name="price[<?php echo $stock['id']; ?>]" value="<?php echo $stock['price']; ?>" /><?php echo pjUtil::formatCurrencySign(number_format($stock['price'], 2), $tpl['option_arr']['o_currency']); ?></td>
			</tr>
			<?php
			if ($xtras)
			{
				?>
				<tr class="<?php echo $k % 2 !== 0 ? 'pj-table-row-even' : 'pj-table-row-odd'; ?>">
					<td colspan="<?php echo 3 + $cnt; ?>">
					<?php
					foreach ($tpl['extra_arr'] as $extra)
					{
						switch ($extra['type'])
						{
							case 'single':
								?>
								<div class="b5">
									<label><input type="checkbox" name="extra_id[<?php echo $stock['id']; ?>][<?php echo $extra['id']; ?>]" value="<?php echo $extra['type']; ?>|<?php echo $extra['price']; ?>" /> <?php echo pjSanitize::html($extra['name']); ?>
									(<?php echo pjUtil::formatCurrencySign(number_format($extra['price'], 2), $tpl['option_arr']['o_currency']); ?>)</label>
								</div>
								<?php
								break;
							case 'multi':
								?><select name="extra_id[<?php echo $stock['id']; ?>][<?php echo $extra['id']; ?>]" class="pj-form-field">
									<option value="">-- Select --</option>
									<?php
									foreach ($extra['extra_items'] as $k => $item)
									{
										?><option value="<?php echo $extra['type']; ?>|<?php echo $item['price']; ?>|<?php echo $item['id']; ?>"><?php echo pjSanitize::html($item['name']); ?> (<?php echo pjUtil::formatCurrencySign(number_format($item['price'], 2), $tpl['option_arr']['o_currency']); ?>)</option><?php
									}
									?>
									</select>
								<?php
								break;
						}
					}
					?>
					</td>
				</tr>
				<?php
			}
		}
	}
	?>
	</tbody>
</table>