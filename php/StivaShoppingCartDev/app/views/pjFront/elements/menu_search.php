<form action="" method="get" class="scSearchForm scSelectorSearchForm">
	<input type="text" name="q" class="scSearchFormInput" placeholder="<?php echo pjSanitize::html(__('front_search', true)); ?>" value="<?php echo urldecode(@$_GET['q']); ?>" />
</form>