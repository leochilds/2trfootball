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
		$this->setLayout('pjActionFront');
		ob_start();
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
	
	public function pjActionSetLocale()
	{
		$this->setLocaleId(@$_GET['locale']);
		pjUtil::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function pjActionLoadCss()
	{
		header("Content-type: text/css");
		$arr = array(
			array('file' => 'front.css', 'path' => PJ_CSS_PATH),
			array('file' => 'front_lib.css', 'path' => PJ_CSS_PATH)
		);
		$pjHttp = new pjHttp();
		foreach ($arr as $item)
		{
			$pjHttp->fileRequest($item['path'] . $item['file']);
			$css_content = $pjHttp->getResponse();
			echo str_replace(array('../img/'), array(PJ_IMG_PATH), $css_content) . "\n";			
		}
		exit;
	}
	
	public function pjActionLoadJs()
	{
		header("Content-type: text/javascript");
		$arr = array(
			array('file' => '', 'path' => 'http://json-sans-eval.googlecode.com/svn/trunk/src/json_sans_eval.js'),
			array('file' => '', 'path' => 'http://maps.google.com/maps/api/js?sensor=false&language=' . $this->option_arr['o_map_language']),
			array('file' => 'jabb-0.4.3.js', 'path' => PJ_LIBS_PATH . 'jabb/'),
			array('file' => 'pjLoad.js', 'path' => PJ_JS_PATH)
		);
		$pjHttp = new pjHttp();
		foreach ($arr as $item)
		{
			$pjHttp->fileRequest($item['path'] . $item['file']);
			$js_content = $pjHttp->getResponse();
			echo $js_content . "\n";
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		$category_arr = pjCategoryModel::factory()->where('status', 'T')->orderBy('category_title ASC')->findAll()->getData();
		$this->set('category_arr', $category_arr);
	}
	
	public function pjActionGenerateXml()
	{
		$pjStoreModel = pjStoreModel::factory();
		
		$center_lat = $_GET["lat"];
		$center_lng = $_GET["lng"];
		$radius = $_GET["radius"];
		$distance = $_GET['distance'];
		
		switch ($distance)
		{
			case 'km':
				$mean_radius = 6371;
				break;
			case 'miles':
			default:
				$mean_radius = 3959;
				break;
		}
		$pjStoreModel->where('t1.status', 'T');
		if(isset($_GET['category_id']) && $_GET['category_id'] != '')
		{
			$pjStoreModel->where("t1.id IN(SELECT TSC.store_id FROM `".pjStoreCategoryModel::factory()->getTable()."` AS TSC WHERE TSC.category_id = ".$_GET['category_id'].")");
		}else{
			$pjStoreModel->where("t1.id NOT IN(SELECT TSC.store_id FROM `".pjStoreCategoryModel::factory()->getTable()."` AS TSC WHERE TSC.category_id IN(SELECT TC1.id FROM `".pjCategoryModel::factory()->getTable()."` AS TC1 WHERE TC1.status = 'F'))");
		}
		
		$sub_query = "($mean_radius * acos(cos(radians('$center_lat')) * cos(radians(lat)) * cos(radians(lng) - radians('$center_lng')) + sin(radians('$center_lat')) * sin(radians(lat)))) AS distance ";
		
		$category_query = "(SELECT TC.marker FROM `".pjCategoryModel::factory()->getTable()."` AS TC LEFT JOIN `".pjStoreCategoryModel::factory()->getTable()."` AS TSC1 ON TC.id = TSC1.category_id WHERE TSC1.store_id = t1.id ORDER BY marker LIMIT 1) as marker";
		
		$arr = $pjStoreModel->select('t1.*, t2.content AS address_country, ' . $sub_query . ', ' . $category_query)
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->having("distance < $radius")
					->orderBy("distance ASC")
					->findAll()
					->getData();
		$this->set('arr', $arr);
	}
	
	function pjActionGetLatLng()
	{
		$_address = $_GET['address'];
		$_address = preg_replace('/\s+/', '+', $_address);
		
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
		
		if (isset($data['lat']) && !is_array($data['lat']))
		{
			$data['code'] = 200;
		}else{
			$data['code'] = 100;
		}
		pjAppController::jsonResponse($data);
	}
	
	function pjActionSendEmail()
	{
		$data = array();
		$pjEmail = new pjEmail();
		
		if(empty($_POST['stl_email_text']))
		{
			$data['code'] = 100;
		}else{
			if (filter_var($_POST['stl_email_text'], FILTER_VALIDATE_EMAIL)) {
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
				
				$pjEmail->setContentType('text/html')
						->setFrom($this->getFromEmail())
						->setTo($_POST['stl_email_text'])
						->setSubject('Map Directions')
						->send($_POST['stl_directions_html']);
						
				$data['code'] = 200;
			}else{
				$data['code'] = 300;
			}
		}
		
		pjAppController::jsonResponse($data);
		exit;
	}
}
?>