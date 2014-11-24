<?php
if (isset($tpl['booking_arr']['payment_method']))
{
	$front_message = __('front_message', true);
	if($tpl['booking_arr']['booking_total'] > 0)
	{
		switch ($tpl['booking_arr']['payment_method'])
		{
			case 'paypal':
				?><p><?php echo $front_message[8]; ?></p><?php
				if (pjObject::getPlugin('pjPaypal') !== NULL)
				{
					$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
				}
				break;
			case 'authorize':
				?><p><?php echo $front_message[8]; ?></p><?php
				if (pjObject::getPlugin('pjAuthorize') !== NULL)
				{
					$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
				}
				break;
			case 'creditcard':
				?><p><?php echo $front_message[6]; ?></p><?php
				break;
			case 'bank':
				?><p><?php echo $front_message[6]; ?></p><p><?php echo stripslashes(nl2br($tpl['option_arr']['o_bank_account'])); ?></p><?php
				break;
		}
	}else{
		?><p><?php echo $front_message[6]; ?></p><?php
	}
}
?>