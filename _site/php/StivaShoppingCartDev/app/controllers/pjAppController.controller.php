<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
if (is_file(PJ_FRAMEWORK_PATH . 'pjController.class.php'))
{
	require_once PJ_FRAMEWORK_PATH . 'pjController.class.php';
}
class pjAppController extends pjController
{
	public $models = array();

	public $defaultLocale = 'admin_locale_id';
	
	private $layoutRange = array(1,2);
	
	public function getLayoutRange()
	{
		return $this->layoutRange;
	}
	
	public function isInvoiceReady()
	{
		return $this->isAdmin();
	}
	
	public function getModel($key)
	{
		if (array_key_exists($key, $this->models))
		{
			return $this->models[$key];
		}
		
		return false;
	}
	
	public function setModel($key, $value)
	{
		$this->models[$key] = $value;
		
		return true;
	}
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
    	pjAppModel::factory()
    		->prepare("SET SESSION time_zone = :offset;")
    		->exec(array('offset' => $offset));
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
    	$this->appendJs('jquery-1.8.2.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
    	$this->appendJs('pjAdminCore.js');
    	$this->appendCss('reset.css');
    	
    	$this->appendJs('jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/js/');
		$this->appendCss('jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/css/smoothness/');
				
		$this->appendCss('admin.css');
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->setModel('Option', pjOptionModel::factory());
			$this->option_arr = $this->getModel('Option')->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				pjObject::import('Model', 'pjLocale:pjLocale');
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			pjAppController::setFields($this->getLocaleId());
		}
    }
    
	public static function getDiscount($data, $option_arr)
	{
		if (!isset($data['code']) || empty($data['code']))
		{
			// Missing params
			return array('status' => 'ERR', 'code' => 100, 'text' => 'Voucher code couldn\'t be empty.');
		}
		$arr = pjVoucherModel::factory()
			->select(sprintf("t1.*, (SELECT GROUP_CONCAT(`product_id`) FROM `%s` WHERE `voucher_id` = `t1`.`id` LIMIT 1) AS `products`", pjVoucherProductModel::factory()->getTable()))
			->where('t1.code', $data['code'])
			->limit(1)
			->findAll()
			->toArray('products', ',')
			->getData();
			
		if (empty($arr))
		{
			// Not found
			return array('status' => 'ERR', 'code' => 101, 'text' => 'Voucher not found.');
		}
		$arr = $arr[0];
		
		$date = $data['date'];
		if (isset($data['hour']) && isset($data['minute']))
		{
			$time = $data['hour'] . ":" . $data['minute'] . ":00";
		}
		if (!isset($time))
		{
			$time = "00:00:00";
		}
		if (empty($date))
		{
			// Empty date
			return array('status' => 'ERR', 'code' => 103, 'text' => 'Date couldn\'t be empty.');
		}
		$d = strtotime($date);
		$dt = strtotime($date . " ". $time);
		
		$valid = false;
		switch ($arr['valid'])
		{
			case 'fixed':
				$time_from = strtotime($arr['date_from'] . " " . $arr['time_from']);
				$time_to = strtotime($arr['date_to'] . " " . $arr['time_to']);
				if ($time_from <= $dt && $time_to >= $dt)
				{
					// Valid
					$valid = true;
				}
				break;
			case 'period':
				$d_from = strtotime($arr['date_from']);
				$d_to = strtotime($arr['date_to']);
				$t_from = strtotime($arr['date_from'] . " " . $arr['time_from']);
				$t_to = strtotime($arr['date_to'] . " " . $arr['time_to']);
				if ($d_from <= $d && $d_to >= $d && $t_from <= $dt && $t_to >= $dt)
				{
					// Valid
					$valid = true;
				}
				break;
			case 'recurring':
				$t_from = strtotime($date . " " . $arr['time_from']);
				$t_to = strtotime($date . " " . $arr['time_to']);
				if ($arr['every'] == strtolower(date("l", $dt)) && $t_from <= $dt && $t_to >= $dt)
				{
					// Valid
					$valid = true;
				}
				break;
		}
	
		if (!$valid)
		{
			// Out of date
			return array('status' => 'ERR', 'code' => 102, 'text' => 'Voucher code is out of date.');
		}
		
		// Valid
		return array(
			'status' => 'OK',
			'code' => 200,
			'text' => 'Voucher code has been applied.',
			'voucher_code' => $arr['code'],
			'voucher_type' => $arr['type'],
			'voucher_discount' => $arr['discount'],
		 	'voucher_products' => $arr['products']
		);
	}
	
    public function getForeignId()
    {
    	return 1;
    }
    
    public static function setFields($locale)
    {
		$fields = pjMultiLangModel::factory()
			->select('t1.content, t2.key')
			->join('pjField', "t2.id=t1.foreign_id", 'inner')
			->where('t1.locale', $locale)
			->where('t1.model', 'pjField')
			->where('t1.field', 'title')
			->findAll()
			->getDataPair('key', 'content');
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public static function friendlyURL($str, $divider='-')
	{
		$str = pjMultibyte::strtolower($str);
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}

	public static function addToHistory($record_id, $user_id, $table, $before, $after)
	{
		return pjHistoryModel::factory()->setAttributes(array(
			'record_id' => $record_id,
			'user_id' => $user_id,
			'table_name' => $table,
			'before' => base64_encode(serialize($before)),
			'after' => base64_encode(serialize($after)),
			'ip' => $_SERVER['REMOTE_ADDR']
		))->insert()->getInsertId();
	}

	public static function getTokens($order_arr, $option_arr)
    {
    	$search = array(
    		'{BillingName}', '{BillingCountry}', '{BillingCity}', '{BillingState}',
    		'{BillingZip}', '{BillingAddress1}', '{BillingAddress2}',
    		'{ShippingName}', '{ShippingCountry}', '{ShippingCity}', '{ShippingState}',
    		'{ShippingZip}', '{ShippingAddress1}', '{ShippingAddress2}',
    		'{ClientName}', '{ClientEmail}', '{ClientPassword}', '{ClientPhone}', '{ClientURL}',
    		'{CCType}', '{CCNum}', '{CCExpMonth}',
    		'{CCExpYear}', '{CCSec}',
    		'{PaymentMethod}', '{Price}', '{Discount}',
    		'{Insurance}', '{Shipping}',
    		'{Tax}', '{Total}', '{Voucher}',
    		'{Notes}', '{OrderID}', '{OrderUUID}',
    		'{DigitalDownload}', '{Products}'
    	);
		$replace = array(
			$order_arr['b_name'], @$order_arr['b_country'], $order_arr['b_city'], $order_arr['b_state'],
			$order_arr['b_zip'], $order_arr['b_address_1'], $order_arr['b_address_2'],
			$order_arr['s_name'], @$order_arr['s_country'], $order_arr['s_city'], $order_arr['s_state'],
			$order_arr['s_zip'], $order_arr['s_address_1'], $order_arr['s_address_2'],
			$order_arr['client_name'], $order_arr['email'], $order_arr['password'], $order_arr['phone'], $order_arr['url'],
			$order_arr['cc_type'], $order_arr['cc_num'], ($order_arr['payment_method'] == 'creditcard' ? $order_arr['cc_exp_month'] : NULL),
			($order_arr['payment_method'] == 'creditcard' ? $order_arr['cc_exp_year'] : NULL), $order_arr['cc_code'],
			$order_arr['payment_method'], $order_arr['price'] . " " . $option_arr['o_currency'], $order_arr['discount'] . " " . $option_arr['o_currency'],
			$order_arr['insurance'] . " " . $option_arr['o_currency'], $order_arr['shipping'] . " " . $option_arr['o_currency'],
			$order_arr['tax'] . " " . $option_arr['o_currency'], $order_arr['total'] . " " . $option_arr['o_currency'], $order_arr['voucher'],
			$order_arr['notes'], $order_arr['id'], $order_arr['uuid'],
			sprintf("%sindex.php?controller=pjFront&action=pjActionDigitalDownload&uuid=%s&hash=%s", PJ_INSTALL_URL, $order_arr['uuid'], md5($order_arr['uuid'] . PJ_SALT)), @$order_arr['products']
		);
		return compact('search', 'replace');
    }
    
    public static function pjActionGetOrderStock($order_id, $locale_id)
    {
    	$os_arr = pjOrderStockModel::factory()
			->select("t1.*, t2.sku, t3.content AS name,
				(SELECT GROUP_CONCAT(CONCAT_WS('_', `attribute_id`, `attribute_parent_id`))
					FROM `".pjStockAttributeModel::factory()->getTable()."`
					WHERE `stock_id` = `t1`.`stock_id`
					LIMIT 1) AS `attr`,
				(SELECT GROUP_CONCAT(CONCAT_WS('.', `extra_id`, `extra_item_id`))
					FROM `".pjOrderExtraModel::factory()->getTable()."`
					WHERE `order_stock_id` = `t1`.`id`
					LIMIT 1) AS `extra`")
			->join('pjProduct', 't2.id=t1.product_id', 'left outer')
			->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'", 'left outer')
			->where('t1.order_id', $order_id)
			->findAll()
			->getData();
		
		$product_id = array();
		foreach ($os_arr as $item)
		{
			$product_id[] = $item['product_id'];
		}
			
		$attr_arr = $a_arr = array();
		if (!empty($product_id))
		{
			// Do not change col_name, direction
			$a_arr = pjAttributeModel::factory()
				->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='$locale_id'", 'left outer')
				->whereIn('t1.product_id', $product_id)
				->orderBy('t1.parent_id ASC, `name` ASC')
				->findAll()
				->getData();
		}
		
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
		$attr_arr = array_values($attr_arr);
		
		$pjExtraItemModel = pjExtraItemModel::factory();
		$extra_arr = pjExtraModel::factory()
			->select('t1.*, t2.content AS name, t3.content AS title')
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='$locale_id' AND t2.field='extra_name'", 'left outer')
			->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='$locale_id' AND t3.field='extra_title'", 'left outer')
			->orderBy('`title` ASC, `name` ASC')
			->findAll()
			->getData();
			

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
		
		return compact('os_arr', 'extra_arr', 'attr_arr');
    }

    public static function pjActionGetProductsString($order_id, $locale_id)
    {
    	$result = pjAppController::pjActionGetOrderStock($order_id, $locale_id);
    	if (!isset($result['os_arr']) || empty($result['os_arr']))
		{
			return '';
		}
		
		$stack = array();
		foreach ($result['os_arr'] as $item)
		{
			$stack[] = sprintf("%s x %u", $item['name'], (int) $item['qty']);
			$attrs = array();
			if (isset($item['attr']) && !empty($item['attr']))
			{
				$at = array();
				$a = explode(",", $item['attr']);
				foreach ($a as $v)
				{
					$t = explode("_", $v);
					$at[$t[1]] = $t[0];
				}
				foreach ($at as $attr_parent_id => $attr_id)
				{
					foreach ($result['attr_arr'] as $attr)
					{
						if ($attr['id'] == $attr_parent_id)
						{
							foreach ($attr['child'] as $child)
							{
								if ($child['id'] == $attr_id)
								{
									$attrs[] = sprintf('%s: %s', $attr['name'], $child['name']);
									break;
								}
							}
						}
					}
				}
			}
			if (!empty($attrs))
			{
				$stack[] = join("; ", $attrs);
			}
			//Extras
			$extras = array();
			if (isset($item['extra']) && !empty($item['extra']))
			{
				$a = explode(",", $item['extra']);
				foreach ($a as $eid)
				{
					if (strpos($eid, ".") === FALSE)
					{
						//single
						foreach ($result['extra_arr'] as $extra)
						{
							if ($extra['id'] == $eid)
							{
								$extras[] = $extra['name'];
								break;
							}
						}
					} else {
						//multi
						list($e_id, $ei_id) = explode(".", $eid);
						foreach ($result['extra_arr'] as $extra)
						{
							if ($extra['id'] == $e_id && isset($extra['extra_items']) && !empty($extra['extra_items']))
							{
								foreach ($extra['extra_items'] as $extra_item)
								{
									if ($extra_item['id'] == $ei_id)
									{
										$extras[] = $extra_item['name'];
										break;
									}
								}
								break;
							}
						}
					}
				}
			}
			if (!empty($extras))
			{
				$stack[] = join("; ", $extras);
			}
			$stack[] = str_repeat("-", 20);
		}
		
		return join("\n", $stack);
    }
    
	protected function pjActionGenerateInvoice($order_id)
	{
		if (!isset($order_id) || (int) $order_id <= 0)
		{
			return array('status' => 'ERR', 'code' => 400, 'text' => 'ID is not set ot invalid.');
		}
		$arr = pjOrderModel::factory()
			->select('t1.*, t2.email, t2.phone, t2.url')
			->join('pjClient', 't2.id=t1.client_id', 'left outer')
			->find($order_id)->getData();
		if (empty($arr))
		{
			return array('status' => 'ERR', 'code' => 404, 'text' => 'Order not found.');
		}
		
		$os_arr = pjOrderStockModel::factory()
			->select(sprintf("t1.*, t2.content AS product_name,
			(SELECT GROUP_CONCAT(CONCAT_WS('~:~', m2.content, m1.content) SEPARATOR '~..~')
				FROM `%1\$s`
				LEFT OUTER JOIN `%2\$s` AS `m1` ON m1.model='pjAttribute' AND m1.foreign_id = attribute_id AND m1.field = 'name' AND m1.locale = '%3\$u'
				LEFT OUTER JOIN `%2\$s` AS `m2` ON m2.model='pjAttribute' AND m2.foreign_id = attribute_parent_id AND m2.field = 'name' AND m2.locale = '%3\$u'
				WHERE stock_id = t1.stock_id
				LIMIT 1) AS `attr`
			", pjStockAttributeModel::factory()->getTable(), pjMultiLangModel::factory()->getTable(), $arr['locale_id']))
			->join('pjMultiLang', sprintf("t2.model='pjProduct' AND t2.foreign_id=t1.product_id AND t2.field='name' AND t2.locale='%u'", $arr['locale_id']), 'left outer')
			->where('t1.order_id', $order_id)
			->findAll()
			->toArray('attr', '~..~')
			->getData();
		
		$items = array();
		if (!empty($os_arr))
		{
			foreach ($os_arr as $attr)
			{
				$items[] = array(
					'name' => $attr['product_name'],
					'description' => str_replace('~:~', ': ', join("; ", $attr['attr'])),
					'qty' => $attr['qty'],
					'unit_price' => $attr['price'],
					'amount' => number_format($attr['qty'] * $attr['price'], 2, ".", "")
				);
			}
			$items[] = array(
				'name' => __('order_insurance', true),
				'description' => NULL,
				'qty' => 1,
				'unit_price' => $arr['insurance'],
				'amount' => $arr['insurance']
			);
			$items[] = array(
				'name' => __('order_shipping', true),
				'description' => NULL,
				'qty' => 1,
				'unit_price' => $arr['shipping'],
				'amount' => $arr['shipping']
			);
		} else {
			$items[] = array(
				'name' => 'Order payment',
				'description' => '',
				'qty' => 1,
				'unit_price' => $arr['total'],
				'amount' => $arr['total']
			);
		}
		
		$map = array(
			'completed' => 'paid',
			'cancelled' => 'cancelled',
			'new' => 'not_paid',
			'pending' => 'not_paid'
		);
		
		$response = $this->requestAction(
			array(
	    		'controller' => 'pjInvoice',
	    		'action' => 'pjActionCreate',
	    		'params' => array(
    				'key' => md5($this->option_arr['private_key'] . PJ_SALT),
					'uuid' => pjUtil::uuid(),
					'order_id' => $arr['uuid'],
					'foreign_id' => $this->getForeignId(),
					'issue_date' => ':CURDATE()',
					'due_date' => ':CURDATE()',
					'created' => ':NOW()',
					//'modified' => ':NULL',
					'status' => @$map[$arr['status']],
					'subtotal' => $arr['price'] + $arr['insurance'] + $arr['shipping'],
					'discount' => $arr['discount'],
					'tax' => $arr['tax'],
					'shipping' => $arr['shipping'],
					'total' => $arr['total'],
					'paid_deposit' => 0,
					'amount_due' => 0,
					'currency' => $this->option_arr['o_currency'],
					'notes' => $arr['notes'],
					'b_billing_address' => $arr['b_address_1'],
					'b_name' => $arr['b_name'],
					'b_address' => $arr['b_address_1'],
					'b_street_address' => $arr['b_address_2'],
					'b_city' => $arr['b_city'],
					'b_state' => $arr['b_state'],
					'b_zip' => $arr['b_zip'],
					'b_phone' => $arr['phone'],
					'b_email' => $arr['email'],
					'b_url' => $arr['url'],
					's_shipping_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_1'] : $arr['s_address_1'],
					's_name' => (int) $arr['same_as'] === 1 ? $arr['b_name'] : $arr['s_name'],
					's_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_1'] : $arr['s_address_1'],
					's_street_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_2'] : $arr['s_address_2'],
					's_city' => (int) $arr['same_as'] === 1 ? $arr['b_city'] : $arr['s_city'],
					's_state' => (int) $arr['same_as'] === 1 ? $arr['b_state'] : $arr['s_state'],
					's_zip' => (int) $arr['same_as'] === 1 ? $arr['b_zip'] : $arr['s_zip'],
					's_phone' => $arr['phone'],
					's_email' => $arr['email'],
					's_url' => $arr['url'],
					'items' => $items
    			)
    		),
    		array('return')
		);

		return $response;
	}

	public function pjActionAfterInstall()
	{
		pjObject::import('Model', 'pjInvoice:pjInvoiceConfig');
		pjInvoiceConfigModel::factory()->set('id', 1)->modify(array(
			'o_booking_url' => "index.php?controller=pjAdminOrders&action=pjActionUpdate&uuid={ORDER_ID}"
		));
		
		$query = sprintf("UPDATE `%s`
			SET `content` = :content
			WHERE `model` = :model
			AND `foreign_id` = (SELECT `id` FROM `%s` WHERE `key` = :key LIMIT 1)
			AND `field` = :field",
			pjMultiLangModel::factory()->getTable(), pjFieldModel::factory()->getTable()
		);
		pjAppModel::factory()->prepare($query)->exec(array(
			'content' => 'Order URL - Token: {ORDER_ID}',
			'model' => 'pjField',
			'field' => 'title',
			'key' => 'plugin_invoice_i_booking_url'
		));
		
		$query = sprintf("UPDATE `%s`
			SET `label` = :label
			WHERE `key` = :key
			LIMIT 1",
			pjFieldModel::factory()->getTable()
		);
		pjAppModel::factory()->prepare($query)->exec(array(
			'label' => 'Invoice plugin / Order URL - Token: {ORDER_ID}',
			'key' => 'plugin_invoice_i_booking_url'
		));
	}
}
?>