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
	
	public function isEditor()
    {
   		return $this->getRoleId() == 2;
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
		mysql_query("SET SESSION time_zone = '$offset';");
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

		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale])) {
	    		pjObject::import('Model', 'pjLocale:pjLocale');
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1){
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			pjAppController::setFields($this->getLocaleId());
		}
    }
    
	public function pjActionBeforeInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('code' => 200, 'info' => array());
		$folders = array('app/web/upload', 'app/web/upload/bookings', 'app/web/upload/events', 'app/web/upload/events/thumb', 'app/web/upload/tickets', 'app/web/upload/tickets/barcodes', 'app/web/upload/tickets/pdfs');
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['code'] = 101;
				$result['info'][] = sprintf('You need to set write permissions (chmod 777) to directory located at %s', $dir);
			}
		}
		
		return $result;
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
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str)); // change everything to lowercase
		$str = trim($str); // trim leading and trailing spaces
		$str = preg_replace('/[_|\s]+/', $divider, $str); // change all spaces and underscores to a hyphen
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str); // remove all non-cyrillic, non-numeric characters except the hyphen
		$str = preg_replace('/[-]+/', $divider, $str); // replace multiple instances of the hyphen with a single instance
		$str = preg_replace('/^-+|-+$/', '', $str); // trim leading and trailing hyphens
		return $str;
	}
	
	protected function setPrice($current_id, $id, $post)
	{
		$pjPriceModel = pjPriceModel::factory();
		foreach ($_POST['price'] as $k => $v)
		{
			$price_data = array();
			$price_data['event_id'] = $id;
			$price_data['recurring'] = md5($current_id. $k . PJ_SALT);
			$price_data['title'] = $post['title'][$k];
			$price_data['price'] = $post['price'][$k];
			$price_data['available'] = $post['available'][$k];
			$pjPriceModel->reset()->setAttributes($price_data)->insert();
		}
	}
	
	protected function updatePrice($event_id, $post, $has_recurring)
	{
		$pjPriceModel = pjPriceModel::factory();
		$pjEventModel = pjEventModel::factory();
		
		$recurring_events_arr = $pjEventModel->reset()->where('recurring_id', $post['recurring_id'])->findAll()->getData();
		
		$exist_arr = array();
		$price_arr = $pjPriceModel->where('t1.event_id', $event_id)->findAll()->getData();
		foreach ($price_arr as $v)
		{
			$exist_arr[] = $v['id'];
		}
		
		$update_arr = array();
		$up_arr = array();
		foreach ($post['price'] as $k => $v)
		{
			if (strpos($k, "x_") === 0)
			{
				list(, $_k) = explode("_", $k);
				$update_arr[] = $_k;
				$up_arr[$_k]['price'] = $v;
				$up_arr[$_k]['title'] = $post['title'][$k];
				$up_arr[$_k]['available'] = $post['available'][$k];
			} else {
				
				if($has_recurring == 1)
				{
					foreach ($recurring_events_arr as $evt)
					{
						$price = array();
						$price['event_id'] = $evt['id'];
						$price['recurring'] = md5($event_id . $k . PJ_SALT);
						$price['title'] = $post['title'][$k];
						$price['price'] = $post['price'][$k];
						$price['available'] = $post['available'][$k];
						$pjPriceModel->reset()->setAttributes($price)->insert();						
					}
				}else{
					$price = array();
					$price['event_id'] = $event_id;
					$price['recurring'] = md5($event_id . $k . PJ_SALT);
					$price['title'] = $post['title'][$k];
					$price['price'] = $post['price'][$k];
					$price['available'] = $post['available'][$k];
					$pjPriceModel->reset()->setAttributes($price)->insert()->getInsertId();
				}
			}
		}
		
		$diff = array_diff($exist_arr, $update_arr);
		
		if (count($diff) > 0)
		{
			
			$delete_prices = $pjPriceModel->reset()->whereIn('t1.id', $diff)->findAll()->getData();
			
			foreach($delete_prices as $v)
			{
				if($has_recurring == 1)
				{
					$pjPriceModel->reset()->where('recurring', $v['recurring'])->eraseAll();
				}else{
					$delete_id = $v['id'];
					$pjPriceModel->reset()->where('id', $delete_id)->eraseAll();
				}
			}
			
		}
			
		foreach ($up_arr as $k => $v)
		{
			$data = array();
			if($has_recurring == 1)
			{
				$arr = $pjPriceModel->reset()->find($k)->getData();
				$data['title'] = $v['title'];
				$data['price'] = $v['price'];
				$data['available'] = $v['available'];
				$pjPriceModel->reset()->where('recurring', $arr['recurring'])->modifyAll($data);
			}else{
				$data['title'] = $v['title'];
				$data['price'] = $v['price'];
				$data['available'] = $v['available'];
				$pjPriceModel->reset()->where('id', $k)->limit(1)->modifyAll($data);
			}
		}
	}
	
	protected function getFromEmail()
	{
		$arr = pjUserModel::factory()->find(1)->getData();
		return $arr['email'];
	}
	
	protected function bookingEmail($booking_arr, $opt)
	{
		if (!in_array($opt, array('confirmation', 'payment')))
		{
			return false;
		}
		if(empty($booking_arr['o_email_'. $opt .'_subject']) || empty($booking_arr['o_email_' . $opt]))
		{
			return false;
		}
		$pjBookingDetailModel = pjBookingDetailModel::factory();
		
		$event_date = pjUtil::getEventDateTime($booking_arr['event_start_ts'], $booking_arr['event_end_ts'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format'], $booking_arr['o_show_start_time'], $booking_arr['o_show_end_time']);
		
		$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionCancel&id='.$booking_arr['id'].'&hash='.md5($booking_arr['id'].$booking_arr['created'].PJ_SALT);
		
		$pdf_tickets = PJ_INSTALL_URL . PJ_UPLOAD_PATH . 'tickets/pdfs/p_' . $booking_arr['unique_id'] . '.pdf';
		
		$event = $booking_arr['event_title']. ' | ' . $event_date;

		$booking_detail_arr = $pjBookingDetailModel->select('t1.*')->where('t1.booking_id', $booking_arr['id'])->findAll()->getData();

		$tickets = "\n";
		foreach($booking_detail_arr as $v)
		{
			$tickets .= $v['cnt'] . " x " . $v['price_title'] . "\n";
		}
		
		$search = array('{Name}', '{Email}', '{Phone}', '{Country}', '{City}', '{State}', '{Zip}', '{Address}', '{Tickets}', '{PDF_Tickets}', '{Notes}', '{CCType}', '{CCNum}', '{CCExp}', '{CCSec}', '{PaymentMethod}', '{Event}', '{EventTitle}', '{EventDateTime}', '{Total}', '{Tax}', '{BookingID}', '{CancelURL}');
		$replace = array($booking_arr['customer_name'], $booking_arr['customer_email'], $booking_arr['customer_phone'], $booking_arr['country_title'], $booking_arr['customer_city'], $booking_arr['customer_state'], $booking_arr['customer_zip'], $booking_arr['customer_address'], $tickets, $pdf_tickets, $booking_arr['customer_notes'], $booking_arr['cc_type'], $booking_arr['cc_num'], ($booking_arr['payment_method'] == 'creditcard' ? $booking_arr['cc_exp'] : NULL), $booking_arr['cc_code'], $booking_arr['payment_method'], $event, $booking_arr['event_title'], $event_date, $booking_arr['booking_total'] . " " . $this->option_arr['o_currency'], $booking_arr['booking_tax'] . " " . $this->option_arr['o_currency'], $booking_arr['unique_id'], $cancelURL);
		
		$pjEmail = new pjEmail();
					
		if ($this->option_arr['o_send_email'] == 'smtp')
		{
			$pjEmail
				->setTransport('smtp')
				->setSmtpHost($this->option_arr['o_smtp_host'])
				->setSmtpPort($this->option_arr['o_smtp_port'])
				->setSmtpUser($this->option_arr['o_smtp_user'])
				->setSmtpPass($this->option_arr['o_smtp_pass'])
			;
		}
		$from = $this->getFromEmail();
		$pjEmail->setContentType('text/plain');
		$pjEmail->setFrom($from);
		
		$subject = str_replace($search, $replace, $booking_arr['o_email_'. $opt .'_subject']);
		$message = str_replace($search, $replace, $booking_arr['o_email_' . $opt]);
		
		$pjEmail->setSubject($subject);
		
		$admin_arr = pjUserModel::factory()->where('role_id', 1)->findAll()->getData();
		
		#send to all administrator
		foreach($admin_arr as $admin)
		{
			$to = $admin['email'];
			$pjEmail->setTo($to);
			$pjEmail->send($message);
		}
		
		$to = $booking_arr['customer_email'];
		$pjEmail->setTo($to);
		$pjEmail->send($message);
		
	}
	
	protected function getTicketInfo($params){
		$ticket_info = '';
		$ticket_price = $params['price_title'] . " " .  __('lblTicket', true) . ", " . pjUtil::formatCurrencySign($params['unit_price'], $this->option_arr['o_currency']) . "<br/>";
		if(empty($params['ticket_detail']))
		{
			$ticket_info .= $params['customer_name'] . "<br/>";
			$ticket_info .= $params['customer_email'] . "<br/>";
			$ticket_info .= $ticket_price;
		}else{
			$search = array('{Name}', '{Email}','{Ticket}', '{EventTitle}', '{EventDateTime}');
			$replace = array($params['customer_name'], $params['customer_email'], $ticket_price, $params['event_title'], $params['event_datetime']);
			$ticket_info = str_replace($search, $replace, $params['ticket_detail']);
			$ticket_info = preg_replace('/\r\n|\n/', '<br />', $ticket_info);
		}
		
		return $ticket_info;
	}
}
?>