<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <title>Ready! Call me back now (CMBN)</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
</head>
<body>

<!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= -->
<!-- My Phone Number & Dial Areas -->
<!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= -->
<div class="container">
    <div class="row">
        <!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= -->
        <!-- Video -->
        <!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= -->
        <div class="VIDEO">
            <div class="">
                <div style="">
                    <div id="video-border">
					<div id="DIALER">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon" style="height:32px; width:120px;"><img src="img/tel.png" style="height:22px; width:22px; float:left;"/><div id="my-number" title="Votre Pseudo ou Numéro">LOADING</div></div>
									<input id="number" class="form-control" placeholder="Qui souhaitez-vous appeler ?">
									<div class="input-group-addon">
										<button id="dial" class="btn">
											<img src="img/call.png" style="height:22px; width:22px; float:left;"/>
										</button>
									</div><div class="input-group-addon">
										<button id="hangup" class="btn">
											<img src="img/endcall.png" style="height:22px; width:22px; float:left;"/>
										</button> 
									</div>
									<div class="input-group-addon">
										<button id="snap" class="btn">
											<img src="img/videocall.png" style="height:22px; width:22px; float:left;"/>
										</button>
									</div>
								</div>
							</div>
						</div>
						
                        <div id="video-display"></div>
						<div id="myself"></div>
	
						<div id="pubnub-chat-section" class="text-center" >
							<div class="input-group">
							<div class="input-group-addon" id="icon_sms">
								<button id="chat" class="btn" title="Ouvrir/Femer le tchat">
									<img src="img/SMS.png" style="height: 22px; width: 22px; float: left;"/>
								</button>
							</div>
							<input id="pubnub-chat-input" type="text" placeholder="Ecrivez-lui quelque chose.." title="Ouvrez le chat et commencez à discuter !" onFocus='showDiv();' >
							</div>
							<div id="pubnub-chat-output" ><div></div></div></input>
						</div>
					</div>
				</div>            
        </div>
		
		
		
		
    </div>
</div>
<!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- -->
<!-- WebRTC Peer Connections -->
<!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- -->
<?php 
/*
if(isset($_GET['number'])){	
session_set_cookie_params('0','/','callmebacknow.orinel.net',true);
session_start();
$_SESSION['pseudo'] = $_GET['number']; 	
setcookie("pseudo", $_GET['number'], 0, "/", "callmebacknow.orinel.net", 1);
 }
 else{
if(isset($_SESSION['pseudo'])){ echo $_SESSION['pseudo']; header( 'Location: index.php?number='.$_SESSION['pseudo'].'' ); }
if(isset($_COOKIE['pseudo'])){ header( 'Location: index.php?number='.$_COOKIE['pseudo'].' ' ); }
 }
	//if(isset($_SESSION['pseudo'])){  header( 'Location: index.php?number='.$_SESSION['pseudo'].' ' ); }
//if(isset($_COOKIE['pseudo'])){ header( 'Location: index.php?number='.$_COOKIE['pseudo'].' ' ); }
*/
?>

<script src="js/jquery-1.11.2.min.js"></script>
<script src="js/pubnub-3.7.13.min.js"></script>
<script src="js/webrtc.js"></script>
<script src="js/sound.js"></script>
<script>
function showDiv(){
    document.getElementById('pubnub-chat-output').style.display = 'inline-block';
	document.getElementById('pubnub-chat-section').style.marginTop = '-170px' ;
	
}

