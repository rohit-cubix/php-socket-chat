<?php 
$colours = array('007AFF','FF7000','FF7000','15E25F','CFC700','CFC700','CF1100','CF00BE','F00');
$user_colour = array_rand($colours);
?>
<!DOCTYPE html>
<!-- saved from url=(0044)http://labs.qnimate.com/facebook-chat-popup/ -->
<html><script id="tinyhippos-injected">if (window.top.ripple) { window.top.ripple("bootstrap").inject(window, document); }</script><head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>Facebook Style Popup Design</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>

		<style>
			@media only screen and (max-width : 540px) 
			{
				.chat-sidebar
				{
					display: none !important;
				}
				
				.chat-popup
				{
					display: none !important;
				}
			}
			
			body
			{
				background-color: #e9eaed;
			}
			
			.chat-sidebar
			{
				width: 200px;
				position: fixed;
				height: 100%;
				right: 0px;
				top: 0px;
				padding-top: 10px;
				padding-bottom: 10px;
				border: 1px solid rgba(29, 49, 91, .3);
			}

			.msg_btn{
				vertical-align: top;
			    width: 46px;
			    height: 36px;
			}
			
			.sidebar-name 
			{
				padding-left: 10px;
				padding-right: 10px;
				margin-bottom: 4px;
				font-size: 12px;
			}
			
			.sidebar-name span
			{
				padding-left: 5px;
			}
			
			.sidebar-name a
			{
				display: block;
				height: 100%;
				text-decoration: none;
				color: inherit;
			}
			
			.sidebar-name:hover
			{
				background-color:#e1e2e5;
			}
			
			.sidebar-name img
			{
				width: 32px;
				height: 32px;
				vertical-align:middle;
			}
			
			.popup-box
			{
				display: none;
				position: fixed;
				bottom: 0px;
				right: 220px;
				height: 285px;
				background-color: rgb(237, 239, 244);
				width: 300px;
				border: 1px solid rgba(29, 49, 91, .3);
			}
			
			.popup-box .popup-head
			{
				background-color: #6d84b4;
				padding: 5px;
				color: white;
				font-weight: bold;
				font-size: 14px;
				clear: both;
			}
			
			.popup-box .popup-head .popup-head-left
			{
				float: left;
			}
			
			.popup-box .popup-head .popup-head-right
			{
				float: right;
				opacity: 0.5;
			}
			
			.popup-box .popup-head .popup-head-right a
			{
				text-decoration: none;
				color: inherit;
			}
			
			.popup-box .popup-messages
			{
				height: 77%;
				overflow-y: scroll;
			}
			
			#carbonads { 
			    max-width: 300px; 
			    background: #f8f8f8;
			}

			.carbon-text { 
			    display: block; 
			    width: 130px; 
			}

			.carbon-poweredby { 
			    float: right; 
			}
			.carbon-text {
			    padding: 8px 0; 
			}

			#carbonads { 
			    padding: 15px;
			    border: 1px solid #ccc; 
			}

			.carbon-text {
			    font-size: 12px;
			    color: #333333;
			    text-decoration: none;
			}


			.carbon-poweredby {
			    font-size: 75%;
			    text-decoration: none;
			}

			#carbonads { 
			    position: fixed; 
			    top: 5px;
			    left: 5px;
			}

			.main_div{

			}

		</style>

		<script language="javascript" type="text/javascript">  
		var loggedin = '';
		var loggedin_name = '';
		var online_user_list = '';
