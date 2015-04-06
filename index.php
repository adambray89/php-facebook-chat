<?php
// Load config.php
include_once('includes/config.php');

// Autoload classes
spl_autoload_register( function($class) {
	include_once('includes/'.strtolower($class).'.php');
});

// Instigate classes
$db 		= new Database($mysql_config);
$session 	= new Session();
$token		= new Token($session);
$chat 		= new Chat($db, $token, $session);

// Post messages
$post_action = (isset($_POST['action'])) ? $_POST['action'] : '';

if($post_action === 'chat_post') {
	try {
		print $chat->addMessage($_POST['name'],$_POST['message'],$_POST['token']);
	}
	catch(Exception $e) {
		print '<li><strong>Error:</strong> <span style="color:red;">'.$e->getMessage().'</span></li>';
	}
	return;
}

// Build content
$page = (isset($_GET['page'])) ? $_GET['page'] : '';

if($page === 'load_chat') {
	try {
		print $chat->getPosts();
	}
	catch(Exception $e) {
		print '<li><strong>Error:</strong> <span style="color:red;">'.$e->getMessage().'</span></li>';
	}
	return;
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Chatbox</title>
<!--META TAGS-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Adam Bray, about, Chelmsford, Essex, London, Web Design, Lawnch.me, personal blog" /> 
<meta name="description" content="Blog posts from Adam Bray. Learn CSS, HTML and PHP skills, or simply read what he has to say." />
<meta name="author" content="Adam Bray" />
<meta name="copyright" content="&copy; Adam Bray 2014" />
<!--open graph-->
<meta property="og:title" content="Web Design Blog - Adam Bray"/>
<meta property="og:description" content="Blog posts from Adam Bray. Learn CSS, HTML and PHP skills, or simply read what he has to say."/>
<!--other-->
<meta name="viewport" content="width=device-width">
<link rel="shortcut icon" href="http://www.adam-bray.com/favicon.ico">
<link rel="search" type="application/opensearchdescription+xml" href="/opensearch.xml" title="Adam Bray Search">
<link rel="author" href="https://plus.google.com/+AdamBray/">
<link rel="alternate" type="application/rss+xml" title="Adam Bray - Blog Feed" href="http://www.adam-bray.com/blog/feed/" />
<link href='http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic' rel='stylesheet' type='text/css'>
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	
	// post new messages
	$('#chat_message').keypress(function(e) {
		if(e.which == 13) {
			
			e.preventDefault();
			
			var message		= $('#chat_message').val();
			var name		= $('#chat_name').val();
			var token 		= $('#chat_token').val();
			
			data_array = {
				'action':'chat_post',
				'message':message,
				'name':name,
				'token':token,
				'type':'ajax'
			};
			
			$.post('index.php', data_array, function(res) {
				$(res).hide().appendTo('.chatArea ul').fadeIn(400);
				
				scrollChatBottom();
				
				$('#chat_message').val('');
				$('#chat_name').attr('readonly','true');
			}).fail(function(error) { 
				alert(error.statusText);
			});
		}
	});
	
	// load the messages when the page is ready
	$.get('index.php',{'page':'load_chat'},function(data) {
		$('.chatArea ul').html(data);
		scrollChatBottom();
	}).fail(function(error) { 
		alert(error.statusText);
	});
	
	// refresh the messages every 1000ms
	window.setInterval(function(){
		$.get('index.php',{'page':'load_chat'},function(data) {
			$('.chatArea ul').html(data);
			
			var container 		= $('.chatArea')
			var height 			= container.height();
        	var scrollHeight 	= container[0].scrollHeight;
        	var st 				= container.scrollTop();
			
       		if(st >= scrollHeight - height) {
				scrollChatBottom();
			}
		}).fail(function(error) { 
			alert(error.statusText);
		});
	}, 1000);
	
	$('.toggle').click(function(e){
		var toggleState = $('.chat').css('display');
		
		$('.chat').slideToggle(400);
		
		if(toggleState == 'block') {
			$('div.toggle').fadeIn(400);
		}
		else {
			$('div.toggle').css('display','none');
		}
	});
	
	// scroll the chat to the bottom
	function scrollChatBottom() {
		var scrollto = $('.chatArea')[0].scrollHeight;
		$('.chatArea').scrollTop(scrollto);	
	}
	
});
</script>
<!--HTML 5 + IE HACK-->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-20970794-1', 'adam-bray.com');
  ga('send', 'pageview');

