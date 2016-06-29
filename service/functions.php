<?php
function get_online_user($id){
	global $db;
	$db->join("tbl_online_users olu", "u.id=olu.user_id", "LEFT");
	$db->where('user_id!='.$id);
	$users = $db->get("tbl_users u", null, "u.id , u.user_full_name, olu.server_id");
	$str = '';
	$user_list = array();
	foreach ($users as $key => $value) {
		$user_list[$value['id']] = ucfirst($value['user_full_name']);
		$str .= '<div class="sidebar-name">
				<a href="javascript:register_popup(\''.$value['id'].'\', \''.$value['user_full_name'].'\');">
					<img width="30" height="30" src="http://www.southwickgrouprealestate.com/wp-content/themes/southwick/images/icon-user.png">
					<span>'.ucfirst($value['user_full_name']).'</span>
				</a>
			</div>';
		//$str .= '<span onclick="select_user(\''.$value['id'].'\')">'.$value['user_full_name'].'</span><br>';
	}
	return json_encode(array('html'=>$str,'list'=>$user_list));
}

function login($user_name,$password){
	global $db;
	$db->where ('user_name', $user_name);
	$db->where ('password', $password);
	$user = $db->getOne('tbl_users');
	if(!empty($user)){
		return json_encode($user);
	}else{
		return false;
	}
}
?>