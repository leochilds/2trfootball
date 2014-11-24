<?php
if (pjObject::getPlugin('pjOneAdmin') !== NULL)
{
	$controller->requestAction(array('controller' => 'pjOneAdmin', 'action' => 'pjActionMenu'));
}
?>

<div class="leftmenu-top"></div>
<div class="leftmenu-middle">
	<ul class="menu">
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionIndex' ? 'menu-focus' : NULL; ?>"><span class="menu-dashboard">&nbsp;</span><?php __('menuDashboard'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminOrders' || ($_GET['controller'] == 'pjInvoice' && $_GET['action'] == 'pjActionInvoices') ? 'menu-focus' : NULL; ?>"><span class="menu-orders">&nbsp;</span><?php __('menuOrders'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminClients' ? 'menu-focus' : NULL; ?>"><span class="menu-clients">&nbsp;</span><?php __('menuClients'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionIndex" class="<?php echo in_array($_GET['controller'], array('pjAdminProducts', 'pjGallery')) ? 'menu-focus' : NULL; ?>"><span class="menu-products">&nbsp;</span><?php __('menuProducts'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex&amp;tab=1" class="<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex'))) || in_array($_GET['controller'], array('pjAdminLocales', 'pjBackup', 'pjLocale', 'pjAdminCountries', 'pjAdminCategories', 'pjSms')) || ($_GET['controller'] == 'pjInvoice' && in_array($_GET['action'], array('pjActionIndex'))) ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuOptions'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminVouchers' ? 'menu-focus' : NULL; ?>"><span class="menu-vouchers">&nbsp;</span><?php __('menuVouchers'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionPreview' ? 'menu-focus' : NULL; ?>" target="_blank"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionInstall" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionInstall' ? 'menu-focus' : NULL; ?>"><span class="menu-install">&nbsp;</span><?php __('menuInstall'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminUsers' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuUsers'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
	</ul>
</div>
<div class="leftmenu-bottom"></div>