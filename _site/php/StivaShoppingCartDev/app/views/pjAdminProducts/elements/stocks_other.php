<table id="boxStockCloneTbl" style="display: none">
	<tbody>
		<tr>
			<td>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnImageStock"><?php __('product_stock_choose_image'); ?></a>
				<span class="boxStockImageId"><span><input type="hidden" name="stock_image_id[{INDEX}]" value="" class="required" /></span></span>
			</td>
			<?php
			if (isset($tpl['attr_arr']))
			{
				foreach ($tpl['attr_arr'] as $attr)
				{
					?>
					<td>
						<select name="stock_attribute[{INDEX}][<?php echo $attr['id']; ?>]" class="pj-form-field">
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
			<td><input type="text" name="stock_qty[{INDEX}]" class="pj-form-field w40 align_right" /></td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="stock_price[{INDEX}]" class="pj-form-field w60 align_right" />
				</span>
			</td>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-table-icon-delete btnRemoveStock"></a></td>
		</tr>
	</tbody>
</table>

<div id="dialogDeleteStock" style="display: none" title="<?php __('product_stock_delete_title'); ?>">
<?php __('product_stock_delete_desc'); ?>
</div>
<div id="dialogImageStock" style="display: none" title="<?php __('product_stock_img_title'); ?>"></div>