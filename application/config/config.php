<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['base_url'] = '';
$config['index_page'] = '';
$config['uri_protocol']	= 'AUTO';
$config['url_suffix'] = '.html';
$config['language']	= 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = TRUE;
$config['subclass_prefix'] = 'MY_';

$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['allow_get_array']		= TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger']	= 'c';
$config['function_trigger']		= 'm';
$config['directory_trigger']	= 'd'; 
$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_date_format'] = 'd-M-Y H:i:s';
$config['cache_path'] = '';
$config['encryption_key'] = 'MIIJRAIBADANBgkqhkiG9w0BAQEFAASCCS4wggkqAgEAAoICAQCs4hvT5V6LXEql';
$config['sess_cookie_name']		= 'hk_session';
$config['sess_expiration']		= 86400;
$config['sess_expire_on_close']	= TRUE;
$config['sess_encrypt_cookie']	= TRUE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= 'hk_session';
$config['sess_match_ip']		= TRUE;
$config['sess_match_useragent']	= TRUE;
$config['sess_time_to_update']	= 86400;

$config['cookie_prefix']	= "hk_";
$config['cookie_domain']	= "";
$config['cookie_path']		= "/";
$config['cookie_secure']	= TRUE;
$config['global_xss_filtering'] = TRUE;

$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_hk_token';
$config['csrf_cookie_name'] = 'csrf_hk_name';
$config['csrf_regenerate'] = TRUE;
$config['csrf_expire'] = 86400;
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = TRUE;
$config['proxy_ips'] = '';
$config['restore_key'] = 'hk@123';