</script>
<style type="text/css">
	* {
		box-sizing: border-box;
		font-family: "Noto Sans", Arial;
		margin: 0;
		padding: 0;	
	}
	
	div.chat_wrap {
		bottom: 0;
		display: block;
		font-size: .85em;
		position: fixed;
		right: 3rem;
		width: 255px;
	}
	
		div.chat_wrap > div.toggle {
			background: rgb(250,250,250);
			background: linear-gradient(to bottom, rgb(250,250,250) 0%,rgb(240,240,240) 100%);
			border: 1px solid rgba(0,0,0,.1);
			color: rgba(0,0,0,.4);
			display: none;
			font-weight: bold;
			padding: .45rem 0;
			text-align: center;
		}
		
			.toggle:hover {
				cursor: pointer;	
			}
		
		div.chat {
			display: block;	
		}
		
		div.chat > header {
			background-color: rgb(76, 102, 164);
			background: linear-gradient(to bottom, rgba(76,102,164,1) 0%,rgba(62,77,132,1) 100%);
			border: 1px solid rgba(0,0,0,.1);
			border-radius: 2px 2px 0 0;
			display: block;
			line-height: 2.25rem;
		}
		
			div.chat > header > h3 {
				color: rgb(255,255,255);
				display:block;
				margin: 0;
				padding: 0;	
				text-align: center;
			}
		
		div.chat > div.chatArea {
			background: rgb(250,250,250);
			border: 1px solid rgba(0,0,0,.1);
			border-top: none;
			display: block;
			height: 160px;
			font-size: .9em;
			overflow: auto;
			padding: .25rem .65rem;
		}
		
			div.chat > div.chatArea > ul {
				list-style-type: none;	
			}
			
				div.chat > div.chatArea > ul > li {
					padding: 0 0 .45rem;
				}
				
					div.chat > div.chatArea > ul > li.time {
						color: rgba(0,0,0,.35);
						font-size: .85em;
						font-weight: bold;
						text-align: center;
						text-transform: uppercase;
					}
			
		div.chat input {
			border: none;
			border: 1px solid rgba(0,0,0,.1);
			border-top: none;
			display: block;
			padding: .35rem .5rem;
			width: 100%;	
		}
</style>
</head>

<body>
<h1>Adam Bray Facebook Chat / Shoutbox</h1>
<p>Here's my PHP, MySQL, jQuery chat / shoutbox. It has anti-spam features, whilst also being written in PHP 5 OOP with a PDO MySQL interface.</p>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Sidebar Low -->
<ins class="adsbygoogle"
	 style="display:inline-block;width:300px;height:250px"
	 data-ad-client="ca-pub-8066383283274201"
	 data-ad-slot="3710749975"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
<div class="chat_wrap">
	<div class="toggle">
		<h3>Chat</h3>
	</div>
	<div class="chat">
		<header>
			<h3 class="toggle">Chat</h3>
		</header>
		<div class="chatArea">
			<ul>
				<li><em>Loading...</em></li>
			</ul>
		</div>
		<form method="post">
			<input type="text" name="chat_name" id="chat_name" maxlength="15" placeholder="Name" required>
			<input type="text" name="chat_message" id="chat_message" maxlength="140" placeholder="Message" required autocomplete="off">
			<input type="hidden" name="chat_token" id="chat_token" value="<?=$token->set();?>">
		</form>
	</div>
</div>
<script src="http://www.adam-bray.com/includes/script.js"></script>
</body>
</html>