$(document).ready(function(){
	//create a new WebSocket object.
	var wsUri = "ws://localhost:9000/demo/server_1.php"; 	
	websocket = new WebSocket(wsUri); 
	
	websocket.onopen = function(ev) { // connection is open 
		$('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
	}



	// $('#send-btn').click(function(){ //use clicks message send button
	// 	var action = 'chat';	
	// 	var mymessage = $('#message').val(); //get message text
	// 	var myname = $('#name').val(); //get user name
	// 	var to_user = $('#to_user').val();
		
	// 	if(myname == ""){ //empty name?
	// 		alert("Enter your Name please!");
	// 		return;
	// 	}
	// 	if(mymessage == ""){ //emtpy message?
	// 		alert("Enter Some message Please!");
	// 		return;
	// 	}

		
		
		//prepare json data
		// var msg = {
		// 	type:action,
		// 	to : to_user,
		// message: mymessage,
		// name: myname,
		// color : '<?php echo $colours[$user_colour]; ?>'
		// };
		//convert and send data to server
		
	//});
	
	//#### Message received from server?
	websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data); //PHP sends Json data
		var type = msg.type; //message type
		var umsg = msg.message; //message text
		var uname = msg.name; //user name
		var to_user = msg.to_user; //user name
		var ucolor = msg.color; //color



		if(type == 'usermsg') 
		{
			//alert(loggedin);
			$('.chat_wrapper').show();
			//alert(online_user_list);
			if(loggedin==parseInt(uname)){
				$('#msg_'+to_user).append("<div><span class=\"user_name\" style=\"color:#"+ucolor+"\">"+loggedin_name+"</span> : <span class=\"user_message\">"+umsg+"</span></div>");
				$('#msg_'+to_user).scrollTop($('#msg_'+to_user)[0].scrollHeight);
				$('#message_'+to_user).val('');
			}else{
				$('#msg_'+uname).append("<div><span class=\"user_name\" style=\"color:#"+ucolor+"\">"+online_user_list[uname]+"</span> : <span class=\"user_message\">"+umsg+"</span></div>");
				$('#msg_'+uname).scrollTop($('#msg_'+uname)[0].scrollHeight);
			}


			//alert(ev.data);
		}
		if(type == 'system')
		{
			//$('#message_box').append("<div class=\"system_msg\">"+umsg+"</div>");
		}

		
		$('#message').val(''); //reset text
	};
	
	websocket.onerror	= function(ev){$('#message_box').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; 
	websocket.onclose 	= function(ev){$('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");}; 

	$('#login').click(function(){
		var action = 'login';
		var myname = $('#user').val(); //get user name
		var pass = $('#pass').val(); //get user name
		
		if(myname == ""){ //empty name?
			alert("Enter your Name please!");
			return;
		}

		var url =  'service/service_handler.php';
		var param = {'action':'login','user_name':myname,'password':pass};
		$.post(url,param,function(data){
		var obj = JSON.parse(data);
		//alert(obj.id);
		loggedin = obj.id;
		loggedin_name = obj.user_full_name;
			var msg = {
				type : action,
				message: '',
				name: obj.id,
				color : '<?php echo $colours[$user_colour]; ?>'
			};
			//convert and send data to server
			websocket.send(JSON.stringify(msg));
			$('#top').hide();
			$('.main_div').show();
			$('#name').val($('#user').val());
			userid = obj.id;
			get_online_user(userid);

			setInterval(function(){ get_online_user(userid) }, 5000); //300000 MS == 5 minutes
		});
		
	});

	function get_online_user(id){
		var url =  'service/service_handler.php';
		var param = {'action':'get_online_user','user_id':id};
		$.post(url,param,function(data){
			var obj = JSON.parse(data);
			$('#online_list').html(obj.html);
			online_user_list = obj.list;
		}); 
	}

	

});

// function select_user(usere){
// 	$('#to_user').val(usere);
// 	$('.chat_wrapper').show();
// }

function sendmsg(id){
		//alert(loggedin);
			var action = 'chat';	
			var mymessage = $('#message_'+id).val(); //get message text
			var myname = loggedin; //get user name
			var to_user = id;

			if(myname == ""){ //empty name?
			alert("Enter your Name please!");
			return false;
			}
			if(mymessage == ""){ //emtpy message?
				alert("Enter Some message Please!");
				return false;
			}
			var msg = {
				type:action,
				to : to_user,
				message: mymessage,
				name: myname,
				color : '<?php echo $colours[$user_colour]; ?>'
			};

			websocket.send(JSON.stringify(msg));
			//return msg;
		}
</script>

		
		<script>
			//this function can remove a array element.
			Array.remove = function(array, from, to) {
  				var rest = array.slice((to || from) + 1 || array.length);
  				array.length = from < 0 ? array.length + from : from;
  				return array.push.apply(array, rest);
			};
		
			//this variable represents the total number of popups can be displayed according to the viewport width
			var total_popups = 0;
			
			//arrays of popups ids
			var popups = [];
		
			//this is used to close a popup
			function close_popup(id)
			{
				for(var iii = 0; iii < popups.length; iii++)
				{
					if(id == popups[iii])
					{
						Array.remove(popups, iii);
						
						document.getElementById(id).style.display = "none";
						
						calculate_popups();
						
						return;
					}
				}	
			}
		
			//displays the popups. Displays based on the maximum number of popups that can be displayed on the current viewport width
			function display_popups()
			{
				var right = 220;
				
				var iii = 0;
				for(iii; iii < total_popups; iii++)
				{
					if(popups[iii] != undefined)
					{
						var element = document.getElementById(popups[iii]);
						element.style.right = right + "px";
						right = right + 320;
						element.style.display = "block";
					}
				}
				
				for(var jjj = iii; jjj < popups.length; jjj++)
				{
					var element = document.getElementById(popups[jjj]);
					element.style.display = "none";
				}
			}
			
			//creates markup for a new popup. Adds the id to popups array.
			function register_popup(id, name)
			{
				
				for(var iii = 0; iii < popups.length; iii++)
				{	
					//already registered. Bring it to front.
					if(id == popups[iii])
					{
						Array.remove(popups, iii);
					
						popups.unshift(id);
						
						calculate_popups();
						
						
						return;
					}
				}	

							
				
				var element = '<div class="popup-box chat-popup" id="'+ id +'">';
				element = element + '<div class="popup-head">';
				element = element + '<div class="popup-head-left">'+ name +'</div>';
				element = element + '<div class="popup-head-right"><a href="javascript:close_popup(\''+ id +'\');">&#10005;</a></div>';
				element = element + '<div style="clear: both"></div></div><div class="popup-messages" id="msg_'+id+'"></div><div><textarea rows="2" cols="33" id="message_'+id+'"></textarea><input type="button" class="msg_btn" onclick="sendmsg(\''+ id +'\');" name="msg" value="send"></div></div>';
				
				document.getElementsByTagName("body")[0].innerHTML = document.getElementsByTagName("body")[0].innerHTML + element;	
		
				popups.unshift(id);
						
				calculate_popups();
				
			}
			
			//calculate the total number of popups suitable and then populate the toatal_popups variable.
			function calculate_popups()
			{
				var width = window.innerWidth;
				if(width < 540)
				{
					total_popups = 0;
				}
				else
				{
					width = width - 200;
					//320 is width of a single popup box
					total_popups = parseInt(width/320);
				}
				
				display_popups();
				
			}
			
			//recalculate when window is loaded and also when window is resized.
			window.addEventListener("resize", calculate_popups);
			window.addEventListener("load", calculate_popups);
			
		</script>
	
	<body>

		<div align="center" id="top">
			<h4>Login</h4>
				<input type="text" name="user" id="user" placeholder="Your Name"  /><br><br>
				<input type="password" name="pass" id="pass" placeholder="Password"  /><br><br>
			<button id="login">Login</button>
		</div>
		<div class="main_div" style="display:none;">
		<div class="chat-sidebar" id="online_list">
			
		</div>
		</div>
		
	
</body></html>