<?php
include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
?>
<h1 class="scHeading"><?php __('front_profile'); ?></h1>
<form action="" method="post" class="scForm scSelectorProfileForm">
	<input type="hidden" name="sc_profile" value="1" />
	
	<div class="scPaper">
		<div class="scPaperSidebar">
			<div class="scPaperSidebarText"><?php __('front_profile_note'); ?></div>
		</div>
		<div class="scPaperSheet">
			<div class="scPaperHeading"><?php __('client_general'); ?></div>
			<div class="scPaperContent">
				<div class="scNotice scSelectorNoticeMsg" style="display: none"></div>
				<p class="scPaperChain">
					<label class="scTitle"><?php __('client_email'); ?> <span class="scRequired">*</span>:</label>
					<input type="text" name="email" class="scText" placeholder="<?php __('front_placeholder_email', false, true); ?>" value="<?php echo isset($tpl['arr']['email']) ? pjSanitize::html($tpl['arr']['email']) : NULL; ?>" />
				</p>
				<p class="scPaperChain">
					<label class="scTitle"><?php __('client_password'); ?> <span class="scRequired">*</span>:</label>
					<input type="text" name="password" class="scText" placeholder="<?php __('front_placeholder_password', false, true); ?>" value="<?php echo isset($tpl['arr']['password']) ? pjSanitize::html($tpl['arr']['password']) : NULL; ?>" />
				</p>
				<p class="scPaperChain">
					<label class="scTitle"><?php __('client_name'); ?> <span class="scRequired">*</span>:</label>
					<input type="text" name="client_name" class="scText" placeholder="<?php __('front_placeholder_name', false, true); ?>" value="<?php echo isset($tpl['arr']['client_name']) ? pjSanitize::html($tpl['arr']['client_name']) : NULL; ?>" />
				</p>
				<p class="scPaperChain">
					<label class="scTitle"><?php __('client_phone'); ?></label>
					<input type="text" name="phone" class="scText" placeholder="<?php __('front_placeholder_phone', false, true); ?>" value="<?php echo isset($tpl['arr']['phone']) ? pjSanitize::html($tpl['arr']['phone']) : NULL; ?>" />
				</p>
				<p class="scPaperChain">
					<label class="scTitle"><?php __('client_url'); ?></label>
					<input type="text" name="url" class="scText" placeholder="<?php __('front_placeholder_url', false, true); ?>" value="<?php echo isset($tpl['arr']['url']) ? pjSanitize::html($tpl['arr']['url']) : NULL; ?>" />
				</p>
				<p class="scPaperChain">
					<label class="scTitle">&nbsp;</label>
					<input type="submit" value="<?php __('front_save_changes', false, true); ?>" class="scButton scButtonDark scButtonDarkNext scSelectorButton" />
				</p>
			</div>
			
			<div class="scPaperHeading scPaperHeadingTop"><?php __('client_address_book'); ?></div>
			<div class="scPaperContent">
				<div class="scSelectorAddresses"><?php include dirname(__FILE__) . '/elements/address.php'; ?></div>
			</div>
		</div>
		<div class="scPaperControl">
			<div class="scPaperControlInner">
				<input type="submit" value="<?php __('front_save_changes', false, true); ?>" class="scButton scButtonDark scButtonDarkNext scSelectorButton" />
				<input type="button" value="<?php __('client_add_address', false, true); ?>" class="scButton scButtonLight scButtonLightPlus scSelectorButton scSelectorAddAddress" />
			</div>
		</div>
	</div>
</form>

<div class="scSelectorCloneAddress" style="display: none"><?php include dirname(__FILE__) . '/elements/address_tpl.php'; ?></div>