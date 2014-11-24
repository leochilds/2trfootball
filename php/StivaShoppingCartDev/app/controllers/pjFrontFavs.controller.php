<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontFavs extends pjFront
{
	public function pjActionAdd()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (isset($_COOKIE[$this->defaultCookie]))
			{
				$data = unserialize(stripslashes($_COOKIE[$this->defaultCookie]));
			}
			
			if (!isset($data) || $data === FALSE)
			{
				$data = array();
			}

			$data[serialize($_POST)] = 1;
			
			setcookie($this->defaultCookie, serialize($data), time() + 60*60*24*30);
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 202, 'text' => __('system_202', true)));
		}
		exit;
	}
	
	public function pjActionCheck()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (!isset($_COOKIE[$this->defaultCookie]) || empty($_COOKIE[$this->defaultCookie]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Fav list not set or empty.'));
			}
			
			$data = unserialize(stripslashes($_COOKIE[$this->defaultCookie]));
			
			if (!isset($data) || $data === FALSE)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Fav list is empty.'));
			}
			
			$key = serialize($_POST);
			if (!array_key_exists($key, $data))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Stock was not found in the favs list.'));
			}
			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Stock found in the favs list.'));
		}
		exit;
	}
	
	public function pjActionRemove()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (isset($_POST['hash']) && !empty($_POST['hash']) && isset($_COOKIE[$this->defaultCookie]) && !empty($_COOKIE[$this->defaultCookie]))
			{
				$favs = unserialize(stripslashes($_COOKIE[$this->defaultCookie]));
				foreach ($favs as $key => $whatever)
				{
					if ($_POST['hash'] == md5($key))
					{
						$favs[$key] = NULL;
						unset($favs[$key]);
						if (empty($favs))
						{
							$favs = "";
							$time = time() - 3600;
						} else {
							$favs = serialize($favs);
							$time = time() + 60*60*24*30;
						}
						setcookie($this->defaultCookie, $favs, $time);
						$response = array('status' => 'OK', 'code' => 203, 'text' => __('system_203', true));
						break;
					}
				}
			}
			if (!isset($response))
			{
				$response = array('status' => 'ERR', 'code' => 102, 'text' => __('system_102', true));
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionEmpty()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (isset($_COOKIE[$this->defaultCookie]) && !empty($_COOKIE[$this->defaultCookie]))
			{
				setcookie($this->defaultCookie, "", time() - 3600);
				$response = array('status' => 'OK', 'code' => 204, 'text' => __('system_204', true));
			} else {
				$response = array('status' => 'ERR', 'code' => 103, 'text' => __('system_103', true));
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
}
?>