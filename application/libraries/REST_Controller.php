<?php defined('BASEPATH') OR exit('No direct script access allowed');

class REST_Controller extends CI_Controller {

	protected $rest_format = NULL; // Set this in a controller to use a default format
	protected $methods = array(); // contains a list of method properties such as limit, log and level
	protected $request = NULL; // Stores accept, language, body, headers, etc
	protected $rest = NULL; // Stores DB, keys, key level, etc
	private $_get_args = array();
	private $_post_args = array();
	private $_put_args = array();
	private $_delete_args = array();
	private $_args = array();
	private $_allow = TRUE;

	// List all supported methods, the first will be the default format
	protected $_supported_formats = array(
		'xml' => 'application/xml',
		'rawxml' => 'application/xml',
		'json' => 'application/json',
		'jsonp' => 'application/javascript',
		'serialize' => 'application/vnd.php.serialized',
		'php' => 'text/plain',
		'html' => 'text/html',
		'csv' => 'application/csv'
	);
	protected function early_checks()
 	{
 	}
	// Constructor function
	public function __construct()
	{
		parent::__construct();
		ob_clean();
		// Lets grab the config and get ready to party
		$this->load->config('rest');
			if(!is_object($this->request))
         {
            $this->request= new stdClass();
         }
		// How is this request being made? POST, DELETE, GET, PUT?
		$this->request->method = $this->_detect_method();

		//$this->load->library('security');

		// Check if there is a specific auth type for the current class/method
		$this->auth_override = $this->_auth_override_check();

		// When there is no specific override for the current class/method, use the default auth value set in the config
		if ( $this->auth_override !== TRUE )
		{
			if ($this->config->item('rest_auth') == 'basic')
			{
				$this->_prepare_basic_auth();
			}
			elseif ($this->config->item('rest_auth') == 'digest')
			{
				$this->_prepare_digest_auth();
			}
		}

		// Some Methods cant have a body
		$this->request->body = NULL;

		switch ($this->request->method)
		{
			case 'get':
				// Grab proper GET variables
				parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $get);

				// If there are any, populate $this->_get_args
				empty($get) OR $this->_get_args = $get;
				break;

			case 'post':
				$this->_post_args = $_POST;

				// It might be a HTTP body
				$this->request->body = file_get_contents('php://input');
				break;

			case 'put':
				// It might be a HTTP body
				$this->request->body = file_get_contents('php://input');

				// Try and set up our PUT variables anyway in case its not
				parse_str($this->request->body, $this->_put_args);
				break;

			case 'delete':
				// Set up out PUT variables
				parse_str(file_get_contents('php://input'), $this->_delete_args);
				break;
		}

		// Set up our GET variables
		$this->_get_args = array_merge($this->_get_args, $this->uri->ruri_to_assoc());

		// Merge both for one mega-args variable
		$this->_args = array_merge($this->_get_args, $this->_put_args, $this->_post_args, $this->_delete_args);

		// Which format should the data be returned in?
		$this->request->format = $this->_detect_format();

		// Which format should the data be returned in?
		$this->request->lang = $this->_detect_lang();

		// Load DB if its enabled
		// if (config_item('rest_database_group') AND (config_item('rest_enable_keys') OR config_item('rest_enable_logging')))
		if (config_item('rest_use_mongodb') === TRUE)
		{
			// $this->rest->db = $this->load->database(config_item('rest_database_group'), TRUE);
			// Load MongoDB active record library
				$this->load->library('mongo_db');
		}else{
			
			if (config_item('rest_database_group') AND (config_item('rest_enable_keys') OR config_item('rest_enable_logging')))
			{
					if(!is_object($this->rest))
			 {
					$this->rest= new stdClass();
			 }
				$this->rest->db = $this->load->database(config_item('rest_database_group'), TRUE);
			}
			elseif (@$this->db)
			{
				$this->rest->db = $this->db;
			}
		}

		// Checking for keys? GET TO WORK!
		if (config_item('rest_enable_keys'))
		{
			$this->_allow = $this->_detect_api_key();
		}
		
