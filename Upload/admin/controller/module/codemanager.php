<?php
class ControllerModuleCodeManager extends Controller {
	// Module Unifier
	private $moduleName = 'CodeManager';
	private $moduleNameSmall = 'codemanager';
	private $moduleData_module = 'codemanager_module';
	private $moduleModel = 'model_module_codemanager';
	// Module Unifier

    public function index() { 
		
		// Module Unifier
		$this->data['moduleName'] = $this->moduleName;
		$this->data['moduleNameSmall'] = $this->moduleNameSmall;
		$this->data['moduleData_module'] = $this->moduleData_module;
		$this->data['moduleModel'] = $this->moduleModel;
		// Module Unifier
	 
        $this->load->language('module/'.$this->data['moduleNameSmall']);
        $this->load->model('module/'.$this->data['moduleNameSmall']);
        $this->load->model('setting/store');
        $this->load->model('localisation/language');
        $this->load->model('design/layout');
		
		if ($this->user->hasPermission('access', 'module/'.$this->data['moduleNameSmall'])) {
			$_SESSION[$this->data['moduleNameSmall']] = true;
			$this->data['usable'] = true;
		} else {
			$this->data['usable'] = false;
		}
		
		if ($this->user->hasPermission('modify', 'module/'.$this->data['moduleNameSmall'])) {
			$this->data['buttons'] = true;
		} else {
			$this->data['buttons'] = false;
		}
			
        $catalogURL = $this->getCatalogURL();
        $this->document->addScript($catalogURL . 'admin/view/javascript/ckeditor/ckeditor.js');
        $this->document->addScript($catalogURL . 'admin/view/javascript/'.$this->data['moduleNameSmall'].'/bootstrap/js/bootstrap.min.js');
        $this->document->addStyle($catalogURL  . 'admin/view/javascript/'.$this->data['moduleNameSmall'].'/bootstrap/css/bootstrap.min.css');
        $this->document->addStyle($catalogURL  . 'admin/view/stylesheet/'.$this->data['moduleNameSmall'].'/font-awesome/css/font-awesome.min.css');
        $this->document->addStyle($catalogURL  . 'admin/view/stylesheet/'.$this->data['moduleNameSmall'].'/'.$this->data['moduleNameSmall'].'.css');
        $this->document->setTitle($this->language->get('heading_title'));

        if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0; 
        }
		
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE name = '".$this->data['moduleName']."'");
		if (!$query->rows) {
			$permissions = array();
			$permissions["access"][] = 'extension/module';
			$permissions["access"][] = 'module/'.$this->data['moduleNameSmall'];
			$this->db->query("INSERT INTO " . DB_PREFIX . "user_group SET name = '" . $this->db->escape($this->data['moduleName']) . "', permission = '" . (isset($permissions) ? serialize($permissions) : '') . "'");	
		}
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE name = '".$this->data['moduleName']."'");
		$this->data['UserGroupID'] = $query->row['user_group_id'];
		
		
        $store = $this->getCurrentStore($this->request->get['store_id']);
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) { 	
            if (!$this->user->hasPermission('modify', 'module/'.$this->data['moduleNameSmall'])) {
                $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
            }

            if (!empty($_POST['OaXRyb1BhY2sgLSBDb21'])) {
                $this->request->post[$this->data['moduleName']]['LicensedOn'] = $_POST['OaXRyb1BhY2sgLSBDb21'];
            }

            if (!empty($_POST['cHRpbWl6YXRpb24ef4fe'])) {
                $this->request->post[$this->data['moduleName']]['License'] = json_decode(base64_decode($_POST['cHRpbWl6YXRpb24ef4fe']), true);
            }
			if(!isset($this->request->post[$this->data['moduleData_module']])) {
				$this->request->post[$this->data['moduleData_module']] = array();
			}
			
			$this->{$this->data['moduleModel']}->editSetting($this->data['moduleData_module'], $this->request->post[$this->data['moduleData_module']], $this->request->post['store_id']);
            $this->{$this->data['moduleModel']}->editSetting($this->data['moduleNameSmall'], $this->request->post, $this->request->post['store_id']);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('module/'.$this->data['moduleNameSmall'], 'store_id='.$this->request->post['store_id'] . '&token=' . $this->session->data['token'], 'SSL'));
        }

        if (isset($this->error['code'])) {
            $this->data['error_code'] = $this->error['code'];
        } else {
            $this->data['error_code'] = '';
        }

        $this->data['breadcrumbs']   = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/'.$this->data['moduleNameSmall'], 'token=' . $this->session->data['token'], 'SSL'),
        );

        $languageVariables = array(
		    // Main
			'heading_title',
			'error_permission',
			'text_success',
			'text_enabled',
			'text_disabled',
			'button_cancel',
			'save_changes',
			'text_default',
			'text_module'
        );
       
        foreach ($languageVariables as $languageVariable) {
            $this->data[$languageVariable] = $this->language->get($languageVariable);
        }
 
        $this->data['stores'] = array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' (' . $this->data['text_default'].')', 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
        $this->data['error_warning']          = '';  
        $this->data['languages']              = $this->model_localisation_language->getLanguages();
        $this->data['store']                  = $store;
        $this->data['token']                  = $this->session->data['token'];
        $this->data['action']                 = $this->url->link('module/'.$this->data['moduleNameSmall'], 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel']                 = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['data']                   = $this->{$this->data['moduleModel']}->getSetting($this->data['moduleNameSmall'], $store['store_id']);
        $this->data['modules']				= $this->{$this->data['moduleModel']}->getSetting($this->data['moduleData_module'], $store['store_id']);
        $this->data['layouts']                = $this->model_design_layout->getLayouts();
        $this->data['catalog_url']			= $catalogURL;
		
		// Module Unifier
		$this->data['moduleData'] = (isset($this->data['data'][$this->data['moduleName']])) ? $this->data['data'][$this->data['moduleName']] : '';
		// Module Unifier
		
        $this->template = 'module/'.$this->data['moduleNameSmall'].'.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    private function getCatalogURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_CATALOG;
        } else {
            $storeURL = HTTP_CATALOG;
        } 
        return $storeURL;
    }

    private function getServerURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        } 
        return $storeURL;
    }

    private function getCurrentStore($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL(); 
        }
        return $store;
    }
    
    public function install() {
	    $this->load->model('module/'.$this->moduleNameSmall);
	    $this->{$this->moduleModel}->install();
    }
    
    public function uninstall() {
    	$this->load->model('setting/setting');
		
		$this->load->model('setting/store');
		$this->model_setting_setting->deleteSetting($this->moduleData_module,0);
		$stores=$this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$this->model_setting_setting->deleteSetting($this->moduleData_module, $store['store_id']);
		}
		
        $this->load->model('module/'.$this->moduleNameSmall);
        $this->{$this->moduleModel}->uninstall();
    }
	
	public function givecredentials() {
		$this->load->model('user/user');
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE name = '".$this->moduleName."'");
		$this->data['user_group_id'] = $query->row['user_group_id'];
		$this->data['username'] = $this->generateRandomUsername();
		$this->data['password'] = $this->generateRandomPassword();
		$this->data['email'] = $this->generateRandomEmail();
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "user` 
			SET 
			username = '" . $this->db->escape($this->data['username']) . "',
			salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "',
			password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($this->data['password'])))) . "',
			firstname = '" . $this->db->escape($this->data['username']) . "',
			lastname = '" . $this->db->escape($this->data['username']) . "',
			email = '" . $this->db->escape($this->data['email']) . "',
			user_group_id = '" . (int)$this->data['user_group_id'] . "',
			status = '1',
			date_added = NOW()");
			
		$this->template = 'module/'.$this->moduleNameSmall.'/user_data.tpl';
        $this->response->setOutput($this->render());		
	}
	
	public function showusers() {
		$this->data['moduleNameSmall'] = $this->moduleNameSmall;
		$this->data['results'] = $this->getUsersByGroup();
		$this->template = 'module/'.$this->moduleNameSmall.'/users.tpl';
        $this->response->setOutput($this->render());	
	}
	
	public function removeuser() {
		if (isset($_POST['user_id'])) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$_POST['user_id'] . "'");
		}
	}
	private function getUsersByGroup() {
		$queryFirst = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE name = '".$this->moduleName."'");
		$user_group_id = $queryFirst->row['user_group_id'];
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . $this->db->escape($user_group_id) . "'");
		return $query->rows;
	}
	
	private function generateRandomUsername($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	private function generateRandomPassword($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz!@#$%';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	private function generateRandomEmail($length = 7) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString."@test.example";
	}

}

?>