<?php
if (isset($tpl['extra_arr']) && count($tpl['extra_arr']) > 0)
{
	?><div class="t10"></div><?php
	$product_extra_types = __('product_extra_types', true);
	krsort($product_extra_types);
	foreach ($tpl['extra_arr'] as $extra)
	{
		switch ($extra['type'])
		{
			case 'single':
				?>
				<div class="extraBox">
					<table class="tblExtras">
						<tbody>
							<tr>
								<td style="width: 85px;"><?php __('product_extra_type'); ?></td>
								<td style="width: 90px"><?php __('product_extra_mandatory'); ?></td>
								<td style="width: 350px">
									<span class="boxSingle"><?php __('product_extra_name'); ?></span>
									<span class="boxMulti" style="display: none"><?php __('product_extra_title'); ?></span>
								</td>
								<td class="" style="padding: 0"><span class="boxSingle"><?php __('product_extra_price'); ?></span></td>
							</tr>
							<tr>
								<td class="align_top">
									<select name="extra_type[<?php echo $extra['id']; ?>]" class="pj-form-field">
										<?php
										$product_extra_types = __('product_extra_types', true);
										krsort($product_extra_types);
										foreach ($product_extra_types as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo $k == $extra['type'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
										}
										?>
									</select>
								</td>
								<td class="align_top"><label><input type="checkbox" name="extra_is_mandatory[<?php echo $extra['id']; ?>]" value="1"<?php echo (int) $extra['is_mandatory'] === 1 ? ' checked="checked"' : NULL; ?> /></label></td>
								<td class="align_top tdExtrasClean" colspan="2">
									<div class="boxSingle"">
										<table style="width: 100%">
											<tbody>
												<tr>
													<td class="align_top">
													<?php
													foreach ($tpl['lp_arr'] as $v)
													{
														?>
														<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
															<span class="inline_block">
																<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_name][<?php echo $extra['id']; ?>]" class="pj-form-field w300" value="<?php echo pjSanitize::html(@$extra['i18n'][$v['id']]['extra_name']); ?>" />
																<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
															</span>
														</p>
														<?php
													}
													?>
													</td>
													<td class="align_top">
														<span class="pj-form-field-custom pj-form-field-custom-before">
															<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
															<input type="text" name="extra_price[<?php echo $extra['id']; ?>]" class="pj-form-field w80 align_right" value="<?php echo (float) $extra['price']; ?>" />
														</span>
													</td>
													<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnDeleteExtra" rel="<?php echo $extra['id']; ?>" title="<?php __('product_extra_delete'); ?>"></a></td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="boxMulti" style="display: none">
										<table style="width: 100%">
											<tbody>
												<tr>
													<td style="width: 342px">
													<?php
													foreach ($tpl['lp_arr'] as $v)
													{
														?>
														<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
															<span class="inline_block">
																<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_title][<?php echo $extra['id']; ?>]" class="pj-form-field w300" disabled="disabled" />
																<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
															</span>
														</p>
														<?php
													}
													?>
													</td>
													<td></td>
													<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnDeleteExtra" rel="<?php echo $extra['id']; ?>" title="<?php __('product_extra_delete'); ?>"></a></td>
												</tr>
												<tr>
													<td><?php __('product_extra_name'); ?></td>
													<td><?php __('product_extra_price'); ?></td>
													<td></td>
												</tr>
												<tr>
													<td class="align_top">
													<?php
													mt_srand();
													$rand = mt_rand(0, 999999);
													foreach ($tpl['lp_arr'] as $v)
													{
														?>
														<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
															<span class="inline_block">
																<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_name][<?php echo $extra['id']; ?>][y_<?php echo $rand; ?>]" class="pj-form-field w300" disabled="disabled" />
																<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
															</span>
														</p>
														<?php
													}
													?>
													</td>
													<td class="align_top">
														<span class="pj-form-field-custom pj-form-field-custom-before">
															<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
															<input type="text" name="extra_price[<?php echo $extra['id']; ?>][y_<?php echo $rand; ?>]" class="pj-form-field w80 align_right" disabled="disabled" />
														</span>
													</td>
													<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnRemoveExtraItem"></a></td>
												</tr>
											</tbody>
										</table>
										<input type="button" class="pj-button btnAddExtraItem l5" data-index="<?php echo $extra['id']; ?>" value="<?php __('product_extra_item_add'); ?>" />
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
				break;
			case 'multi':
				?>
				<div class="extraBox">
					<table class="tblExtras">
						<tbody>
							<tr>
								<td style="width: 85px;"><?php __('product_extra_type'); ?></td>
								<td style="width: 90px"><?php __('product_extra_mandatory'); ?></td>
								<td style="width: 350px">
									<span class="boxSingle" style="display: none"><?php __('product_extra_name'); ?></span>
									<span class="boxMulti"><?php __('product_extra_title'); ?></span>
								</td>
								<td class=""><span class="boxSingle" style="display: none"><?php __('product_extra_price'); ?></span></td>
							</tr>
							<tr>
								<td class="align_top">
									<select name="extra_type[<?php echo $extra['id']; ?>]" class="pj-form-field">
										<?php
										$product_extra_types = __('product_extra_types', true);
										krsort($product_extra_types);
										foreach ($product_extra_types as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo $k == $extra['type'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
										}
										?>
									</select>
								</td>
								<td class="align_top"><label><input type="checkbox" name="extra_is_mandatory[<?php echo $extra['id']; ?>]" value="1"<?php echo (int) $extra['is_mandatory'] === 1 ? ' checked="checked"' : NULL; ?> /></label></td>
								<td class="align_top tdExtrasClean" colspan="2">
									<div class="boxSingle" style="display: none">
										<table style="width: 100%">
											<tbody>
												<tr>
													<td class="align_top">
													<?php
													foreach ($tpl['lp_arr'] as $v)
													{
														?>
														<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
															<span class="inline_block">
																<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_name][<?php echo $extra['id']; ?>]" class="pj-form-field w300" disabled="disabled" />
																<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
															</span>
														</p>
														<?php
													}
													?>
													</td>
													<td class="align_top">
														<span class="pj-form-field-custom pj-form-field-custom-before">
															<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
															<input type="text" name="extra_price[<?php echo $extra['id']; ?>]" class="pj-form-field w80 align_right" disabled="disabled" />
														</span>
													</td>
													<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnDeleteExtra" rel="<?php echo $extra['id']; ?>" title="<?php __('product_extra_delete'); ?>"></a></td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="boxMulti">
										<table style="width: 100%">
											<tbody>
												<tr>
													<td style="width: 342px">
													<?php
													foreach ($tpl['lp_arr'] as $v)
													{
														?>
														<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
															<span class="inline_block">
																<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_title][<?php echo $extra['id']; ?>]" class="pj-form-field w300" value="<?php echo pjSanitize::html(@$extra['i18n'][$v['id']]['extra_title']); ?>" />
																<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
															</span>
														</p>
														<?php
													}
													?>
													</td>
													<td></td>
													<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnDeleteExtra" rel="<?php echo $extra['id']; ?>" title="<?php __('product_extra_delete'); ?>"></a></td>
												</tr>
												<tr>
													<td><?php __('product_extra_name'); ?></td>
													<td><?php __('product_extra_price'); ?></td>
													<td></td>
												</tr>
												<?php
												foreach ($extra['extra_items'] as $item)
												{
													?>
													<tr>
														<td class="align_top">
														<?php
														foreach ($tpl['lp_arr'] as $v)
														{
															?>
															<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
																<span class="inline_block">
																	<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_name][<?php echo $extra['id']; ?>][<?php echo $item['id']; ?>]" class="pj-form-field w300" value="<?php echo pjSanitize::html(@$item['i18n'][$v['id']]['extra_name']); ?>" />
																	<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
																</span>
															</p>
															<?php
														}
														?>
														</td>
														<td class="align_top">
															<span class="pj-form-field-custom pj-form-field-custom-before">
																<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
																<input type="text" name="extra_price[<?php echo $extra['id']; ?>][<?php echo $item['id']; ?>]" class="pj-form-field w80 align_right" value="<?php echo (float) $item['price']; ?>" />
															</span>
														</td>
														<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnRemoveExtraItem"></a></td>
													</tr>
													<?php
												}
												?>
											</tbody>
										</table>
										<input type="button" class="pj-button btnAddExtraItem l5" data-index="<?php echo $extra['id']; ?>" value="<?php __('product_extra_item_add'); ?>" />
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
				break;
		}
	}
}
?>