<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVouchers extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['voucher_create']))
			{
				$data = array();
				$data['code'] = $_POST['code'];
				$data['discount'] = $_POST['discount'];
				$data['type'] = $_POST['type'];
				$data['valid'] = $_POST['valid'];
				switch ($_POST['valid'])
				{
					case 'fixed':
						$data['date_from'] = pjUtil::formatDate($_POST['f_date'], $this->option_arr['o_date_format']);
						$data['date_to'] = $data['date_from'];
						$data['time_from'] = $_POST['f_hour_from'] . ":" . $_POST['f_minute_from'] . ":00";
						$data['time_to'] = $_POST['f_hour_to'] . ":" . $_POST['f_minute_to'] . ":00";
						break;
					case 'period':
						$data['date_from'] = pjUtil::formatDate($_POST['p_date_from'], $this->option_arr['o_date_format']);
						$data['date_to'] = pjUtil::formatDate($_POST['p_date_to'], $this->option_arr['o_date_format']);
						$data['time_from'] = $_POST['p_hour_from'] . ":" . $_POST['p_minute_from'] . ":00";
						$data['time_to'] = $_POST['p_hour_to'] . ":" . $_POST['p_minute_to'] . ":00";
						break;
					case 'recurring':
						$data['every'] = $_POST['r_every'];
						$data['time_from'] = $_POST['r_hour_from'] . ":" . $_POST['r_minute_from'] . ":00";
						$data['time_to'] = $_POST['r_hour_to'] . ":" . $_POST['r_minute_to'] . ":00";
						break;
				}
				
				$id = pjVoucherModel::factory()->setAttributes($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_POST['product_id']) && count($_POST['product_id']) > 0)
					{
						$pjVoucherProductModel = pjVoucherProductModel::factory();
						$pjVoucherProductModel->begin();
						foreach ($_POST['product_id'] as $product_id)
						{
							$pjVoucherProductModel->reset()->setAttributes(array(
								'voucher_id' => $id,
								'product_id' => $product_id
							))->insert();
						}
						$pjVoucherProductModel->commit();
					}
					$err = 'AV01';
				} else {
					$err = 'AV02';
				}
				
				pjUtil::redirect(sprintf("%s?controller=pjAdminVouchers&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
			} else {
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminVouchers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				if (pjVoucherModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
				{
					pjVoucherProductModel::factory()->where('voucher_id', $_GET['id'])->eraseAll();
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Voucher has been deleted.'));
				} else {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Voucher has not been deleted.'));
				}
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing or empty params.'));
		}
		exit;
	}
	
	public function pjActionDeleteVoucherBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['record']) && !empty($_POST['record']))
			{
				pjVoucherModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjVoucherProductModel::factory()->whereIn('voucher_id', $_POST['record'])->eraseAll();
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Voucher(s) has been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing or empty params.'));
		}
		exit;
	}

	public function pjActionGetVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjVoucherModel = pjVoucherModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = trim($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjVoucherModel->where('t1.code LIKE', "%$q%");
			}
			if (isset($_GET['valid']) && !empty($_GET['valid']))
			{
				$pjVoucherModel->where('t1.valid', $_GET['valid']);
			}

			$column = 'code';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjVoucherModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjVoucherModel->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
				
			$daynames = __('daynames', true);
			foreach ($data as $k => $v)
			{
				$data[$k]['discount_f'] = $v['type'] == 'amount' ?
					pjUtil::formatCurrencySign(number_format($v['discount'], 2), $this->option_arr['o_currency']) :
					$v['discount'] . '%';
					
				switch ($v['valid'])
				{
					case 'fixed':
						$data[$k]['valid_f'] = sprintf('%s, %s - %s', pjUtil::formatDate($v['date_from'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_from'], 0, 5), substr($v['time_to'], 0, 5));
						break;
					case 'period':
						$data[$k]['valid_f'] = sprintf('%s, %s &divide; %s, %s', pjUtil::formatDate($v['date_from'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_from'], 0, 5), pjUtil::formatDate($v['date_to'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_to'], 0, 5));
						break;
					case 'recurring':
						$data[$k]['valid_f'] = sprintf('%s, %s - %s', @$daynames[$v['every']], substr($v['time_from'], 0, 5), substr($v['time_to'], 0, 5));
						break;
				}
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionGetProducts()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjProductModel = pjProductModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer');
			if (isset($_GET['term']))
			{
				$q = $pjProductModel->escapeStr(trim($_GET['term']));
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjProductModel->having(sprintf("name LIKE '%%%1\$s%%' OR t1.id LIKE '%%%1\$s%%' OR t1.sku LIKE '%%%1\$s%%'", $q), false);
			}
			if (isset($_GET['voucher_id']) && (int) $_GET['voucher_id'] > 0)
			{
				$pjProductModel->where(sprintf("t1.id NOT IN (SELECT `product_id` FROM `%s` WHERE `voucher_id` = '%u')",
					pjVoucherProductModel::factory()->getTable(), (int) $_GET['voucher_id']
				));
			}

			$arr = $pjProductModel->orderBy('`name` ASC')->findAll()->getData();
			
			$_arr = array();
			foreach ($arr as $v)
			{
				$_arr[] = array('label' => $v['name'], 'value' => $v['id']);
			}
			pjAppController::jsonResponse($_arr);
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminVouchers.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0 &&
				isset($_POST['column']) && isset($_POST['value']) &&
				!empty($_POST['column']))
			{
				$pjVoucherModel = pjVoucherModel::factory();
				if (!in_array($_POST['column'], $pjVoucherModel->getI18n()))
				{
					$pjVoucherModel->set('id', $_GET['id'])->modify(array($_POST['column'] => $_POST['value']));
				} else {
					pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjVoucher');
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Voucher has been saved.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty params.'));
		}
		exit;
	}

	public function pjActionUnlinkProduct()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['voucher_id']) && isset($_POST['product_id']) &&
				(int) $_POST['voucher_id'] > 0 && (int) $_POST['product_id'] > 0)
			{
				if (pjVoucherProductModel::factory()
					->where('voucher_id', $_POST['voucher_id'])
					->where('product_id', $_POST['product_id'])
					->limit(1)
					->eraseAll()
					->getAffectedRows() == 1)
				{
						pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Voucher has been unlinked with given product.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Voucher has not been unlinked with given product.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing parameters'));
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['voucher_update']))
			{
				$data = array();
				$data['id'] = $_POST['id'];
				$data['code'] = $_POST['code'];
				$data['discount'] = $_POST['discount'];
				$data['type'] = $_POST['type'];
				$data['valid'] = $_POST['valid'];
				switch ($_POST['valid'])
				{
					case 'fixed':
						$data['date_from'] = pjUtil::formatDate($_POST['f_date'], $this->option_arr['o_date_format']);
						$data['date_to'] = $data['date_from'];
						$data['time_from'] = $_POST['f_hour_from'] . ":" . $_POST['f_minute_from'] . ":00";
						$data['time_to'] = $_POST['f_hour_to'] . ":" . $_POST['f_minute_to'] . ":00";
						$data['every'] = array('NULL');
						break;
					case 'period':
						$data['date_from'] = pjUtil::formatDate($_POST['p_date_from'], $this->option_arr['o_date_format']);
						$data['date_to'] = pjUtil::formatDate($_POST['p_date_to'], $this->option_arr['o_date_format']);
						$data['time_from'] = $_POST['p_hour_from'] . ":" . $_POST['p_minute_from'] . ":00";
						$data['time_to'] = $_POST['p_hour_to'] . ":" . $_POST['p_minute_to'] . ":00";
						$data['every'] = array('NULL');
						break;
					case 'recurring':
						$data['date_from'] = array('NULL');
						$data['date_to'] = array('NULL');
						$data['every'] = $_POST['r_every'];
						$data['time_from'] = $_POST['r_hour_from'] . ":" . $_POST['r_minute_from'] . ":00";
						$data['time_to'] = $_POST['r_hour_to'] . ":" . $_POST['r_minute_to'] . ":00";
						break;
				}
				$pjVoucherProductModel = pjVoucherProductModel::factory();
				$pjVoucherProductModel->where('voucher_id', $_POST['id'])->eraseAll();
				if (isset($_POST['product_id']) && count($_POST['product_id']) > 0)
				{
					$pjVoucherProductModel->begin();
					foreach ($_POST['product_id'] as $product_id)
					{
						$pjVoucherProductModel->reset()->setAttributes(array(
							'voucher_id' => $_POST['id'],
							'product_id' => $product_id
						))->insert();
					}
					$pjVoucherProductModel->commit();
				}
				
				if (pjVoucherModel::factory()->set('id', $data['id'])->modify($data)->getAffectedRows() == 1)
				{
					$err = 'AV05';
				} else {
					$err = 'AV06';
				}
				pjUtil::redirect(sprintf("%s?controller=pjAdminVouchers&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
			} else {
				$arr = pjVoucherModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(sprintf("%s?controller=pjAdminVouchers&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], 'AV08'));
				}
				$this->set('arr', $arr);
				
				$this->set('vp_arr', pjVoucherProductModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.product_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.voucher_id', $arr['id'])
					->orderBy('t1.product_id ASC')
					->findAll()->getData());
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminVouchers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>