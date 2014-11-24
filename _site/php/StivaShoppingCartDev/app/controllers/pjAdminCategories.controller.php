<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCategories extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['category_create']))
			{
				$id = pjCategoryModel::factory()->saveNode($_POST, $_POST['parent_id']);
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AG01';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjCategory');
					}
				} else {
					$err = 'AG02';
				}
				pjUtil::redirect(sprintf("%s?controller=pjAdminCategories&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
			} else {
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
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->set('node_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCategories.js');
				$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjCategoryModel = pjCategoryModel::factory();
			
			$pjCategoryModel->deleteNode($_GET['id']);
			$pjCategoryModel->rebuildTree(1, 1);
			pjProductCategoryModel::factory()->where('category_id', $_GET['id'])->eraseAll();
			$response['code'] = 200;
			//$response['code'] = 100;
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteCategoryBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjCategoryModel = pjCategoryModel::factory();
				
				$pjCategoryModel->whereIn('id', $_POST['record'])->eraseAll();
				foreach ($_POST['record'] as $id)
				{
					$pjCategoryModel->deleteNode($id);
					$pjCategoryModel->rebuildTree(1, 1);
				}
				pjProductCategoryModel::factory()->whereIn('category_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCategoryModel = pjCategoryModel::factory();
			
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$data = $pjCategoryModel->getNode($this->getLocaleId(), 1);
			
			//$total = $pjCategoryModel->findCount()->getData();
			$total = count($data);
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$c_arr = $pjCategoryModel
				->reset()
				->select(sprintf("t1.id, (SELECT COUNT(*) FROM `%s` WHERE `category_id` = `t1`.`id` LIMIT 1) AS `products`", pjProductCategoryModel::factory()->getTable()))
				->findAll()
				->getDataPair('id', 'products');
			
			//$data = $pjCategoryModel->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			$data = array_slice($data, $offset, $rowCount);
				
			$stack = array();
			foreach ($data as $k => $category)
			{
				$data[$k]['products'] = (int) @$c_arr[$category['data']['id']];
				$data[$k]['up'] = 0;
				$data[$k]['down'] = 0;
				$data[$k]['id'] = (int) $category['data']['id'];
				if (!isset($stack[$category['deep']."|".$category['data']['parent_id']]))
				{
					$stack[$category['deep']."|".$category['data']['parent_id']] = 0;
				}
				$stack[$category['deep']."|".$category['data']['parent_id']] += 1;
				if ($stack[$category['deep']."|".$category['data']['parent_id']] > 1)
				{
					$data[$k]['up'] = 1;
				}
				//FIXME
				if (isset($data[$k + 1]) && $data[$k + 1]['deep'] == $category['deep'] || $stack[$category['deep']."|".$category['data']['parent_id']] < $category['siblings'])
				{
					$data[$k]['down'] = 1;
				}
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
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCategories.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSetOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCategoryModel = pjCategoryModel::factory();
			
			$node = $pjCategoryModel->find($_POST['id'])->getData();
			if (count($node) > 0)
			{
				$pjCategoryModel->reset();
				$opts = array();
				switch ($_POST['direction'])
				{
					case 'up':
						$pjCategoryModel->where('t1.lft <', $node['lft'])->orderBy('t1.lft DESC');
						break;
					case 'down':
						$pjCategoryModel->where('t1.lft >', $node['lft'])->orderBy('t1.lft ASC');
						break;
				}

				$neighbour = $pjCategoryModel
					->where('t1.id !=', $node['id'])
					->where('t1.parent_id', $node['parent_id'])
					->limit(1)->findAll()->getData();
				if (count($neighbour) === 1)
				{
					$neighbour = $neighbour[0];
					$pjCategoryModel->reset()->set('id', $neighbour['id'])->modify(array('lft' => $node['lft'], 'rgt' => $node['rgt']));
					$pjCategoryModel->reset()->set('id', $node['id'])->modify(array('lft' => $neighbour['lft'], 'rgt' => $neighbour['rgt']));
					$pjCategoryModel->reset()->rebuildTree(1, 1);
				} else {
					//last one
				}
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$pjCategoryModel = pjCategoryModel::factory();
			
			if (isset($_POST['category_update']))
			{
				$data = array();
				$pjCategoryModel->updateNode(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjCategory');
				}
				$err = 'AG05';
				
				pjUtil::redirect(sprintf("%s?controller=pjAdminCategories&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
			} else {
				$arr = $pjCategoryModel->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(sprintf("%s?controller=pjAdminCategories&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], 'AG08'));
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCategory');
				$this->set('arr', $arr);
				$this->set('node_arr', $pjCategoryModel->reset()->getNode($this->getLocaleId(), 1));
				$this->set('child_arr', $pjCategoryModel->reset()->getNode($this->getLocaleId(), $arr['id']));
				
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
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCategories.js');
				$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>