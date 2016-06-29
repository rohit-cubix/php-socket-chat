<?php 
include '../includes/define_var.php';
include '../includes/MysqliDb.php';
include 'functions.php';

$db = new MysqliDb (Array (
                'host' => DB_HOST,
                'username' => DB_USER, 
                'password' => DB_PASS,
                'db'=> DB_NAME,
                'port' => 3306));

extract($_REQUEST);

switch($action){
	case 'get_online_user':
		$data = get_online_user($user_id);
	break;
	case 'login':
		$data = login($user_name,$password);
	break;
	case '':
	break;
	case '':
	break;
	case '':
	break;
	case '':
	break;
}

echo $data;
?>