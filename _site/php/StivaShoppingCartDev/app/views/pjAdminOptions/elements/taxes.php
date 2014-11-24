<div class="multilang b10"></div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form clear_right">
	<input type="hidden" name="options_update" value="1" />
	<input type="hidden" name="tab" value="<?php echo @$_GET['tab']; ?>" />
	<input type="hidden" name="next_action" value="pjActionIndex" />
	
	<table cellpadding="0" cellspacing="0" class="pj-table b10" style="width: 100%" id="tblShipping">
		<thead>
			<tr>
				<th><?php __('tax_location'); ?></th>
				<th class="w120"><?php __('tax_shipping'); ?></th>
				<th class="w120"><?php __('tax_free'); ?></th>
				<th class="w120"><?php __('tax_tax'); ?></th>
				<th class="w30"></th>
			</tr>
		</thead>
		<tbody>
	<?php
	if (isset($tpl['tax_arr']) && !empty($tpl['tax_arr']))
	{
		foreach ($tpl['tax_arr'] as $item)
		{
			?>
			<tr>
				<td>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
				?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][location][<?php echo $item['id']; ?>]" class="pj-form-field w300" value="<?php echo pjSanitize::html(@$item['i18n'][$v['id']]['location']); ?>" />
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
					</p>
					<?php
				}
				?>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(null, $tpl['option_arr']['o_currency']); ?></abbr></span>
						<input type="text" name="shipping[<?php echo $item['id']; ?>]" value="<?php echo (float) $item['shipping']; ?>" class="pj-form-field field-float w60">
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(null, $tpl['option_arr']['o_currency']); ?></abbr></span>
						<input type="text" name="free[<?php echo $item['id']; ?>]" value="<?php echo (float) $item['free']; ?>" class="pj-form-field field-float w60">
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text">%</abbr></span>
						<input type="text" name="tax[<?php echo $item['id']; ?>]" value="<?php echo (float) $item['tax']; ?>" class="pj-form-field field-float w60">
					</span>
				</td>
				<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions" class="pj-table-icon-delete btnDeleteShipping" data-id="<?php echo $item['id']; ?>"></a></td>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td>
			<?php
			$rand = rand(1, 99999);
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][location][new_<?php echo $rand; ?>]" class="pj-form-field w300" />
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					</span>
				</p>
				<?php
			}
			?>
			</td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(null, $tpl['option_arr']['o_currency']); ?></abbr></span>
					<input type="text" name="shipping[new_<?php echo $rand; ?>]" value="" class="pj-form-field field-float w60">
				</span>
			</td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(null, $tpl['option_arr']['o_currency']); ?></abbr></span>
					<input type="text" name="free[new_<?php echo $rand; ?>]" value="" class="pj-form-field field-float w60">
				</span>
			</td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text">%</abbr></span>
					<input type="text" name="tax[new_<?php echo $rand; ?>]" value="" class="pj-form-field field-float w60">
				</span>
			</td>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions" class="pj-table-icon-delete btnRemoveShipping"></a></td>
		</tr>
		<?php
	}
	?>
		</tbody>
	</table>
	
	<p>
		<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button btnAddShipping" />
	</p>
</form>

<div id="tmplShipping" style="display: none">
	<table>
		<tbody>
			<tr>
				<td>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
				?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][location][{INDEX}]" class="pj-form-field w300" />
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						</span>
					</p>
					<?php
				}
				?>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(null, $tpl['option_arr']['o_currency']); ?></abbr></span>
						<input type="text" name="shipping[{INDEX}]" class="pj-form-field field-float w60">
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(null, $tpl['option_arr']['o_currency']); ?></abbr></span>
						<input type="text" name="free[{INDEX}]" class="pj-form-field field-float w60">
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text">%</abbr></span>
						<input type="text" name="tax[{INDEX}]" class="pj-form-field field-float w60">
					</span>
				</td>
				<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions" class="pj-table-icon-delete btnRemoveShipping"></a></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="dialogDeleteShipping" style="display: none" title="<?php __('tax_del_title'); ?>"><?php __('tax_del_body'); ?></div>

<script type="text/javascript">
(function ($, undefined) {
	$(function() {
		if ($.fn.multilang !== undefined) {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					// Callback, e.g. ajax requests or whatever
				}
			});
		}
	});
})(jQuery_1_8_2);
var myLabel = myLabel || {};
myLabel.btn_delete = "<?php __('btnDelete'); ?>";
myLabel.btn_cancel = "<?php __('btnCancel'); ?>";
</script>