<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAppController.controller.php';
class pjFront extends pjAppController
{
	public $defaultCaptcha = 'StivaSoftCaptcha';
	
	public $defaultLocale = 'front_locale_id';
	
	public function __construct()
	{
		if($_GET['action'] != 'pjActionCancel')
		{
			$this->setLayout('pjActionFront');
			ob_start();
		}else{
			$this->setLayout('pjActionCancel');
		}
	}

	public function afterFilter()
	{		
		
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();

		if(isset($_GET['topic_id']))
		{
			$this->setTopic($_GET['topic_id']);
		}
		
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
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		
		$Captcha = new pjCaptcha('app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage('app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}


	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
				
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 101;
		}else{
			echo 100;
		}
	}
	
	public function pjActionSetLocale()
	{
		$this->setLocaleId(@$_GET['locale']);
		pjUtil::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function pjActionLoadCss()
	{
		header("Content-type: text/css");
		if(!isset($_GET['cssfile']))
		{
			$css_content = @file_get_contents(PJ_CSS_PATH . 'front_layout_1.css');
		}else{
			$css_content = @file_get_contents(PJ_CSS_PATH . $_GET['cssfile']);
		}
		echo str_replace(array('../img/'), array(PJ_IMG_PATH), $css_content) . "\n";
		$css_content = @file_get_contents(PJ_CSS_PATH . 'front_lib.css');
		echo str_replace(array('../img/'), array(PJ_IMG_PATH), $css_content) . "\n";
		exit;
	}
	
	public function pjActionLoadJs()
	{
		header("Content-type: text/javascript");
		$arr = array(
			array('file' => 'jabb-0.4.3.js', 'path' => PJ_LIBS_PATH . 'jabb/'),
			array('file' => 'pjLoad.js', 'path' => PJ_JS_PATH)
		);
		header("Content-type: text/javascript");
		foreach ($arr as $item)
		{
			$js_content = file_get_contents($item['path'] . $item['file']);
			echo $js_content . "\n";
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		$pjCategoryModel = pjCategoryModel::factory();
		$pjCategoryModel->where("t1.status", 'T');
		$category_arr = $pjCategoryModel->orderBy('t1.category ASC')->findAll()->getData();
		$this->set('category_arr', $category_arr);
		
	}
	
	public function pjActionLoadEvents()
	{
		$this->setAjax(true);
		
		$pjEventModel = pjEventModel::factory();
		
		$pjEventModel->where("t1.status", 'T');
		
		if($_GET['cate'] > 0)
		{
			$pjEventModel->where('t1.category_id', $_GET['cate']);
		}
		$pjEventModel->where("(t1.category_id IN(SELECT t5.id FROM `".pjCategoryModel::factory()->getTable()."` AS t5 WHERE t5.status = 'T') OR t1.category_id IS NULL)");
		if($_GET['view_mode'] == 'list')
		{
			if(!isset($_GET['event_id']))
			{
				list($y, $n, $j) = explode("-", date("Y-n-j"));
				$midnight = mktime(0, 0, 0, $n, $j, $y);
				
				$pjEventModel->where("t1.event_start_ts >=", $midnight);
				
				if(isset($_GET['period']) && $_GET['period'] != '' && $_GET['period'] != 'all'){
					$where = pjUtil::getWherePeriod($_GET['period']);
					if($where != '')
					{
						$pjEventModel->where($where);
					}
				}
				
				$total = $pjEventModel->findCount()->getData();
				$rowCount = $this->option_arr['o_events_per_page'];
				$pages = ceil($total / $rowCount);
				$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
				$offset = ((int) $page - 1) * $rowCount;
				if ($page > $pages)
				{
					$page = $pages;
				}
				$pjEventModel->limit($rowCount, $offset);
				$this->set('pages', $pages);
				$this->set('page', $page);
				$this->set('paginator', array('pages' => $pages, 'total' => $total));
			}else{
				$pjEventModel->where('t1.id', $_GET['event_id']);
			}
		}else{
			$month = (int)$_GET['month'];
			$year = (int)$_GET['year'];
			
			$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
			$lastDayOfMonth = mktime(23, 59, 59, $month + 1, 0, $year);
			
			$pjEventModel->where("(t1.event_start_ts BETWEEN $firstDayOfMonth AND $lastDayOfMonth OR t1.event_end_ts BETWEEN $firstDayOfMonth AND $lastDayOfMonth OR (t1.event_start_ts < $firstDayOfMonth AND t1.event_end_ts > $lastDayOfMonth))");
		}
		$event_arr = $pjEventModel	->select("t1.*, t2.category, (SELECT SUM(t3.available) FROM `".pjPriceModel::factory()->getTable()."` AS t3 WHERE t3.event_id=t1.id) AS `total_avail`, 
											(SELECT SUM(t4.cnt) FROM `".pjBookingDetailModel::factory()->getTable()."` AS t4 WHERE t4.booking_id IN (SELECT t5.id FROM `".pjBookingModel::factory()->getTable()."` as t5 WHERE t5.event_id = t1.id AND t5.booking_status ='confirmed')) AS `total_booked`")
									->join('pjCategory', 't2.id=t1.category_id', 'left outer')
									->orderBy('t1.event_start_ts ASC')->findAll()->getData();
		$event_date_arr = array();
		foreach ($event_arr as $v){
			$start_date = date('Y-m-d', $v['event_start_ts']);
			$end_date = date('Y-m-d', $v['event_end_ts']);
			
			$event_field_arr = array();
			
			$event_field_arr['id'] = $v['id'];
			$event_field_arr['event_title'] = $v['event_title'];
			$event_field_arr['location'] = $v['location'];
			$event_field_arr['category'] = $v['category'];
			$event_field_arr['event_start_ts'] = $v['event_start_ts'];
			$event_field_arr['event_end_ts'] = $v['event_end_ts'];
			$event_field_arr['event_img'] = $v['event_img'];
			$event_field_arr['event_thumb'] = $v['event_thumb'];
			$event_field_arr['o_show_start_time'] = $v['o_show_start_time'];
			$event_field_arr['o_show_end_time'] = $v['o_show_end_time'];
			$event_field_arr['total_booked'] = $v['total_booked'];
			$event_field_arr['total_avail'] = $v['total_avail'];
			$event_field_arr['description'] = $v['description'];
			
			if($start_date == $end_date)
			{
				$event_date_arr[$start_date][] = $event_field_arr;
			}else{
				while($start_date <= $end_date)
				{
					$event_date_arr[$start_date][] = $event_field_arr;
					$start_date = date('Y-m-d', strtotime($start_date . '+1 day'));
				}
			}
		}
		$this->set('event_arr', $event_arr);
		$this->set('event_date_arr', $event_date_arr);
	}
	
	public function pjActionLoadEventDetail()
	{
		$this->setAjax(true);
		
		$date = $_GET['dt'];
		
		$pjEventModel = pjEventModel::factory();
		
		$pjEventModel->where("CAST(FROM_UNIXTIME(t1.event_start_ts) AS DATE) <= '$date' AND CAST(FROM_UNIXTIME(t1.event_end_ts) AS DATE) >= '$date'");
		
		if($_GET['cate'] > 0)
		{
			$pjEventModel->where('t1.category_id', $_GET['cate']);
		}
		
		$event_arr = $pjEventModel	->select("t1.*, t2.category, (SELECT SUM(t3.available) FROM `".pjPriceModel::factory()->getTable()."` AS t3 WHERE t3.event_id=t1.id) AS `total_avail`, 
											(SELECT SUM(t4.cnt) FROM `".pjBookingDetailModel::factory()->getTable()."` AS t4 WHERE t4.booking_id IN (SELECT t5.id FROM `".pjBookingModel::factory()->getTable()."` as t5 WHERE t5.event_id = t1.id AND t5.booking_status ='confirmed')) AS `total_booked`")
									->join('pjCategory', 't2.id=t1.category_id', 'left outer')
									->orderBy('t1.event_start_ts ASC')->findAll()->getData();
									
		$this->set('event_arr', $event_arr);
	}
	
	public function pjActionView()
	{
		$this->setAjax(true);
		
		
		$pjEventModel = pjEventModel::factory();
		
		$arr = $pjEventModel	->select("t1.*, t2.category, (SELECT SUM(t3.available) FROM `".pjPriceModel::factory()->getTable()."` AS t3 WHERE t3.event_id=t1.id) AS `total_avail`, 
											(SELECT SUM(t4.cnt) FROM `".pjBookingDetailModel::factory()->getTable()."` AS t4 WHERE t4.booking_id IN (SELECT t5.id FROM `".pjBookingModel::factory()->getTable()."` as t5 WHERE t5.event_id = t1.id AND t5.booking_status ='confirmed')) AS `total_booked`")
								->join('pjCategory', 't2.id=t1.category_id', 'left outer')
								->find($_GET['id'])->getData();
								
		$price_arr = pjPriceModel::factory()->select("t1.*, (SELECT SUM(cnt) FROM `" .pjBookingDetailModel::factory()->getTable(). "` as t2 WHERE t2.price_id = t1.id AND t2.booking_id IN(SELECT t3.id FROM `".pjBookingModel::factory()->getTable()."` as t3 WHERE t3.booking_status='confirmed')) as cnt_booked")
						->where('event_id', $_GET['id'])->findAll()->getData();
									
		$this->set('arr', $arr);
		$this->set('price_arr', $price_arr);
	}
	
	public function pjActionLoadBookingForm()
	{
		$this->setAjax(true);
		
		$event_id = $_GET['event_id'];
		
		$arr = pjEventModel::factory()->find($event_id)->getData();
		
		$price_arr = pjPriceModel::factory()->select("t1.*, (SELECT SUM(cnt) FROM `" .pjBookingDetailModel::factory()->getTable(). "` as t2 WHERE t2.price_id = t1.id AND t2.booking_id IN(SELECT t3.id FROM `".pjBookingModel::factory()->getTable()."` as t3 WHERE t3.booking_status='confirmed')) as cnt_booked")
						->where('event_id', $event_id)->findAll()->getData();
		
		$country_arr = pjCountryModel::factory()->where('t1.status', 'T')->orderBy('country_title ASC')->findAll()->getData();
		
		$this->set('arr', $arr);
		$this->set('price_arr', $price_arr);
		$this->set('country_arr', $country_arr);
	}
	
	public function pjActionLoadBookingSummary()
	{
		$this->setAjax(true);
		
		$event_id = $_GET['event_id'];
		
		$price = $_POST['total_price'];
		$tax = ($price * $this->option_arr['o_tax_payment']) / 100;
		$total = $price + $tax;
		$deposit = ($total * $this->option_arr['o_deposit_payment']) / 100;
		
		$arr = pjEventModel::factory()->find($event_id)->getData();
		
		$country_arr = pjCountryModel::factory()->where('t1.status', 'T')->orderBy('country_title ASC')->findAll()->getData();
		
		$this->set('arr', $arr);
		$this->set('amount', compact('total', 'tax', 'price', 'deposit'));
		$this->set('country_arr', $country_arr);
	}
	
	public function pjActionBookingSave()
	{
		$this->setAjax(true);
		
		$pjPriceModel = pjPriceModel::factory();
		$pjEventModel = pjEventModel::factory();
		$pjBookingModel = pjBookingModel::factory();
		$pjBookingDetailModel = pjBookingDetailModel::factory();
		$pjBookingTicketModel = pjBookingTicketModel::factory();
		
		$event_id = $_POST['event_id'];
		
		$price = $_POST['total_price'];
		$tax = ($price * $this->option_arr['o_tax_payment']) / 100;
		$total = $price + $tax;
		$deposit = ($total * $this->option_arr['o_deposit_payment']) / 100;
		
		$data = array();
		$data['unique_id'] = pjUtil::getUniqueID();
		$data['booking_status'] = $this->option_arr['o_default_status_if_not_paid'];
		$data['booking_total'] = $total;
		$data['booking_tax'] = $tax;
		$data['booking_deposit'] = $deposit;
		$data['payment_option']= 'deposit';
		$data['customer_ip']= $_SERVER['REMOTE_ADDR'];
		
		if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'creditcard')
		{
			$data['cc_exp'] = $_POST['cc_exp_year'] . '-' . $_POST['cc_exp_month'];
		}
		
		$event_arr = $pjEventModel->find($event_id)->getData();
		if (count($event_arr) == 0)
		{
			$insert_id = false;
		} else {
			$insert_id = $pjBookingModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
		}
		if ($insert_id !== false && (int) $insert_id > 0)
		{
			$details = array();
			$tickets = array();
			$customer_people = 0;
			$ticket_number = 1;
			$price_arr = $pjPriceModel->where('event_id', $event_id)->findAll()->getData();
			$details['booking_id'] = $insert_id;
			$tickets['booking_id'] = $insert_id;
			
			foreach($price_arr as $k => $v)
			{
				$price_id = $v['id'];
				if(isset($_POST['price_' . $price_id]))
				{
					if($_POST['price_' . $price_id] > 0)
					{
						$customer_people += $_POST['price_' . $price_id];
					}
				}
				$details['price_id'] = $price_id;
				$details['price'] = $_POST['price_' . $price_id] * $v['price'];
				$details['unit_price'] = $v['price'];
				$details['price_title'] = $v['title'];
				$details['cnt'] = $_POST['price_' . $price_id];
				
				for($i = 1; $i <= $details['cnt']; $i++)
				{
					$tickets['ticket_id'] = $data['unique_id'] . '-' . $ticket_number;
					$tickets['unit_price'] = $v['price'];
					$tickets['price_title'] = $v['title'];
					$pjBookingTicketModel->reset()->setAttributes($tickets)->insert();
					
					$ticket_number++;
				}
				
				$pjBookingDetailModel->reset()->setAttributes($details)->insert();
			}
			$pjBookingModel->reset()->where('id', $insert_id)->limit(1)->modifyAll(array('customer_people' => $customer_people));
			
			$booking_arr = $pjBookingModel->reset()->select('t1.*, t2.event_title, t2.event_start_ts, t2.event_end_ts, t2.o_show_start_time,t2.o_show_end_time, t2.o_email_confirmation_subject, t2.o_email_confirmation, t2.o_email_payment_subject, t2.o_email_payment, t3.country_title')
							->join('pjEvent', 't1.event_id = t2.id', 'left')
							->join('pjCountry', 't1.customer_country = t3.id', 'left')
							->find($insert_id)->getData();

			$ticket_arr = $pjBookingTicketModel->reset()->select('t1.*')->where('booking_id', $insert_id)->findAll()->getData();
			
			$ticket_data = array();
			
			foreach($ticket_arr as $v){
				$v['event_datetime'] = pjUtil::getEventDateTime($event_arr['event_start_ts'], $event_arr['event_end_ts'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format'], $event_arr['o_show_start_time'], $event_arr['o_show_end_time']);;
				$v['event_title'] = $event_arr['event_title'];
				$v['customer_name'] = $booking_arr['customer_name'];
				$v['customer_email'] = $booking_arr['customer_email'];
				$v['ticket_detail'] = $event_arr['ticket_info'];
				$v['ticket_img'] = $event_arr['ticket_img'];
				$v['unique_id'] = $booking_arr['unique_id'];
				$v['ticket_info'] = $this->getTicketInfo($v);
				$ticket_data[] = $v;
			}
			
			$pjTicketPdf = new pjTicketPdf();
			$pjTicketPdf->generatePdf($ticket_data);
			
			$this->bookingEmail($booking_arr, 'confirmation');
			
			$json = "{'code':200,'text':'','booking_id': $insert_id, 'payment':'".@$_POST['payment_method']."'}";
		}else{
			$json = "{'code':100,'text':''}";
		}
		header("Content-type: text/json");
		echo $json;
	}
		
	public function pjActionGetPaymentForm()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory();
			
			$booking_arr = $pjBookingModel->reset()->select('t1.*, t2.event_title, t2.event_start_ts, t2.event_end_ts, t2.o_email_confirmation_subject, t2.o_email_confirmation, t2.o_email_payment_subject, t2.o_email_payment, t3.country_title')
							->join('pjEvent', 't1.event_id = t2.id', 'left')
							->join('pjCountry', 't1.customer_country = t3.id', 'left')
							->find($_POST['id'])->getData();
							
			switch ($booking_arr['payment_method'])
			{
				case 'paypal':
					$this->set('params', array(
						'name' => 'ebcal_paypal_form',
						'id' => 'ebcal_paypal_form',
						'business' => $this->option_arr['o_paypal_address'],
						'item_name' => $booking_arr['event_title'],
						'custom' => $booking_arr['id'],
						'amount' => $booking_arr['booking_deposit'],
						'currency_code' => $this->option_arr['o_currency'],
						'return' => $this->option_arr['o_thankyou_page'],
						'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaypal',
						'target' => '_self'
					));
					break;
				case 'authorize':
					$this->set('params', array(
						'name' => 'ebcal_authorize_form',
						'id' => 'ebcal_authorize_form',
						'timezone' => $this->option_arr['o_authorize_timezone'],
						'transkey' => $this->option_arr['o_authorize_transkey'],
						'x_login' => $this->option_arr['o_authorize_merchant_id'],
						'x_description' => $booking_arr['event_title'],
						'x_amount' => $booking_arr['booking_deposit'],
						'x_invoice_num' => $booking_arr['id'],
						'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize'
					));
					break;
			}
			
			$this->set('booking_arr', $booking_arr);
			$_POST = array();
			$this->log('submit payment form');
		}
	}
	
	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
		$booking_arr = $pjBookingModel->reset()->select('t1.*, t2.event_title, t2.event_start_ts, t2.event_end_ts, t2.o_show_start_time, t2.o_show_end_time, t2.o_email_confirmation_subject, t2.o_email_confirmation, t2.o_email_payment_subject, t2.o_email_payment, t3.country_title')
							->join('pjEvent', 't1.event_id = t2.id', 'left')
							->join('pjCountry', 't1.customer_country = t3.id', 'left')
							->find($_POST['x_invoice_num'])->getData();

		if (count($booking_arr) > 0)
		{
			$event_arr = pjEventModel::factory()->find($booking_arr['event_id'])->getData();
			
			$params = array(
				'transkey' => $this->option_arr['o_authorize_transkey'],
				'x_login' => $this->option_arr['o_authorize_merchant_id'],
				'md5_setting' => $this->option_arr['o_authorize_md5_hash'],
				'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
			
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && $response['status'] === 'OK')
			{
				$pjBookingModel->reset()
					->setAttributes(array('id' => $response['transaction_id']))
					->modify(array('booking_status' => $this->option_arr['o_default_status_if_paid']));
					
				
				$this->bookingEmail($booking_arr, 'payment');
				
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
		$booking_arr = $pjBookingModel->select('t1.*, t2.event_title, t2.event_start_ts, t2.event_end_ts, t2.o_show_start_time, t2.o_show_end_time, t2.o_email_confirmation_subject, t2.o_email_confirmation, t2.o_email_payment_subject, t2.o_email_payment, t3.country_title')
							->join('pjEvent', 't1.event_id = t2.id', 'left')
							->join('pjCountry', 't1.customer_country = t3.id', 'left')
							->find($_POST['custom'])->getData();
		
		$params = array(
			'txn_id' => @$booking_arr['txn_id'],
			'paypal_address' => $this->option_arr['o_paypal_address'],
			'deposit' => @$booking_arr['booking_deposit'],
			'currency' => $this->option_arr['o_currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjBookingModel->reset()->setAttributes(array('id' => $booking_arr['id']))->modify(array(
				'booking_status' => $this->option_arr['o_default_status_if_paid'],
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
			$this->bookingEmail($booking_arr, 'payment');
			
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		exit;
	}
	
	public function pjActionCancel()
	{
		if(isset($_POST['booking_cancel']))
		{
			$subject = __('emailCancelSubject',true);
			$message = __('emailCancelBody',true);
			
			if(!empty($subject) && !empty($message))
			{
				pjBookingModel::factory()->setAttributes(array('id' => $_POST['id']))->modify(array(
					'booking_status' => 'cancelled'
				));
				
				$search = array('{BookingID}');
				$replace = array($_POST['id']);
				
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
				
				$message = str_replace($search, $replace, $message);
				
				$pjEmail->setSubject($subject);
				
				$to = $from;
				$pjEmail->setTo($to);
				$pjEmail->send($message);
			}	
			pjUtil::redirect($this->option_arr['o_cancel_booking_page']);	
		}else{
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$id = $_GET['id'];
				$hash = $_GET['hash'];
				
				$arr = pjBookingModel::factory()->select('t1.*, t2.event_title, t2.event_start_ts, t2.event_end_ts, t2.o_show_start_time, t2.o_show_end_time, t2.o_email_confirmation_subject, t2.o_email_confirmation, t2.o_email_payment_subject, t2.o_email_payment, t3.country_title')
									->join('pjEvent', 't1.event_id = t2.id', 'left')
									->join('pjCountry', 't1.customer_country = t3.id', 'left')
									->find($id)->getData();
				if(count($arr) > 0)
				{
					if($arr['booking_status'] == 'cancelled')
					{
						$this->set('status', 4);
					}else{
						if($hash == md5($arr['id'].$arr['created'].PJ_SALT)){
							$this->set('arr', $arr);
						}else{
							$this->set('status', 3);
						}
					}
				}else{
					$this->set('status', 2);
				}
			} elseif (!isset($_GET['err'])) {
				$this->set('status', 1);
			}
		}
		
		$this->appendCss('front_cancel.css');
	}
}
?>