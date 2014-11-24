<?php
if (isset($tpl['get']['payment_method']))
{
	$status = __('front_booking_status', true);
	switch ($tpl['get']['payment_method'])
	{
		case 'paypal':
			?><p><?php echo $status[11]; ?></p><?php
			if (pjObject::getPlugin('pjPaypal') !== NULL)
			{
				$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
			}
			break;
		case 'authorize':
			?><p><?php echo $status[11]; ?></p><?php
			if (pjObject::getPlugin('pjAuthorize') !== NULL)
			{
				$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
			}
			break;
		case 'bank':
			?><p><?php echo $status[1]; ?></p><p><?php echo pjSanitize::html(nl2br($tpl['option_arr']['o_bank_account'])); ?></p><?php
			break;
		case 'creditcard':
		case 'cod':
		default:
			?><p><?php echo $status[1]; ?></p><?php
	}
}
?>