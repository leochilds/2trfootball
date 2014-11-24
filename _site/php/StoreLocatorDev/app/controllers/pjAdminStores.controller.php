<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminStores extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['store_create']))
			{
				$pjStoreModel = pjStoreModel::factory();
				
				$data = array();
				if(!empty($_POST['lat']) && !empty($_POST['lng']))
				{
					$data['latlng'] = 1;
				}else{
					$data['latlng'] = 0;
				}
				
				$id = $pjStoreModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					if(isset($_POST['category_id']))
					{
						$pjStoreCategoryModel = pjStoreCategoryModel::factory();
						$pjStoreCategoryModel->begin();
						foreach ($_POST['category_id'] as $category_id){
							$data = array();
							$data['store_id'] = $id;
							$data['category_id'] = $category_id;
							$pjStoreCategoryModel->reset()->setAttributes($data)->insert();
						}
						$pjStoreCategoryModel->commit();
					}
					
					if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name']))
					{
						$Image = new pjImage();
						if ($Image->getErrorCode() !== 200)
						{
							$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
							if ($Image->load($_FILES['image']))
							{
								$resp = $Image->isConvertPossible();
								if ($resp['status'] === true)
								{
									$hash = md5(uniqid(rand(), true));
									$image_path = PJ_UPLOAD_PATH . 'stores/' . $id . '_' . $hash . '.' . $Image->getExtension();
									
									$Image->loadImage();
									if($Image->getWidth() > 88)
									{
										$Image->resizeToWidth(88);
									}
									$Image->saveImage($image_path);
									$data['image_path'] = $image_path;
									$data['image_name'] = $_FILES['image']['name'];
																		
									$pjStoreModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
								}
							}
						}
					}
					$err = 'AS03';
				} else {
					$err = 'AS04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminStores&action=pjActionIndex&err=$err");
			} else {
				pjObject::import('Model', array('pjCountry:pjCountry'));
				
				$category_arr = pjCategoryModel::factory()->where('status', 'T')->orderBy('category_title ASC')->findAll()->getData();
				$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')->findAll()->getData();
				
				$this->set('category_arr', $category_arr);
				$this->set('country_arr', $country_arr);
				
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('http://maps.google.com/maps/api/js?sensor=false', '', true);
				$this->appendJs('pjAdminStores.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionImport()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['store_import']))
			{
				if (isset($_FILES['csv']) && !empty($_FILES['csv']['tmp_name']))
				{
					if(pjUtil::getFileExtension($_FILES['csv']['name']) == 'csv')
					{
						
						$csv_data = $this->loadCSV($_FILES['csv']);
						
						$pjStoreModel = pjStoreModel::factory();
						$pjStoreCategoryModel = pjStoreCategoryModel::factory();
						
						pjObject::import('Model', array('pjCountry:pjCountry'));
				
						$country_data = array();
						$country_arr = pjCountryModel::factory()->select('t1.*')->findAll()->getData();
						
						foreach($country_arr as $country)
						{
							$country_data[$country['alpha_2']] = $country['id'];
						}
						
						foreach ($csv_data as $row)
						{
							$is_updated = false;
							if(isset($row['id']) && (int) $row['id'] > 0)
							{
								if($pjStoreModel->reset()->where('id', $row['id'])->findCount()->getData() > 0)
								{
									$is_updated = true;
								}
							}
							
							$alpha_2 = $row['country_id'];
							$row['country_id'] = ':NULL';
							if(isset($row['country_id']) && !empty($country_data))
							{
								$row['country_id'] = $country_data[$alpha_2];
							}
							
							$category_id_arr = explode("|", $row['categories']); 
							unset($row['categories']);
							if(isset($_POST['with_lat_lng']))
							{
								$coord = $this->pjActionGeocode($row);
								unset($row['lat']);
								unset($row['lng']);
								$row['lat'] = $coord['lat'];
								$row['lng'] = $coord['lng'];
							}
							$row['latlng'] = 0;
							if(!empty($row['lat']) && !empty($row['lng']))
							{
								$row['latlng'] = 1;
							}
							if($is_updated == false)
							{
								unset($row['id']);
								$id = $pjStoreModel->reset()->setAttributes($row)->insert()->getInsertId();
								if ($id !== false && (int) $id > 0)
								{
									$pjStoreCategoryModel->begin();
									foreach ($category_id_arr as $category_id){
										$data = array();
										$data['store_id'] = $id;
										$data['category_id'] = $category_id;
										$pjStoreCategoryModel->reset()->setAttributes($data)->insert();
									}
									$pjStoreCategoryModel->commit();
								}
							}else{
								$pjStoreModel->reset()->where('id', $row['id'])->limit(1)->modifyAll($row);
								$pjStoreCategoryModel->reset()->where('store_id', $row['id'])->eraseAll();
								$pjStoreCategoryModel->begin();
								foreach ($category_id_arr as $category_id){
									$data = array();
									$data['store_id'] = $row['id'];
									$data['category_id'] = $category_id;
									$pjStoreCategoryModel->reset()->setAttributes($data)->insert();
								}
								$pjStoreCategoryModel->commit();
							}
						}
						
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminStores&action=pjActionIndex&err=AS09");
					}else{
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminStores&action=pjActionImport&err=AS10");
					}
				}else{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminStores&action=pjActionImport&err=AS11");
				}
			}else{
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminStores.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteStore()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$response = array();
			if ($this->isAdmin() || $this->isEditor())
			{
				$pjStoreModel = pjStoreModel::factory();
				$arr = $pjStoreModel->find($_GET['id'])->getData();
				if(!empty($arr['image_path']))
				{
					$image_path = $arr['image_path'];
					if (file_exists(PJ_INSTALL_PATH . $image_path)) {
						if(unlink(PJ_INSTALL_PATH . $image_path)){
						}
					}
				}
				if ($pjStoreModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
				{
					pjStoreCategoryModel::factory()->where('store_id', $_GET['id'])->eraseAll();
					
					$response['code'] = 200;
				} else {
					$response['code'] = 100;
				}
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteStoreBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if ($this->isAdmin() || $this->isEditor())
			{
				if (isset($_POST['record']) && count($_POST['record']) > 0)
				{
					$pjStoreModel = pjStoreModel::factory();
					$arr = $pjStoreModel->whereIn('id', $_POST['record'])->findAll()->getData();
					foreach($arr as $v)
					{
						if(!empty($v['image_path']))
						{
							$image_path = $v['image_path'];
							if (file_exists(PJ_INSTALL_PATH . $image_path)) {
								if(unlink(PJ_INSTALL_PATH . $image_path)){
								}
							}
						}
					}
					$pjStoreModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
					pjStoreCategoryModel::factory()->whereIn('store_id', $_POST['record'])->eraseAll();
				}
			}
		}
		exit;
	}
	
	public function pjActionExportStore()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$sub_query = "(	SELECT GROUP_CONCAT(TC.id SEPARATOR '|') FROM " . pjStoreCategoryModel::factory()->getTable() . " AS TSC
							LEFT OUTER JOIN " . pjCategoryModel::factory()->getTable() . " AS TC ON TSC.category_id=TC.id AND TC.status='T'
							WHERE TSC.store_id=t1.`id`) as categories";
			
			pjObject::import('Model', array('pjCountry:pjCountry'));
			$arr = pjStoreModel::factory()
						->join('pjCountry', 't1.country_id=t2.`id`', 'left outer')
						->select("t1.`id`, t1.`name`, t1.`email`, t1.`website`, t2.`alpha_2` as country_id, 
									t1.`address_state`, t1.`address_city`, t1.`address_content`, t1.`address_zip`, 
									t1.`phone`, t1.`opening_times`, t1.`image_path`, t1.`image_name`, 
									t1.`lat`, t1.`lng`, t1.`status`, $sub_query")
						->whereIn('t1.`id`', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Stores-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetCoord()
	{
		$this->checkLogin();
		
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$number_of_stores = 0;
			
			if (isset($_POST['record']) && is_array($_POST['record']))
			{
				$pjStoreModel = pjStoreModel::factory();
				pjObject::import('Model', array('pjCountry:pjCountry'));
				$arr = $pjStoreModel
							->join('pjCountry', 't1.country_id=t2.`id`', 'left outer')
							->select("t1.*")
							->whereIn('t1.`id`', $_POST['record'])
							->findAll()
							->getData();
				foreach($arr as $k => $v)
				{
					if($v['latlng'] != 1)
					{
						$coord = $this->pjActionGeocode($v);
						if($coord['lat'] != null && $coord['lng'] != null)
						{
							$coord['latlng'] = 1;
							$pjStoreModel->reset()->where('id', $v['id'])->limit(1)->modifyAll($coord);
							$number_of_stores++;
						}
					}
				}
			}
			$this->set('number_of_stores', $number_of_stores);
		}
	}
	
	public function pjActionGetStore()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjStoreModel = pjStoreModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjStoreModel->where('t1.name LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjStoreModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['category_id']) && !empty($_GET['category_id']))
			{
				$pjStoreModel->where("(t1.id IN(SELECT TSC.store_id FROM `".pjStoreCategoryModel::factory()->getTable()."` AS TSC WHERE TSC.category_id = ".$_GET['category_id']."))");
			}
				
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjStoreModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$sub_query = "(	SELECT GROUP_CONCAT(TC.category_title SEPARATOR '<br/>') FROM " . pjStoreCategoryModel::factory()->getTable() . " AS TSC
							LEFT OUTER JOIN " . pjCategoryModel::factory()->getTable() . " AS TC ON TSC.category_id=TC.id AND TC.status='T'
							WHERE TSC.store_id=t1.id) as categories";
			
			$data = $pjStoreModel->select('t1.*, , ' . $sub_query)
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

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
			$this->appendJs('pjAdminStores.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveStore()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if($_POST['column'] == 'name')
			{
				if($_POST['value'] != '')
				{
					pjStoreModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				}
			}else{
				pjStoreModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			}
		}
		exit;
	}
	
	public function pjActionStatusStore()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjStoreModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
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
			$pjStoreModel = pjStoreModel::factory();
			if (isset($_POST['store_update']))
			{
				$data = array();
				if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name']))
				{
					$arr = $pjStoreModel->find($_POST['id'])->getData();
					if(!empty($arr['image_path']))
					{
						$image_path = $arr['image_path'];
						if (file_exists(PJ_INSTALL_PATH . $image_path)) {
							if(unlink(PJ_INSTALL_PATH . $image_path)){
							}
						}
					}
					
					$Image = new pjImage();
					if ($Image->getErrorCode() !== 200)
					{
						$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
						if ($Image->load($_FILES['image']))
						{
							$resp = $Image->isConvertPossible();
							if ($resp['status'] === true)
							{
								$hash = md5(uniqid(rand(), true));
								$image_path = PJ_UPLOAD_PATH . 'stores/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
								
								$Image->loadImage();
								if($Image->getWidth() > 88)
								{
									$Image->resizeToWidth(88);
								}
								$Image->saveImage($image_path);
								$data['image_path'] = $image_path;
								$data['image_name'] = $_FILES['image']['name'];
							}
						}
					}
				}

				if(!empty($_POST['lat']) && !empty($_POST['lng']))
				{
					$data['latlng'] = 1;
				}else{
					$data['latlng'] = 0;
				}
				
				$pjStoreModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				$pjStoreCategoryModel = pjStoreCategoryModel::factory();
				$pjStoreCategoryModel->where('store_id', $_POST['id'])->eraseAll();
				if(isset($_POST['category_id']))
				{
					$pjStoreCategoryModel->reset()->begin();
					foreach ($_POST['category_id'] as $category_id){
						$data = array();
						$data['store_id'] = $_POST['id'];
						$data['category_id'] = $category_id;
						$pjStoreCategoryModel->reset()->setAttributes($data)->insert();
					}
					$pjStoreCategoryModel->commit();
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminStores&action=pjActionIndex&err=AS01");
				
			} else {
				$arr = $pjStoreModel->find($_GET['id'])->getData();
				
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStores&action=pjActionIndex&err=AS08");
				}
				$this->set('arr', $arr);
				
				$category_arr = pjCategoryModel::factory()->where('status', 'T')->orderBy('category_title ASC')->findAll()->getData();
				$store_category_arr = pjStoreCategoryModel::factory()->where('store_id', $_GET['id'])->findAll()->getData();
				
				pjObject::import('Model', array('pjCountry:pjCountry'));
				$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')->findAll()->getData();
				
				$category_id_arr = array();
				if(!empty($store_category_arr))
				{
					foreach($store_category_arr as $v)
					{
						$category_id_arr[] = $v['category_id'];
					}
				}
				
				$this->set('category_arr', $category_arr);
				$this->set('country_arr', $country_arr);
				$this->set('category_id_arr', $category_id_arr);
				
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('http://maps.google.com/maps/api/js?sensor=false', '', true);
				$this->appendJs('pjAdminStores.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetGeocode()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$geo = pjAdminStores::pjActionGeocode($_POST);
			$response = array('code' => 100);
			if (isset($geo['lat']) && !is_array($geo['lat']))
			{
				$response = $geo;
				$response['code'] = 200;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	private static function pjActionGeocode($post)
	{
		$address = array();
		$address[] = $post['address_zip'];
		$address[] = $post['address_content'];
		$address[] = $post['address_city'];
		$address[] = $post['address_state'];

		foreach ($address as $key => $value)
		{
			$tmp = preg_replace('/\s+/', '+', $value);
			$address[$key] = $tmp;
		}
		$_address = join(",+", $address);
						
		$gfile = "http://maps.googleapis.com/maps/api/geocode/json?address=$_address&sensor=false";
		$Http = new pjHttp();
		$response = $Http->request($gfile)->getResponse();

		$geoObj = pjAppController::jsonDecode($response);
		
		$data = array();
		$geoArr = (array) $geoObj;
		if ($geoArr['status'] == 'OK')
		{
			$geoArr['results'][0] = (array) $geoArr['results'][0];
			$geoArr['results'][0]['geometry'] = (array) $geoArr['results'][0]['geometry'];
			$geoArr['results'][0]['geometry']['location'] = (array) $geoArr['results'][0]['geometry']['location'];
			
			$data['lat'] = $geoArr['results'][0]['geometry']['location']['lat'];
			$data['lng'] = $geoArr['results'][0]['geometry']['location']['lng'];
		} else {
			$data['lat'] = NULL;
			$data['lng'] = NULL;
		}
		return $data;
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			$pjStoreModel = pjStoreModel::factory();
			$arr = $pjStoreModel->find($_GET['id'])->getData(); 
			
			if(!empty($arr))
			{
				$image_path = $arr['image_path'];
				if (file_exists(PJ_INSTALL_PATH . $image_path)) {
					if(unlink(PJ_INSTALL_PATH . $image_path)){
					}
				}
				$data = array();
				$data['image_path'] = ':NULL';
				$data['image_name'] = ':NULL';
				$pjStoreModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
				
				$response['code'] = 200;
				
			}else{
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
}
?>