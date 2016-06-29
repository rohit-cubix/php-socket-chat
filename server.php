<?php
//ini_set('max_execution_time', 30000);
//error_reporting(0);
include 'includes/define_var.php';
include 'includes/MysqliDb.php';

$db = new MysqliDb (Array (
                'host' => DB_HOST,
                'username' => DB_USER, 
                'password' => DB_PASS,
                'db'=> DB_NAME,
                'port' => 3306));

//Create TCP/IP sream socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

//echo $socket;
//reuseable port
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

//bind socket to specified host
socket_bind($socket, 0, PORT);

//listen to port
socket_listen($socket);

$uniqu = time().rand(000,99999999999999).base64_encode(rand(1245,999999999));
//create & add listning socket to the list
$clients = array($uniqu=>$socket);
$u_info = array($uniqu=>$socket);

//start endless loop, so that our script doesn't stop
while (true) {

	if(!$db){
		$mysqli = new mysqli (DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$db = new MysqliDb ($mysqli);
	}
	//if(!empty($clients)){
	//manage multipal connections
	$changed = $clients;
	//returns the socket resources in $changed array
	socket_select($changed, $null, $null, 0, 10);
	
	//check for new socket
	if (in_array($socket, $changed)) {
		$uniqu = time().rand(000,99999999999999).base64_encode(rand(1245,999999999));
		$socket_new = socket_accept($socket); //accpet new socket
		$clients[$uniqu] = $socket_new; //add socket to client array
		//echo json_encode($clients);
		$header = socket_read($socket_new, 1024); //read data sent by the socket
		perform_handshaking($header, $socket_new, HOST, PORT); //perform websocket handshake
		
		socket_getpeername($socket_new, $ip); //get ip address of connected socket
		//echo json_encode(array('type'=>'system', 'message'=>$ip.' connected'))."\n";
		$response = mask(json_encode(array('type'=>'system','message'=>$ip.' connected'))); //prepare json data
		send_message($response); //notify all users about new connection
		
		//make room for new socket
		$found_socket = array_search($socket, $changed);
		unset($changed[$found_socket]);
	}
	
	//loop through all connected sockets
	foreach ($changed as $t=>$changed_socket) {	
		if($t!=0){
		//check for any incomming data
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
		{
			$received_text = unmask($buf); //unmask data
			//echo $received_text;
			$tst_msg = json_decode($received_text); //json decode 
			$user_name = $tst_msg->name; //sender name
			$type = $tst_msg->type; //sender name
			
			$user_message = $tst_msg->message; //message text
			$user_color = $tst_msg->color; //color
			
			if($type=='chat'){
				echo $to = $tst_msg->to; //sender name
				echo json_encode(array('type'=>'usermsg', 'to_user'=>$to, 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color));
				$response_text = mask(json_encode(array('type'=>'usermsg', 'to_user'=>$to, 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color)));
				// $data = Array ("from" => $found_socket,
				//                "to" => $user_name,
				//                "message" => $user_message
				//                );
				// $id = $db->insert ('tbl_online_users', $data);
				//prepare data to be sent to client
				//echo json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color))."\n";
				send_message($response_text,$to,$changed_socket); //send data
			}else{
				$found_socket = array_search($changed_socket, $clients);
				//echo $found_socket;
				$u_info[$found_socket]  = $user_name;
				$data = Array("server_id" => $found_socket,
				               "user_id" => $user_name
				               );
				$id = $db->insert ('tbl_online_users', $data);
				//print_r($u_info);
			}
			break 2; //exist this loop
		}
		
		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
		if ($buf === false) { // check disconnected client
			// remove client for $clients array
			//print_r($clients);
			$found_socket = array_search($changed_socket, $clients);
			echo $found_socket;
			socket_getpeername($changed_socket, $ip);
			print_r($u_info);
			if(!$db){
				$mysqli = new mysqli (DB_HOST, DB_USER, DB_PASS, DB_NAME);
				$db = new MysqliDb ($mysqli);
			}
			$db->where('server_id', $found_socket);
			$db->delete('tbl_online_users');
			unset($clients[$found_socket],$u_info[$found_socket]);
			print_r($u_info);
			//unset();
			//print_r($clients);
			//notify all users about disconnected connection
			$response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
			send_message($response);
		}
	}
	}
//}
}
// close the listening socket
socket_close($socket);

function send_message($msg,$to='',$from='')
{
	global $clients;
	global $u_info;
	//print_r($clients);
	if($to!=''){
		$found_socket = array_search($to, $u_info);
		@socket_write($clients[$found_socket],$msg,strlen($msg));
		@socket_write($from,$msg,strlen($msg));
	}else{
		foreach($clients as $k=>$changed_socket)
		{
			if($k!=0){
				@socket_write($changed_socket,$msg,strlen($msg));
			}
		}
	}
	return true;
}


//Unmask incoming framed message
function unmask($text) {
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

//Encode message for transfer to client.
function mask($text)
{
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);
	
	if($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCNN', $b1, 127, $length);
	return $header.$text;
}

//handshake new client.
function perform_handshaking($receved_header,$client_conn, $host, $port)
{
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line)
	{
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
		{
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: ".HOST."\r\n" .
	"WebSocket-Location: ws://".HOST.":".PORT."/demo/shout.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
}
