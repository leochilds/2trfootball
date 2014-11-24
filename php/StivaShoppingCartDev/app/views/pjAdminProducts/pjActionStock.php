<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 1:
			pjUtil::printNotice($status[1]);
			break;
		case 2:
			pjUtil::printNotice($status[2]);
			break;
		case 9:
			pjUtil::printNotice($status[9]);
			break;
	}
} else {
	$errors = __('errors', true);
	$titles = __('titles', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$errors[$_GET['err']], @$titles[$_GET['err']]);
	}
	?>
	<style type="text/css">
	.s-Pic{
		display: inline-block;
		float: left;
		margin: 0 10px 0 0;
	}
	.s-Img{
		background-color: #fff;
		border: solid 1px #ccc;
		max-height: 75px;
		max-width: 75px;
		padding: 1px;
		vertical-align: middle;
	}
	.s-Name{
		color: #306dab;
		display: block;
		font: bold 14px/18px ArchivoNarrowBold, "Myriad Pro", "Trebuchet MS", Helvetica, Arial, sans-serif;
		text-transform: uppercase;
	}
	.s-Attr{
		color: #babcbe;
		display: block;
		margin: 3px 0 0;
	}
	</style>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts"><?php __('lblProductsList'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate"><?php __('lblProductsCreate'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionStock"><?php __('product_stock_tab'); ?></a></li>
		</ul>
	</div>
	<?php
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	pjUtil::printNotice(@$titles['AP11'], @$bodies['AP11']);
	?>
	
	<div class="b10">
		<form action="" method="get" class="pj-form frm-filter-stock">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
	</div>

	<div id="grid_stock"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.price = "<?php __('product_stock_price'); ?>";
	myLabel.qty = "<?php __('product_stock_qty'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	</script>
	<?php
}
?>