<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{
	public $defaultForm = 'SCart_Form';
	
	public $defaultCaptcha = 'SCart_Captcha';
	
	public $defaultUser = 'SCart_Client';
	
	public $defaultVoucher = 'SCart_Voucher';
	
	public $defaultCookie = 'SCart_Cookie';
	
	public $defaultTax = 'SCart_Tax';
	
	public $defaultLocale = 'SCart_LocaleId';
	
	public $defaultHash = 'SCart_Hash';
	
	public $cart = NULL;

	public function __construct()
	{
		$this->setLayout('pjActionFront');
		
		if (!isset($_SESSION[$this->defaultHash]))
		{
			if ($this->isLoged())
			{
				$_SESSION[$this->defaultHash] = md5(PJ_SALT . $this->getUserId());
			} else {
				$_SESSION[$this->defaultHash] = md5(uniqid(rand(), true));
			}
		}
		
		$this->setModel('Cart', pjCartModel::factory());
		$this->cart = new pjShoppingCart($this->getModel('Cart'), $_SESSION[$this->defaultHash]);
		$this->set('cart_arr', $this->cart->getAll());
	}
	
	public function afterFilter()
	{
		if (!isset($_GET['hide']) || (isset($_GET['hide']) && (int) $_GET['hide'] !== 1) &&
			in_array($_GET['action'], array('pjActionLogin', 'pjActionForgot', 'pjActionRegister',
				'pjActionProfile', 'pjActionFavs', 'pjActionProducts', 'pjActionProduct',
				'pjActionCart', 'pjActionCheckout', 'pjActionPreview')))
		{
			pjObject::import('Model', array('pjLocale:pjLocale', 'pjLocale:pjLocaleLanguage'));
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$this->set('locale_arr', $locale_arr);
		}
	}
	
	public function beforeFilter()
	{
		$this->setModel('Option', pjOptionModel::factory());
		$pjOptionModel = $this->getModel('Option');
		$this->option_arr = $pjOptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();
		if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
		{
			$this->pjActionSetLocale($_GET['locale']);
		}
		
		if ($this->pjActionGetLocale() === FALSE)
		{
			pjObject::import('Model', 'pjLocale:pjLocale');
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$this->pjActionSetLocale($locale_arr[0]['id']);
			}
		}
		pjAppController::setFields($this->getLocaleId());
	}

	public function beforeRender()
	{
		$this->set('price_arr', $this->pjActionGetPrice());
	}
	
	protected function pjActionGetPrice()
	{
		if ($this->cart->isEmpty())
		{
			return array('status' => 'ERR', 'code' => 105, 'text' => 'Empty cart.');
		}
		$data = $stock_id = $stocks = $product_id = array();
		$cart_arr = $this->get('cart_arr');
		foreach ($cart_arr as $cart_item)
		{
			if (isset($cart_item['stock_id']) && (int) $cart_item['stock_id'] > 0)
			{
				$stock_id[] = $cart_item['stock_id'];
			}
			$product_id[] = $cart_item['product_id'];
		}
		if (empty($stock_id))
		{
			return array('status' => 'ERR', 'code' => 105, 'text' => 'Empty cart.');
		}
		$stocks = pjStockModel::factory()
			->whereIn('t1.id', $stock_id)
			->findAll()
			->getDataPair('id');

		if (empty($stocks))
		{
			return array('status' => 'ERR', 'code' => 106, 'text' => 'Stocks in cart not found into the database.');
		}
		
		$pjExtraItemModel = pjExtraItemModel::factory();
		$extra_arr = pjExtraModel::factory()->whereIn('t1.product_id', $product_id)->findAll()->getDataPair('id', 'price');
		foreach ($extra_arr as $e_id => $e_price)
		{
			$extra_arr[$e_id] = array(
				'price' => $e_price,
				'extra_items' => $pjExtraItemModel->reset()
					->join('pjExtra', "t2.id=t1.extra_id AND t2.type='multi'", 'inner')
					->where('t1.extra_id', $e_id)->findAll()->getDataPair('id', 'price')
			);
		}
		
		$price = $discount = $tax = $shipping = $insurance = 0;
		foreach ($cart_arr as $cart_item)
		{
			if ($cart_item['qty'] > @$stocks[$cart_item['stock_id']]['qty'])
			{
				return array('status' => 'ERR', 'code' => 108, 'text' => 'Stock qty not enough.');
				break;
			}
			
			$amount = @$stocks[$cart_item['stock_id']]['price'] * $cart_item['qty'];
			
			$item = unserialize($cart_item['key_data']);
			if (isset($item['extra']) && is_array($item['extra']))
			{
				$extras = array();
				foreach ($item['extra'] as $extra)
				{
					if (strpos($extra, ".") !== FALSE)
					{
						list($extras['extra_id'], $extras['extra_item_id']) = explode(".", $extra);
						$amount += @$extra_arr[$extras['extra_id']]['extra_items'][$extras['extra_item_id']];
					} else {
						$amount += @$extra_arr[$extra]['price'];
					}
				}
			}
			
			$price += $amount;
			$discount += pjUtil::getDiscount($amount, $cart_item['product_id'], @$_SESSION[$this->defaultVoucher]);
		}
		
		if (isset($_SESSION[$this->defaultTax]) && (int) $_SESSION[$this->defaultTax] > 0)
		{
			$tax_arr = pjTaxModel::factory()->find($_SESSION[$this->defaultTax])->getData();
			if (!empty($tax_arr))
			{
				$shipping = (float) $tax_arr['shipping'];
				if ((float) $tax_arr['free'] > 0 && (float) $price >= (float) $tax_arr['free'])
				{
					$shipping = 0;
				}
				if ((float) $tax_arr['tax'] > 0)
				{
					$tax = (($price - $discount) * (float) $tax_arr['tax']) / 100;
				}
			}
		}
		
		switch ($this->option_arr['o_insurance_type'])
		{
			case 'percent':
				$insurance = (($price - $discount) * (float) $this->option_arr['o_insurance']) / 100;
				break;
			case 'amount':
				$insurance = (float) $this->option_arr['o_insurance'];
				break;
			default:
				$insurance = 0;
		}
		
		$data['price'] = $price;
		$data['discount'] = $discount;
		$data['insurance'] = $insurance;
		$data['shipping'] = $shipping;
		$data['tax'] = $tax;
		$data['total'] = $price + $shipping + $tax + $insurance - $discount;
		$data['total'] = $data['total'] > 0 ? $data['total'] : 0;
		
		return array('status' => 'OK', 'code' => 200, 'text' => 'Success', 'data' => $data);
	}
	
	protected function pjActionGetCart()
	{
		# Find out what qty is in current shopping cart for each stock
		$order_arr = $product_id = $stock_id = array();
		$cart_arr = $this->get('cart_arr');
		foreach ($cart_arr as $cart_item)
		{
			if (!isset($order_arr[$cart_item['stock_id']]))
			{
				$order_arr[$cart_item['stock_id']] = 0;
			}
			$order_arr[$cart_item['stock_id']] += $cart_item['qty'];
		
			$product_id[] = $cart_item['product_id'];
			if (!empty($cart_item['stock_id']))
			{
				$stock_id[] = $cart_item['stock_id'];
			}
		}
		
		$arr = pjProductModel::factory()
			->select(sprintf("t1.*, t2.content AS name,
				(SELECT GROUP_CONCAT(`category_id`) FROM `%1\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`",
				pjProductCategoryModel::factory()->getTable()
			))
			->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
			->join('pjStock', 't3.product_id=t1.id', 'inner')
			->whereIn('t1.id', $product_id)
			->groupBy('t1.id')
			->findAll()
			->toArray('category_ids', ',')
			->getData();

		$extra_arr = pjExtraModel::factory()
			->select('t1.*, t2.content AS name, t3.content AS title')
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
			->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='extra_title'", 'left outer')
			->whereIn('t1.product_id', $product_id)
			->orderBy('`title` ASC, `name` ASC')
			->findAll()
			->getData();

		$pjExtraItemModel = pjExtraItemModel::factory();
		$locale_id = $this->getLocaleId();
		foreach ($extra_arr as $k => $extra)
		{
			$extra_arr[$k]['extra_items'] = $pjExtraItemModel
				->reset()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='$locale_id' AND t2.field='extra_name'", 'left outer')
				->where('t1.extra_id', $extra['id'])
				->orderBy('t1.price ASC')
				->findAll()
				->getData();
		}
		
		$attr_arr = array();
		// Do not change col_name, direction
		$a_arr = pjAttributeModel::factory()
			->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
			->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
			->whereIn('t1.product_id', $product_id)
			->orderBy('t1.parent_id ASC, `name` ASC')
			->findAll()
			->getData();

		foreach ($a_arr as $attr)
		{
			if ((int) $attr['parent_id'] === 0)
			{
				$attr_arr[$attr['id']] = $attr;
			} else {
				if (!isset($attr_arr[$attr['parent_id']]['child']))
				{
					$attr_arr[$attr['parent_id']]['child'] = array();
				}
				$attr_arr[$attr['parent_id']]['child'][] = $attr;
			}
		}
		
		$tax_arr = pjTaxModel::factory()
			->select('t1.*, t2.content AS location')
			->join('pjMultiLang', "t2.model='pjTax' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->orderBy('`location` ASC')
			->findAll()
			->getData();
			
		$_stock_arr = pjStockModel::factory()
			->whereIn('t1.id', $stock_id)
			->findAll()
			->getData();
		$stock_arr = array();
		foreach ($_stock_arr as $stock)
		{
			$stock_arr[$stock['id']] = $stock;
		}
		
		$attr_arr = array_values($attr_arr);
		
		pjObject::import('Model', 'pjGallery:pjGallery');
		$image_arr = pjStockModel::factory()
			->select('t1.id, t2.small_path')
			->join('pjGallery', 't2.id=t1.image_id', 'left outer')
			->whereIn('t1.id', $stock_id)
			->findAll()
			->getDataPair('id', 'small_path');
		
		return compact('arr', 'extra_arr', 'order_arr', 'attr_arr', 'stock_arr', 'tax_arr', 'image_arr');
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
		
		$pjCaptcha = new pjCaptcha(PJ_WEB_PATH . 'obj/Lato-Bol.ttf', $this->defaultCaptcha, 6);
		$pjCaptcha
			->setImage(PJ_IMG_PATH . 'button.png')
			->init(@$_GET['rand']);
		exit;
	}
	
	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			echo isset($_SESSION[$this->defaultCaptcha]) && isset($_GET['captcha'])
				&& pjCaptcha::validate($_GET['captcha'], $_SESSION[$this->defaultCaptcha])
				? 'true' : 'false';
		}
		exit;
	}
		
	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		
		if (!isset($_POST['x_invoice_num']))
		{
			$this->log('Missing arguments');
			exit;
		}
		
		pjObject::import('Model', 'pjInvoice:pjInvoice');
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjOrderModel = pjOrderModel::factory();

		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['x_invoice_num'])
			->limit(1)
			->findAll()
			->getData();
		if (empty($invoice_arr))
		{
			$this->log('Invoice not found');
			exit;
		}
		$invoice_arr = $invoice_arr[0];
		$order_arr = $pjOrderModel
			->select(sprintf("t1.*, t2.content AS b_country, t3.content AS s_country, t4.email AS `admin_email`, t4.phone AS `admin_phone`,
				t6.content AS `payment_subject_client`, t7.content AS `payment_tokens_client`, t8.content AS `payment_subject_admin`,
				t9.content AS `payment_tokens_admin`, t10.content AS `payment_sms_admin`,
				t5.email, t5.client_name, t5.phone, t5.url, AES_DECRYPT(t5.password, '%s') AS `password`", PJ_SALT))
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
			->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
			->join('pjUser', 't4.id=1', 'left outer')
			->join('pjClient', 't5.id=t1.client_id', 'left outer')
			->join('pjMultiLang', sprintf("t6.model='pjOption' AND t6.foreign_id='%u' AND t6.locale=t1.locale_id AND t6.field='payment_subject_client'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t7.model='pjOption' AND t7.foreign_id='%u' AND t7.locale=t1.locale_id AND t7.field='payment_tokens_client'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t8.model='pjOption' AND t8.foreign_id='%u' AND t8.locale=t1.locale_id AND t8.field='payment_subject_admin'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t9.model='pjOption' AND t9.foreign_id='%u' AND t9.locale=t1.locale_id AND t9.field='payment_tokens_admin'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t10.model='pjOption' AND t10.foreign_id='%u' AND t10.locale=t1.locale_id AND t10.field='payment_sms_admin'", $this->getForeignId()), 'left outer')
			->where('t1.uuid', $invoice_arr['order_id'])
			->limit(1)
			->findAll()
			->getData();
		
		if (empty($order_arr))
		{
			$this->log('Order not found');
			exit;
		}
		$order_arr = $order_arr[0];
		
		$params = array(
			'transkey' => $this->option_arr['o_authorize_key'],
			'x_login' => $this->option_arr['o_authorize_mid'],
			'md5_setting' => $this->option_arr['o_authorize_hash'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		
		$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$pjOrderModel
				->set('id', $order_arr['id'])
				->modify(array(
					'status' => 'completed',
					'processed_on' => ':NOW()'
				));
				
			$pjInvoiceModel
				->reset()
				->set('id', $invoice_arr['id'])
				->modify(array('status' => 'paid', 'modified' => ':NOW()'));
						
			pjFront::pjActionConfirmSend($this->option_arr, $order_arr, 'payment');
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Order not confirmed. ' . $response['response_reason_text']);
		}
		exit;
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		
		if (!isset($_POST['custom']))
		{
			$this->log('Missing arguments');
			exit;
		}
		
		pjObject::import('Model', 'pjInvoice:pjInvoice');
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjOrderModel = pjOrderModel::factory();
		
		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['custom'])
			->limit(1)
			->findAll()
			->getData();

		if (empty($invoice_arr))
		{
			$this->log('Invoice not found');
			exit;
		}
		$invoice_arr = $invoice_arr[0];
		
		$order_arr = $pjOrderModel
			->select(sprintf("t1.*, t2.content AS b_country, t3.content AS s_country, t4.email AS `admin_email`, t4.phone AS `admin_phone`,
				t6.content AS `payment_subject_client`, t7.content AS `payment_tokens_client`, t8.content AS `payment_subject_admin`,
				t9.content AS `payment_tokens_admin`, t10.content AS `payment_sms_admin`,
				t5.email, t5.client_name, t5.phone, t5.url, AES_DECRYPT(t5.password, '%s') AS `password`", PJ_SALT))
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
			->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
			->join('pjUser', 't4.id=1', 'left outer')
			->join('pjClient', 't5.id=t1.client_id', 'left outer')
			->join('pjMultiLang', sprintf("t6.model='pjOption' AND t6.foreign_id='%u' AND t6.locale=t1.locale_id AND t6.field='payment_subject_client'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t7.model='pjOption' AND t7.foreign_id='%u' AND t7.locale=t1.locale_id AND t7.field='payment_tokens_client'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t8.model='pjOption' AND t8.foreign_id='%u' AND t8.locale=t1.locale_id AND t8.field='payment_subject_admin'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t9.model='pjOption' AND t9.foreign_id='%u' AND t9.locale=t1.locale_id AND t9.field='payment_tokens_admin'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t10.model='pjOption' AND t10.foreign_id='%u' AND t10.locale=t1.locale_id AND t10.field='payment_sms_admin'", $this->getForeignId()), 'left outer')
			->where('t1.uuid', $invoice_arr['order_id'])
			->limit(1)
			->findAll()
			->getData();
		if (empty($order_arr))
		{
			$this->log('Order not found');
			exit;
		}
		$order_arr = $order_arr[0];
		
		$params = array(
			'txn_id' => @$invoice_arr['txn_id'],
			'paypal_address' => @$this->option_arr['o_paypal_address'],
			'deposit' => @$invoice_arr['total'],
			'currency' => @$invoice_arr['currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);

		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjOrderModel->reset()->set('id', $order_arr['id'])->modify(array(
				'status' => 'completed',
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
			
			$pjInvoiceModel
				->reset()
				->set('id', $invoice_arr['id'])
				->modify(array('status' => 'paid', 'modified' => ':NOW()'));
						
			pjFront::pjActionConfirmSend($this->option_arr, $order_arr, 'payment');
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		exit;
	}
	
	protected static function pjActionConfirmSend($option_arr, $order_arr, $type)
	{
		if (!in_array($type, array('confirm', 'payment')))
		{
			return false;
		}
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$order_arr['products'] = pjAppController::pjActionGetProductsString($order_arr['id'], $order_arr['locale_id']);
		$tokens = pjAppController::getTokens($order_arr, $option_arr);

		switch ($type)
		{
			case 'confirm':
				//client
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr['confirm_subject_client']);
				$message = str_replace($tokens['search'], $tokens['replace'], $order_arr['confirm_tokens_client']);
				$Email
					->setTo($order_arr['email'])
					->setFrom($order_arr['admin_email'])
					->setSubject($subject)
					->send($message);
				//admin
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr['confirm_subject_admin']);
				$message = str_replace($tokens['search'], $tokens['replace'], $order_arr['confirm_tokens_admin']);
				$Email
					->setTo($order_arr['admin_email'])
					->setFrom($order_arr['admin_email'])
					->setSubject($subject)
					->send($message);
				
				# SMS
				if (pjObject::getPlugin('pjSms') !== NULL && isset($order_arr['admin_phone']) && !empty($order_arr['admin_phone']))
				{
					$dispatcher = new pjDispatcher();
					$controller = $dispatcher->createController(array('controller' => 'pjFront'));
					$controller->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => array(
						'number' => $order_arr['admin_phone'],
						'text' => str_replace($tokens['search'], $tokens['replace'], @$order_arr['confirm_sms_admin']),
						'key' => md5($option_arr['private_key'] . PJ_SALT),
					)), array('return'));
				}
				break;
			case 'payment':
				//client
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr['payment_subject_client']);
				$message = str_replace($tokens['search'], $tokens['replace'], $order_arr['payment_tokens_client']);
				$Email
					->setTo($order_arr['email'])
					->setFrom($order_arr['admin_email'])
					->setSubject($subject)
					->send($message);
				//admin
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr['payment_subject_admin']);
				$message = str_replace($tokens['search'], $tokens['replace'], $order_arr['payment_tokens_admin']);
				$Email
					->setTo($order_arr['admin_email'])
					->setFrom($order_arr['admin_email'])
					->setSubject($subject)
					->send($message);
				
				# SMS
				if (pjObject::getPlugin('pjSms') !== NULL && isset($order_arr['admin_phone']) && !empty($order_arr['admin_phone']))
				{
					$dispatcher = new pjDispatcher();
					$controller = $dispatcher->createController(array('controller' => 'pjFront'));
					$controller->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => array(
						'number' => $order_arr['admin_phone'],
						'text' => str_replace($tokens['search'], $tokens['replace'], @$order_arr['payment_sms_admin']),
						'key' => md5($option_arr['private_key'] . PJ_SALT),
					)), array('return'));
				}
				break;
		}
	}
	
	public function pjActionDigitalDownload()
	{
		$this->setLayout('pjActionEmpty');
		
		if (!isset($_GET['uuid']) || empty($_GET['uuid']) || !isset($_GET['hash']) || empty($_GET['hash']) || md5($_GET['uuid'] . PJ_SALT) != $_GET['hash'])
		{
			$this->set('status', 1);
			return;
		}
		
		$order = pjOrderModel::factory()->where('t1.uuid', $_GET['uuid'])->limit(1)->findAll()->getData();
		if (empty($order))
		{
			$this->set('status', 2);
			return;
		}
		
		$order = $order[0];
		if ($order['status'] != 'completed')
		{
			$this->set('status', 3);
			return;
		}
		
		$os_arr = pjOrderStockModel::factory()
			->select('t3.digital_file, t3.digital_name, t3.digital_expire, t2.processed_on,
				DATE_ADD(t2.processed_on, INTERVAL t3.digital_expire HOUR_SECOND) AS `expire_at`,
				IF(DATE_ADD(t2.processed_on, INTERVAL t3.digital_expire HOUR_SECOND) < NOW(), 1, 0) AS `is_expired`')
			->join('pjOrder', 't2.id=t1.order_id', 'inner')
			->join('pjProduct', "t3.id=t1.product_id AND t3.is_digital='1'", 'inner')
			->where('t1.order_id', $order['id'])
			->findAll()
			->getData();

		if (empty($os_arr))
		{
			$this->set('status', 4);
			return;
		}
		
		$digitals = $expired = array();
		foreach ($os_arr as $item)
		{
			if ((int) $item['is_expired'] === 0 || $item['digital_expire'] == '00:00:00')
			{
				$digitals[] = $item;
			} else {
				$expired[] = $item;
			}
		}
		
		if (empty($digitals))
		{
			$this->set('status', 5);
			return;
		}
		
		$zip = new pjZipStream();
		foreach ($digitals as $file)
		{
			if (empty($file['digital_file']) || !is_file($file['digital_file']))
			{
				continue;
			}
			$handle = @fopen($file['digital_file'], "rb");
			if ($handle)
			{
				$buffer = "";
				while (!feof($handle))
				{
					$buffer .= fgets($handle, 4096);
				}
				$zip->addFile($buffer, $file['digital_name']);
				fclose($handle);
			}
		}
		$zip->finalize();
		$zip->sendZip(sprintf("%s.zip", $order['uuid']));
		exit;
	}
	
	public function pjActionGetStocks()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				# Find out what qty is in current shopping cart for each stock
				$order_arr = array();
				$cart_arr = $this->get('cart_arr');
				foreach ($cart_arr as $cart_item)
				{
					if (!isset($order_arr[$cart_item['stock_id']]))
					{
						$order_arr[$cart_item['stock_id']] = 0;
					}
					$order_arr[$cart_item['stock_id']] += $cart_item['qty'];
				}
				
				$pjStockModel = pjStockModel::factory();
				$pjStockAttributeModel = pjStockAttributeModel::factory();
				$pjAttributeModel = pjAttributeModel::factory();
				
				$stock_arr = $pjStockModel
					->where('t1.product_id', $_GET['id'])
					->where('t1.qty > 0')
					->findAll()->getData();
				$stocks = $stock_ids = $qty = $price = array();
				foreach ($stock_arr as $k => $stock)
				{
					$_qty = $stock['qty'];
					if (isset($order_arr[$stock['id']]))
					{
						$_qty -= $order_arr[$stock['id']];
						if ($_qty < 1)
						{
							continue;
						}
					}
					$stock_ids[] = $stock['id'];
					$stocks[] = $pjStockAttributeModel
						->reset()
						->where('t1.stock_id', $stock['id'])
						->orderBy('t1.attribute_id ASC')
						->findAll()
						->getDataPair('attribute_parent_id', 'attribute_id');
						
					$qty[] = $_qty;
					$price[] = $stock['price'];
				}

				# -- Fix for empty values in stocks
				$attr_arr = $pjAttributeModel
					->where('t1.product_id', $_GET['id'])
					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
							SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
							FROM `%s` AS `TSA`
							INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
							WHERE TSA.product_id = t1.product_id
						) OR t1.parent_id IS NULL OR t1.parent_id = '0')", $pjStockAttributeModel->getTable(), $pjStockModel->getTable()))
					->findAll()
					->getDataPair('id', 'parent_id');
				
				foreach ($stocks as $k => $stock)
				{
					foreach ($stock as $_k => $_v)
					{
						if ((int) $_v === 0)
						{
							$stokkk = $stock;
							pjUtil::reArrange($stocks, $qty, $price, $stokkk, $attr_arr, $_k, $k);
						}
					}
				}
				# -- End fix
				
				# Attributes --
				$attr_arr = array();
				// Do not change col_name, direction
				$a_arr = $pjAttributeModel
					->reset()
					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
					->where('t1.product_id', $_GET['id'])
					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
							SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
							FROM `%s` AS `TSA`
							INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
							WHERE TSA.product_id = t1.product_id
						) OR t1.parent_id IS NULL OR t1.parent_id = '0')", $pjStockAttributeModel->getTable(), $pjStockModel->getTable()))
					->orderBy('t1.parent_id ASC, `name` ASC')
					->findAll()
					->getData();

				foreach ($a_arr as $attr)
				{
					if ((int) $attr['parent_id'] === 0)
					{
						$attr_arr[$attr['id']] = $attr;
					} else {
						if (!isset($attr_arr[$attr['parent_id']]['child']))
						{
							$attr_arr[$attr['parent_id']]['child'] = array();
						}
						$attr_arr[$attr['parent_id']]['child'][] = $attr;
					}
				}
				$attributes = array_values($attr_arr);
				# Attributes --
				
				pjAppController::jsonResponse(compact('stocks', 'qty', 'price', 'stock_ids', 'attributes'));
			}
		}
		exit;
	}

	public function pjActionSendToFriend()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['url']) || empty($_POST['url']) ||
				!isset($_POST['your_email']) || empty($_POST['your_email']) || !pjValidation::pjActionEmail($_POST['your_email']) ||
				!isset($_POST['your_name']) || empty($_POST['your_name']) ||
				!isset($_POST['friend_email']) || empty($_POST['friend_email']) || !pjValidation::pjActionEmail($_POST['friend_email']) ||
				!isset($_POST['friend_name']) || empty($_POST['friend_name'])
			)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('system_100', true)));
			}
			$Email = new pjEmail();
			if ($this->option_arr['o_send_email'] == 'smtp')
			{
				$Email
					->setTransport('smtp')
					->setSmtpHost($this->option_arr['o_smtp_host'])
					->setSmtpPort($this->option_arr['o_smtp_port'])
					->setSmtpUser($this->option_arr['o_smtp_user'])
					->setSmtpPass($this->option_arr['o_smtp_pass'])
				;
			}
			
			$subject = 'Shopping Cart / Product';
			$message = sprintf("Dear %1\$s,
			
Your friend %3\$s thinks this may be interested you:
%5\$s", $_POST['friend_name'], $_POST['friend_email'], $_POST['your_name'], $_POST['your_email'], $_POST['url']);

			$result = $Email
				->setTo($_POST['friend_email'])
				->setFrom($_POST['your_email'])
				->setSubject($subject)
				->send($message);
				
			if (!$result)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('system_101', true)));
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => __('system_200', true)));
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-type: text/javascript");
	}
	
	public function pjActionLoadCss()
	{
		$layout = isset($_GET['layout']) && in_array($_GET['layout'], $this->getLayoutRange()) ?
			(int) $_GET['layout'] : (int) $this->option_arr['o_layout'];

		$arr = array(
			array('file' => 'ShoppingCart'.$layout.'.txt', 'path' => PJ_CSS_PATH),
			array('file' => 'ShoppingCart'.$layout.'.css', 'path' => PJ_CSS_PATH),
			array('file' => 'jquery-ui-1.9.2.custom.min.css', 'path' => PJ_LIBS_PATH . 'pjQ/css/'),
			array('file' => 'jquery.fancybox-1.3.4.min.css', 'path' => PJ_LIBS_PATH . 'pjQ/fancybox/')
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			$string = FALSE;
			if ($stream = fopen($item['path'] . $item['file'], 'rb'))
			{
				$string = stream_get_contents($stream);
				fclose($stream);
			}
			
			if ($string !== FALSE)
			{
				echo str_replace(
					array('../img/', '[URL]', 'images/', 'fancybox/f',
						"'fancybox.png'", "'fancybox-x.png'", "'fancybox-y.png'", "'fancy_title_over.png'"),
					array(
						PJ_IMG_PATH,
						PJ_INSTALL_URL,
						PJ_LIBS_PATH . 'pjQ/css/images/',
						PJ_LIBS_PATH . 'pjQ/fancybox/f',
						PJ_LIBS_PATH . 'pjQ/fancybox/fancybox.png',
						PJ_LIBS_PATH . 'pjQ/fancybox/fancybox-x.png',
						PJ_LIBS_PATH . 'pjQ/fancybox/fancybox-y.png',
						PJ_LIBS_PATH . 'pjQ/fancybox/fancy_title_over.png'),
					$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionLogout()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if ($this->isLoged())
			{
				$_SESSION[$this->defaultUser] = NULL;
				unset($_SESSION[$this->defaultUser]);
				
				$_SESSION[$this->defaultHash] = NULL;
				unset($_SESSION[$this->defaultHash]);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => __('system_201', true)));
		}
		exit;
	}
	
	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				$this->pjActionSetLocale($_GET['locale_id']);
			}
		}
		exit;
	}
	
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}

	public function pjActionShowShipping()
	{
		$cart_arr = $this->get('cart_arr');
		foreach ($cart_arr as $cart_item)
		{
			$item = unserialize($cart_item['key_data']);
			if ((int) $item['is_digital'] === 0)
			{
				return true;
				break;
			}
		}
		
		return false;
	}
	
	protected function pjActionSaveToAddressBook($client_id, $data, $prefix='b_')
	{
		return pjAddressModel::factory()->setAttributes(array(
			'client_id' => $client_id,
			'country_id' => @$data[$prefix.'country_id'],
			'state' => @$data[$prefix.'state'],
			'city' => @$data[$prefix.'city'],
			'zip' => @$data[$prefix.'zip'],
			'address_1' => @$data[$prefix.'address_1'],
			'address_2' => @$data[$prefix.'address_2'],
			'name' => @$data[$prefix.'name']
		))->insert()->getInsertId();
	}
}
?>