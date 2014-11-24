<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminCategories extends pjAdmin
{
	public function pjActionCheckCategoryName()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && isset($_GET['category']))
		{
			$pjCategoryModel = pjCategoryModel::factory();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjCategoryModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjCategoryModel->where('t1.category', $_GET['category'])->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['category_create']))
			{
				$id = pjCategoryModel::factory(array_merge($_POST))->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AC03';
				} else {
					$err = 'AC04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCategories&action=pjActionIndex&err=$err");
			} else {
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminCategories.js');
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
			if (pjCategoryModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
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
				pjCategoryModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportCategory()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjCategoryModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Categories-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCategoryModel = pjCategoryModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjCategoryModel->where('t1.category LIKE', "%$q%");
			}
			
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjCategoryModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'category';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			
			$total = $pjCategoryModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			$data = $pjCategoryModel->select('t1.*')
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
			$this->appendJs('pjAdminCategories.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['column']) && count($_POST['column']) > 0)
			{
				$pjCategoryModel = pjCategoryModel::factory();
				
				if (isset($_GET['id']) && (int) $_GET['id'] > 0)
				{
					$pjCategoryModel->where('t1.id !=', $_GET['id']);
				}
				$check = $pjCategoryModel->where('t1.category', $_POST['value'])->findCount()->getData() == 0 ? true : false;
				if($check == true)
				{
					$pjCategoryModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				}
			}
		}
		exit;
	}
	
	public function pjActionStatusCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjCategoryModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
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
				
			if (isset($_POST['category_update']))
			{
				pjCategoryModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($_POST);
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=AC01");
				
			} else {
				$arr = pjCategoryModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCategories&action=pjActionIndex&err=AC08");
				}
			
				$this->set('arr', $arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminCategories.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>