(function(){
	
if (document.body)
{
var larg = (window.innerWidth);
var haut = (window.innerHeight);
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Transfom link http
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function urlify(text)
{
var urlRegex = /(https?:\/\/[^\s]+)/g;

return text.replace(
	urlRegex, function(url) 
	{
	return '<a href=' + url + ' target="_blank">' + url + '</a>';
	}
)
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Generate Random Number if Needed
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
var urlargs         = urlparams();
var my_number       = PUBNUB.$('my-number');
var number          = urlargs.number;

my_number.number    = number;
my_number.innerHTML = ' '+my_number.number;

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Update Location if Not Set
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
if (!('number' in urlargs)) {
	var retour_prompt = prompt("Votre pseudo ?","");
	urlargs.number = retour_prompt;
	
	if(!(urlargs.number)){ alert('Erreur vous devez spécifier un pseudo !'); location.href = windows.history.back(); }
	else{
	location.href = urlstring(urlargs);
	return; }
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Get URL Params
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function urlparams() {
    var params = {};
    if (location.href.indexOf('?') < 0) return params;

    PUBNUB.each(
        location.href.split('?')[1].split('&'),
        function(data) { var d = data.split('='); params[d[0]] = d[1]; }
    );

    return params;
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Construct URL Param String
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function urlstring(params) {
    return location.href.split('?')[0] + '?' + PUBNUB.map(
        params, function( key, val) { return key + '=' + val }
    ).join('&');
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Calling & Answering Service
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
var video_out = PUBNUB.$('video-display');
var myself    = PUBNUB.$('myself');

var phone     = window.phone = PHONE({
    number        : my_number.number, // listen on this line
    publish_key   : 'pub-c-561a7378-fa06-4c50-a331-5c0056d0163c',
    subscribe_key : 'sub-c-17b7db8a-3915-11e4-9868-02ee2ddab7fe',
	//publish_key   : 'pub-c-3b63879e-bd9d-488c-962e-6293abcde596',
	//subscribe_key : 'sub-c-a96972bc-7430-11e5-8768-0619f8945a4f',
    ssl           : true
});





// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Ready to Send or Receive Calls
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
phone.ready(function(){
    // Ready To Call
	var my_number = urlargs['number'];
	add_chat( "Système" , "Vous êtes connecté en tant que: " + my_number );
	document.getElementById('myself').style.marginTop = '-90px' ;
	document.getElementById('myself').style.marginRight = '35%' ;
	localvideo();							//video local

    // Auto Call
    if ('call' in urlargs) {
        var number = urlargs['call'];
        PUBNUB.$('number').value = number;
        dial(number);
    }

    // Make a Phone Call
    PUBNUB.bind( 'mousedown,touchstart', PUBNUB.$('dial'), function(){
        var number = PUBNUB.$('number').value;
        //if (!number) return;
        dial(number);
    } );

    // Hanup Call
    PUBNUB.bind( 'mousedown,touchstart', PUBNUB.$('hangup'), function() {
        phone.hangup();
		return;
    } );
	

	
	//tchat on/off
	PUBNUB.bind( 'mousedown,touchstart', PUBNUB.$('chat'), function() {
			if(document.getElementById('pubnub-chat-output').style.display == 'none'){
			document.getElementById('pubnub-chat-output').style.display = 'inline-block';
			document.getElementById('pubnub-chat-section').style.marginTop = '-170px' ;
			}
			else{ 
			document.getElementById('pubnub-chat-output').style.display = 'none';
			document.getElementById('pubnub-chat-section').style.marginTop = '10px' ;
				}
    } );
});

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Start Phone Call
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function dial(number) {

    var session = phone.dial(number);
	add_chat( "Système" , "Numérotation vers " + session.number	);
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Receiver for Calls
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
phone.receive(function(session){
alert("Vous allez entrer en cumunication avec " + session.number );

session.message(message);
session.connected(connected);
session.ended(ended);

});

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Video Local
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

function localvideo(){
 myself.appendChild(phone.video);
}

//if(video_out.innerHTML == ''){ document.getElementById('myself').style.marginTop = '200px'; }

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Video Session Connected
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function connected(session){
	
	document.getElementById('pubnub-chat-input').style.display = 'inline-block';
	document.getElementById('icon_sms').style.display = 'inline-block';
	document.getElementById('myself').style.marginTop = '-136px' ;
	document.getElementById('myself').style.marginRight = '7px' ;
	
	add_chat( "Système" , "Appel en cours: " + my_number.number + " vers " + session.number	);
	
	video_out.appendChild(session.video); 	//video ext

	document.title=( "Appel vers " + session.number + " (CMBN)");
    PUBNUB.$('number').value = ''+ session.number;
    sounds.play('sound/hi');
    console.log("Hi!");
	
	
		 // video on/off
	 var etat=0;
    PUBNUB.bind( 'mousedown,touchstart', PUBNUB.$('snap'), function() {
    if(etat == 1){  alert("vidéo desactivée"); myself.appendChild(phone.video); video_out.appendChild(session.video); etat=0; }
	else{ alert("vidéo activée"); video_out.innerHTML = ''; myself.innerHTML = '';  etat=1; }
	  
    } );
	
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Video Session Ended
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function ended(session) {
	add_chat( "Système" , "Fin appel vers: " + session.number );
	
	video_out.innerHTML = '';
	document.getElementById('icon_sms').style.display = 'none';
	document.getElementById('myself').style.marginTop = '-90px' ; //recadrage myself
	document.getElementById('myself').style.marginRight = '35%' ; //recadrage myself
	document.getElementById('pubnub-chat-input').style.display = 'none';
	document.getElementById('pubnub-chat-output').style.display = 'none';
	
	document.title=( "Ready! - Call me back now (CMBN)");	
    sounds.play('sound/goodbye');
    console.log("Bye!");
	
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Chat
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
var chat_in  = PUBNUB.$('pubnub-chat-input');
var chat_out = PUBNUB.$('pubnub-chat-output');

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Send Chat MSG and update UI for Sending Messages
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
PUBNUB.bind( 'keydown', chat_in, function(e) {
    if ((e.keyCode || e.charCode) !== 13)     return true;
    if (!chat_in.value.replace( /\s+/g, '' )) return true;

    phone.send({ text : chat_in.value });
    add_chat( my_number.number + " (Me)", chat_in.value );
    chat_in.value = '';
} )

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Update Local GUI
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function add_chat( number, text ) {
    if (!text.replace( /\s+/g, '' )) return true;

    var newchat       = document.createElement('div');
    newchat.innerHTML = PUBNUB.supplant(
        '<strong>{number}: </strong> {message}', {
        message : urlify(safetxt(text)),
        number  : safetxt(number)
    } );
    chat_out.insertBefore( newchat, chat_out.firstChild );
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// WebRTC Message Callback
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function message( session, message ) {
    add_chat( session.number, message.text );
	sounds.play('sound/hi');
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// XSS Prevent
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function safetxt(text) {
    return (''+text).replace( /[<>]/g, '' );
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Problem Occured During Init
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
phone.unable(function(details){
    console.log("Alert! - Reload Page.");
    console.log(details);
});

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Debug Output
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
phone.debug(function(details){
     // console.log(details);
});

})();</script>
</body>
</html>
