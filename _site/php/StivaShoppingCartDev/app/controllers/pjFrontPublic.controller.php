<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontPublic extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setAjax(true);
		
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionRouter()
	{
		$this->setAjax(false);

		if (isset($_GET['_escaped_fragment_']))
		{
			$templates = array('Cart', 'Checkout', 'Preview', 'Login', 'Forgot', 'Profile', 'Register', 'Product', 'Products', 'Favs');
			preg_match('/^\/(\w+).*/', $_GET['_escaped_fragment_'], $m);
			if (isset($m[1]) && in_array($m[1], $templates))
			{
				$template = 'pjAction'.$m[1];
			
				if (method_exists($this, $template))
				{
					$this->$template();
				}
				$this->setTemplate('pjFrontPublic', $template);
			}
		}
	}
	
	public function pjActionCart()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (!$this->cart->isEmpty() && (int) $this->option_arr['o_disable_orders'] === 0)
			{
				$data = $this->pjActionGetCart();
				
				if (isset($_SESSION[$this->defaultTax]) && (int) $_SESSION[$this->defaultTax] > 0)
				{
					foreach ($data['tax_arr'] as $item)
					{
						if ($item['id'] == $_SESSION[$this->defaultTax])
						{
							$this->set('o_tax', $item['tax'])->set('o_shipping', $item['shipping'])->set('o_free', $item['free']);
							break;
						}
					}
				}
				
				$this
					->set('arr', $data['arr'])
					->set('extra_arr', $data['extra_arr'])
					->set('order_arr', $data['order_arr'])
					->set('attr_arr', $data['attr_arr'])
					->set('stock_arr', $data['stock_arr'])
					->set('tax_arr', $data['tax_arr'])
					->set('image_arr', $data['image_arr'])
				;
			}
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		}
	}
	
	public function pjActionCheckout()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_POST['sc_checkout']))
			{
				$_SESSION[$this->defaultForm] = $_POST;
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 211, 'text' => __('system_211', true)));
			} else {
				if (!$this->cart->isEmpty() && (int) $this->option_arr['o_disable_orders'] === 0)
				{
					if ($this->pjActionShowShipping() && (!isset($_SESSION[$this->defaultTax]) || empty($_SESSION[$this->defaultTax])) &&
						0 < pjTaxModel::factory()->findCount()->getData())
					{
						$this->set('status', 'ERR');
						# Shipping location is not set
						$this->set('code', '100');
					} else {
						$this->set('country_arr', pjCountryModel::factory()
							->select('t1.*, t2.content AS name')
							->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->where('t1.status', 'T')
							->orderBy('`name` ASC')
							->findAll()
							->getData()
						);

						$terms = $this->getModel('Option')
							->reset()
							->select('t1.*, t2.content AS `terms_url`, t3.content AS `terms_body`')
							->join('pjMultiLang', sprintf("t2.model='pjOption' AND t2.foreign_id='%u' AND t2.locale='%u' AND t2.field='terms_url'", $this->getForeignId(), $this->pjActionGetLocale()), 'left outer')
							->join('pjMultiLang', sprintf("t3.model='pjOption' AND t3.foreign_id='%u' AND t3.locale='%u' AND t3.field='terms_body'", $this->getForeignId(), $this->pjActionGetLocale()), 'left outer')
							->limit(1)
							->findAll()
							->getData();
						$this->set('terms', @$terms[0]);

						if ($this->isLoged())
						{
							$this->set('address_arr', pjAddressModel::factory()
								->where('t1.client_id', $this->getUserId())
								->findAll()
								->getData()
							);
						}
						$this->set('status', 'OK');
						
						$data = $this->pjActionGetCart();
						$this
							->set('arr', $data['arr'])
							->set('extra_arr', $data['extra_arr'])
							->set('attr_arr', $data['attr_arr']);
					}
				} else {
					$this->set('status', 'ERR');
					# Empty cart
					$this->set('code', '101');
				}
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			}
		}
	}
	
	public function pjActionPreview()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (!$this->cart->isEmpty() && (int) $this->option_arr['o_disable_orders'] === 0)
			{
				if ($this->pjActionShowShipping() && (!isset($_SESSION[$this->defaultTax]) || empty($_SESSION[$this->defaultTax])) &&
					0 < pjTaxModel::factory()->findCount()->getData())
				{
					$this->set('status', 'ERR');
					$this->set('code', '100');
					//Shipping location is not set
				} elseif (!isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm])) {
					$this->set('status', 'ERR');
					$this->set('code', '102');
					//Checkout form not filled
				} else {
					$this->set('country_arr', pjCountryModel::factory()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->where('t1.status', 'T')
						->orderBy('`name` ASC')
						->findAll()
						->getData()
					);
					$this->set('status', 'OK');
					
					$data = $this->pjActionGetCart();
					$this
						->set('arr', $data['arr'])
						->set('extra_arr', $data['extra_arr'])
						->set('attr_arr', $data['attr_arr']);
				}
			} else {
				$this->set('status', 'ERR');
				$this->set('code', '101');
				//Empty cart
			}
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		}
	}
	
	public function pjActionLogin()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_POST['sc_login']))
			{
				if (!isset($_POST['email']) || !pjValidation::pjActionNotEmpty($_POST['email']) || !pjValidation::pjActionEmail($_POST['email']) ||
					!isset($_POST['password']) || !pjValidation::pjActionNotEmpty($_POST['password']))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 120, 'text' => __('system_120', true)));
				}
			
				$pjClientModel = pjClientModel::factory();
				$arr = $pjClientModel->where('t1.email', $_POST['email'])->limit(1)->findAll()->getData();
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 121, 'text' => __('system_121', true)));
				}
				$arr = $arr[0];
				if ($arr['password'] != $_POST['password'])
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 122, 'text' => __('system_122', true)));
				}
				if ($arr['status'] != 'T')
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 132, 'text' => __('system_132', true)));
				}
				
				$pjClientModel->reset()->set('id', $arr['id'])->modify(array('last_login' => ':NOW()'));
				
				$_SESSION[$this->defaultUser] = $arr;
				# ---
				$hash = md5(PJ_SALT . $this->getUserId());
				$this->cart->transform($hash);
				$_SESSION[$this->defaultHash] = $hash;
				# ---
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 212, 'text' => __('system_212', true)));
			}
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		}
	}
	
	public function pjActionForgot()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_POST['sc_forgot']))
			{
				if (!isset($_POST['email']) || !pjValidation::pjActionNotEmpty($_POST['email']) || !pjValidation::pjActionEmail($_POST['email']))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 123, 'text' => __('system_123', true)));
				}
				
				$arr = pjClientModel::factory()->where('t1.email', $_POST['email'])->limit(1)->findAll()->getData();
				
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 124, 'text' => __('system_124', true)));
				}
				$arr = $arr[0];
				
				$pjEmail = new pjEmail();
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$pjEmail
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSmtpPort($this->option_arr['o_smtp_port']);
				}
				
				$body = str_replace(
					array('{Name}', '{Password}'),
					array($arr['client_name'], $arr['password']),
					$this->option_arr['o_email_password_reminder']
				);
				
				$result = $pjEmail
					->setTo($arr['email'])
					->setFrom($arr['email'])
					->setSubject($this->option_arr['o_email_password_reminder_subject'])
					->send($body);
				
				if (!$result)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 125, 'text' => __('system_125', true)));
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 213, 'text' => __('system_213', true)));
			}
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		}
	}
	
	public function pjActionProfile()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			$pjClientModel = pjClientModel::factory();
			
			if (isset($_POST['sc_profile']))
			{
				if (!$pjClientModel->validates($_POST))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 126, 'text' => __('system_126', true)));
				}
				
				if (0 != $pjClientModel->where('t1.email', $_POST['email'])->where('t1.id !=', $this->getUserId())->findCount()->getData())
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 127, 'text' => __('system_127', true)));
				}
				
				$pjClientModel->reset()->set('id', $this->getUserId())->modify($_POST);
				
				$pjAddressModel = pjAddressModel::factory();
				
				# Delete existing in DB and not presented in POST
				$pjAddressModel->where('client_id', $this->getUserId());
				if (isset($_POST['name']) && !empty($_POST['name']))
				{
					$pjAddressModel->whereNotIn('id', array_keys($_POST['name']));
				}
				$pjAddressModel->eraseAll();
				
				if (isset($_POST['name']))
				{
					$client_id = $this->getUserId();
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
								'client_id' => $client_id,
								'country_id' => $_POST['country_id'][$k],
								'state' => $_POST['state'][$k],
								'city' => $_POST['city'][$k],
								'zip' => $_POST['zip'][$k],
								'address_1' => $_POST['address_1'][$k],
								'address_2' => $_POST['address_2'][$k],
								'name' => $_POST['name'][$k],
								'is_default_shipping' => (@$_POST['is_default_shipping'] == $k ? 1 : 0),
								'is_default_billing' => (@$_POST['is_default_billing'] == $k ? 1 : 0)
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
								'is_default_shipping' => (@$_POST['is_default_shipping'] == $k ? 1 : 0),
								'is_default_billing' => (@$_POST['is_default_billing'] == $k ? 1 : 0)
							));
						}
					}
					$pjAddressModel->commit();
				}
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 214, 'text' => __('system_214', true)));
			} else {
				$this->set('arr', $pjClientModel->find($this->getUserId())->getData());
				
				$this->set('address_arr', pjAddressModel::factory()
					->where('t1.client_id', $this->getUserId())
					->orderBy('FIELD(`is_default_shipping`,1,0), FIELD(`is_default_billing`,1,0), t1.id ASC')
					->findAll()
					->getData());
				
				$this->set('country_arr', pjCountryModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.status', 'T')
					->orderBy('`name` ASC')
					->findAll()
					->getData());
			}
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		}
	}
	
	public function pjActionRegister()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_POST['sc_register']))
			{
				$pjClientModel = pjClientModel::factory();

				if (!isset($_POST['captcha']) || !pjValidation::pjActionNotEmpty($_POST['captcha']) ||
					!isset($_SESSION[$this->defaultCaptcha]) || !pjValidation::pjActionNotEmpty($_SESSION[$this->defaultCaptcha]) ||
					!pjCaptcha::validate($_POST['captcha'], $_SESSION[$this->defaultCaptcha]))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 128, 'text' => __('system_128', true)));
				}
				
				if (!$pjClientModel->validates($_POST))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 129, 'text' => __('system_129', true)));
				}
				
				if (0 != $pjClientModel->where('t1.email', $_POST['email'])->findCount()->getData())
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 130, 'text' => __('system_130', true)));
				}
				
				$client_id = $pjClientModel->setAttributes($_POST)->insert()->getInsertId();
				if ($client_id === FALSE || (int) $client_id <= 0)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 131, 'text' => __('system_131', true)));
				}
				
				$pjEmail = new pjEmail();
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$pjEmail
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSmtpPort($this->option_arr['o_smtp_port']);
				}
				
				$arr = $pjClientModel->reset()->find($client_id)->getData();
				
				$body = str_replace(
					array('{Name}', '{Password}', '{Email}', '{Phone}', '{URL}'),
					array($arr['client_name'], $arr['password'], $arr['email'], $arr['phone'], $arr['url']),
					$this->option_arr['o_email_new_registration']
				);
				
				$result = $pjEmail
					->setTo($arr['email'])
					->setFrom($arr['email'])
					->setSubject($this->option_arr['o_email_new_registration_subject'])
					->send($body);

				if (!$result)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 215, 'text' => __('system_215', true)));
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 216, 'text' => __('system_216', true)));
			}
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		}
	}
	
	public function pjActionProduct()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$id = (int) $_GET['id'];
			} elseif (isset($_GET['_escaped_fragment_'])) {
				preg_match('/\/Product\/(\d+)/', $_GET['_escaped_fragment_'], $matches);
				if (isset($matches[1]))
				{
					$id = $matches[1];
				}
			}
			
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
			pjObject::import('Model', 'pjGallery:pjGallery');

			$pjStockModel = pjStockModel::factory();
			
			$arr = pjProductModel::factory()
				->select(sprintf("t1.*, t2.content AS name, t3.content AS full_desc,
					(SELECT MIN(`price`) FROM `%2\$s`
						WHERE `product_id` = `t1`.`id`
						LIMIT 1) AS `price`,
					(SELECT `id` FROM `%2\$s`
						WHERE `product_id` = `t1`.`id`
						ORDER BY `price` ASC
						LIMIT 1) AS `stockId`,
					(SELECT CONCAT_WS('~:~', `medium_path`, `large_path`) FROM `%1\$s`
						WHERE `foreign_id` = `t1`.`id`
						AND `id` = (SELECT `image_id` FROM `%2\$s` WHERE `id` = `stockId` LIMIT 1)
						LIMIT 1) AS `pic`,
					(SELECT GROUP_CONCAT(COALESCE(`small_path`, '') ORDER BY `sort` ASC SEPARATOR '~:~') FROM `%1\$s` WHERE `foreign_id` = `t1`.`id` LIMIT 1) AS `small_path`,
					(SELECT GROUP_CONCAT(COALESCE(`medium_path`, '') ORDER BY `sort` ASC SEPARATOR '~:~') FROM `%1\$s` WHERE `foreign_id` = `t1`.`id` LIMIT 1) AS `medium_path`,
					(SELECT GROUP_CONCAT(COALESCE(`large_path`, '') ORDER BY `sort` ASC SEPARATOR '~:~') FROM `%1\$s` WHERE `foreign_id` = `t1`.`id` LIMIT 1) AS `large_path`,
					(SELECT GROUP_CONCAT(COALESCE(`alt`, '') ORDER BY `sort` ASC SEPARATOR '~:~') FROM `%1\$s` WHERE `foreign_id` = `t1`.`id` LIMIT 1) AS `alt`,
					(SELECT GROUP_CONCAT(`category_id`) FROM `%3\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`
					", pjGalleryModel::factory()->getTable(), $pjStockModel->getTable(), pjProductCategoryModel::factory()->getTable()))
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='full_desc'", 'left outer')
				->find($id)
				->toArray('small_path', '~:~')
				->toArray('medium_path', '~:~')
				->toArray('large_path', '~:~')
				->toArray('alt', '~:~')
				->toArray('category_ids', ',')
				->getData();
			if (!empty($arr))
			{
				if ($arr['status'] != 2)
				{
					$pjStockAttributeModel = pjStockAttributeModel::factory();
					$pjExtraItemModel = pjExtraItemModel::factory();
					
					// Stock images
					$arr['image_arr'] = $pjStockModel
						->select('t1.id AS stock_id, t2.medium_path, t2.large_path, t2.alt AS title')
						->join('pjGallery', 't2.id=t1.image_id', 'inner')
						->where('t1.product_id', $arr['id'])
						->orderBy('ISNULL(t2.sort), t2.sort ASC, t2.id ASC')
						->findAll()
						->getData();
					
					$extra_arr = pjExtraModel::factory()
						->select('t1.*, t2.content AS name, t3.content AS title')
						->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
						->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='extra_title'", 'left outer')
						->where('t1.product_id', $arr['id'])
						->orderBy('`title` ASC, `name` ASC')
						->findAll()
						->getData();
					
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
					$this->set('extra_arr', $extra_arr);
					
					$attr_arr = array();
					// Do not change col_name, direction
					$a_arr = pjAttributeModel::factory()
						->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
						->where('t1.product_id', $arr['id'])
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
					$this->set('attr_arr', array_values($attr_arr));
					
					$stock_arr = $pjStockModel
						->reset()
						->select('t1.*, t2.small_path')
						->join('pjGallery', 't2.id=t1.image_id', 'left outer')
						->where('t1.product_id', $arr['id'])
						->where('t1.qty > 0')
						->findAll()
						->getData();

					$_arr = array();
					foreach ($stock_arr as $k => $stock)
					{
						$_qty = $stock['qty'];
						if (isset($order_arr[$stock['id']]))
						{
							$_qty -= $order_arr[$stock['id']];
							if ($_qty < 1)
							{
								unset($stock_arr[$k]);
								continue;
							}
						}
						$stock_arr[$k]['qty'] = $_qty;
						
						$_arr[$stock['id']] = $pjStockAttributeModel
							->reset()
							->where('t1.stock_id', $stock['id'])
							->orderBy('t1.attribute_id ASC')
							->findAll()
							->getDataPair('attribute_parent_id', 'attribute_id');
					}
					$this->set('stock_attr_arr', $_arr);
					$this->set('stock_arr', array_values($stock_arr));
					
				} else {
					$arr = array();
				}
			}
			$this
				->set('product_arr', $arr)
				->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1))
				->set('similar_arr', pjProductSimilarModel::factory()
					->select(sprintf("t2.*, t3.content AS name,
						(SELECT `medium_path` FROM `%1\$s` WHERE `foreign_id` = `t1`.`similar_id` ORDER BY ISNULL(`sort`), `sort` ASC, `id` ASC LIMIT 1) AS `pic`,
						(SELECT MIN(`price`) FROM `%2\$s` WHERE `product_id` = `t1`.`similar_id` LIMIT 1) AS `price`,
						(SELECT GROUP_CONCAT(`category_id`) FROM `%3\$s` WHERE `product_id` = `t1`.`similar_id` LIMIT 1) AS `category_ids`
					", pjGalleryModel::factory()->getTable(), pjStockModel::factory()->getTable(), pjProductCategoryModel::factory()->getTable()))
					->join('pjProduct', 't2.id=t1.similar_id AND t2.status!=2', 'inner')
					->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.product_id', $id)
					->orderBy('`name` ASC')
					->limit(5)
					->findAll()
					->toArray('category_ids', ',')
					->getData()
				)
			;
		}
	}
	
	public function pjActionProducts()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_GET['_escaped_fragment_']))
			{
				preg_match('/\/Products\/q:(.*)?\/category:(\d+)?\/page:(\d+)?/', $_GET['_escaped_fragment_'], $matches);
				if (!empty($matches))
				{
					$q = $matches[1];
					$category_id = $matches[2];
					$page = $matches[3];
				}
			} else {
				$q = @$_GET['q'];
				$category_id = @$_GET['category_id'];
				$page = @$_GET['page'];
			}
			
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
			
			$pjProductModel = pjProductModel::factory()
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='short_desc'", 'left outer')
				->where('t1.status !=', 2);
				// Uncomment below to hide out of stock products.
				//->where(sprintf("t1.id IN (SELECT `product_id` FROM `%s`)", pjStockModel::factory()->getTable()))
			
			if (isset($category_id) && (int) $category_id > 0)
			{
				$pjProductModel->where(sprintf("t1.id IN (SELECT `product_id` FROM `%s` WHERE `category_id` = '%u')",
					pjProductCategoryModel::factory()->getTable(), (int) $category_id));
			}
			
			if (isset($q) && !empty($q))
			{
				$q = str_replace(array('_', '%'), array('\_', '\%'), $pjProductModel->escapeStr(trim(urldecode($q))));
				$pjProductModel->where("(t2.content LIKE '%$q%' OR t3.content LIKE '%$q%')");
			}
			
			$page = isset($page) && (int) $page > 0 ? intval($page) : 1;
			$row_count = 16;
			$offset = ((int) $page - 1) * $row_count;
			$count = $pjProductModel->findCount()->getData();
			$pages = ceil($count / $row_count);
			
			pjObject::import('Model', 'pjGallery:pjGallery');
			
			$this->set('product_arr', $pjProductModel
				->select(sprintf("t1.*, t2.content AS `name`,
					(SELECT `medium_path` FROM `%1\$s`
						WHERE `foreign_id` = `t1`.`id`
						ORDER BY ISNULL(`sort`), `sort` ASC, `id` ASC
						LIMIT 1) AS `pic`,
					(SELECT MIN(`price`) FROM `%2\$s`
						WHERE `product_id` = `t1`.`id` AND `qty` > 0
						LIMIT 1) AS `min_price`,
					(SELECT MAX(`price`) FROM `%2\$s`
						WHERE `product_id` = `t1`.`id` AND `qty` > 0
						LIMIT 1) AS `max_price`,
					(SELECT `id` FROM `%2\$s`
						WHERE `product_id` = `t1`.`id` AND `qty` > 0
						ORDER BY `price` ASC
						LIMIT 1) AS `stockId`,
					(SELECT `qty` FROM `%2\$s`
						WHERE `id` = `stockId`
						LIMIT 1) AS `stockQty`,
					(SELECT GROUP_CONCAT(CONCAT_WS('_', attribute_id, attribute_parent_id))
						FROM `%3\$s`
						WHERE `product_id` = `t1`.`id` AND `stock_id` = `stockId`
						LIMIT 1) AS `stockId_attr`,
					(SELECT GROUP_CONCAT(`category_id`) FROM `%4\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`,
					(SELECT GROUP_CONCAT(CONCAT_WS('.', `id`, IF(`type`='single',NULL,(SELECT `id` FROM `%6\$s` WHERE `extra_id` = te.id ORDER BY `price` ASC LIMIT 1)))) FROM `%5\$s` AS `te` WHERE `product_id` = t1.id AND `is_mandatory` = '1' LIMIT 1) AS `m_extras`",
					pjGalleryModel::factory()->getTable(), pjStockModel::factory()->getTable(),
					pjStockAttributeModel::factory()->getTable(), pjProductCategoryModel::factory()->getTable(),
					pjExtraModel::factory()->getTable(), pjExtraItemModel::factory()->getTable()
				))
				->orderBy('`is_featured` DESC, `name` ASC')
				->limit($row_count, $offset)
				->findAll()
				->toArray('category_ids', ',')
				->toArray('m_extras', ',')
				->getData()
			);
			
			$this
				->set('order_arr', $order_arr)
				->set('paginator', compact('pages', 'page'))
				->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		}
	}
	
	public function pjActionFavs()
	{
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_COOKIE[$this->defaultCookie]) && !empty($_COOKIE[$this->defaultCookie]))
			{
				$favs = unserialize(stripslashes($_COOKIE[$this->defaultCookie]));
				$arr = $extra_arr = $attr_arr = $stock_arr = $image_arr = $product_id = $stock_id = array();
				foreach ($favs as $fav => $whatever)
				{
					$item = unserialize($fav);
					$product_id[] = $item['product_id'];
					$stock_id[] = $item['stock_id'];
				}
				
				if (!empty($product_id))
				{
					$arr = pjProductModel::factory()
						->select(sprintf("t1.*, t2.content AS name,
							(SELECT GROUP_CONCAT(`category_id`) FROM `%1\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`
							", pjProductCategoryModel::factory()->getTable()
						))
						->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
						->whereIn('t1.id', $product_id)
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
					
					if (!empty($extra_arr))
					{
						$locale_id = $this->getLocaleId();
						$pjExtraItemModel = pjExtraItemModel::factory();
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
					}
					
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
				}
				if (!empty($stock_id))
				{
					pjObject::import('Model', 'pjGallery:pjGallery');
					$stock_arr = pjStockModel::factory()
						->whereIn('t1.id', $stock_id)
						->findAll()
						->getDataPair('id', 'price');
						
					$image_arr = pjStockModel::factory()
						->select('t1.id, t2.small_path')
						->join('pjGallery', 't2.id=t1.image_id', 'left outer')
						->whereIn('t1.id', $stock_id)
						->findAll()
						->getDataPair('id', 'small_path');
				}
				$this->set('arr', $arr);
				$this->set('extra_arr', $extra_arr);
				$this->set('attr_arr', array_values($attr_arr));
				$this->set('stock_arr', $stock_arr);
				$this->set('image_arr', $image_arr);
			}
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
		}
	}
}
?>