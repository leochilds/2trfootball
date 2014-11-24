<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminOptions extends pjAdmin
{
	public function pjActionDeleteShipping()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (!isset($_POST['id']) || empty($_POST['id']))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing or empty parameters.'));
			}
			
			if (1 == pjTaxModel::factory()->set('id', $_POST['id'])->erase()->getAffectedRows())
			{
				pjMultiLangModel::factory()->where('model', 'pjTax')->where('foreign_id', $_POST['id'])->eraseAll();
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Shipping location has been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Shipping location has not been deleted.'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$tab_id = isset($_GET['tab']) && (int) $_GET['tab'] > 0 ? (int) $_GET['tab'] : 1;
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->where('tab_id', $tab_id)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			if (isset($_GET['tab']) && in_array((int) $_GET['tab'], array(5,6)))
			{
				pjObject::import('Model', array('pjLocale:pjLocale', 'pjLocale:pjLocaleLanguage'));
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $v)
				{
					$lp_arr[$v['id']."_"] = $v['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
				}
				$this->set('lp_arr', $locale_arr);
				
				$arr = array();
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
				$this->set('arr', $arr);
				
				if ((int) $this->option_arr['o_multi_lang'] === 1)
				{
					$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
					$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				}
			} elseif (isset($_GET['tab']) && in_array((int) $_GET['tab'], array(4))) {
				$tax_arr = pjTaxModel::factory()->findAll()->getData();
				foreach ($tax_arr as $k => $v)
				{
					$tax_arr[$k]['i18n'] = pjMultiLangModel::factory()->getMultiLang($v['id'], 'pjTax');
				}
				
				pjObject::import('Model', array('pjLocale:pjLocale', 'pjLocale:pjLocaleLanguage'));
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $v)
				{
					$lp_arr[$v['id']."_"] = $v['file'];
				}
				$this
					->set('tax_arr', $tax_arr)
					->set('lp_arr', $locale_arr)
					->set('locale_str', pjAppController::jsonEncode($lp_arr))
					->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
					->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/')
					->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/')
				;
			}
				
			$tmp = $this->getModel('Option')->reset()->where('foreign_id', $this->getForeignId())->findAll()->getData();
			$o_arr = array();
			foreach ($tmp as $item)
			{
				$o_arr[$item['key']] = $item;
			}
			
			$this
				->set('arr', $arr)
				->set('o_arr', $o_arr)
				->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['options_update']))
			{
				if (isset($_POST['tab']) && in_array($_POST['tab'], array(4)))
				{
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjTaxModel = pjTaxModel::factory();
						
					foreach ($_POST['shipping'] as $k => $v)
					{
						if (strpos($k, "new_") === 0)
						{
							# Insert
							$insert_id = $pjTaxModel->reset()->setAttributes(array(
								'shipping' => $_POST['shipping'][$k],
								'free' => $_POST['free'][$k],
								'tax' => $_POST['tax'][$k]
							))->insert()->getInsertId();
							
							if ($insert_id !== false && (int) $insert_id > 0)
							{
								if (isset($_POST['i18n']))
								{
									$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'location', $k);
									$pjMultiLangModel->reset()->saveMultiLang($tmp, $insert_id, 'pjTax');
								}
							}
						} else {
							# Update
							$pjTaxModel->reset()->set('id', $k)->modify(array(
								'shipping' => $_POST['shipping'][$k],
								'free' => $_POST['free'][$k],
								'tax' => $_POST['tax'][$k]
							));
							
							if (isset($_POST['i18n']))
							{
								$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'location', $k);
								$pjMultiLangModel->reset()->updateMultiLang($tmp, $k, 'pjTax');
							}
						}
					}
				} elseif (isset($_POST['tab']) && in_array($_POST['tab'], array(5,6))) {
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $this->getForeignId(), 'pjOption', 'data');
					}
				} else {
					$OptionModel = pjOptionModel::factory();
					$OptionModel
						->where('foreign_id', $this->getForeignId())
						->where('type', 'bool')
						->where('tab_id', $_POST['tab'])
						->modifyAll(array('value' => '1|0::0'));
				
					foreach ($_POST as $key => $value)
					{
						if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
						{
							list(, $type, $k) = explode("-", $key);
							if (!empty($k))
							{
								$OptionModel
									->reset()
									->where('foreign_id', $this->getForeignId())
									->where('`key`', $k)
									->limit(1)
									->modifyAll(array('value' => $value));
							}
						}
					}
				}
				
				if (isset($_POST['next_action']))
				{
					switch ($_POST['next_action'])
					{
						case 'pjActionIndex':
						default:
							$err = 'AO01';
							break;
					}
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . @$_POST['next_action'] ."&tab=" . @$_POST['tab']. "&err=$err");
			}
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionInstall()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			pjObject::import('Model', array('pjLocale:pjLocale', 'pjLocale:pjLocaleLanguage'));
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
				->orderBy('t1.sort ASC')->findAll()->getData();
			$this->set('locale_arr', $locale_arr);
					
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionPreview()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
	}

	private function pjActionTurnI18n($data, $key, $id, $index=NULL)
	{
		$arr = array();
		foreach ($data as $locale => $locale_arr)
		{
			$arr[$locale] = array(
				$key => is_null($index) ?
					(isset($locale_arr[$key]) && isset($locale_arr[$key][$id]) ? $locale_arr[$key][$id] : NULL) :
					(isset($locale_arr[$key]) && isset($locale_arr[$key][$id]) && isset($locale_arr[$key][$id][$index]) ? $locale_arr[$key][$id][$index] : NULL)
			);
		}
		
		return $arr;
	}
}
?>