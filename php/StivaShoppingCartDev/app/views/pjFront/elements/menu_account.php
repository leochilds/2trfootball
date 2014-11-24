<ul class="scAccountMenu">
	<?php
	if (!$controller->isLoged())
	{
		?>
		<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Register" class="scSelectorRegister"><?php __('front_register'); ?></a></li>
		<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Login" class="scSelectorLogin"><?php __('front_login'); ?></a></li>
		<?php
	} else {
		?>
		<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Logout" class="scSelectorLogout"><?php __('front_logout'); ?></a></li>
		<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Profile" class="scSelectorProfile"><?php __('front_profile'); ?></a></li>
		<?php
	}
	$myFavs = array();
	if (isset($_COOKIE[$controller->defaultCookie]) && !empty($_COOKIE[$controller->defaultCookie]))
	{
		$myFavs = unserialize(stripslashes($_COOKIE[$controller->defaultCookie]));
	}
	?>
	<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Favs" class="scSelectorViewFavs"><?php __('front_favs'); ?><?php echo !empty($myFavs) ? sprintf(" (%u)", count($myFavs)): NULL; ?></a></li>
</ul>