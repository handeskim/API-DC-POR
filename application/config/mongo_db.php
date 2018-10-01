<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$primary = '127.0.0.1';
$secondary = '127.0.0.1'; 
$username = "root";
$password = "";
$database = "";
$port = "port";
$dns = 'mongodb://'.$primary.':'.$port.'/'.$database;
try{
	$connect =  new MongoClient("mongodb://".$primary.":27017/".$database, array("username" => $username, "password" => $password));
	$config['mongo_db']['primary']['hostname'] = $primary;
}catch(MongoConnectionException $e){ $config['mongo_db']['primary']['hostname'] = $secondary; }
$config['mongo_db']['active'] = 'primary';
$config['mongo_db']['primary']['no_auth'] = TRUE;
$config['mongo_db']['primary']['port'] = '27017';
$config['mongo_db']['primary']['username'] = 'root';
$config['mongo_db']['primary']['password'] = '1234fF@#@@';
$config['mongo_db']['primary']['database'] =	'db_doicard';
$config['mongo_db']['primary']['db_debug'] = TRUE;
$config['mongo_db']['primary']['return_as'] = 'array';
$config['mongo_db']['primary']['write_concerns'] = (int)1;
$config['mongo_db']['primary']['journal'] = TRUE;
$config['mongo_db']['primary']['read_preference'] = NULL;
$config['mongo_db']['primary']['read_preference_tags'] = NULL;
