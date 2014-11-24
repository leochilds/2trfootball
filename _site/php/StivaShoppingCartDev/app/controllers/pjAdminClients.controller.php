<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminClients extends pjAdmin
{
	public function pjActionCheckEmail()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['email']) && !empty($_GET['email']))
			{
				$pjClientModel = pjClientModel::factory()->where('t1.email', $_GET['email']);
				if (isset($_GET['id']) && (int) $_GET['id'] > 0)
				{
					$pjClientModel->where('t1.id !=', $_GET['id']);
				}
				echo 0 == $pjClientModel->findCount()->getData() ? 'true' : 'false';
			} else {
				echo 'false';
			}
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['create_form']))
			{
				$pjClientModel = pjClientModel::factory();
				
				if (0 == $pjClientModel->where('t1.email', $_POST['email'])->findCount()->getData())
				{
					$data = array();
					$client_id = $pjClientModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
					if ($client_id !== false && (int) $client_id > 0)
					{
						if (isset($_POST['name']))
						{
							$pjAddressModel = pjAddressModel::factory();
							$pjAddressModel->begin();
							foreach ($_POST['name'] as $k => $v)
							{
								if (!empty($v))
								{
									$pjAddressModel->reset()->setAttributes(array(
										'client_id' => $client_id,
										'country_id' => $_POST['country_id'][$k],
										'state' => $_POST['state'][$k],
										'city' => $_POST['city'][$k],
										'zip' => $_POST['zip'][$k],
										'address_1' => $_POST['address_1'][$k],
										'address_2' => $_POST['address_2'][$k],
										'name' => $_POST['name'][$k],
										'is_default_shipping' => ($_POST['is_default_shipping'] == $k ? 1 : 0),
										'is_default_billing' => ($_POST['is_default_billing'] == $k ? 1 : 0)
									))->insert();
								}
							}
							$pjAddressModel->commit();
						}
						$err = 'AC01';
					} else {
						$err = 'AC02';
					}
				} else {
					$err = 'AC07';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminClients&action=pjActionIndex&err=" . $err);
			} else {
				$this->set('country_arr', pjCountryModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`name` ASC')
					->findAll()
					->getData()
				);
				
				$this
					->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/')
					->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/')
					->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
					->appendJs('pjAdminClients.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjClientModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				pjAddressModel::factory()->where('client_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteClientBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjClientModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjAddressModel::factory()->whereIn('client_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionDeleteAddress()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (pjAddressModel::factory()->set('id', $_POST['id'])->erase()->getAffectedRows() == 1)
			{
				$resp = array('code' => 200);
			} else {
				$resp = array('code' => 100);
			}
			pjAppController::jsonResponse($resp);
			
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionExportClient()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjClientModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Clients-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetAddresses()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$this->set('address_arr', pjAddressModel::factory()
				->where('t1.client_id', $_GET['id'])
				->orderBy('FIELD(`is_default_shipping`, 1, 0), FIELD(`is_default_billing`, 1, 0), t1.id ASC')
				->findAll()
				->getData()
			);
			$this->set('country_arr', pjCountryModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy('`name` ASC')
				->findAll()
				->getData()
			);
		}
	}
	
	public function pjActionGetClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjClientModel = pjClientModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = trim($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjClientModel->where('t1.email LIKE', "%$q%");
				$pjClientModel->orWhere('t1.client_name LIKE', "%$q%");
				$pjClientModel->orWhere('t1.phone LIKE', "%$q%");
				$pjClientModel->orWhere('t1.client_name LIKE', "%$q%");
				$pjClientModel->orWhere(sprintf("t1.id IN (SELECT `client_id` FROM `%2\$s` WHERE `name` LIKE '%%%1\$s%%')", $pjClientModel->escapeStr($q), pjAddressModel::factory()->getTable()));
			}

			$column = 'client_name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjClientModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjClientModel
				->select(sprintf("t1.id, t1.client_name, t1.email, t1.status,
					(SELECT COUNT(*) FROM `%1\$s` WHERE `client_id` = `t1`.`id` LIMIT 1) AS `orders`,
				 	(SELECT `created` FROM `%1\$s` WHERE `client_id` = `t1`.`id` ORDER BY `created` DESC LIMIT 1) AS `last_order`", pjOrderModel::factory()->getTable()))
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminClients.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjClientModel = pjClientModel::factory();
			if (!in_array($_POST['column'], @$pjClientModel->getI18n()))
			{
				$pjClientModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjClient');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['update_form']))
			{
				if (pjClientModel::factory()->set('id', $_POST['id'])->modify($_POST)->getAffectedRows() == 1)
				{
					$err = 'AC05';
				} else {
					$err = 'AC06';
				}
				
				if (isset($_POST['name']))
				{
					$pjAddressModel = pjAddressModel::factory();
					$pjAddressModel->begin();
					foreach ($_POST['name'] as $k => $v)
					{
						if (empty($v))
						{
							continue;
						}
						if (strpos($k, 'new_') === 0)
						{
							# Add new
							$pjAddressModel->reset()->setAttributes(array(
								'client_id' => $_POST['id'],
								'country_id' => $_POST['country_id'][$k],
								'state' => $_POST['state'][$k],
								'city' => $_POST['city'][$k],
								'zip' => $_POST['zip'][$k],
								'address_1' => $_POST['address_1'][$k],
								'address_2' => $_POST['address_2'][$k],
								'name' => $_POST['name'][$k],
								'is_default_shipping' => ($_POST['is_default_shipping'] == $k ? 1 : 0),
								'is_default_billing' => ($_POST['is_default_billing'] == $k ? 1 : 0)
							))->insert();
						} else {
							# Update existing
							$pjAddressModel->reset()->set('id', $k)->modify(array(
								'country_id' => $_POST['country_id'][$k],
								'state' => $_POST['state'][$k],
								'city' => $_POST['city'][$k],
								'zip' => $_POST['zip'][$k],
								'address_1' => $_POST['address_1'][$k],
								'address_2' => $_POST['address_2'][$k],
								'name' => $_POST['name'][$k],
								'is_default_shipping' => ($_POST['is_default_shipping'] == $k ? 1 : 0),
								'is_default_billing' => ($_POST['is_default_billing'] == $k ? 1 : 0)
							));
						}
					}
					$pjAddressModel->commit();
				}
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminClients&action=pjActionIndex&err=" . $err);
			} else {
				$arr = pjClientModel::factory()
					->select(sprintf("t1.*, AES_DECRYPT(`password`, '%s') AS `password`, (SELECT COUNT(*) FROM `%s` WHERE `client_id` = `t1`.`id` LIMIT 1) AS `orders`", PJ_SALT, pjOrderModel::factory()->getTable()))
					->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminClients&action=pjActionIndex&err=AC08");
				}
				$this->set('address_arr', pjAddressModel::factory()
					->where('t1.client_id', $arr['id'])
					->orderBy('FIELD(`is_default_shipping`,1,0), FIELD(`is_default_billing`,1,0), t1.id ASC')
					->findAll()
					->getData());
				$this->set('arr', $arr);
				
				$this->set('country_arr', pjCountryModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`name` ASC')
					->findAll()
					->getData()
				);
				
				$this
					->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/')
					->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/')
					->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
					->appendJs('pjAdminClients.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>