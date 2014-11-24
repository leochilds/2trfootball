<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAppController.controller.php';
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
	
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin', 'pjActionForgot', 'pjActionPreview')))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			}
		}
	}
	
	public function beforeRender()
	{
		
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$pjClientModel = pjClientModel::factory();
			$pjStockAttributeModel = pjStockAttributeModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			$pjOrderModel = pjOrderModel::factory()->orderBy('t1.id DESC')->limit(5)->findAll();
			$order_arr = $pjOrderModel->getData();
			$client_arr = $pjClientModel->orderBy('t1.id DESC')->limit(5)->findAll()->getData();
			$stock_arr = pjStockModel::factory()
				->select(sprintf("t1.*, t2.content AS `name`,
					(SELECT GROUP_CONCAT(CONCAT_WS('~:~', `tm2`.`content`, `tm1`.`content`) SEPARATOR '~|~')
						FROM `%1\$s` AS `tsa`
						LEFT JOIN `%2\$s` AS `tm1` ON `tm1`.`model` = 'pjAttribute' AND `tm1`.`foreign_id` = `tsa`.`attribute_id` AND `tm1`.`field` = 'name' AND `tm1`.`locale` = '%3\$u'
						LEFT JOIN `%2\$s` AS `tm2` ON `tm2`.`model` = 'pjAttribute' AND `tm2`.`foreign_id` = `tsa`.`attribute_parent_id` AND `tm2`.`field` = 'name' AND `tm2`.`locale` = '%3\$u'
						WHERE `tsa`.`product_id` = `t1`.`product_id`
						AND `tsa`.`stock_id` = `t1`.`id`
						LIMIT 1) AS `stock_attr`
					", $pjStockAttributeModel->getTable(), $pjMultiLangModel->getTable(), $this->getLocaleId()))
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.product_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy('t1.qty ASC')
				->limit(5)
				->findAll()
				->toArray('stock_attr', '~|~')
				->getData();
			
			$order_ids = $pjOrderModel->getDataPair(null, 'id');
			$os_arr = array();
			if (!empty($order_ids))
			{
				$os_arr = pjOrderStockModel::factory()
					->select(sprintf("t1.*, t2.content AS `name`,
						(SELECT GROUP_CONCAT(CONCAT_WS('~:~', `tm2`.`content`, `tm1`.`content`) SEPARATOR '~|~')
							FROM `%1\$s` AS `tsa`
							LEFT JOIN `%2\$s` AS `tm1` ON `tm1`.`model` = 'pjAttribute' AND `tm1`.`foreign_id` = `tsa`.`attribute_id` AND `tm1`.`field` = 'name' AND `tm1`.`locale` = '%3\$u'
							LEFT JOIN `%2\$s` AS `tm2` ON `tm2`.`model` = 'pjAttribute' AND `tm2`.`foreign_id` = `tsa`.`attribute_parent_id` AND `tm2`.`field` = 'name' AND `tm2`.`locale` = '%3\$u'
							WHERE `tsa`.`product_id` = `t1`.`product_id`
							AND `tsa`.`stock_id` = `t1`.`stock_id`
							LIMIT 1) AS `stock_attr`
						", $pjStockAttributeModel->getTable(), $pjMultiLangModel->getTable(), $this->getLocaleId()))
					->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.product_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->whereIn('t1.order_id', $order_ids)
					->findAll()
					->toArray('stock_attr', '~|~')
					->getData();
			}

			foreach ($order_arr as $k => $order)
			{
				$order_arr[$k]['order_stock_arr'] = array();
				foreach ($os_arr as $order_stock)
				{
					if ($order_stock['order_id'] == $order['id'])
					{
						$order_arr[$k]['order_stock_arr'][] = $order_stock;
					}
				}
			}
			
			$info_arr = pjAppModel::factory()
				->prepare(sprintf("SELECT 1,
					(SELECT COUNT(*) FROM `%1\$s` WHERE DATE(`created`) = CURDATE() LIMIT 1) AS `orders`,
					(SELECT COUNT(*) FROM `%2\$s` WHERE DATE(`created`) = CURDATE() LIMIT 1) AS `clients`,
					(SELECT COUNT(*) FROM `%3\$s` WHERE 1 LIMIT 1) AS `products`
				", $pjOrderModel->getTable(), $pjClientModel->getTable(), pjProductModel::factory()->getTable()))
				->exec(array())
				->getData();
				
			$this
				->set('order_arr', $order_arr)
				->set('client_arr', $client_arr)
				->set('stock_arr', $stock_arr)
				->set('info_arr', $info_arr);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionForgot()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['forgot_user']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			}
			$pjUserModel = pjUserModel::factory();
			$user = $pjUserModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
				
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			} else {
				$user = $user[0];
				
				$Email = new pjEmail();
				$Email
					->setTo($user['email'])
					->setFrom($user['email'])
					->setSubject(__('emailForgotSubject', true));
				
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
				
				$body = str_replace(
					array('{Name}', '{Password}'),
					array($user['name'], $user['password']),
					__('emailForgotBody', true)
				);

				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionMessages()
	{
		$this->setAjax(true);
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLogin()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['login_user']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
				!pjValidation::pjActionEmail($_POST['login_email']))
			{
				// Data not validate
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=4");
			}
			$pjUserModel = pjUserModel::factory();

			$user = $pjUserModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", $pjUserModel->escapeStr($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();

			if (count($user) != 1)
			{
				# Login failed
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=1");
			} else {
				$user = $user[0];
				unset($user['password']);
															
				if (!in_array($user['role_id'], array(1,2,3)))
				{
					# Login denied
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['role_id'] == 3 && $user['is_active'] == 'F')
				{
					# Login denied
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['status'] != 'T')
				{
					# Login forbidden
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=3");
				}
				
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
    			$_SESSION[$this->defaultUser] = $user;
    			
    			# Update
    			$data = array();
    			$data['last_login'] = $last_login;
    			$pjUserModel->reset()->setAttributes(array('id' => $user['id']))->modify($data);

    			if ($this->isAdmin())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionLogout()
	{
		if ($this->isLoged())
        {
        	unset($_SESSION[$this->defaultUser]);
        }
       	pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
	}
	
	public function pjActionProfile()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			if (isset($_POST['profile_update']))
			{
				$pjUserModel = pjUserModel::factory();
				$arr = $pjUserModel->find($this->getUserId())->getData();
				$data = array();
				$data['role_id'] = $arr['role_id'];
				$data['status'] = $arr['status'];
				$post = array_merge($_POST, $data);
				if (!$pjUserModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA14");
				}
				$pjUserModel->set('id', $this->getUserId())->modify($post);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA13");
			} else {
				$this->set('arr', pjUserModel::factory()->find($this->getUserId())->getData());
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdmin.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>