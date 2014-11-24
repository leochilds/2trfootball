<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOrders extends pjAdmin
{
	public function pjActionDeleteOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjOrderModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				$pjOrderStockModel = pjOrderStockModel::factory();
				$os_arr = $pjOrderStockModel->where('order_id', $_GET['id'])->findAll()->getDataPair('stock_id', 'qty');
				if (!empty($os_arr))
				{
					$pjOrderStockModel->reset()->where('order_id', $_GET['id'])->eraseAll();
					$pjStockModel = pjStockModel::factory();
					foreach ($os_arr as $stock_id => $qty)
					{
						$pjStockModel->reset()->set('id', $stock_id)->modify(array('qty' => ":qty + " . (int) $qty));
					}
				}
				pjOrderExtraModel::factory()->where('order_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteOrderBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjOrderModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				$pjOrderStockModel = pjOrderStockModel::factory();
				$os_arr = $pjOrderStockModel->whereIn('order_id', $_POST['record'])->findAll()->getData();
				if (!empty($os_arr))
				{
					$pjOrderStockModel->reset()->whereIn('order_id', $_POST['record'])->eraseAll();
					$pjStockModel = pjStockModel::factory();
					foreach ($os_arr as $item)
					{
						$pjStockModel->reset()->set('id', $item['stock_id'])->modify(array('qty' => ":qty + " . (int) $item['qty']));
					}
				}
				pjOrderExtraModel::factory()->whereIn('order_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportOrder()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjOrderModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Orders-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$this->set('client_arr', pjClientModel::factory()->find($_GET['client_id'])->getData());
			}
		}
	}
	
	public function pjActionGetAddress()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$address_arr = pjAddressModel::factory()
					->select('t1.*, t2.content AS country_name')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->find($_GET['id'])
					->getData();
				if (!isset($_GET['json']))
				{
					$this->set('address_arr', $address_arr);
				} else {
					pjAppController::jsonResponse($address_arr);
				}
			}
		}
	}
	
	public function pjActionGetAddressBook()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$this->set('order_arr', pjOrderModel::factory()->find($_GET['order_id'])->getData());
				$this->set('address_arr', pjAddressModel::factory()->where('t1.client_id', $_GET['client_id'])->orderBy('t1.address_1 ASC')->findAll()->getData());
			}
		}
	}
	
	public function pjActionGetOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjOrderModel = pjOrderModel::factory()
				->join('pjClient', 't2.id=t1.client_id', 'left outer');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = trim($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjOrderModel->where('t1.uuid LIKE', "%$q%");
				$pjOrderModel->orWhere('t2.client_name LIKE', "%$q%");
				$pjOrderModel->orWhere('t2.email LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.s_name LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.s_address_1 LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.s_city LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.b_name LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.b_address_1 LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.b_city LIKE', "%$q%");
			}
			
			# Update order (other orders list)
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$pjOrderModel->where('t1.client_id', (int) $_GET['client_id']);
			}
			if (isset($_GET['order_id']) && (int) $_GET['order_id'] > 0)
			{
				$pjOrderModel->where('t1.id !=', (int) $_GET['order_id']);
			}
			# ---
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$pjOrderModel->where(sprintf("t1.id IN (SELECT `order_id` FROM `%s` WHERE `product_id` = '%u')", pjOrderStockModel::factory()->getTable(), (int) $_GET['product_id']));
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('new', 'pending', 'cancelled', 'completed')))
			{
				$pjOrderModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['payment_method']) && !empty($_GET['payment_method']) && in_array($_GET['payment_method'], array('paypal', 'authorize', 'creditcard', 'bank', 'cod')))
			{
				$pjOrderModel->where('t1.payment_method', $_GET['payment_method']);
			}
			if (isset($_GET['total_from']) && (float) $_GET['total_from'] > 0)
			{
				$pjOrderModel->where('t1.total >=', $_GET['total_from']);
			}
			if (isset($_GET['total_to']) && (float) $_GET['total_to'] > 0)
			{
				$pjOrderModel->where('t1.total <=', $_GET['total_to']);
			}
			if (isset($_GET['date_from']) && !empty($_GET['date_from']))
			{
				$pjOrderModel->where('DATE(t1.created) >=', pjUtil::formatDate($_GET['date_from'], $this->option_arr['o_date_format']));
			}
			if (isset($_GET['date_to']) && !empty($_GET['date_to']))
			{
				$pjOrderModel->where('DATE(t1.created) <=', pjUtil::formatDate($_GET['date_to'], $this->option_arr['o_date_format']));
			}
			
			$column = 't1.id';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjOrderModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			$data = $pjOrderModel
				->select('t1.id, t1.uuid, t1.total, t1.status, t1.created, t2.client_name')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();

			foreach ($data as $k => $v)
			{
				$data[$k]['total_formated'] = pjUtil::formatCurrencySign(number_format($v['total'], 2), $this->option_arr['o_currency']);
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->set('product_arr', pjProductModel::factory()
				->select('t1.id, t2.content AS `name`')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy('`name` ASC')
				->findAll()->getData()
			);
			
			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminOrders.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetPrice()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$price = $discount = $tax = $shipping = $insurance = 0;
			
			$pjOrderStockModel = pjOrderStockModel::factory()->where('t1.order_id', $_POST['id'])->findAll();
			
			$os_arr = $pjOrderStockModel->getData();
			$oe_arr = pjOrderExtraModel::factory()->where('t1.order_id', $_POST['id'])->findAll()->getData();
			
			if (!empty($_POST['voucher']))
			{
				$product_ids = $pjOrderStockModel->getDataPair(null, 'product_id');
				$product_ids = array_unique($product_ids);
				
				$pre = array();
				$pre['code'] = $_POST['voucher'];
				list($pre['date'], $pre['hour'], $pre['minute']) = explode(",", date("Y-m-d,H,i"));
	
				$response = pjAppController::getDiscount($pre, $this->option_arr);
				if ($response['status'] == 'OK')
				{
					$intersect = array_intersect($response['voucher_products'], $product_ids);
					if (empty($response['voucher_products'][0]) || !empty($intersect))
					{
						$voucher = array(
							'voucher_code' => $response['voucher_code'],
							'voucher_type' => $response['voucher_type'],
							'voucher_discount' => $response['voucher_discount'],
							'voucher_products' => empty($response['voucher_products'][0]) ? 'all' : $response['voucher_products']
						);
					}
				}
			}
			
			foreach ($os_arr as $item)
			{
				$amount = $item['qty'] * $item['price'];
				foreach ($oe_arr as $oe_item)
				{
					if ($item['id'] == $oe_item['order_stock_id'])
					{
						$amount += $oe_item['price'];
					}
				}
				$price += $amount;
				$discount += pjUtil::getDiscount($amount, $item['product_id'], @$voucher);
			}
			
			if (isset($_POST['tax_id']) && (int) $_POST['tax_id'] > 0)
			{
				$tax_arr = pjTaxModel::factory()->find($_POST['tax_id'])->getData();
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
			$total = $price + $shipping + $tax + $insurance - $discount;
			$total = $total > 0 ? $total : 0;
			
			$data = compact('discount', 'price', 'insurance', 'shipping', 'tax', 'total');
			$data = array_map('floatval', $data);
			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'data' => $data));
		}
		exit;
	}
	
	public function pjActionSaveOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjOrderModel = pjOrderModel::factory();
			if (!in_array($_POST['column'], @$pjOrderModel->getI18n()))
			{
				$data = array($_POST['column'] => $_POST['value']);
				if ($_POST['column'] == 'status' && $_POST['value'] == 'completed')
				{
					$before = $pjOrderModel->find($_GET['id'])->getData();
					if ($before['status'] != 'completed')
					{
						$data['processed_on'] = ':NOW()';
					}
				}
				$pjOrderModel->reset()->set('id', $_GET['id'])->modify($data);
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjOrder');
			}
		}
		exit;
	}
	
	public function pjActionSend()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['form_send']))
			{
				if (!isset($_POST['to']) || !isset($_POST['from']) || !isset($_POST['subject']) || !isset($_POST['body']) ||
					!pjValidation::pjActionEmail($_POST['to']) || !pjValidation::pjActionEmail($_POST['from']) ||
					!pjValidation::pjActionNotEmpty($_POST['subject']) ||
					!pjValidation::pjActionNotEmpty($_POST['body']))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Form does not validate'));
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
				
				$r = $Email
					->setTo($_POST['to'])
					->setFrom($_POST['from'])
					->setSubject($_POST['subject'])
					->send($_POST['body']);
				
				if ($r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email has not been sent'));
			}
			
			$to = $from = $subject = $body = null;
			if (isset($_GET['id']) && (int) $_GET['id'] > 0 && isset($_GET['type']) &&
				in_array($_GET['type'], array('confirm', 'payment')))
			{
				$order_arr = pjOrderModel::factory()
					->select(sprintf("t1.*, t2.content AS `b_country`, t3.content AS `s_country`, t4.email AS `admin_email`,
						t6.content AS `confirm_subject_client`, t7.content AS `confirm_tokens_client`, t8.content AS `payment_subject_client`, t9.content AS `payment_tokens_client`,
						t5.email, t5.client_name, t5.phone, t5.url, AES_DECRYPT(t5.password, '%s') AS `password`", PJ_SALT))
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
					->join('pjUser', 't4.id=1', 'left outer')
					->join('pjClient', 't5.id=t1.client_id', 'left outer')
					->join('pjMultiLang', sprintf("t6.model='pjOption' AND t6.foreign_id='%u' AND t6.locale=t1.locale_id AND t6.field='confirm_subject_client'", $this->getForeignId()), 'left outer')
					->join('pjMultiLang', sprintf("t7.model='pjOption' AND t7.foreign_id='%u' AND t7.locale=t1.locale_id AND t7.field='confirm_tokens_client'", $this->getForeignId()), 'left outer')
					->join('pjMultiLang', sprintf("t8.model='pjOption' AND t8.foreign_id='%u' AND t8.locale=t1.locale_id AND t8.field='payment_subject_client'", $this->getForeignId()), 'left outer')
					->join('pjMultiLang', sprintf("t9.model='pjOption' AND t9.foreign_id='%u' AND t9.locale=t1.locale_id AND t9.field='payment_tokens_client'", $this->getForeignId()), 'left outer')
					->find($_GET['id'])
					->getData();
					
				if (empty($order_arr))
				{
					return;
				}
				$order_arr['products'] = pjAppController::pjActionGetProductsString($order_arr['id'], $order_arr['locale_id']);
				
				$tokens = pjAppController::getTokens($order_arr, $this->option_arr);
				
				$to = $order_arr['email'];
				$from = $order_arr['admin_email'];
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr[$_GET['type'].'_subject_client']);
				$body = str_replace($tokens['search'], $tokens['replace'], $order_arr[$_GET['type'].'_tokens_client']);
			}
			
			$this->set('arr', compact('to', 'subject', 'body', 'from'));
		}
	}
	
	public function pjActionStockDelete()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$pjOrderStockModel = pjOrderStockModel::factory();
				$arr = $pjOrderStockModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Stock not found.'));
				}
				if (1 == $pjOrderStockModel->set('id', $_POST['id'])->erase()->getAffectedRows())
				{
					pjOrderExtraModel::factory()->where('order_stock_id', $_POST['id'])->eraseAll();
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty + " . (int) $arr['qty']));
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Stock has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Stock has not been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing parameters.'));
		}
		pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Access denied.'));
		exit;
	}
	
	public function pjActionStockGet()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			$stack = pjAppController::pjActionGetOrderStock($_GET['order_id'], $this->getLocaleId());
			
			$this
				->set('os_arr', $stack['os_arr'])
				->set('extra_arr', $stack['extra_arr'])
				->set('attr_arr', $stack['attr_arr'])
			;
		}
	}
	
	public function pjActionStockAdd()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['stock_add']))
			{
				if (isset($_POST['qty']) && !empty($_POST['qty']))
				{
					$pjOrderStockModel = pjOrderStockModel::factory();
					$pjOrderExtraModel = pjOrderExtraModel::factory();
					$pjStockModel = pjStockModel::factory();
					foreach ($_POST['qty'] as $stock_id => $qty)
					{
						if (empty($qty))
						{
							continue;
						}
						$order_stock_id = $pjOrderStockModel->reset()->setAttributes(array(
							'order_id' => $_POST['order_id'],
							'product_id' => $_POST['product_id'],
							'stock_id' => $stock_id,
							'price' => $_POST['price'][$stock_id],
							'qty' => $qty
						))->insert()->getInsertId();
						
						if ($order_stock_id !== FALSE && (int) $order_stock_id > 0 &&
							isset($_POST['extra_id']) && isset($_POST['extra_id'][$stock_id]) && !empty($_POST['extra_id'][$stock_id]))
						{
							$pjStockModel->reset()->set('id', $stock_id)->modify(array('qty' => ":qty - " . (int) $qty));

							$oe_data = array(
								'order_id' => $_POST['order_id'],
								'order_stock_id' => $order_stock_id
							);
							foreach ($_POST['extra_id'][$stock_id] as $extra_id => $value)
							{
								if (!empty($value) && strpos($value, "|") !== false)
								{
									$e_arr = array();
									$e_arr = explode("|", $value);
									switch ($e_arr[0])
									{
										case 'single':
											$oe_data['extra_item_id'] = NULL;
											break;
										case 'multi':
											$oe_data['extra_item_id'] = $e_arr[2];
											break;
									}
									$oe_data['extra_id'] = $extra_id;
									$oe_data['price'] = $e_arr[1];
									
									$pjOrderExtraModel->reset()->setAttributes($oe_data)->insert();
								}
							}
						}
					}
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Stock has been added.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Stock couldn\'t be empty.'));
			}
			
			$this->set('product_arr', pjProductModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.status != 2')
				->orderBy("`name` ASC")
				->findAll()->getData());
		}
	}
	
	public function pjActionStockGetByProduct()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$pjStockAttributeModel = pjStockAttributeModel::factory();
				$pjExtraItemModel = pjExtraItemModel::factory();
				
				$extra_arr = pjExtraModel::factory()
					->select('t1.*, t2.content AS name, t3.content AS title')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='extra_title'", 'left outer')
					->where('t1.product_id', $_GET['product_id'])
					->orderBy('`title` ASC, `name` ASC')
					->findAll()
					->getData();
	
				foreach ($extra_arr as $k => $extra)
				{
					$extra_arr[$k]['extra_items'] = $pjExtraItemModel
						->reset()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
						->where('t1.extra_id', $extra['id'])
						->orderBy('t1.price ASC')
						->findAll()
						->getData();
				}
				
				$attr_arr = array();
				// Do not change col_name, direction
				$a_arr = pjAttributeModel::factory()
					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.product_id', $_GET['product_id'])
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
						
				$stock_arr = pjStockModel::factory()->where('t1.product_id', $_GET['product_id'])->findAll()->getData();
				
				foreach ($stock_arr as $k => $stock)
				{
					$stock_arr[$k]['attrs'] = $pjStockAttributeModel
						->reset()
						->where('t1.stock_id', $stock['id'])
						->orderBy('t1.attribute_id ASC')
						->findAll()
						->getDataPair('attribute_parent_id', 'attribute_id');
				}
				$this
					->set('extra_arr', $extra_arr)
					->set('attr_arr', array_values($attr_arr))
					->set('stock_arr', $stock_arr)
				;
			}
		}
	}
	
	public function pjActionStockEdit()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['stock_edit']))
			{
				$pjOrderStockModel = pjOrderStockModel::factory();
				$arr = $pjOrderStockModel->find($_POST['order_stock_id'])->getData();
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Order/stock not found'));
				}
				$qty = (int) $_POST['qty'];
				$pjOrderStockModel->modify(array('qty' => $qty));
				if ($arr['qty'] > $qty)
				{
					$diff = $arr['qty'] - $qty;
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty + $diff"));
				} elseif ($arr['qty'] < $qty) {
					$diff = $qty - $arr['qty'];
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty - $diff"));
				}
				
				$pjOrderExtraModel = pjOrderExtraModel::factory();
				
				$pjOrderExtraModel->reset()->where('order_stock_id', $_POST['order_stock_id']);
				if (isset($_POST['extra_id']) && !empty($_POST['extra_id']))
				{
					$pjOrderExtraModel->whereNotIn('extra_id', array_keys($_POST['extra_id']));
				}
				$pjOrderExtraModel->eraseAll();
				
				if (isset($_POST['extra_id']) && !empty($_POST['extra_id']))
				{
					$empty_id = $exist_id = array();
					foreach ($_POST['extra_id'] as $extra_id => $value)
					{
						if (empty($value))
						{
							$empty_id[] = $extra_id;
							continue;
						}

						$stack = explode("|", $value);
						switch ($stack[0])
						{
							case 'single':
								if (0 == $pjOrderExtraModel->reset()
									->where('order_stock_id', $_POST['order_stock_id'])
									->where('extra_id', $extra_id)
									->where('extra_item_id IS NULL')
									->findCount()->getData())
								{
									$pjOrderExtraModel->reset()->setAttributes(array(
										'order_id' => $_POST['order_id'],
										'order_stock_id' => $_POST['order_stock_id'],
										'extra_id' => $extra_id,
										'price' => $stack[1]
									))->insert();
								} else {
									//do nothing
									$exist_id[] = $extra_id;
								}
								break;
							case 'multi':
								if (0 == $pjOrderExtraModel->reset()
									->where('order_stock_id', $_POST['order_stock_id'])
									->where('extra_id', $extra_id)
									->findCount()->getData())
								{
									$pjOrderExtraModel->reset()->setAttributes(array(
										'order_id' => $_POST['order_id'],
										'order_stock_id' => $_POST['order_stock_id'],
										'extra_id' => $extra_id,
										'extra_item_id' => $stack[2],
										'price' => $stack[1]
									))->insert();
								} else {
									$pjOrderExtraModel->reset()
										->where('order_id', $_POST['order_id'])
										->where('order_stock_id', $_POST['order_stock_id'])
										->where('extra_id', $extra_id)
										->limit(1)
										->modifyAll(array(
											'extra_item_id' => $stack[2],
											'price' => $stack[1]
										)
									);
									$exist_id[] = $extra_id;
								}
								break;
						}
					}
					
					$pjOrderExtraModel->reset();
					if (!empty($empty_id))
					{
						$pjOrderExtraModel
							->where('order_stock_id', $_POST['order_stock_id'])
							->whereIn('extra_id', $empty_id);
							
						if (!empty($exist_id))
						{
							$pjOrderExtraModel->whereNotIn('extra_id', $exist_id);
						}
						$pjOrderExtraModel->eraseAll();
					}
				} else {
					pjOrderExtraModel::factory()->where('order_stock_id', $_POST['order_stock_id'])->eraseAll();
				}
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			
			$os_arr = pjOrderStockModel::factory()->find($_GET['order_stock_id'])->getData();
			$oe_arr = pjOrderExtraModel::factory()->where('t1.order_stock_id', $_GET['order_stock_id'])->findAll()->getDataPair('extra_id', 'extra_item_id');
			$stock_arr = pjStockModel::factory()->find($os_arr['stock_id'])->getData();
			
			$stock_arr['attrs'] = pjStockAttributeModel::factory()
				->where('t1.stock_id', $os_arr['stock_id'])
				->orderBy('t1.attribute_id ASC')
				->findAll()
				->getDataPair('attribute_parent_id', 'attribute_id');
			
			$pjExtraItemModel = pjExtraItemModel::factory();
				
			$extra_arr = pjExtraModel::factory()
				->select('t1.*, t2.content AS name, t3.content AS title')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='extra_title'", 'left outer')
				->where('t1.product_id', $os_arr['product_id'])
				->orderBy('`title` ASC, `name` ASC')
				->findAll()
				->getData();

			foreach ($extra_arr as $k => $extra)
			{
				$extra_arr[$k]['extra_items'] = $pjExtraItemModel
					->reset()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
					->where('t1.extra_id', $extra['id'])
					->orderBy('t1.price ASC')
					->findAll()
					->getData();
			}
				
			$attr_arr = array();
			// Do not change col_name, direction
			$a_arr = pjAttributeModel::factory()
				->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.product_id', $os_arr['product_id'])
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
			
			$this
				->set('os_arr', $os_arr)
				->set('oe_arr', $oe_arr)
				->set('attr_arr', $attr_arr)
				->set('stock_arr', $stock_arr)
				->set('extra_arr', $extra_arr)
			;
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$pjOrderModel = pjOrderModel::factory();
			if (isset($_REQUEST['id']) && (int) $_REQUEST['id'] > 0)
			{
				$pjOrderModel->where('t1.id', $_REQUEST['id']);
			} elseif (isset($_GET['uuid']) && !empty($_GET['uuid'])) {
				$pjOrderModel->where('t1.uuid', $_GET['uuid']);
			}
			$arr = $pjOrderModel
				->select('t1.*, t2.email AS client_email, t2.phone AS client_phone, t2.url AS client_url')
				->join('pjClient', 't2.id=t1.client_id', 'left outer')
				->limit(1)
				->findAll()
				->getData();
				
			if (empty($arr))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AOR08");
			}
			$arr = $arr[0];
			
			if (isset($_POST['update_form']))
			{
				if (0 != $pjOrderModel->reset()->where('t1.uuid', $_POST['uuid'])->where('t1.id !=', $_POST['id'])->findCount()->getData())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AOR02");
				}
				
				$data = array();
				if (isset($_POST['same_as']))
				{
					$data['b_name'] = $_POST['s_name'];
					$data['b_country_id'] = $_POST['s_country_id'];
					$data['b_state'] = $_POST['s_state'];
					$data['b_city'] = $_POST['s_city'];
					$data['b_zip'] = $_POST['s_zip'];
					$data['b_address_1'] = $_POST['s_address_1'];
					$data['b_address_2'] = $_POST['s_address_2'];
				} else {
					$data['same_as'] = array(0);
				}
				
				if ($arr['status'] != 'completed' && $_POST['status'] == 'completed')
				{
					$data['processed_on'] = ':NOW()';
				}
				
				$pjOrderModel->reset()->set('id', $_POST['id'])->modify(array_merge($_POST, $data));
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AOR05");
			} else {
				$this->set('os_arr', pjOrderStockModel::factory()
					->select(sprintf("t1.*, t2.content AS product_name,
					(SELECT GROUP_CONCAT(CONCAT_WS('~:~', m2.content, m1.content) SEPARATOR '~..~')
						FROM `%1\$s`
						LEFT OUTER JOIN `%2\$s` AS `m1` ON m1.model='pjAttribute' AND m1.foreign_id = attribute_id AND m1.field = 'name' AND m1.locale = '%3\$u'
						LEFT OUTER JOIN `%2\$s` AS `m2` ON m2.model='pjAttribute' AND m2.foreign_id = attribute_parent_id AND m2.field = 'name' AND m2.locale = '%3\$u'
						WHERE stock_id = t1.stock_id
						LIMIT 1) AS `attr`
					", pjStockAttributeModel::factory()->getTable(), pjMultiLangModel::factory()->getTable(), $arr['locale_id']))
					->join('pjMultiLang', sprintf("t2.model='pjProduct' AND t2.foreign_id=t1.product_id AND t2.field='name' AND t2.locale='%u'", $arr['locale_id']), 'left outer')
					->where('t1.order_id', $arr['id'])
					->findAll()
					->toArray('attr', '~..~')
					->getData()
				);
				
				$this
					->set('arr', $arr)
					->set('country_arr', pjCountryModel::factory()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`name` ASC')
						->findAll()
						->getData()
					)
					->set('client_arr', pjClientModel::factory()->orderBy('t1.client_name ASC')->findAll()->getData())
					->set('address_arr', pjAddressModel::factory()
						->select('t1.*, t2.content AS country_name')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->where('t1.client_id', $arr['client_id'])
						->orderBy('t1.address_1 ASC')->findAll()->getData()
					)
					->set('tax_arr', pjTaxModel::factory()
						->select('t1.*, t2.content AS location')
						->join('pjMultiLang', "t2.model='pjTax' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`location` ASC')
						->findAll()
						->getData()
					)
					->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/')
					->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/')
					->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
					->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
					->appendJs('pjAdminOrders.js')
					->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
				;
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>