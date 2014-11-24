<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminProducts extends pjAdmin
{
	private function pjActionLoadLocales()
	{
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
			
		return $this;
	}
	
	public function pjActionOpenDigital()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$arr = pjProductModel::factory()->find($_GET['id'])->getData();
				if (empty($arr))
				{
					exit;
				}
				if ((int) $arr['is_digital'] !== 1)
				{
					exit;
				}
				if (empty($arr['digital_file']) || !is_file($arr['digital_file']))
				{
					exit;
				}
				
				$handle = @fopen($arr['digital_file'], "rb");
				if ($handle)
				{
					$buffer = "";
					while (!feof($handle))
					{
						$buffer .= fgets($handle, 4096);
					}
					fclose($handle);
				}
				
				$this->setAjax(true);
				$this->setLayout('pjActionEmpty');
				
				$ext = pjUtil::getFileExtension($arr['digital_file']);
				$mime_type = pjUtil::getMimeType($ext);
				$charset = pjMultibyte::detect_encoding($buffer);
				
				header('Pragma: public');
		        header('Expires: 0');
		        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		        header('Cache-Control: private', FALSE);
				header('Content-Type: '.$mime_type.'; charset='.$charset);
				header('Content-Disposition: inline; filename="' . basename($arr['digital_name']) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: ' . filesize($arr['digital_file']));
				echo $buffer;
				exit;
			} else {
				exit;
			}
		} else {
			$this->set('status', 2);
		}
		exit;
	}

	public function pjActionAttrGroupDelete()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$pjAttributeModel = pjAttributeModel::factory();
				$ids = $pjAttributeModel->where('t1.parent_id', $_POST['id'])->findAll()->getDataPair(null, 'id');
				if ($pjAttributeModel->reset()->set('id', $_POST['id'])->erase()->getAffectedRows() == 1)
				{
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjMultiLangModel->where('model', 'pjAttribute')->where('foreign_id', $_POST['id'])->eraseAll();
					
					if (!empty($ids))
					{
						$pjAttributeModel->reset()->whereIn('id', $ids)->eraseAll();
						$pjMultiLangModel->reset()->where('model', 'pjAttribute')->whereIn('foreign_id', $ids)->eraseAll();
					}
					
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				}
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionAttrDelete()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				if (pjAttributeModel::factory()->set('id', $_POST['id'])->erase()->getAffectedRows() == 1)
				{
					pjMultiLangModel::factory()->where('model', 'pjAttribute')->where('foreign_id', $_POST['id'])->eraseAll();
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				}
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionAttrCopy()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['from_product_id']) && (int) $_POST['from_product_id'] > 0)
			{
				if (isset($_POST['product_id']) && (int) $_POST['product_id'] > 0)
				{
					$ak = 'product_id';
					$av = $_POST['product_id'];
				} elseif (isset($_POST['hash']) && !empty($_POST['hash'])) {
					$ak = 'hash';
					$av = $_POST['hash'];
				}
			
				$pjAttributeModel = pjAttributeModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$attr = $pjAttributeModel->where('t1.product_id', $_POST['from_product_id'])->orderBy('t1.parent_id ASC')->findAll()->getDataPair('id', 'parent_id');
				$arr = array();
				foreach ($attr as $id => $parent_id)
				{
					if (empty($parent_id))
					{
						$arr[$id] = array();
					} else {
						$arr[$parent_id][] = $id;
					}
				}
				
				$multi = $pjMultiLangModel
					->where('t1.model', 'pjAttribute')
					->whereIn('t1.foreign_id', array_keys($attr))
					->where("t1.field='name'")
					->findAll()
					->getData();

				$stack = array();
				foreach ($multi as $item)
				{
					if (!isset($stack[$item['foreign_id']]))
					{
						$stack[$item['foreign_id']] = array();
					}
					$stack[$item['foreign_id']][] = $item;
				}
				
				foreach ($arr as $parent_id => $items)
				{
					$insert_id = $pjAttributeModel->reset()->setAttributes(array($ak => $av))->insert()->getInsertId();
					if ($insert_id !== false && (int) $insert_id > 0)
					{
						if (isset($stack[$parent_id]))
						{
							foreach ($stack[$parent_id] as $locale)
							{
								$pjMultiLangModel->reset()->setAttributes(array(
									'model' => $locale['model'],
									'foreign_id' => $insert_id,
									'field' => $locale['field'],
									'locale' => $locale['locale'],
									'content' => $locale['content'],
									'source' => 'data'
								))->insert();
							}
						}
					
						foreach ($items as $id)
						{
							$attr_id = $pjAttributeModel->reset()->setAttributes(array($ak => $av, 'parent_id' => $insert_id))->insert()->getInsertId();
							if ($attr_id !== false && (int) $attr_id > 0)
							{
								if (isset($stack[$id]))
								{
									foreach ($stack[$id] as $locale)
									{
										$pjMultiLangModel->reset()->setAttributes(array(
											'model' => $locale['model'],
											'foreign_id' => $attr_id,
											'field' => $locale['field'],
											'locale' => $locale['locale'],
											'content' => $locale['content'],
											'source' => 'data'
										))->insert();
									}
								}
							}
						}
					}
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
		}
		exit;
	}
	
	private function pjActionAttrHandle($product_id)
	{
		if (isset($_POST['attr']) && !empty($_POST['attr']))
		{
			$pjAttributeModel = pjAttributeModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			
			$keys = array_keys($_POST['i18n']);
			$fkey = $keys[0];
			
			foreach ($_POST['attr'] as $group_id => $whatever)
			{
				if (strpos($group_id, 'x_') === 0)
				{
					// insert new group attr
					$attr_group_id = $pjAttributeModel->reset()
						->set('product_id', $product_id)
						->insert()->getInsertId();
					if ($attr_group_id !== false && (int) $attr_group_id > 0)
					{
						$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'attr_group', $group_id, NULL, 'name');
						$pjMultiLangModel->saveMultiLang($tmp, $attr_group_id, 'pjAttribute');
						
						foreach ($_POST['i18n'][$fkey]['attr_item'][$group_id] as $index => $value)
						{
							$attr_item_id = $pjAttributeModel->reset()->setAttributes(array(
								'product_id' => $product_id,
								'parent_id' => $attr_group_id
							))->insert()->getInsertId();
							if ($attr_item_id !== false && (int) $attr_item_id > 0)
							{
								$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'attr_item', $group_id, $index, 'name');
								$pjMultiLangModel->saveMultiLang($tmp, $attr_item_id, 'pjAttribute');
							}
						}
					}
				} else {
					// update group attr
					$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'attr_group', $group_id, NULL, 'name');
					$pjMultiLangModel->updateMultiLang($tmp, $group_id, 'pjAttribute');
												
					foreach ($_POST['i18n'][$fkey]['attr_item'][$group_id] as $index => $value)
					{
						if (strpos($index, 'y_') === 0)
						{
							# Add items
							$attr_item_id = $pjAttributeModel->reset()->setAttributes(array(
								'product_id' => $product_id,
								'parent_id' => $group_id
							))->insert()->getInsertId();
							if ($attr_item_id !== false && (int) $attr_item_id > 0)
							{
								$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'attr_item', $group_id, $index, 'name');
								$pjMultiLangModel->saveMultiLang($tmp, $attr_item_id, 'pjAttribute');
							}
						} else {
							# Update items
							$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'attr_item', $group_id, $index, 'name');
							$pjMultiLangModel->updateMultiLang($tmp, $index, 'pjAttribute');
						}
					}
				}
			}
			foreach ($_POST['i18n'] as $locale_id => $whatever)
			{
				if (isset($_POST['i18n'][$locale_id]['attr_group']))
				{
					unset($_POST['i18n'][$locale_id]['attr_group']);
				}
				if (isset($_POST['i18n'][$locale_id]['attr_item']))
				{
					unset($_POST['i18n'][$locale_id]['attr_item']);
				}
			}
		}
	}
	
	public function pjActionCheckSku()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (!isset($_GET['sku']) || empty($_GET['sku']))
			{
				echo 'false';
				exit;
			}
			$pjProductModel = pjProductModel::factory()->where('t1.sku', $_GET['sku']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjProductModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjProductModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['product_create']))
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$data = array();
				$data['is_featured'] = isset($_POST['is_featured']) ? 1 : array(0);
				
				$product_id = pjProductModel::factory()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($product_id !== false && (int) $product_id > 0)
				{
					if (isset($_POST['i18n']))
					{
						$pjMultiLangModel->saveMultiLang($_POST['i18n'], $product_id, 'pjProduct');
					}
					
					# Categories start ---
					if (isset($_POST['category_id']) && count($_POST['category_id']) > 0)
					{
						$pjProductCategoryModel = pjProductCategoryModel::factory();
						$pjProductCategoryModel->begin();
						foreach ($_POST['category_id'] as $category_id)
						{
							$pjProductCategoryModel
								->reset()
								->set('product_id', $product_id)
								->set('category_id', $category_id)
								->insert();
						}
						$pjProductCategoryModel->commit();
					}
					# Categories end ---
					$err = 'AP01';
					pjUtil::redirect(sprintf("%s?controller=pjAdminProducts&action=pjActionUpdate&id=%u&tab=0&err=%s", $_SERVER['PHP_SELF'], $product_id, $err));
				} else {
					$err = 'AP02';
				}
				pjUtil::redirect(sprintf("%s?controller=pjAdminProducts&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
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
				
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
				
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminProducts.js');
			}
			
		} else {
			$this->set('status', 2);
		}
	}
	
	private function pjActionDeleteProductAttr($product_id)
	{
		if (empty($product_id))
		{
			return false;
		}
		
		$pjAttributeModel = pjAttributeModel::factory();
		if (is_array($product_id))
		{
			$pjAttributeModel->whereIn('product_id', $product_id);
		} else {
			$pjAttributeModel->where('product_id', $product_id);
		}
		
		$attr_ids = $pjAttributeModel->findAll()->getDataPair(null, 'id');
		if (!empty($attr_ids))
		{
			$pjAttributeModel->eraseAll();
			$pjMultiLangModel = pjMultiLangModel::factory();
			$pjMultiLangModel->reset()->where('model', 'pjAttribute')->whereIn('foreign_id', $attr_ids)->eraseAll();
		}
	}
	
	private function pjActionDeleteStockAttr($product_id)
	{
		if (empty($product_id))
		{
			return false;
		}
		
		$pjStockAttributeModel = pjStockAttributeModel::factory();
		if (is_array($product_id))
		{
			$pjStockAttributeModel->whereIn('product_id', $product_id);
		} else {
			$pjStockAttributeModel->where('product_id', $product_id);
		}
		$pjStockAttributeModel->eraseAll();
	}
	
	public function pjActionDeleteProduct()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && isset($_GET['id']) && (int) $_GET['id'] > 0)
		{
			if (pjProductModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				$pjExtraModel = pjExtraModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$pjMultiLangModel->where('model', 'pjProduct')->where('foreign_id', $_GET['id'])->eraseAll();
				
				pjStockModel::factory()->where('product_id', $_GET['id'])->eraseAll();
				$this->pjActionDeleteStockAttr($_GET['id']);
				pjProductSimilarModel::factory()->where('product_id', $_GET['id'])->orWhere('similar_id', $_GET['id'])->eraseAll();
				
				$extra_arr = $pjExtraModel->where('product_id', $_GET['id'])->findAll()->getDataPair(null, 'id');
				if (!empty($extra_arr))
				{
					$pjExtraItemModel = pjExtraItemModel::factory();
					$pjExtraModel->eraseAll();
					$pjMultiLangModel->reset()->where('model', 'pjExtra')->whereIn('foreign_id', $extra_arr)->eraseAll();
					$extra_item_arr = $pjExtraItemModel->whereIn('extra_id', $extra_arr)->findAll()->getDataPair(NULL, 'id');
					if (!empty($extra_item_arr))
					{
						$pjExtraItemModel->reset()->whereIn('extra_id', $extra_arr)->eraseAll();
						$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $extra_item_arr)->eraseAll();
					}
				}
				$this->pjActionDeleteProductAttr($_GET['id']);
				pjProductCategoryModel::factory()->where('product_id', $_GET['id'])->eraseAll();
				
				pjObject::import('Model', 'pjGallery:pjGallery');
				$pjGalleryModel = pjGalleryModel::factory();
				$image_arr = $pjGalleryModel->where('foreign_id', $_GET['id'])->findAll()->getData();
				if (!empty($image_arr))
				{
					$pjGalleryModel->eraseAll();
					foreach ($image_arr as $image)
					{
						@clearstatcache();
						if (!empty($image['small_path']) && is_file($image['small_path']))
						{
							@unlink($image['small_path']);
						}
						@clearstatcache();
						if (!empty($image['medium_path']) && is_file($image['medium_path']))
						{
							@unlink($image['medium_path']);
						}
						@clearstatcache();
						if (!empty($image['large_path']) && is_file($image['large_path']))
						{
							@unlink($image['large_path']);
						}
						@clearstatcache();
						if (!empty($image['source_path']) && is_file($image['source_path']))
						{
							@unlink($image['source_path']);
						}
					}
				}
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			} else {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
		exit;
	}
	
	public function pjActionDeleteProductBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjExtraModel = pjExtraModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				pjProductModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				$pjMultiLangModel->where('model', 'pjProduct')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				
				pjStockModel::factory()->whereIn('product_id', $_POST['record'])->eraseAll();
				$this->pjActionDeleteStockAttr($_POST['record']);
				pjProductSimilarModel::factory()->whereIn('product_id', $_POST['record'])->orWhereIn('similar_id', $_POST['record'])->eraseAll();
				
				$extra_arr = $pjExtraModel->whereIn('product_id', $_POST['record'])->findAll()->getDataPair(null, 'id');
				if (!empty($extra_arr))
				{
					$pjExtraItemModel = pjExtraItemModel::factory();
					$pjExtraModel->eraseAll();
					$pjMultiLangModel->reset()->where('model', 'pjExtra')->whereIn('foreign_id', $extra_arr)->eraseAll();
					$extra_item_arr = $pjExtraItemModel->whereIn('extra_id', $extra_arr)->findAll()->getDataPair(NULL, 'id');
					if (!empty($extra_item_arr))
					{
						$pjExtraItemModel->reset()->whereIn('extra_id', $extra_arr)->eraseAll();
						$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $extra_item_arr)->eraseAll();
					}
				}

				$this->pjActionDeleteProductAttr($_POST['record']);
				pjProductCategoryModel::factory()->whereIn('product_id', $_POST['record'])->eraseAll();
				
				pjObject::import('Model', 'pjGallery:pjGallery');
				$pjGalleryModel = pjGalleryModel::factory();
				$image_arr = $pjGalleryModel->whereIn('foreign_id', $_POST['record'])->findAll()->getData();
				if (!empty($image_arr))
				{
					$pjGalleryModel->eraseAll();
					foreach ($image_arr as $image)
					{
						@clearstatcache();
						if (!empty($image['small_path']) && is_file($image['small_path']))
						{
							@unlink($image['small_path']);
						}
						@clearstatcache();
						if (!empty($image['medium_path']) && is_file($image['medium_path']))
						{
							@unlink($image['medium_path']);
						}
						@clearstatcache();
						if (!empty($image['large_path']) && is_file($image['large_path']))
						{
							@unlink($image['large_path']);
						}
						@clearstatcache();
						if (!empty($image['source_path']) && is_file($image['source_path']))
						{
							@unlink($image['source_path']);
						}
					}
				}
			}
		}
		exit;
	}

	public function pjActionDeleteSimilar()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && isset($_GET['id']) && (int) $_GET['id'] > 0)
		{
			if (pjProductSimilarModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Similar product has been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Similar product has not been deleted.'));
		}
		exit;
	}
	
	public function pjActionDeleteSimilarBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				if (pjProductSimilarModel::factory()->whereIn('id', $_POST['record'])->eraseAll()->getAffectedRows() > 0)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Similar products has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Similar products has been deleted.'));
			}
		}
		exit;
	}
	
	public function pjActionGetProduct()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjProductModel = pjProductModel::factory()
				->join('pjMultilang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
				->join('pjMultilang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='short_desc'", 'left outer')
				->join('pjMultilang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='".$this->getLocaleId()."' AND t4.field='full_desc'", 'left outer');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($_GET['q']));
				$pjProductModel->where('t2.content LIKE', "%$q%");
				$pjProductModel->orWhere('t3.content LIKE', "%$q%");
				$pjProductModel->orWhere('t4.content LIKE', "%$q%");
				$pjProductModel->orWhere('t1.sku LIKE', "%$q%");
			}
			
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array(1,2)))
			{
				$pjProductModel->where('t1.status', $_GET['status']);
			}

			if (isset($_GET['name']) && !empty($_GET['name']))
			{
				$q = str_replace(array('%', '_'), array('\%', '\_'), $_GET['name']);
				$pjProductModel->where('t2.content LIKE', "%$q%");
			}
			
			if (isset($_GET['sku']) && !empty($_GET['sku']))
			{
				$q = str_replace(array('%', '_'), array('\%', '\_'), $_GET['sku']);
				$pjProductModel->where('t1.sku LIKE', "%$q%");
			}
			
			if (isset($_GET['category_id']) && (int) $_GET['category_id'] > 0)
			{
				$pjProductModel->where(sprintf("t1.id IN (SELECT `product_id` FROM `%s` WHERE `category_id` = '%u')",
					pjProductCategoryModel::factory()->getTable(), (int) $_GET['category_id']));
			}
			
			if (isset($_GET['is_digital']))
			{
				$pjProductModel->where('t1.is_digital', 1);
			}
			
			if (isset($_GET['is_featured']))
			{
				$pjProductModel->where('t1.is_featured', 1);
			}
			
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjProductModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			pjObject::import('Model', 'pjGallery:pjGallery');
			
			$data = $pjProductModel
				->select(sprintf("t1.*, t2.content AS name,
					(SELECT `small_path` FROM `%1\$s`
						WHERE `foreign_id` = `t1`.`id`
						ORDER BY `sort` ASC
						LIMIT 1) AS `pic`,
					(SELECT COALESCE(SUM(`qty`), 0) FROM `%2\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `total_stock`,
					(SELECT MIN(`price`) FROM `%2\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `min_price`,
					(SELECT COUNT(`id`) FROM `%2\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `cnt_stock`
				", pjGalleryModel::factory()->getTable(), pjStockModel::factory()->getTable()))
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
				
			foreach ($data as $k => $v)
			{
				$data[$k]['min_price_format'] = pjUtil::formatCurrencySign(number_format($v['min_price'], 2), $this->option_arr['o_currency']);
				if ($v['cnt_stock'] > 1)
				{
					$data[$k]['min_price_format'] = __('front_price_from', true) . " " . $data[$k]['min_price_format'];
				}
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionGetStock()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjStockModel = pjStockModel::factory()
				->join('pjMultilang', "t2.model='pjProduct' AND t2.foreign_id=t1.product_id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
				->join('pjProduct', 't3.id=t1.product_id', 'left outer')
			;
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = trim($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjStockModel->where('t2.content LIKE', "%$q%");
				$pjStockModel->orWhere('t3.sku LIKE', "%$q%");
			}
			
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjStockModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			pjObject::import('Model', 'pjGallery:pjGallery');
			
			$data = $pjStockModel
				->select(sprintf("t1.*, t2.content AS name,
					(SELECT `small_path` FROM `%1\$s`
						WHERE `id` = `t1`.`image_id`
						LIMIT 1) AS `pic`,
					(SELECT GROUP_CONCAT(CONCAT_WS('~:~', `tm2`.`content`, `tm1`.`content`) SEPARATOR '~|~')
						FROM `%2\$s` AS `tsa`
						LEFT JOIN `%3\$s` AS `tm1` ON `tm1`.`model` = 'pjAttribute' AND `tm1`.`foreign_id` = `tsa`.`attribute_id` AND `tm1`.`field` = 'name' AND `tm1`.`locale` = '%4\$u'
						LEFT JOIN `%3\$s` AS `tm2` ON `tm2`.`model` = 'pjAttribute' AND `tm2`.`foreign_id` = `tsa`.`attribute_parent_id` AND `tm2`.`field` = 'name' AND `tm2`.`locale` = '%4\$u'
						WHERE `tsa`.`product_id` = `t1`.`product_id`
						AND `tsa`.`stock_id` = `t1`.`id`
						LIMIT 1) AS `stock_attr`
				", pjGalleryModel::factory()->getTable(), pjStockAttributeModel::factory()->getTable(), pjMultiLangModel::factory()->getTable(), $this->getLocaleId()))
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->toArray('stock_attr', '~|~')
				->getData();
			
			foreach ($data as $k => $v)
			{
				$data[$k]['price_formated'] = pjUtil::formatCurrencySign(number_format($v['price'], 2), $this->option_arr['o_currency']);
			}
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	public function pjActionDeleteExtra()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$resp = array('code' => 100);
			if (pjExtraModel::factory()->set('id', $_POST['id'])->erase()->getAffectedRows() == 1)
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				$pjExtraItemModel = pjExtraItemModel::factory();
				
				$pjMultiLangModel->reset()
					->where('model', 'pjExtra')
					->where('foreign_id', $_POST['id'])
					->eraseAll();
					
				$extra_item = $pjExtraItemModel->where('extra_id', $_POST['id'])->findAll()->getDataPair(NULL, 'id');
				if (!empty($extra_item))
				{
					$pjMultiLangModel->reset()
						->where('model', 'pjExtraItem')
						->whereIn('foreign_id', $extra_item)
						->eraseAll();
				
					$pjExtraItemModel->reset()->where('extra_id', $_POST['id'])->eraseAll();
				}
				$resp['code'] = 200;
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	public function pjActionDeleteStock()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$resp = array('code' => 100);
			if (pjStockModel::factory()->set('id', $_POST['id'])->erase()->getAffectedRows() == 1)
			{
				pjStockAttributeModel::factory()->where('stock_id', $_POST['id'])->eraseAll();
				$resp['code'] = 200;
			}
			pjAppController::jsonResponse($resp);
		}
	}
	
	public function pjActionDeleteDigital()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjProductModel = pjProductModel::factory();
			
			$arr = $pjProductModel->find($_POST['id'])->getData();
			if (count($arr) > 0)
			{
				if ($pjProductModel
					->reset()
					->set('id', $arr['id'])
					->modify(array(
						'digital_file' => ':NULL',
						'digital_name' => ':NULL'
					))->getAffectedRows() == 1)
				{
					@unlink($arr['digital_file']);
				}
			}
		}
	}
	
	public function pjActionExportProduct()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjProductModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Products-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionExtraCopy()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['from_product_id']) && (int) $_POST['from_product_id'] > 0 &&
				isset($_POST['product_id']) && (int) $_POST['product_id'] > 0)
			{
				$pjExtraModel = pjExtraModel::factory();
				$pjExtraItemModel = pjExtraItemModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$extras = $pjExtraModel->where('t1.product_id', $_POST['from_product_id'])->findAll()->getData();
				$extras_items = $pjExtraItemModel
					->where(sprintf("t1.extra_id IN (SELECT `id` FROM `%s` WHERE `product_id` = '%u')", $pjExtraModel->getTable(), $_POST['from_product_id']))
					->findAll()
					->getData();
				
				foreach ($extras as $k => $extra)
				{
					$extras[$k]['items'] = array();
					if ($extra['type'] == 'multi')
					{
						foreach ($extras_items as $key => $item)
						{
							if ($item['extra_id'] == $extra['id'])
							{
								$extras[$k]['items'][] = $item;
							}
						}
					}
				}
				
				$query = sprintf("INSERT INTO `%1\$s` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
						SELECT :foreign_id, `model`, `locale`, `field`, `content`, `source`
						FROM `%1\$s`
						WHERE `foreign_id` = :fid
						AND `model` = :model",
					$pjMultiLangModel->getTable());
							
				foreach ($extras as $extra)
				{
					$extra_id = $pjExtraModel->reset()->setAttributes(array(
						'product_id' => $_POST['product_id'],
						'type' => $extra['type'],
						'price' => $extra['price'],
						'is_mandatory' => $extra['is_mandatory']
					))->insert()->getInsertId();
					if ($extra_id !== FALSE && (int) $extra_id > 0)
					{
						$pjMultiLangModel->reset()->prepare($query)->exec(array(
							'foreign_id' => $extra_id,
							'fid' => $extra['id'],
							'model' => 'pjExtra'
						));
						
						if ($extra['type'] == 'multi' && isset($extra['items']) && !empty($extra['items']))
						{
							foreach ($extra['items'] as $item)
							{
								$extra_item_id = $pjExtraItemModel->reset()->setAttributes(array(
									'extra_id' => $extra_id,
									'price' => $item['price']
								))->insert()->getInsertId();
								if ($extra_item_id !== FALSE && (int) $extra_item_id > 0)
								{
									$pjMultiLangModel->reset()->prepare($query)->exec(array(
										'foreign_id' => $extra_item_id,
										'fid' => $item['id'],
										'model' => 'pjExtraItem'
									));
								}
							}
						}
					}
				}
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Extras has been copied'));
			}
		}
		exit;
	}
	
	public function pjActionGetAttributes()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$wk = 't1.product_id';
				$wv = $_GET['product_id'];
			} elseif (isset($_GET['hash']) && !empty($_GET['hash'])) {
				$wk = 't1.hash';
				$wv = $_GET['hash'];
			}
			
			$attr_arr = array();
			$a_arr = pjAttributeModel::factory()
				->select('t1.id, t1.product_id, t1.parent_id, t1.hash, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where($wk, $wv)
				->orderBy('t1.parent_id ASC, `name` ASC')
				->findAll()
				->getData();
			
			$pjMultiLangModel = pjMultiLangModel::factory();
				
			foreach ($a_arr as $attr)
			{
				$attr['i18n'] = $pjMultiLangModel->reset()->getMultiLang($attr['id'], 'pjAttribute');
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
		}
	}
	
	public function pjActionGetExtras()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (!isset($_GET['product_id']) || (int) $_GET['product_id'] <= 0)
			{
				return;
			}
			
			$extra_arr = pjExtraModel::factory()->where('t1.product_id', $_GET['product_id'])->findAll()->getData();
			$pjExtraItemModel = pjExtraItemModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			foreach ($extra_arr as $k => $extra)
			{
				$extra_arr[$k]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($extra['id'], 'pjExtra');
				$extra_arr[$k]['extra_items'] = $pjExtraItemModel->reset()->where('t1.extra_id', $extra['id'])->orderBy('t1.price ASC')->findAll()->getData();
				foreach ($extra_arr[$k]['extra_items'] as $key => $val)
				{
					$extra_arr[$k]['extra_items'][$key]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($val['id'], 'pjExtraItem');
				}
			}
			$this->set('extra_arr', $extra_arr);
				
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
		}
	}
	
	public function pjActionGetHistory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$this->set('history_arr', pjHistoryModel::factory()
				->select('t1.*, t2.id AS sid, t3.content AS name')
				->join('pjStock', 't2.id=t1.record_id', 'left outer')
				->join('pjMultilang', "t3.model='pjProduct' AND t3.foreign_id=t2.product_id AND t3.locale='".$this->getLocaleId()."' AND t3.field='name'", 'left outer')
				->where('t1.table_name', pjStockModel::factory()->getTable())
				->where('t2.product_id', $_GET['id'])
				->orderBy('t1.created ASC')
				->findAll()
				->getData()
			);
			
		}
	}
	
	public function pjActionGetProducts()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjProductModel = pjProductModel::factory();
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$pjProductModel->where('t1.id !=', $_GET['product_id']);
			}
			if (isset($_GET['copy']))
			{
				switch ($_GET['copy'])
				{
					case 'Attr':
						$pjProductModel->where(sprintf("t1.id IN (SELECT `product_id` FROM `%s` WHERE 1)", pjAttributeModel::factory()->getTable()));
						break;
					case 'Extra':
						$pjProductModel->where(sprintf("t1.id IN (SELECT `product_id` FROM `%s` WHERE 1)", pjExtraModel::factory()->getTable()));
						break;
				}
			}
			$this->set('arr', $pjProductModel
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
				->orderBy('name ASC')->findAll()->getData());
		}
	}
	
	public function pjActionGetSimilar()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjProductSimilarModel = pjProductSimilarModel::factory()
					->join('pjProduct', 't2.id=t1.similar_id', 'inner')
					->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='name'", 'left outer')
					->where('t1.product_id', $_GET['id'])
				;
				
				$column = 'name';
				$direction = 'ASC';
				if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
				{
					$column = $_GET['column'];
					$direction = strtoupper($_GET['direction']);
				}
	
				$total = $pjProductSimilarModel->findCount()->getData();
				$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
				$pages = ceil($total / $rowCount);
				$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
				$offset = ((int) $page - 1) * $rowCount;
				if ($page > $pages)
				{
					$page = $pages;
				}
			
				$data = $pjProductSimilarModel
					->select('t1.id, t2.sku, t2.status, t3.content AS name')
					->orderBy("$column $direction")
					->findAll()
					->getData();
				
				pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
			}
		}
		exit;
	}
	
	public function pjActionSearchProducts()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			$pjProductModel = pjProductModel::factory();
			
			if (isset($_GET['term']))
			{
				$q = $pjProductModel->escapeStr($_GET['term']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjProductModel->where("(t1.id LIKE '%$q%' OR t1.sku LIKE '%$q%' OR t1.id IN (SELECT `foreign_id` FROM `".pjMultiLangModel::factory()->getTable()."`
					WHERE `field` = 'name'
					AND `model` = 'pjProduct'
					AND `content` LIKE '%$q%'))");
			}
			$arr = $pjProductModel
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.id !=', $_GET['id'])
				->where(sprintf("t1.id NOT IN (SELECT `similar_id` FROM `%s` WHERE `product_id` = '%u')", pjProductSimilarModel::factory()->getTable(), $_GET['id']))
				->orderBy('`name` ASC')->findAll()->getData();
			
			$_arr = array();
			foreach ($arr as $v)
			{
				$_arr[] = array('label' => $v['name'], 'value' => $v['id']);
			}
			
			pjAppController::jsonResponse($_arr);
		}
		exit;
	}
	
	public function pjActionStock()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminProducts.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionAddSimilar()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['product_id']) && isset($_POST['similar_id']) && (int) $_POST['product_id'] > 0 && (int) $_POST['similar_id'] > 0)
			{
				$insert_id = pjProductSimilarModel::factory($_POST)->insert()->getInsertId();
				if ($insert_id !== FALSE && (int) $insert_id > 0)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Similar product has been added.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Similar product has not been added.'));
			} else {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing parameters.'));
			}
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminProducts.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveProduct()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjProductModel = pjProductModel::factory();
			if (!in_array($_POST['column'], $pjProductModel->getI18n()))
			{
				if ($_POST['column'] != "sku" || $pjProductModel->where('t1.sku', $_POST['value'])->findCount()->getData() == 0)
				{
					$pjProductModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				}
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjProduct');
			}
		}
		exit;
	}
	
	public function pjActionSaveStock()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjStockModel = pjStockModel::factory();
			if (!in_array($_POST['column'], $pjStockModel->getI18n()))
			{
				if ($_POST['column'] == 'qty')
				{
					$before = $pjStockModel->reset()->find($_GET['id'])->getData();
				}
				$affected_rows = $pjStockModel->reset()->set('id', $_GET['id'])->modify(array($_POST['column'] => $_POST['value']))->getAffectedRows();
				if ($_POST['column'] == 'qty' && $affected_rows == 1)
				{
					$after = $pjStockModel->reset()->find($_GET['id'])->getData();
					pjAppController::addToHistory($_GET['id'], $this->getUserId(), $pjStockModel->getTable(), $before, $after);
				}
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjStock');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$pjExtraModel = pjExtraModel::factory();
			$pjExtraItemModel = pjExtraItemModel::factory();
			$pjAttributeModel = pjAttributeModel::factory();
			$pjStockModel = pjStockModel::factory();
			$pjStockAttributeModel = pjStockAttributeModel::factory();
			pjObject::import('Model', 'pjGallery:pjGallery');
			$pjProductCategoryModel = pjProductCategoryModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			$pjProductModel = pjProductModel::factory();
			
			if (isset($_POST['product_update']))
			{
				if (!isset($_POST['id']) || empty($_POST['id']))
				{
					pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjAdminProducts&action=pjActionIndex&err=');
				}
				$product_arr = $pjProductModel->find($_POST['id'])->getData();
				if (empty($product_arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjAdminProducts&action=pjActionIndex&err=');
				}
				
				# Stock start ----------------------------------------------
				# Get/prepare
				
				$sa_arr = $pjStockAttributeModel
					->select("t1.stock_id, GROUP_CONCAT(CONCAT_WS(':', t1.attribute_parent_id, t1.attribute_id) ORDER BY t1.attribute_parent_id ASC, t1.attribute_id ASC SEPARATOR '|') AS `str`")
					->join('pjStock', 't2.id=t1.stock_id AND t2.product_id=t1.product_id', 'inner')
					->where('t2.product_id', $_POST['id'])
					->groupBy('t1.stock_id')
					->findAll()
					->getDataPair('stock_id', 'str');
				
				$i_arr = $u_arr = array();
				if (isset($_POST['stock_qty']))
				{
					foreach ($_POST['stock_qty'] as $k => $whatever)
					{
						if (strpos($k, 'x_') === 0)
						{
							if ((float) $_POST['stock_qty'][$k] > 0 && (float) $_POST['stock_price'][$k] > 0 && (int) $_POST['stock_image_id'][$k] > 0)
							{
								# Insert new stock
								$tmp = array();
								if (isset($_POST['stock_attribute']) && isset($_POST['stock_attribute'][$k]))
								{
									foreach ($_POST['stock_attribute'][$k] as $attr_parent_id => $attr_id)
									{
										$tmp[] = $attr_parent_id . ":" . $attr_id;
									}
								}
								asort($tmp);
								$i_arr[$k] = join("|", $tmp);
							}
						} else {
							# Update attr
							if (isset($_POST['stock_attribute']) && isset($_POST['stock_attribute'][$k]))
							{
								# Add items again
								$tmp = array();
								foreach ($_POST['stock_attribute'][$k] as $attr_parent_id => $attr_id)
								{
									$tmp[] = $attr_parent_id . ":" . $attr_id;
								}
								asort($tmp);
								$u_arr[$k] = join("|", $tmp);
							}
						}
					}
				
					foreach ($_POST['stock_qty'] as $k => $whatever)
					{
						if (strpos($k, 'x_') === 0)
						{
							if ((float) $_POST['stock_qty'][$k] > 0 && (float) $_POST['stock_price'][$k] > 0 && (int) $_POST['stock_image_id'][$k] > 0 && !in_array($i_arr[$k], $sa_arr))
							{
								# Insert new stock
								$stock_id = $pjStockModel
									->reset()
									->set('product_id', $_POST['id'])
									->set('image_id', $_POST['stock_image_id'][$k])
									->set('qty', $_POST['stock_qty'][$k])
									->set('price', $_POST['stock_price'][$k])
									->insert()
									->getInsertId();
		
								if ($stock_id !== false && (int) $stock_id > 0)
								{
									if (isset($_POST['stock_attribute']) && isset($_POST['stock_attribute'][$k]))
									{
										$pjStockAttributeModel->begin();
										foreach ($_POST['stock_attribute'][$k] as $attr_parent_id => $attr_id)
										{
											$pjStockAttributeModel
												->reset()
												->set('stock_id', $stock_id)
												->set('product_id', $_POST['id'])
												->set('attribute_parent_id', $attr_parent_id)
												->set('attribute_id', $attr_id)
												->insert();
										}
										$pjStockAttributeModel->commit();
									}
								}
							}
						} else {
							# Update attr
							$before = $pjStockModel->reset()->find($k)->getData();
							if ($pjStockModel
								->reset()
								->set('id', $k)
								->modify(array(
									'image_id' => $_POST['stock_image_id'][$k],
									'qty' => $_POST['stock_qty'][$k],
									'price' => $_POST['stock_price'][$k]
								))
								->getAffectedRows() == 1)
							{
								$after = $pjStockModel->reset()->find($k)->getData();
								pjAppController::addToHistory($k, $this->getUserId(), $pjStockModel->getTable(), $before, $after);
							}
							//if (isset($sa_arr[$k]) && isset($u_arr[$k]) && $sa_arr[$k] != $u_arr[$k] && !in_array($u_arr[$k], $sa_arr))
							if (isset($u_arr[$k]) && !in_array($u_arr[$k], $sa_arr) && (
									!isset($sa_arr[$k]) ||
									(isset($sa_arr[$k]) && $sa_arr[$k] != $u_arr[$k])
								)
							)
							{
								# Delete items ------------------
								$pjStockAttributeModel->reset()->where('stock_id', $k)->eraseAll();
								if (isset($_POST['stock_attribute']) && isset($_POST['stock_attribute'][$k]))
								{
									# Add items again
									$pjStockAttributeModel->begin();
									foreach ($_POST['stock_attribute'][$k] as $attr_parent_id => $attr_id)
									{
										$pjStockAttributeModel
											->reset()
											->set('stock_id', $k)
											->set('product_id', $_POST['id'])
											->set('attribute_parent_id', $attr_parent_id)
											->set('attribute_id', $attr_id)
											->insert();
									}
									$pjStockAttributeModel->commit();
								}
							}
						}
					}
				}
				# Stock end ----------------------------------------------

				$this->pjActionAttrHandle($_POST['id']);
				if (isset($_POST['is_digital']))
				{
					$this->pjActionDeleteProductAttr($product_arr['id']);
					$this->pjActionDeleteStockAttr($product_arr['id']);
					
					$statement = sprintf("DELETE FROM `%1\$s`
						WHERE `product_id` = :product_id
						AND `id` NOT IN (
						  SELECT `id`
						  FROM (
						    SELECT `id`
						    FROM `%1\$s`
						    WHERE `product_id` = :product_id
						    ORDER BY `id` ASC
						    LIMIT 1
						  ) `foo`);", $pjStockModel->getTable());
					
					$pjStockModel->prepare($statement)->exec(array('product_id' => $product_arr['id']));
				}

				# Extras start -----------------------------------------
				# Get/Prepare
				$extra_items = array();
				$extra_arr = $pjExtraModel->where('t1.product_id', $_POST['id'])->findAll()->getDataPair('id', 'type');
				foreach ($extra_arr as $id => $type)
				{
					if ($type == 'multi')
					{
						$extra_items[$id] = $pjExtraItemModel->reset()->where('t1.extra_id', $id)->findAll()->getDataPair('id', 'id');
					}
				}
				# Update ------------------
				if (isset($_POST['extra_type']))
				{
					foreach ($_POST['extra_type'] as $k => $type)
					{
						# Insert new extra ------------------
						if (strpos($k, 'x_') === 0)
						{
							$data = array();
							$data['product_id'] = $_POST['id'];
							$data['type'] = $type;
							switch ($type)
							{
								case 'single':
									$data['price'] = $_POST['extra_price'][$k];
									break;
								case 'multi':
									break;
							}
							$data['is_mandatory'] = isset($_POST['extra_is_mandatory'][$k]) ? 1 : 0;
							$extra_id = $pjExtraModel->reset()->setAttributes($data)->insert()->getInsertId();
							if ($extra_id !== false && (int) $extra_id > 0)
							{
								switch ($type)
								{
									case 'single':
										$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'extra_name', $k);
										$pjMultiLangModel->saveMultiLang($tmp, $extra_id, 'pjExtra');
										break;
									case 'multi':
										$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'extra_title', $k);
										$pjMultiLangModel->saveMultiLang($tmp, $extra_id, 'pjExtra');
										
										$edata = array();
										$edata['extra_id'] = $extra_id;
										foreach ($_POST['extra_price'][$k] as $index => $price)
										{
												$edata['price'] = $price;
												$ei_id = $pjExtraItemModel->reset()->setAttributes($edata)->insert()->getInsertId();
												if ($ei_id !== false && (int) $ei_id > 0)
												{
													$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'extra_name', $k, $index);
													$pjMultiLangModel->saveMultiLang($tmp, $ei_id, 'pjExtraItem');
												}
										}
										break;
								}
							}
						} else {
							# Update extra
							$pjExtraModel->reset()->set('id', $k)->modify(array(
								'type' => $type,
								'price' => $type == 'single' ? $_POST['extra_price'][$k] : ':NULL',
								'is_mandatory' => isset($_POST['extra_is_mandatory'][$k]) ? 1 : 0
							));
							### ---
							switch ($type)
							{
								case 'multi':
									# value of column 'type' in DB is single
									$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'extra_title', $k);
									if ($extra_arr[$k] == 'single')
									{
										$pjMultiLangModel->reset()->where('model', 'pjExtra')->where('foreign_id', $k)->eraseAll();
										$pjMultiLangModel->saveMultiLang($tmp, $k, 'pjExtra');
									} else {
										$pjMultiLangModel->updateMultiLang($tmp, $k, 'pjExtra');
									}
									# Delete items ------------------
									$diff = array_diff($extra_items[$k], array_keys($_POST['extra_price'][$k]));
									if (count($diff) > 0)
									{
										$pjExtraItemModel->reset()->whereIn('id', $diff)->eraseAll();
										$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $diff)->eraseAll();
									}
									
									foreach ($_POST['extra_price'][$k] as $index => $price)
									{
										if (strpos($index, 'y_') === 0)
										{
											# Add items
												$ei_id = $pjExtraItemModel->reset()->setAttributes(array(
													'extra_id' => $k,
													'price' => $price
												))->insert()->getInsertId();
												if ($ei_id !== false && (int) $ei_id > 0)
												{
													$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'extra_name', $k, $index);
													$pjMultiLangModel->saveMultiLang($tmp, $ei_id, 'pjExtraItem');
												}
										} else {
											# Update items
												$pjExtraItemModel->reset()->set('id', $index)->modify(array(
													'price' => $price
												));
												$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'extra_name', $k, $index);
												$pjMultiLangModel->updateMultiLang($tmp, $index, 'pjExtraItem');
										}
									}
									break;
								case 'single':
									# value of column 'type' in DB is multi
									if ($extra_arr[$k] == 'multi')
									{
										$ei_ids = $pjExtraItemModel->reset()->where('extra_id', $k)->findAll()->getDataPair(NULL, 'id');
										if (!empty($ei_ids))
										{
											$pjExtraItemModel->eraseAll();
											$pjMultiLangModel->reset()->where('model', 'pjExtraItem')->whereIn('foreign_id', $ei_ids)->eraseAll();
										}
										$pjMultiLangModel->reset()->where('model', 'pjExtra')->where('foreign_id', $k)->where('field', 'extra_title')->eraseAll();
									}
									$tmp = $this->pjActionTurnI18n($_POST['i18n'], 'extra_name', $k);
									$pjMultiLangModel->updateMultiLang($tmp, $k, 'pjExtra');
									break;
							}
							### ---
						}
					}
				}
				# Extras end -----------------------------------------

				# Categories start -----------------------------------
				$pjProductCategoryModel->where('product_id', $_POST['id'])->eraseAll();
				if (isset($_POST['category_id']) && count($_POST['category_id']) > 0)
				{
					$pjProductCategoryModel->begin();
					foreach ($_POST['category_id'] as $category_id)
					{
						$pjProductCategoryModel
							->reset()
							->set('product_id', $_POST['id'])
							->set('category_id', $category_id)
							->insert();
					}
					$pjProductCategoryModel->commit();
				}
				# Categories end -------------------------------------
					
				$data = array();
				$data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
				$data['is_digital'] = isset($_POST['is_digital']) ? 1 : 0;
				if (isset($_POST['is_digital']))
				{
					if (isset($_POST['digital_choose']))
					{
						switch ($_POST['digital_choose'])
						{
							case 1:
								if (isset($_FILES['digital_file']))
								{
									$pjUpload = new pjUpload();
									if ($pjUpload->load($_FILES['digital_file']))
									{
										$name = $pjUpload->getFile('name');
										$file = PJ_UPLOAD_PATH . 'digital/' . md5(uniqid(rand(), true)) . "." . $pjUpload->getExtension();
										if ($pjUpload->save($file))
										{
											$data['digital_file'] = $file;
											$data['digital_name'] = $name;
										}
									}
								}
								break;
							case 2:
								$data['digital_file'] = $_POST['digital_file'];
								$data['digital_name'] = basename($_POST['digital_file']);
								break;
						}
					}
					$data['digital_expire'] = sprintf("%s:%s:00", $_POST['hour'], $_POST['minute']);
				} else {
					$data['digital_file'] = ':NULL';
					$data['digital_name'] = ':NULL';
					$data['digital_expire'] = ':NULL';
				}
				if ($pjProductModel->reset()->set('id', $_POST['id'])->modify(array_merge($_POST, $data))->getAffectedRows() == 1)
				{
					$err = 'AP05';
				} else {
					$err = 'AP05';
				}
				if (isset($_POST['i18n']))
				{
					foreach ($_POST['i18n'] as $locale_id => $locale_arr)
					{
						unset($_POST['i18n'][$locale_id]['extra_title']);
						unset($_POST['i18n'][$locale_id]['extra_name']);
					}
					$pjMultiLangModel->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjProduct');
				}
				pjUtil::redirect(sprintf("%s?controller=pjAdminProducts&action=pjActionUpdate&id=%u&tab=%u&err=%s", $_SERVER['PHP_SELF'], @$_POST['id'], @$_POST['tab'], $err));
			} else {
				$arr = $pjProductModel->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(sprintf("%s?controller=pjAdminProducts&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], 'AP08'));
				}
				$pjMultiLangModel = pjMultiLangModel::factory();
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjProduct');
				$this->set('arr', $arr);
				
				$this->set('category_arr', pjCategoryModel::factory()->getNode($this->getLocaleId(), 1));
				$this->set('pc_arr', pjProductCategoryModel::factory()->where('t1.product_id', $arr['id'])->orderBy('t1.category_id ASC')->findAll()->getDataPair('category_id', 'category_id'));
				$extra_arr = pjExtraModel::factory()->where('t1.product_id', $arr['id'])->findAll()->getData();
				$pjExtraItemModel = pjExtraItemModel::factory();
				foreach ($extra_arr as $k => $extra)
				{
					$extra_arr[$k]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($extra['id'], 'pjExtra');
					$extra_arr[$k]['extra_items'] = $pjExtraItemModel->reset()->where('t1.extra_id', $extra['id'])->orderBy('t1.price ASC')->findAll()->getData();
					foreach ($extra_arr[$k]['extra_items'] as $key => $val)
					{
						$extra_arr[$k]['extra_items'][$key]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($val['id'], 'pjExtraItem');
					}
				}
				$this->set('extra_arr', $extra_arr);
				$attr_arr = array();
				// Do not change col_name, direction
				$a_arr = pjAttributeModel::factory()
					->select('t1.id, t1.product_id, t1.parent_id, t1.hash, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.product_id', $arr['id'])
					->orderBy('t1.parent_id ASC, `name` ASC')->findAll()->getData();

				foreach ($a_arr as $attr)
				{
					$attr['i18n'] = $pjMultiLangModel->reset()->getMultiLang($attr['id'], 'pjAttribute');
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

				$stock_arr = pjStockModel::factory()
					->select('t1.*, t2.small_path')
					->join('pjGallery', 't2.id=t1.image_id', 'left outer')
					->where('t1.product_id', $arr['id'])
					->findAll()
					->getData();

				$pjStockAttributeModel = pjStockAttributeModel::factory();
				foreach ($stock_arr as $k => $stock)
				{
					$stock_arr[$k]['attrs'] = $pjStockAttributeModel->reset()
						->where('t1.stock_id', $stock['id'])
						->orderBy('t1.attribute_id ASC')
						->findAll()
						->getDataPair('attribute_parent_id', 'attribute_id');
				}
				$this->set('stock_arr', $stock_arr);
				
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
				
				# Gallery plugin
				$this->appendCss('pj-gallery.css', pjObject::getConstant('pjGallery', 'PLUGIN_CSS_PATH'));
				$this->appendJs('ajaxupload.js', pjObject::getConstant('pjGallery', 'PLUGIN_JS_PATH'));
				$this->appendJs('jquery.gallery.js', pjObject::getConstant('pjGallery', 'PLUGIN_JS_PATH'));
				
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				
				$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminProducts.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	private function pjActionTurnI18n($data, $key, $id, $index=NULL, $new_key=NULL)
	{
		$arr = array();
		$arr_index = is_null($new_key) ? $key : $new_key;
		foreach ($data as $locale => $locale_arr)
		{
			$arr[$locale] = array(
				$arr_index => is_null($index) ?
					(isset($locale_arr[$key]) && isset($locale_arr[$key][$id]) ? $locale_arr[$key][$id] : NULL) :
					(isset($locale_arr[$key]) && isset($locale_arr[$key][$id]) && isset($locale_arr[$key][$id][$index]) ? $locale_arr[$key][$id][$index] : NULL)
			);
		}
		
		return $arr;
	}

	public function pjActionLoadImages()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			$arr = array();
			pjObject::import('Model', 'pjGallery:pjGallery');
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$arr = pjGalleryModel::factory()->where('t1.foreign_id', $_GET['product_id'])->orderBy('ISNULL(t1.sort), t1.sort ASC, t1.id ASC')->findAll()->getData();
			} elseif (isset($_GET['hash']) && !empty($_GET['hash'])) {
				$arr = pjGalleryModel::factory()->where('t1.hash', $_GET['hash'])->orderBy('ISNULL(t1.sort), t1.sort ASC, t1.id ASC')->findAll()->getData();
			}
			
			$this->set('arr', $arr);
		}
	}
}
?>