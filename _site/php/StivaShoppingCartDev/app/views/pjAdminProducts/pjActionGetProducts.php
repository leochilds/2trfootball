<?php
if (count($tpl['arr']) > 0)
{
	?>
	<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
		<thead>
			<tr>
				<th><?php __('product_name'); ?></th>
				<th><?php __('product_sku'); ?></th>
				<th class="w50">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($tpl['arr'] as $k => $product)
		{
			?>
			<tr class="<?php echo $k % 2 === 0 ? 'pj-table-row-odd' : 'pj-table-row-even'; ?>">
				<td><?php echo pjSanitize::html($product['name']); ?></td>
				<td><?php echo pjSanitize::html($product['sku']); ?></td>
				<td><button value="<?php echo $product['id']; ?>" class="btnCopy btnCopy<?php echo @$_GET['copy']; ?>"><?php __('product_attr_copy_btn'); ?></button></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
} else {
	pjUtil::printNotice(__('product_empty', true), NULL);
}
?>