		// only allow ajax requests
		if( ! $this->input->is_ajax_request() AND config_item('rest_ajax_only') )
		{
			$this->response( array('status' => false, 'error' => 'Only AJAX requests are accepted.'), 505 );
		}
	}

	/*
	 * Remap
	 *
	 * Requests are not made to methods directly The request will be for an "object".
	 * this simply maps the object and method to the correct Controller method.
	 */
	public function _remap($object_called)
	{
		$controller_method = $object_called . '_' . $this->request->method;

		// Do we want to log this method (if allowed by config)?
		$log_method = !(isset($this->methods[$controller_method]['log']) AND $this->methods[$controller_method]['log'] == FALSE);

		// Use keys for this method?
		$use_key = !(isset($this->methods[$controller_method]['key']) AND $this->methods[$controller_method]['key'] == FALSE);

		// Get that useless shitty key out of here
		if (config_item('rest_enable_keys') AND $use_key AND $this->_allow === FALSE)
		{
			$this->response(array('status' => false, 'error' => 'Invalid API Key .'), 403);
			//$this->response(array('status' => false, 'error' => $this->_allow), 403);
			return;
		}

		// Sure it exists, but can they do anything with it?
		if (!method_exists($this, $controller_method))
		{
			$this->response(array('status' => false, 'error' => 'Unknown method.'), 404);
			return;
		}

		// Doing key related stuff? Can only do it if they have a key right?
		if (config_item('rest_enable_keys') AND !empty($this->rest->key))
		{
			// Check the limit
			if (config_item('rest_enable_limits') AND !$this->_check_limit($controller_method))
			{
				$this->response(array('status' => false, 'error' => 'This API key has reached the hourly limit for this method.'), 401);
				return;
			}

			// If no level is set use 0, they probably aren't using permissions
			$level = isset($this->methods[$controller_method]['level']) ? $this->methods[$controller_method]['level'] : 0;

			// If no level is set, or it is lower than/equal to the key's level
			$authorized = $level <= $this->rest->level;

			// IM TELLIN!
			if (config_item('rest_enable_logging') AND $log_method)
			{
				$this->_log_request($authorized);
			}

			// They don't have good enough perms
			if (!$authorized)
			{
				$this->response(array('status' => false, 'error' => 'This API key does not have enough permissions.'), 401);
				return;
			}
		}

		// No key stuff, but record that stuff is happening
		else if (config_item('rest_enable_logging') AND $log_method)
		{
			$this->_log_request($authorized = TRUE);
		}

		// And...... GO!
		$this->$controller_method();
	}

	/*
	 * response
	 *
	 * Takes pure data and optionally a status code, then creates the response
	 */
	protected  function _log_response($data=null,$authorized = FALSE)
	{
		
		$log = array(
			'uri' => $this->uri->uri_string(),
			'method' => $this->request->method,
			'params' => $this->_args,
			'api_key' => isset($this->rest->key) ? $this->rest->key : '',
			'ip_address' => $this->input->ip_address(),
			'time' => function_exists('now') ? now() : time(),
			'date_transaction' => date("Y-m-d H:i:s.u A",microtime(true)),
			'authorized' => $authorized,
			'response' => $data,
		);
		return $this->mongo_db->insert(config_item('rest_reponse_collection'), $log);
		
	}
	public function response($data = array(), $http_code = null)
	{
		// If data is empty and not code provide, error and bail
		if (empty($data) && $http_code === null)
    	{
    		$http_code = 404;
    	}else
		{
			is_numeric($http_code) OR $http_code = 200;

			// If the format method exists, call and return the output in that format
			$data['transaction_id'] = getObjectId($this->_log_response($data,$authorized = TRUE));
			
			if (method_exists($this, '_format_'.$this->request->format))
			{
				// Set the correct format header
				header('Content-type: '.$this->_supported_formats[$this->request->format]);

				$formatted_data = $this->{'_format_'.$this->request->format}($data);
				$output = $formatted_data;
			}

			else
			{
				// $this->log_response($output);
				$output = $data;
				
			}
		}

		header('HTTP/1.1: ' . $http_code);
		header('Status: ' . $http_code);

		exit($output);
	}
	
	/*
	 * Detect format
	 *
	 * Detect which format should be used to output the data
	 */

	private function _detect_format()
	{
		$pattern = '/\.(' . implode('|', array_keys($this->_supported_formats)) . ')$/';

		// Check if a file extension is used
		if (preg_match($pattern, end($this->_get_args), $matches))
		{
			// The key of the last argument
			$last_key = end(array_keys($this->_get_args));

			// Remove the extension from arguments too
			$this->_get_args[$last_key] = preg_replace($pattern, '', $this->_get_args[$last_key]);
			$this->_args[$last_key] = preg_replace($pattern, '', $this->_args[$last_key]);

			return $matches[1];
		}

		// A format has been passed as an argument in the URL and it is supported
		if (isset($this->_args['format']) AND isset($this->_supported_formats))
		{
			return $this->_args['format'];
		}
		if ($this->config->item('rest_ignore_http_accept') === FALSE AND $this->input->server('HTTP_ACCEPT'))
		{
			foreach (array_keys($this->_supported_formats) as $format)
			{
				if (strpos($this->input->server('HTTP_ACCEPT'), $format) !== FALSE)
				{
					if ($format != 'html' AND $format != 'xml')
					{

						return $format;
					}
					else
					{
						if ($format == 'html' AND strpos($this->input->server('HTTP_ACCEPT'), 'xml') === FALSE)
						{
							return $format;
						}
						elseif ($format == 'xml' AND strpos($this->input->server('HTTP_ACCEPT'), 'html') === FALSE)
						{
							return $format;
						}
					}
				}
			}
		} 
		
		if (!empty($this->rest_format))
		{
			return $this->rest_format;
		}

		return config_item('rest_default_format');
	}

	
	private function _detect_method()
	{
		$method = strtolower($this->input->server('REQUEST_METHOD'));

		if ($this->config->item('enable_emulate_request') && $this->input->post('_method'))
		{
			$method =  $this->input->post('_method');
		}

		if (in_array($method, array('get', 'delete', 'post', 'put')))
		{
			return $method;
		}

		return 'get';
	}

	protected  function _api_key()
	{
		
		$key_name = 'HTTP_' . strtoupper(str_replace('-', '_', config_item('rest_key_name')));
		if(!is_object($this->rest))
		 {
				$this->rest= new stdClass();
		 }
		$this->rest->key = NULL;
		$this->rest->level = 0;
		$this->rest->ignore_limits = 0;
			$api_key_variable = config_item('rest_key_name');
		
		if ($key = isset($this->_args[$api_key_variable]) ? $this->_args[$api_key_variable] : $this->input->server($key_name))
		{
			if (config_item('rest_use_mongodb'))
 			{
				// $private_key = decrypt_obj($key,config_item('private_key'));
				$document = $this->mongo_db->where('key', $key)->get(config_item('rest_keys_collection'));
				if (empty($document))
				{
					return FALSE;
				}
				$row = (object) $document[0];
			}
			$this->rest->key = $row->key;
			$this->rest->level = (int)$row->level;
			$this->rest->ignore_limits = $row->ignore_limits;
			if($this->rest->level > 0 ){
				return $this->rest->key;
			}
			
		}
		return FALSE;
	}
	protected  function _detect_api_key()
	{
		
		$key_name = 'HTTP_' . strtoupper(str_replace('-', '_', config_item('rest_key_name')));
		if(!is_object($this->rest))
		 {
				$this->rest= new stdClass();
		 }
		$this->rest->key = NULL;
		$this->rest->level = 0;
		$this->rest->ignore_limits = 0;
			$api_key_variable = config_item('rest_key_name');
		
		if ($key = isset($this->_args[$api_key_variable]) ? $this->_args[$api_key_variable] : $this->input->server($key_name))
		{
			if (config_item('rest_use_mongodb'))
 			{
				// $private_key = decrypt_obj($key,config_item('private_key'));
				$document = $this->mongo_db->where('key', $key)->get(config_item('rest_keys_collection'));
				if (empty($document))
				{
					return FALSE;
				}
				$row = (object) $document[0];
			}
			$this->rest->key = $row->key;
			$this->rest->level = (int)$row->level;
			$this->rest->ignore_limits = $row->ignore_limits;
			if($this->rest->level > 0 ){
				return TRUE;
			}
			
		}
		return FALSE;
	}


	private function _detect_lang()
	{
		if ( ! $lang = $this->input->server('HTTP_ACCEPT_LANGUAGE'))
		{
			return NULL;
		}

		// They might have sent a few, make it an array
		if (strpos($lang, ',') !== FALSE)
		{
			$langs = explode(',', $lang);

			$return_langs = array();
			$i = 1;
			foreach ($langs as $lang)
			{
				// Remove weight and strip space
				list($lang) = explode(';', $lang);
				$return_langs[] = trim($lang);
			}

			return $return_langs;
		}

		// Nope, just return the string
		return $lang;
	}

	/*
	 * Log request
	 *
	 * Record the entry for awesomeness purposes
	 */

	protected  function _log_request($authorized = FALSE)
	{
		
		$log = array(
			'uri' => $this->uri->uri_string(),
			'method' => $this->request->method,
			'params' => $this->_args,
			'api_key' => isset($this->rest->key) ? $this->rest->key : '',
			'ip_address' => $this->input->ip_address(),
			'time' => function_exists('now') ? now() : time(),
			'date_create' => date("Y-m-d H:i:s",time()),
			'authorized' => $authorized,
		);
		$this->mongo_db->insert(config_item('rest_logs_collection'), $log);
	}

	/*
	 * Log request
	 *
	 * Record the entry for awesomeness purposes
	 */

	private function _check_limit($controller_method)
	{
		// They are special, or it might not even have a limit
		if (!empty($this->rest->ignore_limits) OR !isset($this->methods[$controller_method]['limit']))
		{
			// On your way sonny-jim.
			return TRUE;
		}

		// How many times can you get to this method an hour?
		$limit = $this->methods[$controller_method]['limit'];
		if (config_item('rest_use_mongodb'))
		{
			$result = $this->mongo_db
				->where('uri', $this->uri->uri_string())
				->where('api_key', $this->rest->key)
				->get(config_item('rest_limits_collection'));
			$result = (object) $result[0];
		}
		// No calls yet, or been an hour since they called
		if (!$result OR $result->hour_started < time() - (60 * 60))
		{
			
			$data = array(
 				'uri' => $this->uri->uri_string(),
 				'api_key' => isset($this->rest->key) ? $this->rest->key : '',
 				'count' => 1,
 				'hour_started' => time()
			);
			$this->mongo_db->insert(config_item('rest_limits_collection'), $data);
				
		}
		// They have called within the hour, so lets update
		else
		{
			// Your luck is out, you've called too many times!
			if ($result->count > $limit){return FALSE; }
			$this->mongo_db->where('uri', $this->uri->uri_string())
				->where('api_key', $this->rest->key)->inc('count', 1)
				->update(config_item('rest_limits_collection'));
		}

		return TRUE;
	}
	/*
	 * Auth override check
	 *
	 * Check if there is a specific auth type set for the current class/method being called
	 */

	private function _auth_override_check()
	{

		// Assign the class/method auth type override array from the config
		$this->overrides_array = $this->config->item('auth_override_class_method');

		// Check to see if the override array is even populated, otherwise return false
		if ( empty($this->overrides_array) )
		{
			return false;
		}

		// Check to see if there's an override value set for the current class/method being called
		if ( empty($this->overrides_array[$this->router->class][$this->router->method]) )
		{
			return false;
		}

		// None auth override found, prepare nothing but send back a true override flag
		if ($this->overrides_array[$this->router->class][$this->router->method] == 'none')
		{
			return true;
		}

		// Basic auth override found, prepare basic
		if ($this->overrides_array[$this->router->class][$this->router->method] == 'basic')
		{
			$this->_prepare_basic_auth();
			return true;
		}

		// Digest auth override found, prepare digest
		if ($this->overrides_array[$this->router->class][$this->router->method] == 'digest')
		{
			$this->_prepare_digest_auth();
			return true;
		}

		// Return false when there is an override value set but it doesn't match 'basic', 'digest', or 'none'.  (the value was misspelled)
		return false;

	}


	// INPUT FUNCTION --------------------------------------------------------------

	public function get($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_get_args;
		}

		return array_key_exists($key, $this->_get_args) ? $this->_xss_clean($this->_get_args[$key], $xss_clean) : FALSE;
	}

	public function post($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_post_args;
		}

		return $this->input->post($key, $xss_clean);
	}

	public function put($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_put_args;
		}

		return array_key_exists($key, $this->_put_args) ? $this->_xss_clean($this->_put_args[$key], $xss_clean) : FALSE;
	}

	public function delete($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_delete_args;
		}

		return array_key_exists($key, $this->_delete_args) ? $this->_xss_clean($this->_delete_args[$key], $xss_clean) : FALSE;
	}

	private function _xss_clean($val, $bool)
	{
		if (CI_VERSION < 2)
		{
			return $bool ? $this->input->xss_clean($val) : $val;
		}
		else
		{
			return $bool ? $this->security->xss_clean($val) : $val;
		}
	}

	public function validation_errors()
	{
		$string = strip_tags($this->form_validation->error_string());

		return explode("\n", trim($string, "\n"));
	}

	// SECURITY FUNCTIONS ---------------------------------------------------------

	private function _check_login($username = '', $password = NULL)
	{
		if (empty($username))
		{
			return FALSE;
		}

		$valid_logins = & $this->config->item('rest_valid_logins');

		if (!array_key_exists($username, $valid_logins))
		{
			return FALSE;
		}

		// If actually NULL (not empty string) then do not check it
		if ($password !== NULL AND $valid_logins[$username] != $password)
		{
			return FALSE;
		}

		return TRUE;
	}

	private function _prepare_basic_auth()
	{
		$username = NULL;
		$password = NULL;

		// mod_php
		if ($this->input->server('PHP_AUTH_USER'))
		{
			$username = $this->input->server('PHP_AUTH_USER');
			$password = $this->input->server('PHP_AUTH_PW');
		}

		// most other servers
		elseif ($this->input->server('HTTP_AUTHENTICATION'))
		{
			if (strpos(strtolower($this->input->server('HTTP_AUTHENTICATION')), 'basic') === 0)
			{
				list($username, $password) = explode(':', base64_decode(substr($this->input->server('HTTP_AUTHORIZATION'), 6)));
			}
		}

		if (!$this->_check_login($username, $password))
		{
			$this->_force_login();
		}
	}

	private function _prepare_digest_auth()
	{
		$uniqid = uniqid(""); // Empty argument for backward compatibility
		// We need to test which server authentication variable to use
		// because the PHP ISAPI module in IIS acts different from CGI
		if ($this->input->server('PHP_AUTH_DIGEST'))
		{
			$digest_string = $this->input->server('PHP_AUTH_DIGEST');
		}
		elseif ($this->input->server('HTTP_AUTHORIZATION'))
		{
			$digest_string = $this->input->server('HTTP_AUTHORIZATION');
		}
		else
		{
			$digest_string = "";
		}

		/* The $_SESSION['error_prompted'] variabile is used to ask
		  the password again if none given or if the user enters
		  a wrong auth. informations. */
		if (empty($digest_string))
		{
			$this->_force_login($uniqid);
		}

		// We need to retrieve authentication informations from the $auth_data variable
		preg_match_all('@(username|nonce|uri|nc|cnonce|qop|response)=[\'"]?([^\'",]+)@', $digest_string, $matches);
		$digest = array_combine($matches[1], $matches[2]);

		if (!array_key_exists('username', $digest) OR !$this->_check_login($digest['username']))
		{
			$this->_force_login($uniqid);
		}

		$valid_logins = & $this->config->item('rest_valid_logins');
		$valid_pass = $valid_logins[$digest['username']];

		// This is the valid response expected
		$A1 = md5($digest['username'] . ':' . $this->config->item('rest_realm') . ':' . $valid_pass);
		$A2 = md5(strtoupper($this->request->method) . ':' . $digest['uri']);
		$valid_response = md5($A1 . ':' . $digest['nonce'] . ':' . $digest['nc'] . ':' . $digest['cnonce'] . ':' . $digest['qop'] . ':' . $A2);

		if ($digest['response'] != $valid_response)
		{
			header('HTTP/1.0 401 Unauthorized');
			header('HTTP/1.1 401 Unauthorized');
			exit;
		}
	}

	private function _force_login($nonce = '')
	{
		header('HTTP/1.0 401 Unauthorized');
		header('HTTP/1.1 401 Unauthorized');

		if ($this->config->item('rest_auth') == 'basic')
		{
			header('WWW-Authenticate: Basic realm="' . $this->config->item('rest_realm') . '"');
		}
		elseif ($this->config->item('rest_auth') == 'digest')
		{
			header('WWW-Authenticate: Digest realm="' . $this->config->item('rest_realm') . '" qop="auth" nonce="' . $nonce . '" opaque="' . md5($this->config->item('rest_realm')) . '"');
		}

		exit('Not authorized.');
	}

	// Force it into an array
	private function _force_loopable($data)
	{
		// Force it to be something useful
		if ( ! is_array($data) AND ! is_object($data))
		{
			$data = (array) $data;
		}

		return $data;
	}

	// FORMATING FUNCTIONS ---------------------------------------------------------
	// Format XML for output
	private function _format_xml($data = array(), $structure = NULL, $basenode = 'xml')
	{
		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		{
			ini_set('zend.ze1_compatibility_mode', 0);
		}

		if ($structure == NULL)
		{
			$structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
		}

		// loop through the data passed in.
		$data = $this->_force_loopable($data);
		foreach ($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				//$key = "item_". (string) $key;
				$key = "item";
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z_]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value) OR is_object($value))
			{
				$node = $structure->addChild($key);
				// recrusive call.
				$this->_format_xml($value, $node, $basenode);
			}
			else
			{
				// Actual boolean values need to be converted to numbers
				is_bool($value) AND $value = (int) $value;

				// add single node.
				$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");

				$UsedKeys[] = $key;

				$structure->addChild($key, $value);
			}
		}

		// pass back as string. or simple xml object if you want!
		return $structure->asXML();
	}

	// Format Raw XML for output
	private function _format_rawxml($data = array(), $structure = NULL, $basenode = 'xml')
	{
		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		{
			ini_set('zend.ze1_compatibility_mode', 0);
		}

		if ($structure == NULL)
		{
			$structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
		}

		// loop through the data passed in.
		$data = $this->_force_loopable($data);
		foreach ($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				//$key = "item_". (string) $key;
				$key = "item";
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z0-9_-]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value) OR is_object($value))
			{
				$node = $structure->addChild($key);
				// recrusive call.
				$this->_format_rawxml($value, $node, $basenode);
			}
			else
			{
				// Actual boolean values need to be converted to numbers
				is_bool($value) AND $value = (int) $value;

				// add single node.
				$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");

				$UsedKeys[] = $key;

				$structure->addChild($key, $value);
			}
		}

		// pass back as string. or simple xml object if you want!
		return $structure->asXML();
	}

	// Format HTML for output
	private function _format_html($data = array())
	{
		// Multi-dimentional array
		if (isset($data[0]))
		{
			$headings = array_keys($data[0]);
		}

		// Single array
		else
		{
			$headings = array_keys($data);
			$data = array($data);
		}

		$this->load->library('table');

		$this->table->set_heading($headings);

		foreach ($data as &$row)
		{
			$this->table->add_row($row);
		}

		return $this->table->generate();
	}

	// Format HTML for output
	private function _format_csv($data = array())
	{
		// Multi-dimentional array
		if (isset($data[0]))
		{
			$headings = array_keys($data[0]);
		}

		// Single array
		else
		{
			$headings = array_keys($data);
			$data = array($data);
		}

		$output = implode(',', $headings) . "\r\n";
		foreach ($data as &$row)
		{
			$output .= '"' . implode('","', $row) . "\"\r\n";
		}

		return $output;
	}

	// Encode as JSON
	private function _format_json($data = array())
	{
		return json_encode($data);
	}

	// Encode as JSONP
	private function _format_jsonp($data = array())
	{
		return $this->get('callback') . '(' . json_encode($data) . ')';
	}

	// Encode as Serialized array
	private function _format_serialize($data = array())
	{
		return serialize($data);
	}

	// Encode raw PHP
	private function _format_php($data = array())
	{
		return var_export($data, TRUE);
	}
}

