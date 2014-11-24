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
	?>
	<div class="dashboard_header">
		<div class="dashboard_header_item">
			<div class="dashboard_info">
				<abbr><?php echo (int) @$tpl['info_arr'][0]['orders']; ?></abbr>
				<?php (int) @$tpl['info_arr'][0]['orders'] !== 1 ? __('dashboard_orders_today') : __('dashboard_order_today'); ?>
			</div>
		</div>
		<div class="dashboard_header_item">
			<div class="dashboard_info">
				<abbr><?php echo (int) @$tpl['info_arr'][0]['clients']; ?></abbr>
				<?php (int) @$tpl['info_arr'][0]['clients'] !== 1 ? __('dashboard_clients_today') : __('dashboard_client_today'); ?>
			</div>
		</div>
		<div class="dashboard_header_item dashboard_header_item_last">
			<div class="dashboard_info">
				<abbr><?php echo (int) @$tpl['info_arr'][0]['products']; ?></abbr>
				<?php (int) @$tpl['info_arr'][0]['products'] !== 1 ? __('dashboard_products') : __('dashboard_product'); ?>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('dashboard_last_orders'); ?></div>
			<div class="dashboard_column_top"><?php __('dashboard_last_clients'); ?></div>
			<div class="dashboard_column_top dashboard_column_top_last"><?php __('dashboard_stock'); ?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<?php
				foreach ($tpl['order_arr'] as $k => $order)
				{
					?>
					<div class="dashboard_item b10">
						<div class="">
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionUpdate&amp;id=<?php echo $order['id']; ?>"><?php echo $order['uuid']; ?></a>
						</div>
						<?php
						foreach ($order['order_stock_arr'] as $order_stock)
						{
							?>
							<div class="b5">
							
								<div><?php echo $order_stock['qty']; ?> x <?php echo pjSanitize::html($order_stock['name']); ?></div>
								<div class="fs11"><?php
								$attr = array();
								foreach ($order_stock['stock_attr'] as $stock)
								{
									$attr[] = str_replace('~:~', ': ', $stock);
								}
								echo join("; ", $attr);
								?></div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="dashboard_column">
			<?php
			foreach ($tpl['client_arr'] as $client)
			{
				?>
				<div class="dashboard_item b10">
					<div>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&amp;id=<?php echo $client['id']; ?>"><?php echo pjSanitize::html($client['client_name']); ?></a>
					</div>
					<div class="fs11"><?php echo pjSanitize::html($client['email']); ?></div>
					<div class="fs11"><?php __('client_created'); ?>: <span class="bold"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($client['created'])); ?></span></div>
				</div>
				<?php
			}
			?>
			</div>
			<div class="dashboard_column dashboard_column_last">
			<?php
			foreach ($tpl['stock_arr'] as $stock)
			{
				?>
				<div class="dashboard_item b10">
					<div><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionUpdate&amp;id=<?php echo $stock['product_id']; ?>"><?php echo pjSanitize::html($stock['name']); ?></a></div>
					<div class="fs11"><?php
					$arr = array();
					foreach ($stock['stock_attr'] as $attr)
					{
						$arr[] = str_replace('~:~', ': ', $attr);
					}
					echo join("; ", $arr); ?></div>
					<div class="fs11"><?php __('product_stock_price'); ?>: <span class="bold"><?php echo pjUtil::formatCurrencySign(number_format($stock['price'], 2), $tpl['option_arr']['o_currency']); ?></span></div>
					<div class="fs11"><?php __('product_stock_qty'); ?>: <span class="bold"><?php echo $stock['qty']; ?></span></div>
				</div>
				<?php
			}
			?>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	<?php
	$months = __('months', true);
	?>
	<div class="clear_left t20 overflow">
		<div class="float_left black pt15">
			<span class="gray"><?php echo ucfirst(__('dashboard_last_login', true)); ?>:</span>
			<?php
			list($month_index, $other) = explode("_", date("n_d, Y H:i", strtotime($_SESSION[$controller->defaultUser]['last_login'])));
			printf("%s %s", $months[$month_index], $other);
			?>
		</div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $month_index, $other) = explode("_", date("H:i_l_n_d, Y"));
		?>
			<div class="dashboard_date">
				<abbr><?php echo $day; ?></abbr>
				<?php printf("%s %s", $months[$month_index], $other); ?>
			</div>
			<div class="dashboard_hour"><?php echo $hour; ?></div>
		</div>
	</div>
	<?php
}
?>