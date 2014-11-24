<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminEvents extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['event_create']))
			{
				$pjEventModel = pjEventModel::factory();
				
				$data = array();
				$range_days = 0;
				$one_day = 60 * 60 * 24;
			
				$_start = $_POST['event_start_ts']; unset($_POST['event_start_ts']);
				$_end = $_POST['event_end_ts']; unset($_POST['event_end_ts']);
				
				if(count(explode(" ", $_start)) == 3)
				{
					list($_start_date, $_start_time, $_start_period) = explode(" ", $_start);
					list($_end_date, $_end_time, $_end_period) = explode(" ", $_end);
					$_start_time = pjUtil::formatTime($_start_time . ' ' . $_start_period, $this->option_arr['o_time_format']);
					$_end_time = pjUtil::formatTime($_end_time . ' ' . $_end_period, $this->option_arr['o_time_format']);
				}else{
					list($_start_date, $_start_time) = explode(" ", $_start);
					list($_end_date, $_end_time) = explode(" ", $_end);
					$_start_time = pjUtil::formatTime($_start_time, $this->option_arr['o_time_format']);
					$_end_time = pjUtil::formatTime($_end_time, $this->option_arr['o_time_format']);
				}
				
				$data['event_start_ts'] = strtotime(pjUtil::formatDate($_start_date, $this->option_arr['o_date_format']) . ' ' . $_start_time);
				$data['event_end_ts'] = strtotime(pjUtil::formatDate($_end_date, $this->option_arr['o_date_format']) . ' ' . $_end_time);
				
				$event_start_ts = $data['event_start_ts'];
				$event_end_ts = $data['event_end_ts'];
				
				$id = $pjEventModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$hash = md5(uniqid(rand(), true));
					if (isset($_FILES['event_img']) && !empty($_FILES['event_img']['tmp_name']))
					{
						$handle = new pjCUpload($_FILES['event_img']);
						if ($handle->uploaded)
						{
							$handle->allowed = array('image/*');
							$handle->mime_check = true;
							$handle->file_new_name_body = $id . '_' .$hash;
							$handle->image_convert = 'jpg';
							$handle->jpeg_quality = 100;
							$handle->process(PJ_UPLOAD_PATH . 'events/');
							if ($handle->processed)
							{
								$data['event_img'] = str_replace('\\', '/', $handle->file_dst_pathname);
								$data['event_img'] = preg_replace('/\/+/', '/', $data['event_img']);
								$data['event_img'] = $data['event_img'];
							}
							
							$handle->allowed = array('image/*');
							$handle->mime_check = true;
							$handle->file_new_name_body = $id . '_' .$hash;
							$handle->image_convert = 'jpg';
							$handle->jpeg_quality = 100;
							$handle->image_resize = true;
							$handle->image_x = 90;
							$handle->image_ratio_y = true;
							$handle->process(PJ_UPLOAD_PATH . 'events/thumb');
							if ($handle->processed)
							{
								$data['event_thumb'] = str_replace('\\', '/', $handle->file_dst_pathname);
								$data['event_thumb'] = preg_replace('/\/+/', '/', $data['event_thumb']);
								$data['event_thumb'] = $data['event_thumb'];
							}
							
							$handle->allowed = array('image/*');
							$handle->mime_check = true;
							$handle->file_new_name_body = $id . '_' .$hash;
							$handle->image_convert = 'jpg';
							$handle->jpeg_quality = 100;
							$handle->image_resize = true;
							$handle->image_x = 226;
							$handle->image_ratio_y = true;
							$handle->process(PJ_UPLOAD_PATH . 'events/medium');
							if ($handle->processed)
							{
								$data['event_medium'] = str_replace('\\', '/', $handle->file_dst_pathname);
								$data['event_medium'] = preg_replace('/\/+/', '/', $data['event_medium']);
								$data['event_medium'] = $data['event_medium'];
							}
							
							$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
						}
					}else{
						if($_POST['copy'] > 0 && $_POST['copy_image'] == 1)
						{
							$event_arr = $pjEventModel->reset()->find($_POST['copy'])->getData();
							if(!empty($event_arr['event_img']))
							{
								$data = array();
								$file_ext = substr($event_arr['event_img'], strrpos($event_arr['event_img'], '.')+1);
								$data['event_img'] = PJ_UPLOAD_PATH . 'events/' . $id . '_' .$hash . '.' . $file_ext;
								$data['event_thumb'] = PJ_UPLOAD_PATH . 'events/thumb/' . $id . '_' .$hash . '.' . $file_ext;
								$data['event_medium'] = PJ_UPLOAD_PATH . 'events/medium/' . $id . '_' .$hash . '.' . $file_ext;
								@copy($event_arr['event_img'], $data['event_img']);
								@copy($event_arr['event_thumb'], $data['event_thumb']);
								@copy($event_arr['event_medium'], $data['event_medium']);
								$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
							}
						}
					}
					
					if($_POST['copy'] > 0)
					{
						$event_arr = $pjEventModel->reset()->find($_POST['copy'])->getData();
						$data = array();
						if(!empty($event_arr['ticket_img']))
						{
							$hash = md5(uniqid(rand(), true));
							$file_ext = substr($event_arr['ticket_img'], strrpos($event_arr['ticket_img'], '.')+1);
							$data['ticket_img'] = PJ_UPLOAD_PATH . 'events/' . $id . '_' .$hash . '.' . $file_ext;
							@copy($event_arr['ticket_img'], $data['ticket_img']);
							
						}
						$data['o_email_confirmation_subject'] = $event_arr['o_email_confirmation_subject'];
						$data['o_email_confirmation'] = $event_arr['o_email_confirmation'];
						$data['o_email_payment_subject'] = $event_arr['o_email_payment_subject'];
						$data['o_email_payment'] = $event_arr['o_email_payment'];
						$data['terms'] = $event_arr['terms'];
						$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
					}
					
					$recurring_id = md5($id . PJ_SALT);
					$data['recurring_id'] = $recurring_id;
					
					$recurring_start_date = pjUtil::formatDate($_start_date, $this->option_arr['o_date_format']);
					$recurring_end_date = pjUtil::formatDate($_end_date, $this->option_arr['o_date_format']);

					if($_POST['repeat'] == 'none')
					{
						$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll(array('recurring_id' => $recurring_id));
						
					}else if($_POST['repeat'] == 'daily'){
						$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll(array('recurring_id' => $recurring_id));
						$number_of_days = 0;
						if($_POST['end_repeat_date'] != '')
						{
							$end_repeat_date = pjUtil::formatDate($_POST['end_repeat_date'], $this->option_arr['o_date_format']);
							$number_of_days = pjUtil::dateDiff('d', $recurring_start_date, $end_repeat_date);
							
						}else{
							if($_POST['end_repeat_times'] != '' && is_numeric($_POST['end_repeat_times']))
							{
								$number_of_days = intval($_POST['end_repeat_times']);
							}
						}
						if($number_of_days > 0)
						{
							for($i = 0; $i < $number_of_days; $i++)
							{
								$recurring_start_date = date('Y-m-d', strtotime($recurring_start_date . " +1 day"));
								$recurring_end_date = date('Y-m-d', strtotime($recurring_end_date . " +1 day"));
								
								$data['event_start_ts'] = strtotime($recurring_start_date . ' ' . $_start_time);
								$data['event_end_ts'] = strtotime($recurring_end_date . ' ' . $_end_time);
								$data['recurring_id'] = $recurring_id;
								$event_id = $pjEventModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
								if ($event_id !== false && (int) $event_id > 0)
								{
									$this->setPrice($id, $event_id, $_POST);
								}
							}
						}
					}else if($_POST['repeat'] == 'weekly'){
						$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll(array('recurring_id' => $recurring_id));
						$number_of_weeks = 0;
						if($_POST['end_repeat_date'] != '')
						{
							$end_repeat_date = pjUtil::formatDate($_POST['end_repeat_date'], $this->option_arr['o_date_format']);
							$number_of_weeks = pjUtil::dateDiff('ww', $recurring_end_date, $end_repeat_date);
							
						}else{
							if($_POST['end_repeat_times'] != '' && is_numeric($_POST['end_repeat_times']))
							{
								$number_of_weeks = intval($_POST['end_repeat_times']);
							}
						}
						if($number_of_weeks > 0)
						{
							for($i = 0; $i < $number_of_weeks; $i++)
							{
								$recurring_start_date = date('Y-m-d', strtotime($recurring_start_date . " +7 day"));
								$recurring_end_date = date('Y-m-d', strtotime($recurring_end_date . " +7 day"));
								
								$data['event_start_ts'] = strtotime($recurring_start_date . ' ' . $_start_time);
								$data['event_end_ts'] = strtotime($recurring_end_date . ' ' . $_end_time);
								
								$data['recurring_id'] = $recurring_id;
								$event_id = $pjEventModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
								if ($event_id !== false && (int) $event_id > 0)
								{
									$this->setPrice($id, $event_id, $_POST);
								}
							}
						}
					}else if($_POST['repeat'] == 'monthly'){
						$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll(array('recurring_id' => $recurring_id));
						
						$recurring_start_date = date('Y-m-d', strtotime($recurring_start_date . "+1 month"));
						if($_POST['repeat-monthly-date'] != 0)
						{
							$recurring_start_date = date('Y-m-d', mktime(0,0,0,date('n', strtotime($recurring_start_date)), $_POST['repeat-monthly-date'], date('Y', strtotime($recurring_start_date))));
						}else{
							$recurring_start_date = date('Y-m-d', strtotime(date('Y-m', strtotime($recurring_start_date)) . '-01 ' .$_POST['repeat-monthly-each'] . ' '  . $_POST['repeat-monthly-day']));
						}
						$number_of_months = 0;
						
						if($_POST['end_repeat_date'] != '')
						{
							$end_repeat_date = pjUtil::formatDate($_POST['end_repeat_date'], $this->option_arr['o_date_format']);
							$number_of_months = pjUtil::dateDiff("m", $recurring_start_date, $end_repeat_date, false);
						}else{
							if($_POST['end_repeat_times'] != '' && is_numeric($_POST['end_repeat_times']))
							{
								$number_of_months = intval($_POST['end_repeat_times']);
							}
						}
						
						if($number_of_months > 0)
						{
							if($event_start_ts < $event_end_ts)
							{
								$range_days = floor(($event_end_ts - $event_start_ts) / $one_day);
							}
							$recurring_end_date = date('Y-m-d', strtotime($recurring_start_date . " +$range_days day"));
							
							for($i = 0; $i < $number_of_months; $i++)
							{
								$data['event_start_ts'] = strtotime($recurring_start_date . ' ' . $_start_time);
								$data['event_end_ts'] = strtotime($recurring_end_date . ' ' . $_end_time);
								
								$data['recurring_id'] = $recurring_id;
								$event_id = $pjEventModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
								if ($event_id !== false && (int) $event_id > 0)
								{
									$this->setPrice($id, $event_id, $_POST);
								}
								if($_POST['repeat-monthly-date'] != 0)
								{
									$recurring_start_date = date('Y-m-d', strtotime($recurring_start_date . " +1 month"));
									$recurring_end_date = date('Y-m-d', strtotime($recurring_end_date . " +1 month"));
								}else{
									$month_year = date('F Y', strtotime($recurring_start_date . " +1 month"));
                                	$recurring_start_date = pjUtil::ordinalDate($_POST['repeat-monthly-each'], $_POST['repeat-monthly-day'], $month_year);
                                	$recurring_end_date = date('Y-m-d', strtotime($recurring_start_date . " +$range_days day"));
								}
							}
						}
					}else if($_POST['repeat'] == 'quarterly'){
						$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll(array('recurring_id' => $recurring_id));
						
						$number_of_quarter = 0;
						if($_POST['end_repeat_date'] != '')
						{
							$end_repeat_date = pjUtil::formatDate($_POST['end_repeat_date'], $this->option_arr['o_date_format']);
							$number_of_months = pjUtil::dateDiff("m", $recurring_start_date, $end_repeat_date, false);
							$number_of_quarter = floor($number_of_months / 3);
						}else{
							if($_POST['end_repeat_times'] != '' && is_numeric($_POST['end_repeat_times']))
							{
								$number_of_quarter = intval($_POST['end_repeat_times']);
							}
						}
						if($number_of_quarter > 0)
						{
							for($i = 0; $i < $number_of_quarter; $i++)
							{
								$recurring_start_date = date('Y-m-d', strtotime($recurring_start_date . " +3 months"));
								$recurring_end_date = date('Y-m-d', strtotime($recurring_end_date . " +3 months"));
								
								$data['event_start_ts'] = strtotime($recurring_start_date . ' ' . $_start_time);
								$data['event_end_ts'] = strtotime($recurring_end_date . ' ' . $_end_time);
								
								$data['recurring_id'] = $recurring_id;
								$event_id = $pjEventModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
								if ($event_id !== false && (int) $event_id > 0)
								{
									$this->setPrice($id, $event_id, $_POST);
								}
							}
						}
					}else if($_POST['repeat'] == 'yearly'){
						$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll(array('recurring_id' => $recurring_id));
						
						$number_of_years = 0;
						if($_POST['end_repeat_date'] != '')
						{
							$end_repeat_date = pjUtil::formatDate($_POST['end_repeat_date'], $this->option_arr['o_date_format']);
							$number_of_years = pjUtil::dateDiff("yyyy", $recurring_start_date, $end_repeat_date, false);
						}else{
							if($_POST['end_repeat_times'] != '' && is_numeric($_POST['end_repeat_times']))
							{
								$number_of_years = intval($_POST['end_repeat_times']);
							}
						}
						if($number_of_years > 0)
						{
							for($i = 0; $i < $number_of_years; $i++)
							{
								$recurring_start_date = date('Y-m-d', strtotime($recurring_start_date . " +1 year"));
								$recurring_end_date = date('Y-m-d', strtotime($recurring_end_date . " +1 year"));
								
								$data['event_start_ts'] = strtotime($recurring_start_date . ' ' . $_start_time);
								$data['event_end_ts'] = strtotime($recurring_end_date . ' ' . $_end_time);
								
								$data['recurring_id'] = $recurring_id;
								$event_id = $pjEventModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
								if ($event_id !== false && (int) $event_id > 0)
								{
									$this->setPrice($id, $event_id, $_POST);
								}
							}
						}
					}else if($_POST['repeat'] == 'custom'){
						$pjEventModel->reset()->where('id', $id)->limit(1)->modifyAll(array('recurring_id' => $recurring_id));
						$steps = 0;
						$number_of_days = 0;
						if($_POST['repeat-custom-days'] != '' && is_numeric($_POST['repeat-custom-days']))
						{
							if($_POST['end_repeat_date'] != '')
							{
								$end_repeat_date = pjUtil::formatDate($_POST['end_repeat_date'], $this->option_arr['o_date_format']);
								$number_of_days = pjUtil::dateDiff('d', $recurring_start_date, $end_repeat_date);
								$steps = floor($number_of_days / $_POST['repeat-custom-days']);
							}else{
								if($_POST['end_repeat_times'] != '' && is_numeric($_POST['end_repeat_times']))
								{
									$steps = intval($_POST['end_repeat_times']);
								}
							}
						}
						if($steps > 0)
						{
							$number_of_days = intval($_POST['repeat-custom-days']);
							for($i = 0; $i < $steps; $i++)
							{
								$recurring_start_date = date('Y-m-d', strtotime($recurring_start_date . " +$number_of_days day"));
								$recurring_end_date = date('Y-m-d', strtotime($recurring_end_date . " +$number_of_days day"));
								
								$data['event_start_ts'] = strtotime($recurring_start_date . ' ' . $_start_time);
								$data['event_end_ts'] = strtotime($recurring_end_date . ' ' . $_end_time);
								
								$data['recurring_id'] = $recurring_id;
								$event_id = $pjEventModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
								if ($event_id !== false && (int) $event_id > 0)
								{
									$this->setPrice($id, $event_id, $_POST);
								}
							}
						}
					}
					
					$this->setPrice($id, $id, $_POST);
					
					$err = 'AE03';
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminEvents&action=pjActionUpdate&id=$id&err=$err");
				}else{
					$err = 'AE04';
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminEvents&action=pjActionIndex&err=$err");
				}
				
				
			} else {
				$category_arr = pjCategoryModel::factory()->select('t1.*')->where('t1.status', 'T')->orderBy("category ASC")->findAll()->getData();
					
				$this->set('category_arr', $category_arr);
				
				if(isset($_GET['id']))
				{
					$arr = pjEventModel::factory()->find($_GET['id'])->getData();
					$price_arr = pjPriceModel::factory()->where('t1.event_id', $_GET['id'])->findAll()->getData();
					$this->set('arr', $arr);
					$this->set('price_arr', $price_arr);
				}
				
				# Timepicker
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				
				$this->appendJs('jquery.validate.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminEvents.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteEvent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjEventModel = pjEventModel::factory();
			$arr = $pjEventModel->find($_GET['id'])->getData();
			if ($pjEventModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if(!empty($arr['event_img']))
				{
					$cnt = $pjEventModel->reset()->where('event_img', $arr['event_img'])->findCount()->getData();
					if($cnt == 1)
					{
						if (is_file(PJ_INSTALL_PATH . $arr['event_img']))
						{
							@unlink(PJ_INSTALL_PATH . $arr['event_img']);
						}
						if (is_file(PJ_INSTALL_PATH . $arr['event_thumb']))
						{
							@unlink(PJ_INSTALL_PATH . $arr['event_thumb']);
						}
						if (is_file(PJ_INSTALL_PATH . $arr['event_medium']))
						{
							@unlink(PJ_INSTALL_PATH . $arr['event_medium']);
						}
					}
				}
				if(!empty($arr['ticket_img']))
				{
					$cnt = $pjEventModel->reset()->where('ticket_img', $arr['ticket_img'])->findCount()->getData();
					if($cnt == 1)
					{
						if (is_file(PJ_INSTALL_PATH . $arr['ticket_img']))
						{
							@unlink(PJ_INSTALL_PATH . $arr['ticket_img']);
						}
					}
				}
				$booking_pdf_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'bookings/event-' . $_GET['id'] . '.pdf';
				$ticket_pdf_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/event-' . $_GET['id'] . '.pdf';
				
				if(is_file($booking_pdf_path)){
					@unlink($booking_pdf_path);
				}
				if(is_file($ticket_pdf_path)){
					@unlink($ticket_pdf_path);
				}
				
				$pjBookingModel = pjBookingModel::factory();
				
				$in_where = "booking_id IN(SELECT `TB`.`id` FROM `".$pjBookingModel->getTable()."` as `TB` WHERE `TB`.`event_id` = " .$_GET['id']. ")";
				pjBookingTicketModel::factory()->where($in_where)->eraseAll();
				pjBookingDetailModel::factory()->where($in_where)->eraseAll();
				pjPriceModel::factory()->where('event_id', $_GET['id'])->eraseAll();
				$pjBookingModel->where('event_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteEventBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjEventModel = pjEventModel::factory();
			
			$pjBookingModel = pjBookingModel::factory();
			$pjBookingTicketModel = pjBookingTicketModel::factory();
			$pjBookingDetailModel = pjBookingDetailModel::factory();
				
			$arr = $pjEventModel->whereIn('id', $_POST['record'])->findAll()->getData();
			if(!empty($arr))
			{
				foreach($arr as $v)
				{
					if(!empty($v['ticket_img']))
					{
						$cnt = $pjEventModel->reset()->where('ticket_img', $v['ticket_img'])->findCount()->getData();
						if($cnt == 1)
						{
							if (is_file(PJ_INSTALL_PATH . $v['ticket_img']))
							{
								@unlink(PJ_INSTALL_PATH . $v['ticket_img']);
							}
						}
					}
					if(!empty($v['event_img']))
					{
						$cnt = $pjEventModel->reset()->where('event_img', $v['event_img'])->findCount()->getData();
						if($cnt == 1)
						{
							if (is_file(PJ_INSTALL_PATH . $v['event_img']))
							{
								@unlink(PJ_INSTALL_PATH . $v['event_img']);
							}
							if (is_file(PJ_INSTALL_PATH . $v['event_thumb']))
							{
								@unlink(PJ_INSTALL_PATH . $v['event_thumb']);
							}
							if (is_file(PJ_INSTALL_PATH . $v['event_medium']))
							{
								@unlink(PJ_INSTALL_PATH . $v['event_medium']);
							}
						}
					}
					$booking_pdf_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'bookings/event-' . $v['id'] . '.pdf';
					$ticket_pdf_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/event-' . $v['id'] . '.pdf';
					
					if(is_file($booking_pdf_path)){
						@unlink($booking_pdf_path);
					}
					if(is_file($ticket_pdf_path)){
						@unlink($ticket_pdf_path);
					}
					
					$in_where = "booking_id IN(SELECT `TB`.`id` FROM `".$pjBookingModel->getTable()."` as `TB` WHERE `TB`.`event_id` = " .$v['id']. ")";
					$pjBookingTicketModel->reset()->where($in_where)->eraseAll();
					$pjBookingDetailModel->reset()->where($in_where)->eraseAll();
					pjPriceModel::factory()->where('event_id', $v['id'])->eraseAll();
					$pjBookingModel->reset()->where('event_id', $v['id'])->eraseAll();
				}
				$pjEventModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportEvent()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjEventModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Events-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetEvent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjEventModel = pjEventModel::factory();
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				
				$search_date = pjUtil::formatDate($q, $this->option_arr['o_date_format']);
				if($search_date != FALSE)
				{
					$pjEventModel->where("CAST(FROM_UNIXTIME(t1.event_start_ts) AS DATE) <= '$search_date' AND CAST(FROM_UNIXTIME(t1.event_end_ts) AS DATE) >= '$search_date'");
				}else{
					$pjEventModel->where('t1.event_title LIKE', "%$q%");
				}
			}
			
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjEventModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'event_title';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				if($_GET['column'] == 'event_date')
				{
					$column = 'event_start_ts';	
				}elseif($_GET['column'] == 'tickets'){
					$column = 'total_booked';
				}else{
					$column = $_GET['column'];
				}
				$direction = strtoupper($_GET['direction']);
			}
			
			$total = $pjEventModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$event_arr = $pjEventModel->select("t1.*, (SELECT COUNT(*) FROM `".pjBookingModel::factory()->getTable()."` AS t2 WHERE t2.event_id = t1.id) as `cnt_bookings`,
													  (SELECT SUM(t3.available) FROM `".pjPriceModel::factory()->getTable()."` AS t3 WHERE t3.event_id=t1.id) AS `total_avail`,
													  (SELECT SUM(t4.cnt) FROM `".pjBookingDetailModel::factory()->getTable()."` AS t4 WHERE t4.booking_id IN(SELECT t5.id FROM `".pjBookingModel::factory()->getTable()."` AS t5 WHERE t5.event_id = t1.id)) AS `total_booked`")
									  ->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			$data = array();
			foreach($event_arr as $k => $v){
				$v['event_date'] = pjUtil::getEventDateTime($v['event_start_ts'], $v['event_end_ts'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format'],$v['o_show_start_time'], $v['o_show_end_time']);
				$v['tickets'] = ((int) $v['total_booked']) . ' ' . __('lblOf', true, false) . ' ' . $v['total_avail'];
				if(((int) $v['total_booked']) > 0){
					$v['linked'] = 1;
				}else{
					$v['linked'] = 0;
				}
				$data[$k] = $v;
			}	
			if($column == 'event_start_ts')
			{
				$column = 'event_date';
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
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminEvents.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveEvent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjEventModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionStatusEvent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjEventModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
					'status' => ":IF(`status`='F','T','F')"
				));
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
				
			if (isset($_POST['event_update']))
			{
				$pjEventModel = pjEventModel::factory();
				
				$data = array();
				
				$event_id = $_POST['id'];
				
				if(!isset($_POST['o_show_start_time']))
				{
					$data['o_show_start_time'] = 'T';
				}
				if(!isset($_POST['o_show_end_time']))
				{
					$data['o_show_end_time'] = 'T';
				}
				
				if (isset($_FILES['ticket_img']) && !empty($_FILES['ticket_img']['tmp_name']))
				{
					$pjUpload = new pjUpload();
					if ($pjUpload->load($_FILES['ticket_img']))
					{
						if (!in_array($pjUpload->getExtension(), array('jpg', 'jpeg', 'pjpeg')))
						{
							pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminEvents&action=pjActionUpdate&id=".$event_id."&err=AE09&tab_id=3");
						}
						$size = getimagesize($_FILES['ticket_img']['tmp_name']);
						if ($size[0] != 510 || $size[1] != 280)
						{
							pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminEvents&action=pjActionUpdate&id=".$event_id."&err=AE10&tab_id=3&size=". $size[1]);
						}
						$event_arr = $pjEventModel->find($event_id)->getData();
						if (!empty($event_arr['ticket_img']) && is_file(PJ_INSTALL_PATH . $event_arr['ticket_img']))
						{
							@unlink(PJ_INSTALL_PATH . $event_arr['ticket_img']);
						}
						$image_path = PJ_UPLOAD_PATH . 'events/' . $_POST['recurring_id'] . "." . $pjUpload->getExtension();
						if ($pjUpload->save($image_path))
						{
 							$data['ticket_img'] = $image_path;
						}
					}
				}
				
				if (isset($_FILES['event_img']) && !empty($_FILES['event_img']['tmp_name']))
				{
					$event_arr = $pjEventModel->reset()->find($event_id)->getData();
					$recurring_id = $event_arr['recurring_id'];
					
					$cnt_recurrings = $pjEventModel->reset()->where('recurring_id', $recurring_id)->findCount()->getData();
					
					if($cnt_recurrings == 1 || isset($_POST['apply_recurring']))
					{
						if (!empty($event_arr['event_img']) && is_file(PJ_INSTALL_PATH . $event_arr['event_img']))
						{
							@unlink(PJ_INSTALL_PATH . $event_arr['event_img']);
						}
						if (!empty($event_arr['event_thumb']) && is_file(PJ_INSTALL_PATH . $event_arr['event_thumb']))
						{
							@unlink(PJ_INSTALL_PATH . $event_arr['event_thumb']);
						}
						if (!empty($event_arr['event_medium']) && is_file(PJ_INSTALL_PATH . $event_arr['event_medium']))
						{
							@unlink(PJ_INSTALL_PATH . $event_arr['event_medium']);
						}
					}
					$hash = md5(uniqid(rand(), true));
					$handle = new pjCUpload($_FILES['event_img']);
					if ($handle->uploaded)
					{
						$handle->allowed = array('image/*');
						$handle->mime_check = true;
						$handle->file_new_name_body = $event_id . '_'. $hash;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->process(PJ_UPLOAD_PATH . 'events/');
						if ($handle->processed)
						{
							$data['event_img'] = str_replace('\\', '/', $handle->file_dst_pathname);
							$data['event_img'] = preg_replace('/\/+/', '/', $data['event_img']);
						}
						
						$handle->allowed = array('image/*');
						$handle->mime_check = true;
						$handle->file_new_name_body = $event_id . '_'. $hash;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->image_resize = true;
						$handle->image_x = 90;
						$handle->image_ratio_y = true;
						$handle->process(PJ_UPLOAD_PATH . 'events/thumb');
						if ($handle->processed)
						{
							$data['event_thumb'] = str_replace('\\', '/', $handle->file_dst_pathname);
							$data['event_thumb'] = preg_replace('/\/+/', '/', $data['event_thumb']);
						}
						
						$handle->allowed = array('image/*');
						$handle->mime_check = true;
						$handle->file_new_name_body = $event_id . '_'. $hash;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->image_resize = true;
						$handle->image_x = 226;
						$handle->image_ratio_y = true;
						$handle->process(PJ_UPLOAD_PATH . 'events/medium');
						if ($handle->processed)
						{
							$data['event_medium'] = str_replace('\\', '/', $handle->file_dst_pathname);
							$data['event_medium'] = preg_replace('/\/+/', '/', $data['event_medium']);
						}
					}
				}
				
				$_start = $_POST['event_start_ts']; unset($_POST['event_start_ts']);
				$_end = $_POST['event_end_ts']; unset($_POST['event_end_ts']);
				
				if(count(explode(" ", $_start)) == 3)
				{
					list($_start_date, $_start_time, $_start_period) = explode(" ", $_start);
					list($_end_date, $_end_time, $_end_period) = explode(" ", $_end);
					$_start_time = pjUtil::formatTime($_start_time . ' ' . $_start_period, $this->option_arr['o_time_format']);
					$_end_time = pjUtil::formatTime($_end_time . ' ' . $_end_period, $this->option_arr['o_time_format']);
				}else{
					list($_start_date, $_start_time) = explode(" ", $_start);
					list($_end_date, $_end_time) = explode(" ", $_end);
					$_start_time = pjUtil::formatTime($_start_time, $this->option_arr['o_time_format']);
					$_end_time = pjUtil::formatTime($_end_time, $this->option_arr['o_time_format']);
				}
				
				$data['event_start_ts'] = strtotime(pjUtil::formatDate($_start_date, $this->option_arr['o_date_format']) . ' ' . $_start_time);
				$data['event_end_ts'] = strtotime(pjUtil::formatDate($_end_date, $this->option_arr['o_date_format']) . ' ' . $_end_time);
				
				$pjEventModel->reset()->where('id', $event_id)->limit(1)->modifyAll(array_merge($data,$_POST));
				
				$has_recurring = 0;
				if(isset($_POST['apply_recurring']))
				{
					unset($_POST['id']);
					unset($data['event_start_ts']);
					unset($data['event_end_ts']);
					
					$pjEventModel->reset()->where('recurring_id', $_POST['recurring_id'])->modifyAll(array_merge($data,$_POST));
					
					$has_recurring = 1;
					
				}
				$this->updatePrice($event_id, $_POST, $has_recurring);
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminEvents&action=pjActionUpdate&id=".$event_id."&err=AE01&tab_id=" . $_POST['tab_id']);
				
			} else {
				$pjEventModel = pjEventModel::factory();
				$pjBookingTicketModel = pjBookingTicketModel::factory();
				
				$pjEventModel->select("t1.*, 
											(SELECT COUNT(*) FROM  `" . pjBookingModel::factory()->getTable(). "` AS t2 ) AS `ctn_bookings`,
											(SELECT SUM(t3.available) FROM `".pjPriceModel::factory()->getTable()."` AS t3 WHERE t3.event_id=t1.id) AS `total_avail`");
				$arr = $pjEventModel->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminEvents&action=pjActionIndex&err=AE08");
				}
				
				$pjCategoryModel = pjCategoryModel::factory();
				$category_arr = $pjCategoryModel->select('t1.*')->where('t1.status', 'T')->orderBy("category ASC")->findAll()->getData();
				
				$recurring_id = $arr['recurring_id'];
				$number_of_events = $pjEventModel->reset()->where('recurring_id', $recurring_id)->findCount()->getData();
				
				$price_arr = pjPriceModel::factory()->where('t1.event_id', $_GET['id'])->findAll()->getData();
				$booking_arr = pjBookingModel::factory()
											->where('t1.event_id', $_GET['id'])
											->where("(t1.booking_status = 'confirmed' OR t1.booking_status = 'pending')")
											->findAll()->getData();
				
				$details_arr = pjBookingDetailModel::factory()
											->where("t1.booking_id IN(SELECT t2.id FROM `".pjBookingModel::factory()->getTable()."` as t2 WHERE t2.event_id = '".$_GET['id']."' AND (t2.booking_status = 'confirmed' OR t2.booking_status = 'pending'))")
											->findAll()->getData();
											
				$booking_detail_arr = array();
				$total_tickets = 0;												
				foreach($details_arr as $v)
				{
					$booking_detail_arr[$v['booking_id']][] = $v;
					$total_tickets += $v['cnt'];
				}

				$tickets_arr = $pjBookingTicketModel
											->select('t1.*, t2.unique_id, t2.customer_name, t2.customer_email')
											->join('pjBooking', "t1.booking_id = t2.id", 'left')
											->where("t1.is_used", 'T')
											->where("t1.booking_id IN(SELECT t3.id FROM `".pjBookingModel::factory()->getTable()."` as t3 WHERE t3.event_id = '".$_GET['id']."' AND t3.booking_status = 'confirmed')")
											->findAll()->getData();
				
				$used_tickets = $pjBookingTicketModel
										->reset()
										->where("t1.booking_id IN(SELECT t2.id FROM `".pjBookingModel::factory()->getTable()."` as t2 WHERE t2.event_id = '".$_GET['id']."' AND t2.booking_status = 'confirmed')")
										->where('t1.is_used', 'T')
										->findCount()->getData();
									
				if(count($booking_arr) > 0)
				{
					$this->set('print_file', $this->doPrintBookings($booking_arr, $booking_detail_arr, $_GET['id']));				
				}
				if(count($tickets_arr) > 0)
				{
					$this->set('print_tickets_file', $this->doPrintTickets($tickets_arr, $_GET['id']));				
				}
							
				$this->set('arr', $arr);
				$this->set('price_arr', $price_arr);
				$this->set('category_arr', $category_arr);
				$this->set('number_of_events', $number_of_events);
				$this->set('booking_arr', $booking_arr);
				$this->set('detail_arr', $booking_detail_arr);
				$this->set('tickets_arr', $tickets_arr);
				$this->set('total_tickets', $total_tickets);
				$this->set('used_tickets', $used_tickets);
				
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
				# Timepicker
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				
				$this->appendJs('jquery.validate.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminEvents.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
		
		$pjEventModel = pjEventModel::factory();
		
		$arr = $pjEventModel->find($_POST['id'])->getData();
		
		$json_arr = array();
		if(!empty($arr))
		{
			$d['event_img'] = ':NULL';
			$d['event_thumb'] = ':NULL';

			$pjEventModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll($d);
			
			$cnt_recurring = $pjEventModel->reset()->where('recurring_id', $arr['recurring_id'])->findCount()->getData();
			
			if($cnt_recurring == 1)
			{
				if (!empty($arr['event_img']) && is_file(PJ_INSTALL_PATH . $arr['event_img']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['event_img']);
				}
				if (!empty($arr['event_thumb']) && is_file(PJ_INSTALL_PATH . $arr['event_thumb']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['event_thumb']);
				}
				if (!empty($arr['event_medium']) && is_file(PJ_INSTALL_PATH . $arr['event_medium']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['event_medium']);
				}
			}
			$json_arr['status'] = 1;
			
		}else{
			$json_arr['status'] = 0;
		}
		pjAppController::jsonResponse($json_arr);		
	}
	
	private function doPrintBookings($arr, $detail_arr, $event_id)
	{
		require_once(PJ_LIBS_PATH . 'tcpdf/config/lang/eng.php');
		require_once(PJ_LIBS_PATH . 'tcpdf/tcpdf.php');
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(10, 10, 10);
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		
		$pdf->AddPage();
		
		$booking_statuses = __('booking_statuses', true);
		
		$tbl = '<table style="width: 600px;" cellspacing="0">';
		$tbl .= '<tr>';
		$tbl .= 	'<td colspan="5" style="height: 30px;">' . __('lblCurrentDateTime', true) . ': '.pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $this->option_arr['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s'), 'H:i:s', $this->option_arr['o_time_format']).'</td>';
		$tbl .= '</tr>';
		$tbl .= '<tr>';
		$tbl .= 	'<td style="border: 1px solid #000000; width: 100px; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblID', true) . '</td>';
		$tbl .= 	'<td style="border: 1px solid #000000; width: 140px; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblBookingName', true) . '</td>';
		$tbl .= 	'<td style="border: 1px solid #000000; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblBookingEmail', true) . '</td>';
		$tbl .= 	'<td style="border: 1px solid #000000; width: 100px; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblTickets', true) . '</td>';
		$tbl .= 	'<td style="border: 1px solid #000000; width: 80px; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblStatus', true) . '</td>';
		$tbl .= '</tr>';
		foreach($arr as $v)
		{
			$id = $v['unique_id'];
			$name = $v['customer_name'];
			$email = $v['customer_email'];
			$tickets = '';
			$price_arr = $detail_arr[$v['id']];
			if(count($price_arr) > 0)
			{
				foreach($price_arr as $d)
				{
					$tickets .= $d['cnt'] . ' x ' . $d['price_title'] . '<br/>';
				}
			}else{
				$tickets = '&nbsp;';
			}
			$status = stripslashes($booking_statuses[$v['booking_status']]);
			
			$tbl .= '<tr>';
			$tbl .= 	'<td style="border: 1px solid #000000; width: 100px;height:30px;vertical-align: middle;">' . $id . '</td>';
			$tbl .= 	'<td style="border: 1px solid #000000; width: 140px;height:30px;vertical-align: middle;">' . $name . '</td>';
			$tbl .= 	'<td style="border: 1px solid #000000;vertical-align: middle;height:30px;">' . $email . '</td>';
			$tbl .= 	'<td style="border: 1px solid #000000; width: 100px;height:30px;vertical-align: middle;">' . $tickets . '</td>';
			$tbl .= 	'<td style="border: 1px solid #000000; width: 80px;height:30px;vertical-align: middle;">' . $status . '</td>';
			$tbl .= '</tr>';
		}
		$tbl .= '</table>';
		$pdf->writeHTML($tbl, true, false, false, false, '');
		
		$pdf->Output(PJ_UPLOAD_PATH . 'bookings/event-'.$event_id.'.pdf', 'F');
		$filename = PJ_UPLOAD_PATH . 'bookings/event-'.$event_id.'.pdf';
		return $filename;
	}
	
	private function doPrintTickets($arr, $event_id)
	{
		require_once(PJ_LIBS_PATH . 'tcpdf/config/lang/eng.php');
		require_once(PJ_LIBS_PATH . 'tcpdf/tcpdf.php');
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(10, 10, 10);
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		
		$pdf->AddPage();
		
		$tbl = '<table style="width: 600px;" cellspacing="0">';
		$tbl .= '<tr>';
		$tbl .= 	'<td colspan="4" style="height: 30px;">' . __('lblCurrentDateTime', true) . ': '.pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $this->option_arr['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s'), 'H:i:s', $this->option_arr['o_time_format']).'</td>';
		$tbl .= '</tr>';
		$tbl .= '<tr>';
		$tbl .= 	'<td style="border: 1px solid #000000; width: 140px; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblBookingName', true) . '</td>';
		$tbl .= 	'<td style="border: 1px solid #000000; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblBookingEmail', true) . '</td>';
		$tbl .= 	'<td style="border: 1px solid #000000; width: 120px; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblTicketType', true) . '</td>';
		$tbl .= 	'<td style="border: 1px solid #000000; width: 100px; height:30px;vertical-align: middle;background-color: #c2bebe;">' . __('lblUsedTickets', true) . '</td>';
		$tbl .= '</tr>';
		foreach($arr as $v)
		{
			$price_title = $v['price_title'];
			$name = $v['customer_name'];
			$email = $v['customer_email'];
			$ticket_id = $v['ticket_id'];
			
			$tbl .= '<tr>';
			$tbl .= 	'<td style="border: 1px solid #000000; width: 140px;height:30px;vertical-align: middle;">' . $name . '</td>';
			$tbl .= 	'<td style="border: 1px solid #000000;vertical-align: middle;height:30px;">' . $email . '</td>';
			$tbl .= 	'<td style="border: 1px solid #000000; width: 120px;height:30px;vertical-align: middle;">' . $price_title . '</td>';
			$tbl .= 	'<td style="border: 1px solid #000000; width: 100px;height:30px;vertical-align: middle;">' . $ticket_id . '</td>';
			$tbl .= '</tr>';
		}
		$tbl .= '</table>';
		$pdf->writeHTML($tbl, true, false, false, false, '');
		
		$pdf->Output(PJ_UPLOAD_PATH . 'tickets/event-'.$event_id.'.pdf', 'F');
		$filename = PJ_UPLOAD_PATH . 'tickets/event-'.$event_id.'.pdf';
		return $filename;
	}
}
?>