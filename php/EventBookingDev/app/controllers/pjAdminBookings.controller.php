<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminBookings extends pjAdmin
{                  
	public function pjActionCheckUniqueId()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && isset($_GET['unique_id']))
		{
			$pjBookingModel = pjBookingModel::factory();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjBookingModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjBookingModel->where('t1.unique_id', $_GET['unique_id'])->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionGetBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory()->join('pjEvent', 't2.id=t1.event_id');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where('t1.unique_id LIKE', "%$q%");
				$pjBookingModel->orWhere('t1.customer_name LIKE', "%$q%");
				$pjBookingModel->orWhere('t1.customer_email LIKE', "%$q%");
				$pjBookingModel->orWhere('t1.customer_phone LIKE', "%$q%");
			}
						
			if (isset($_GET['event_id']) && (int) $_GET['event_id'] > 0)
			{
				$pjBookingModel->where('t1.event_id', $_GET['event_id']);
			}
			if (isset($_GET['unique_id']) && $_GET['event_id'] != '')
			{
				$unique_id = pjObject::escapeString($_GET['unique_id']);
				$pjBookingModel->where('t1.unique_id LIKE', "%$unique_id%");
			}
			if (isset($_GET['customer_name']) && $_GET['customer_name'] != '')
			{
				$customer_name = pjObject::escapeString($_GET['customer_name']);
				$pjBookingModel->where('t1.customer_name LIKE', "%$customer_name%");
			}
			if (isset($_GET['customer_email']) && $_GET['customer_email'] != '')
			{
				$customer_email = pjObject::escapeString($_GET['customer_email']);
				$pjBookingModel->where('t1.customer_email LIKE', "%$customer_email%");
			}
			if (isset($_GET['from_ticket']) && $_GET['from_ticket'] != '')
			{
				$from = $_GET['from_ticket'];
				$pjBookingModel->where("t1.customer_people >=" , $from);
			}
			if (isset($_GET['to_ticket']) && $_GET['to_ticket'] != '')
			{
				$to = $_GET['to_ticket'];
				$pjBookingModel->where("t1.customer_people <=" , $to);
			}
			if (isset($_GET['from_price']) && $_GET['from_price'] != '')
			{
				$from = $_GET['from_price'];
				$pjBookingModel->where("t1.booking_total >=" , $from);
			}
			if (isset($_GET['to_price'])  && $_GET['to_price'] != '')
			{
				$to = $_GET['to_price'];
				$pjBookingModel->where("t1.booking_total <=" , $to);
			}	
			if (isset($_GET['status']) && !empty($_GET['status']))
			{
				$pjBookingModel->where('t1.booking_status', $_GET['status']);
			}
			
			$column = 'event_start_ts';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjBookingModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			$booking_arr = $pjBookingModel
				->select("t1.id, t1.unique_id, t1.event_id, t1.customer_name, 
							t1.booking_status, t1.booking_total, t1.customer_people,
							t2.event_start_ts, t2.event_end_ts, t2.o_show_start_time, t2.o_show_end_time")
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			
			$data = array();
			foreach($booking_arr as $k => $v){
				if(!empty($v['booking_total']))
				{
					$v['booking_total'] = pjUtil::formatCurrencySign($v['booking_total'], $this->option_arr['o_currency']);
				}else{
					$v['booking_total'] = pjUtil::formatCurrencySign(0, $this->option_arr['o_currency']);
				}
				$v['event_date'] = pjUtil::getEventDateTime($v['event_start_ts'], $v['event_end_ts'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format'],$v['o_show_start_time'], $v['o_show_end_time']);
				$data[$k] = $v;
			}	
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$event_arr = pjEventModel::factory()->where('t1.status', 'T')->select('t1.*')->orderBy("t1.event_start_ts ASC")->findAll()->getData();
			
			$this->set('event_arr', $event_arr);
			
			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjBookingModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionExportBooking()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjBookingModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Bookings-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjEventModel = pjEventModel::factory();
			
			if (isset($_POST['booking_create']))
			{
				$data = array();
				
				$pjBookingModel = pjBookingModel::factory();
				$pjBookingDetailModel = pjBookingDetailModel::factory();
				$pjBookingTicketModel = pjBookingTicketModel::factory();
				
				$data['customer_ip']= $_SERVER['REMOTE_ADDR'];
				
				$post = array_merge($_POST, $data);

				if (!$pjBookingModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR04");
				}
				
				$insert_id = $pjBookingModel->setAttributes($post)->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					$details = array();
					$tickets = array();
					$customer_people = 0;
					$ticket_number = 1;
					$price_arr = pjPriceModel::factory()->where('event_id', $_POST['event_id'])->findAll()->getData();
					
					$details['booking_id'] = $insert_id;
					$tickets['booking_id'] = $insert_id;
					foreach($price_arr as $v)
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
						
						$pjBookingDetailModel->reset()->setAttributes($details)->insert();
						
						for($i = 1; $i <= $details['cnt']; $i++)
						{
							$tickets['ticket_id'] = $_POST['unique_id'] . '-' . $ticket_number;
							$tickets['unit_price'] = $v['price'];
							$tickets['price_title'] = $v['title'];
							$pjBookingTicketModel->reset()->setAttributes($tickets)->insert();
							
							$ticket_number++;
						}
					}
					$pjBookingModel->reset()->where('id', $insert_id)->limit(1)->modifyAll(array('customer_people' => $customer_people));
					
					$booking_arr = $pjBookingModel->reset()->select('t1.*, t2.event_title, t2.event_start_ts, t2.event_end_ts, t2.o_email_confirmation_subject, t2.o_email_confirmation, t2.o_email_payment_subject, t2.o_email_payment, t3.country_title')
							->join('pjEvent', 't1.event_id = t2.id', 'left')
							->join('pjCountry', 't1.customer_country = t3.id', 'left')
							->find($insert_id)->getData();

					$ticket_arr = $pjBookingTicketModel->reset()->select('t1.*')->where('booking_id', $insert_id)->findAll()->getData();
					$event_arr = $pjEventModel->reset()->find($_POST['event_id'])->getData();
					
					$ticket_data = array();
					
					foreach($ticket_arr as $v){
						$v['event_title'] = $booking_arr['event_title'];
						$v['event_datetime'] = pjUtil::getEventDateTime($event_arr['event_start_ts'], $event_arr['event_end_ts'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format'], $event_arr['o_show_start_time'], $event_arr['o_show_end_time']);
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
					
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR03");
				} else {
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR04");
				}
			}
			
			$event_arr = $pjEventModel->reset()->select('t1.*')->orderBy("t1.event_start_ts ASC")->findAll()->getData();
			
			$event_arr = pjSanitize::clean($event_arr);
			
			$country_arr = pjCountryModel::factory()->where('t1.status', 'T')->orderBy('country_title ASC')->findAll()->getData();
			
			$this->set('event_arr', $event_arr);
			$this->set('country_arr', $country_arr);

			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjBookingModel = pjBookingModel::factory();
			$pjBookingDetailModel = pjBookingDetailModel::factory();
			$pjBookingTicketModel = pjBookingTicketModel::factory();
			$pjEventModel = pjEventModel::factory();

			$booking = $pjBookingModel
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp, '%1\$s') AS `cc_exp`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`", PJ_SALT))
				->join('pjEvent', 't2.id=t1.event_id')
				->find($_REQUEST['id'])->getData();

			if (empty($booking) || count($booking) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR08");
			}
			
			$event = $pjEventModel->find($booking['event_id'])->getData();
			
			if (empty($event) || count($event) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR09");
			}
			
			if (isset($_POST['booking_update']))
			{
				
				$data = array();
				
				$data['customer_ip']= $_SERVER['REMOTE_ADDR'];
				
				$post = array_merge($_POST, $data);
				
				if (!$pjBookingModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR02");
				}
				$pjBookingModel->reset()->set('id', $_POST['id'])->modify($post);
				
				$details = array();
				$tickets = array();
				$customer_people = 0;
				$ticket_number = 1;
				
				$price_arr = pjPriceModel::factory()->where('event_id', $_POST['event_id'])->findAll()->getData();
				
				$details['booking_id'] = $_POST['id'];
				$tickets['booking_id'] = $_POST['id'];
				
				$this->deleteTicketInfo($_POST['id'], $_POST['unique_id']);
				
				$pjBookingDetailModel->where('booking_id', $_POST['id'])->eraseAll();
				$pjBookingTicketModel->where('booking_id', $_POST['id'])->eraseAll();
				
				foreach($price_arr as $v)
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
					
					$pjBookingDetailModel->reset()->setAttributes($details)->insert();
					
					for($i = 1; $i <= $details['cnt']; $i++)
					{
						$tickets['ticket_id'] = $_POST['unique_id'] . '-' . $ticket_number;
						$tickets['unit_price'] = $v['price'];
						$tickets['price_title'] = $v['title'];
						$pjBookingTicketModel->reset()->setAttributes($tickets)->insert();
						
						$ticket_number++;
					}
				}
				$pjBookingModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array('customer_people' => $customer_people));
				
				$booking_arr = $pjBookingModel->reset()->select('t1.*, t2.event_title, t2.event_start_ts, t2.event_end_ts, t2.o_email_confirmation_subject, t2.o_email_confirmation, t2.o_email_payment_subject, t2.o_email_payment, t3.country_title')
						->join('pjEvent', 't1.event_id = t2.id', 'left')
						->join('pjCountry', 't1.customer_country = t3.id', 'left')
						->find($_POST['id'])->getData();

				$ticket_arr = $pjBookingTicketModel->reset()->select('t1.*')->where('booking_id', $_POST['id'])->findAll()->getData();
				$event_arr = $pjEventModel->reset()->find($_POST['event_id'])->getData();
				$ticket_data = array();
				
				foreach($ticket_arr as $v){
					$v['event_title'] = $booking_arr['event_title'];
					$v['event_datetime'] = pjUtil::getEventDateTime($event_arr['event_start_ts'], $event_arr['event_end_ts'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format'], $event_arr['o_show_start_time'], $event_arr['o_show_end_time']);
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
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR01");
			} else {
				$this->set('arr', $booking);
			}
			
			$event_arr = $pjEventModel->reset()->select('t1.*')->orderBy("t1.event_start_ts ASC")->findAll()->getData();
			$event_arr = pjSanitize::clean($event_arr);
			
			$country_arr = pjCountryModel::factory()->where('t1.status', 'T')->orderBy('country_title ASC')->findAll()->getData();
			
			$this->set('event_arr', $event_arr);
			$this->set('country_arr', $country_arr);
			
			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminBookings.js');
		}
	}
	
	public function pjActionResend()
	{
		if(isset($_POST['resend_email']))
		{			
			$booking_id = $_POST['id'];
				
			if(!empty($_POST['to']))
			{
				$subject = stripslashes($_POST['subject']);
				$to = stripslashes($_POST['to']);
				$from = $this->getFromEmail();
				$message = stripslashes($_POST['message']);
				
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
				
				$pjEmail->setContentType('text/plain');
				$pjEmail->setFrom($from);
				$pjEmail->setSubject($subject);
				$pjEmail->setTo($to);
				$pjEmail->send($message);

				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionResend&id=$booking_id&err=ARS02");
			}else{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionResend&id=$booking_id&err=ARS01");
			}
			
		}else{
			
			$booking_id = $_GET['id'];
			
			$booking_arr = pjBookingModel::factory()->select('t1.*, t2.event_title, t2.event_start_ts, t2.event_end_ts, t2.o_show_start_time, t2.o_show_end_time, t3.country_title')
							->join('pjEvent', 't1.event_id = t2.id', 'left')
							->join('pjCountry', 't1.customer_country = t3.id', 'left')
							->find($booking_id)->getData();
			
			$event_id = $booking_arr['event_id'];
			$event_arr = pjEventModel::factory()->find($event_id)->getData();
			
			$event_date = pjUtil::getEventDateTime($booking_arr['event_start_ts'], $booking_arr['event_end_ts'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format'], $booking_arr['o_show_start_time'], $booking_arr['o_show_end_time']);
			
			$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionCancel&id='.$booking_arr['id'].'&hash='.md5($booking_arr['id'].$booking_arr['created'].PJ_SALT);
			
			$pdf_tickets = PJ_INSTALL_URL . PJ_UPLOAD_PATH . 'tickets/pdfs/p_' . $booking_arr['unique_id'] . '.pdf';
			
			$event = $booking_arr['event_title'] . ' | ' . $event_date;

			$booking_detail_arr = pjBookingDetailModel::factory()->select('t1.*')->where('t1.booking_id', $booking_arr['id'])->findAll()->getData();
	
			$tickets = "\n";
			foreach($booking_detail_arr as $v)
			{
				$tickets .= $v['cnt'] . " x " . $v['price_title'] . "\n";
			}
			$search = array('{Name}', '{Email}', '{Phone}', '{Country}', '{City}', '{State}', '{Zip}', '{Address}', '{Tickets}', '{PDF_Tickets}', '{Notes}', '{CCType}', '{CCNum}', '{CCExp}', '{CCSec}', '{PaymentMethod}', '{Event}', '{EventTitle}', '{EventDateTime}', '{Total}', '{Tax}', '{BookingID}', '{CancelURL}');
			$replace = array($booking_arr['customer_name'], $booking_arr['customer_email'], $booking_arr['customer_phone'], $booking_arr['country_title'], $booking_arr['customer_city'], $booking_arr['customer_state'], $booking_arr['customer_zip'], $booking_arr['customer_address'], $tickets, $pdf_tickets, $booking_arr['customer_notes'], $booking_arr['cc_type'], $booking_arr['cc_num'], ($booking_arr['payment_method'] == 'creditcard' ? $booking_arr['cc_exp'] : NULL), $booking_arr['cc_code'], $booking_arr['payment_method'], $event, $booking_arr['event_title'], $event_date, $booking_arr['booking_total'] . " " . $this->option_arr['o_currency'], $booking_arr['booking_tax'] . " " . $this->option_arr['o_currency'], $booking_arr['unique_id'], $cancelURL);
			
			$event_arr['o_email_confirmation'] = str_replace($search, $replace, $event_arr['o_email_confirmation']);
			$event_arr['o_email_payment'] = str_replace($search, $replace, $event_arr['o_email_payment']);
			
			$this->set('booking_arr', $booking_arr);
			$this->set('event_arr', $event_arr);
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminBookings.js');
		}
	}
	
	public function pjActionGetPrices()
	{
		$this->setAjax(true);
		
		$event_id = $_GET['id'];
		
		$price_arr = pjPriceModel::factory()->select("t1.*, (SELECT SUM(cnt) FROM `" .pjBookingDetailModel::factory()->getTable(). "` as t2 WHERE t2.price_id = t1.id AND t2.booking_id IN(SELECT t3.id FROM `".pjBookingModel::factory()->getTable()."` as t3 WHERE t3.booking_status='confirmed')) as cnt_booked")
						->where('event_id', $event_id)->findAll()->getData();
		$this->set('price_arr', $price_arr);
	}
	
	public function pjActionGetUpdatePrices()
	{
		$this->setAjax(true);
		
		$pjBookingDetailModel = pjBookingDetailModel::factory();
		$pjBookingModel = pjBookingModel::factory();
		
		$event_id = $_GET['id'];
		$booking_id = $_GET['booking_id'];
		$booking_arr = $pjBookingModel->find($booking_id)->getData();
		$booking_detail_arr = $pjBookingDetailModel->where('booking_id', $booking_id)->findAll()->getData();
		
		$price_booking = array();
		foreach($booking_detail_arr as $v)
		{
			$price_booking[$v['price_id']] = $v['cnt'];
		}
		
		$price_arr = pjPriceModel::factory()->select("t1.*, (SELECT SUM(cnt) FROM `" .$pjBookingDetailModel->getTable(). "` as t2 WHERE t2.price_id = t1.id AND t2.booking_id IN(SELECT t3.id FROM `".$pjBookingModel->getTable()."` as t3 WHERE t3.event_id = $event_id AND t3.booking_status='confirmed')) as cnt_booked")
						->where('event_id', $event_id)->findAll()->getData();
		
		if($booking_arr['booking_status'] == 'confirmed')
		{
			$this->set('is_confirmed', 1);
		}else{
			$this->set('is_confirmed', 0);
		}				
		$this->set('price_arr', $price_arr);
		$this->set('price_booking', $price_booking);
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjBookingModel = pjBookingModel::factory();
			$arr = $pjBookingModel->find($_GET['id'])->getData();
			if ($pjBookingModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjBookingDetailModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				$this->deleteTicketInfo($_GET['id'], $arr['unique_id']);
				pjBookingTicketModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjBookingModel = pjBookingModel::factory();
				
				$booking_arr = $pjBookingModel->whereIn('id', $_POST['record'])->findAll()->getData();
				
				foreach($booking_arr as $b_arr){
					$this->deleteTicketInfo($b_arr['id'], $b_arr['unique_id']);
				}
				
				$pjBookingModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjBookingDetailModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingTicketModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionReadBarcode()
	{
		if(isset($_POST['read_barcode']))
		{
			$ticket_arr = pjBookingTicketModel::factory()->select('t1.*, t3.event_title, t3.event_start_ts, t3.event_end_ts, t3.o_show_start_time, t3.o_show_end_time, t2.event_id, t2.booking_status, t2.customer_name, t2.customer_email, t2.customer_phone')
							->join('pjBooking', 't1.booking_id=t2.id', 'left')
							->join('pjEvent', 't2.event_id = t3.id', 'left')
							->where('ticket_id', $_POST['barcode_label'])->findAll()->getData();
							
			$status = 1;
			
			if(count($ticket_arr) > 0)
			{
				$arr = $ticket_arr[0];
				
				if($arr['booking_status'] != 'confirmed')
				{
					$status = 2;
				}else if($arr['is_used'] == 'T'){
					$status = 3;
				}else{
					
				}
				$details_arr = pjBookingDetailModel::factory()->where('booking_id', $arr['booking_id'])->findAll()->getData();
				$this->set('arr', $arr);
				$this->set('details_arr', $details_arr);
			}else{
				$status = 4;
			}
			$this->set('ticket_status', $status);
		}
		
		$this->appendJs('pjAdminBookings.js');
	}
	
	public function pjActionSetUseTicket()
	{
		$this->setAjax(true);
		
		$json_arr = array();
		
		pjBookingTicketModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array('is_used' => 'T'));
		$json_arr['status'] = 1;
		
		pjAppController::jsonResponse($json_arr);		
	}
	
	private function deleteTicketInfo($booking_id, $unique_id)
	{
		$ticket_arr = pjBookingTicketModel::factory()->where('booking_id', $booking_id)->findAll()->getData();
		foreach($ticket_arr as $v)
		{
			$barcode_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/barcodes/b_'. $v['ticket_id'] .'.png';
			$ticket_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/t_' . $v['ticket_id'] . '.png';
			if(is_file($barcode_path)){
				@unlink($barcode_path);
			}
			if(is_file($ticket_path)){
				@unlink($ticket_path);
			}
		}
		$pdf_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/pdfs/p_'. $unique_id . '.pdf';
		if(is_file($pdf_path)){
			@unlink($pdf_path);
		}
	}
}
?>