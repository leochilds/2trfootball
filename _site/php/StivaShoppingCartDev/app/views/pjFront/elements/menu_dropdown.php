<div class="scMenuDropdown">
	<select name="category_id" class="scSelect scSelectorCategoryId" style="width: 100%">
		<option value=""><?php __('front_select_category'); ?></option>
		<?php
		foreach ($tpl['category_arr'] as $category)
		{
			?><option value="<?php echo $category['data']['id']; ?>"<?php echo !isset($_GET['category_id']) || (int) $_GET['category_id'] != $category['data']['id'] ? NULL : ' selected="selected"'; ?>><?php echo str_repeat("-----", $category['deep']) . " " .pjSanitize::html($category['data']['name']); ?></option><?php
		}
		?>
	</select>
</div>