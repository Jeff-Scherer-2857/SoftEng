<?php
	include('php/pagesetup.php');
?>
<!DOCTYPE html>
<html lang="en">
        <head>

			<link rel="stylesheet" type="text/css" href="css/style.css">
			<link rel="stylesheet" type="text/css" href="css/userheader.css">
			
            <title>Elevator Control</title>
			<meta name="description" content="This is the design page"/>
            <meta name="robots" content="noindex nofollow" />
            <meta http-eqiv="author" content="Matt H" />
            <meta http-equiv="pragma" content="no-cache" />

			<script>
				// ---------------------------------------------
				// WEBPAGE AUDIO
				// ---------------------------------------------
			
				//Audio for down button
				var down = new Audio();
				down.src="Audio/control/down.wav";

				//Audio for up button
				var up = new Audio();
				up.src="Audio/control/up.wav";

				//Audio for selecting first floor
				var first = new Audio();
				first.src="Audio/control/first.wav";

				//Audio for selecting second floor
				var second = new Audio();
				second.src="Audio/control/second.wav";

				//Audio for selecting third floor
				var third = new Audio();
				third.src="Audio/control/third.wav";

				//Audio for emergency button
				var emergency = new Audio();
				emergency.src="Audio/control/emergency.wav";
				emergency.volume=0.25;

				//Audio for elevator music
				var elevator = new Audio();
				elevator.src="Audio/control/elevator.wav";
				elevator.volume=0.25;
				
				//Function to play/pause elevator music
				function toggleplay(){
					if(elevator.paused){
						elevator.play();
					} else {
						elevator.pause();
					}
				}
				
				// ---------------------------------------------
				// WEBSOCKET
				// ---------------------------------------------
				const CMD_CALL = 67;		// ASCII 'C'; CALL floor commmand
				const CMD_SELECT = 83;		// ASCII 'S'; SELECT floor commmand
				const CMD_UPDATE = 85		// ASCII 'R'; UPDATE commmand
				const CMD_EMERG = 69;		// ASCII 'E'; EMERGENCY commmand
				
				const websock = new WebSocket('ws://192.168.0.31:61415');	// MH local server
				//const websock = new WebSocket('ws://142.156.193.130:50024');	// VPN
				//const websock = new WebSocket('ws://68.183.197.89:62054');	// Public
				
				var floor_calls = [0, 0, 0, 0];
				var floor_req = [0, 0, 0];

				function websock_init(websock) {
					
					// Websocket connect event listener
					websock.addEventListener('open', function(event) {
						console.log('WS connected');
						document.getElementById("connstatus").innerHTML = '<p style="color: rgb(0,145,2)"> CONNECTED </p>';
					});
					
					// Websocket message event listener
					websock.addEventListener('message', function(event) {
						console.log('WS Message from server: ', event.data);
						
						// get rid of extra characters out front & parse
						var bracketpos = event.data.indexOf('{');	// parse fails without this!
						var messageobj = JSON.parse(event.data.substr(bracketpos, event.data.length));
						
						switch(messageobj.cmd) {
							case CMD_UPDATE:
								console.log("Update received.\n");
								
								// change floor display img
								var floordisplaystring = '';
								if (messageobj.travelling) {
									if (messageobj.current_floor > messageobj.prev_floor) {
										floordisplaystring = 'images/elevatorDisplay_up.png';
										document.getElementById("statusdisplay").src = floordisplaystring;
									} else if (messageobj.current_floor < messageobj.prev_floor) {
										floordisplaystring = 'images/elevatorDisplay_down.png';
										document.getElementById("statusdisplay").src = floordisplaystring;
									}										
								} else {
									floordisplaystring = 'images/elevatorDisplay_' + messageobj.current_floor + '.png';								
									document.getElementById("statusdisplay").src = floordisplaystring;
									
									for (var i=0; i<3; i++) {
										document.getElementById('floor' + (i+1) + 'indicator').src = 'images/indicatorOff.png';
									}
									document.getElementById('floor' + messageobj.current_floor + 'indicator').src = 'images/indicatorOn.png';
								}
								
								// change floor call buttons
								for (var i=0; i<4; i++) {
									if (messageobj.calls[i]) {
										if (i == 0) {
											document.getElementById('call1').src = 'images/calledbttnup.png';
											floor_calls[0] = 1;
										} else if (i == 1) {
											document.getElementById('call2').src = 'images/calledbttndown.png';
											floor_calls[1] = 1;
										} else if (i == 2) {
											document.getElementById('call3').src = 'images/calledbttnup.png';
											floor_calls[2] = 1;
										} else if (i == 3) {
											document.getElementById('call4').src = 'images/calledbttndown.png';
											floor_calls[3] = 1;
										}
									} else {
										if (i == 0) {
											document.getElementById('call1').src = 'images/calledbttndarkup.png';
											floor_calls[0] = 0;
										} else if (i == 1) {
											document.getElementById('call2').src = 'images/calledbttndarkdown.png';
											floor_calls[1] = 0;
										} else if (i == 2) {
											document.getElementById('call3').src = 'images/calledbttndarkup.png';
											floor_calls[2] = 0;
										} else if (i == 3) {
											document.getElementById('call4').src = 'images/calledbttndarkdown.png';
											floor_calls[3] = 0;
										}
									}
								}
								
								// change floor request buttons
								if (messageobj.fifo.includes(3)) {
									document.getElementById('floor3ind').src = 'images/floor3bttn.png';
									floor_req[2] = 1;
								} 
								if (messageobj.fifo.includes(2)) {
									document.getElementById('floor2ind').src = 'images/floor2bttn.png';
									floor_req[1] = 1;
								} 
								if (messageobj.fifo.includes(1)) {
									document.getElementById('floor1ind').src = 'images/floor1bttn.png';
									floor_req[0] = 1;
								}
								
								break;
							
							case CMD_SELECT:
								//console.log("Floor select received.\n");
								break;
								
							case CMD_CALL:
								//console.log("Floor call received.\n");
								break;
								
							case CMD_EMERG:
								console.log("Emergency!\n");
								break;
								
							default:
								console.log("Uknown command received.\n");
								break;
						}
						
					});
					
					// Websocket message event listener
					websock.addEventListener('close', function(event) {						
						document.getElementById("connstatus").innerHTML = '<p style="color: rgb(212, 53, 0)"> CONNECTION ERROR: PLEASE RELOAD PAGE</p>';
					});
				}
				
				// send floor request function
				function floorRequest(floorNum) {
					var message = {'cmd':CMD_SELECT, 'floor':floorNum};
					console.log(message);
					websock.send(JSON.stringify(message));
				}
				
				// send floor call function
				function floorCall(floorNum,dir) {
					var message = {'cmd':CMD_CALL, 'floor':floorNum, 'dir':dir};
					console.log(message);
					websock.send(JSON.stringify(message));
				}
				
				// call onmoouseout function (keeps lit buttons lit)
				function callbttn_mouseout(callbttn) {
					if (floor_calls[callbttn-1] == 1) {
						if (callbttn == 1 || callbttn == 3) {
							document.getElementById('call' + callbttn).src = 'images/calledbttnup.png';
						} else {
							document.getElementById('call' + callbttn).src = 'images/calledbttndown.png';
						}
					} else {
						if (callbttn == 1 || callbttn == 3) {
							document.getElementById('call' + callbttn).src = 'images/calledbttndarkup.png';
						} else {
							document.getElementById('call' + callbttn).src = 'images/calledbttndarkdown.png';
						}
					}
				}
				
				// request onmouseout function (keeps lit buttons lit)
				function reqbttn_mouseout(reqbttn) {
					if (floor_req[reqbttn-1] == 1) {
						document.getElementById('floor' + reqbttn + 'ind').src = 'images/floor' + reqbttn + 'bttn.png';
					} else {
						document.getElementById('floor' + reqbttn + 'ind').src = 'images/floor' + reqbttn + 'bttndark.png';
					}
				}
				
				<div class="controlcolumn">
					<p class="controltitle">CONTROLS</p>
					<div class="controlcolumnleft">
						<div class="controlname">Floor 3</div>
						<div class="controlname">Floor 2</div>
						<div class="controlname">Floor 1</div>
						<div class="controlname" style="color: rgb(200,40,40)">Emergency</div>
						<div class="controlname">Music</div>
					</div>
					<div class="controlcolumnright">
						<div class="controlbttn"><img class="controlbttnimg" src="images/floor3bttndark.png" alt="Floor 3" onmouseover="this.src='images/floor3bttn.png'" onmouseout="this.src='images/floor3bttndark.png'" onclick="third.play()"></div>
						<div class="controlbttn"><img class="controlbttnimg" src="images/floor2bttndark.png" alt="Floor 2" onmouseover="this.src='images/floor2bttn.png'" onmouseout="this.src='images/floor2bttndark.png'" onclick="second.play()"></div>
						<div class="controlbttn"><img class="controlbttnimg" src="images/floor1bttndark.png" alt="Floor 1" onmouseover="this.src='images/floor1bttn.png'" onmouseout="this.src='images/floor1bttndark.png'" onclick="first.play()"></div>
						<div class="controlbttn"><img class="controlbttnimg" src="images/emergbttndark.png" alt="Emergency" onmouseover="this.src='images/emergbttn.png'" onmouseout="this.src='images/emergbttndark.png'" onclick="emergency.play()"></div>
						<div class="controlbttn"><img class="controlbttnimg" src="images/musicbttndark.png" alt="Elevator Music" onmouseover="this.src='images/musicbttn.png'" onmouseout="this.src='images/musicbttndark.png'" onclick="toggleplay()"></div> <!--toggleplay()-->
					</div>
				</div>
			  
				<div class="statuscolumn">
					<p class="statustitle">STATUS</p>
					<div class="statuscolumnleft">
						<div class="statusname">3</div>
						<div class="statusname">2</div>
						<div class="statusname">1</div>
					</div>
					<div class="statuscolumncenter">
						<div class="callbttn"></div>
						<div class="callbttn"><img class="callbttnimg" src="images/calledbttndarkdown.png" alt="Floor 3 Call" onmouseover="this.src='images/calledbttndown.png'" onmouseout="this.src='images/calledbttndarkdown.png'" onclick="down.play()"></div>
						<div class="callbttn"></div>
						<div class="callbttn"><img class="callbttnimg" src="images/calledbttndarkup.png" alt="Floor 2 Call Up" onmouseover="this.src='images/calledbttnup.png'" onmouseout="this.src='images/calledbttndarkup.png'" onclick="up.play()"></div>
						<div class="callbttn"><img class="callbttnimg" src="images/calledbttndarkdown.png" alt="Floor 2 Call Down" onmouseover="this.src='images/calledbttndown.png'" onmouseout="this.src='images/calledbttndarkdown.png'" onclick="down.play()"></div>
						<div class="callbttn"></div>
						<div class="callbttn"><img class="callbttnimg" src="images/calledbttndarkup.png" alt="Floor 1 Call Up" onmouseover="this.src='images/calledbttnup.png'" onmouseout="this.src='images/calledbttndarkup.png'" onclick="up.play()"></div>
					</div>
					<div class="statuscolumnright">
						<div class="locstatus"><img class="locstatusimg" src="images/carlocation.png" alt="Elevator Car Location"></div>
					</div>
                </div>
            </main>
            
            <script>
				//code soucred from: https://github.com/googlearchive/webplatform-samples/blob/master/webspeechdemo/webspeechdemo.html
				// ---------------------------------------------
				// VOICE CONTROL
				// ---------------------------------------------
				
				// Siri button event listener
				var siribttn = document.getElementById("siribttn");
				var siristatus = document.getElementById("siristatus");
				
				function resetSiri() {
					document.getElementById("siristatus").innerHTML = 'IDLE';
				}
				
				//code soucred from: https://github.com/googlearchive/webplatform-samples/blob/master/webspeechdemo/webspeechdemo.html
					
				var finalSpeech = '';
				
				/*
				if (!('webkitSpeechRecognition' in window)) {						//check for web speech API
					alert("Please upgrade your browser for Speech Rocognition.");	//if user does not have web speech API, user should upgrade browser
				} else {
					var recognition = new webkitSpeechRecognition();
					

					recognition.continuous = true;
					//recognition.interimResults = true;

					recognition.start = function(){
						recognizing = true;
						recognition.start();
						//change button here
					};

					recognition.onerror = function(event) {
						if (event.error == 'no-speech') {
							alert("No speech detected.");
							ignore_onend = true;
						}
						if (event.error == 'audio-capture') {
							alert("No audio capture device detected.");
							ignore_onend = true;
						}
						if (event.error == 'not-allowed') {
							alert("Speech detection not enabled.");
							ignore_onend = true;
						}
					};
					  
					recognition.end = function() {
						recognizing = false;
						if(ignore_onend){
							return;
						}
						if(!finalSpeech){
							alert("No speech detected.");
							return;
						}
					};

					recognition.onresult = function(event) {
						//if(typeof(event.results) == 'undefined'){
							//recognition.onend = null;
							//recogntion.stop();
							//window.alert("There was an issue with speech recognition. You may need to upgrade your browser.");
							//return;
						//}
							
						for(var i = 0; i < event.results.length; i++) {
							if(event.results.[i].isFinal){
								finalSpeech += event.results[i][0].transcript;
							}
						}

						for(var i = 0; i < finalSpeech.length; i++) {
							int voicefloor = 0
							if (finalSpeech.search('1')) {
								voicefloor = 1;
							} else if (finalSpeech.search('2') {
								voicefloor = 2;
							} else if (finalSpeech.search('3') {
								voicefloor = 3;
							}
							
							if (finalSpeech.search("call")) {
								floorCall(voicefloor,'up');
							} else if (finalSpeech.search("request")) {
								floorRequest(voicefloor);
							}
							
							
							switch(finalSpeech[i]){
								case '1':
									floor = 1;
									break;
								case '2':
									floor = 2;
									break;
								case '3':
									floor = 3;
									break;
							}
						}
						alert("Floor" + floor + "was selected via voice command.");
					};//end onresult

				} 
				
				function startButton(event){
					if(recognizing){
						recognition.stop();
						return;
					}
					finalSpeech = '';
					recognition.start();
					ignore_onend = false;
				}
				*/
								
				// WINDOW ONLOAD FUNCTIONS
				window.onload = websock_init(websock);
			</script>
			
        </head>
        <body>

			<div id="userheader">
			
				<?php
					if ($_SESSION["status"] == 'active') {
						echo '<div id="userheaderbttn"><a href="php/wipesession.php">LOGOUT</a></div>';
						echo '<div id="userheadername">';
						echo $_SESSION['username'];
						echo '</div>';
						if ($_SESSION["admin"]) {
							echo '<div id="userheaderadmin">ADMIN</div>';
						}
					} else {
						echo '<div id="userheaderbttn"><a href="login.php">LOGIN</a></div>';
					}						
				?>
				
			</div>
            
			<header>
				<p class="headertitle">ELEVATOR CONTROL</p>
			</header>
			
			<nav>
				<a class="navbttn" href="index.php">HOME</a>
				<a class="navbttn" href="about.php">ABOUT</a>
				<a class="navbttn" href="stats.php">STATS</a>
			</nav>
			
			<main>
				
				<div id="websockbanner">
					CONNECTION STATUS: <div id="connstatus"><p style="color: rgb(255, 187, 0)">CONNECTING...</p></div>
				</div>
				
				<div id="siribanner">
					<div id="sirilabel">VOICE CONTROL: </div>
					<div id="siristatus" style="color: rgb(160,160,160)">IDLE</div>
					<img id="start_button" src="images/soundwave.png" alt="Siri" onmouseover="this.src='images/soundwaveOver.png';" onmouseout="this.src='images/soundwave.png';" onmousedown="document.getElementById('siristatus').innerHTML = 'LISTENING';setTimeout(resetSiri, 3000);" onclick="startButton(event)">
				</div>
				
				<div class="controlcolumn">
					<p class="controltitle">CONTROLS</p>
					<div class="controlcolumnleft">
						<div class="controlname">Floor 3</div>
						<div class="controlname">Floor 2</div>
						<div class="controlname">Floor 1</div>
						<div class="controlname">Emergency</div>
						<div class="controlname">Music</div>
					</div>
					<div class="controlcolumnright">
						<div class="controlbttn"><img class="controlbttnimg" id="floor3ind" src="images/floor3bttndark.png" alt="Floor 3" onmouseover="this.src='images/floor3bttn.png'" onmouseout="reqbttn_mouseout(3);" onclick="floorRequest(3); third.play()"></div>
						<div class="controlbttn"><img class="controlbttnimg" id="floor2ind" src="images/floor2bttndark.png" alt="Floor 2" onmouseover="this.src='images/floor2bttn.png'" onmouseout="reqbttn_mouseout(2);" onclick="floorRequest(2); second.play()"></div>
						<div class="controlbttn"><img class="controlbttnimg" id="floor1ind" src="images/floor1bttndark.png" alt="Floor 1" onmouseover="this.src='images/floor1bttn.png'" onmouseout="reqbttn_mouseout(1);" onclick="floorRequest(1); first.play()"></div>
						<div class="controlbttn"><img class="controlbttnimg" src="images/emergbttndark.png" alt="Emergency" onmouseover="this.src='images/emergbttn.png'" onmouseout="this.src='images/emergbttndark.png'" onclick="emergency.play()"></div>
						<div class="controlbttn"><img class="controlbttnimg" src="images/musicbttndark.png" alt="Elevator Music" onmouseover="this.src='images/musicbttn.png'" onmouseout="this.src='images/musicbttndark.png'" onclick="toggleplay()"></div>
					</div>
				</div>
			  
				<div class="statuscolumn">
					<p class="statustitle">STATUS</p>
					<div id="statusdisplaycontainer"><img id="statusdisplay" src="images/elevatorDisplay_1.png" alt="Elevator Display"></div>
					<div class="statuscolumnleft">
						<div class="statusname">3</div>
						<div class="statusname">2</div>
						<div class="statusname">1</div>
					</div>
					<div class="statuscolumncenter">
						<div class="callbttn"></div>
						<div class="callbttn"><img class="callbttnimg" id="call4" src="images/calledbttndarkdown.png" alt="Floor 3 Call" onmouseover="this.src='images/calledbttndown.png'" onmouseout="callbttn_mouseout(4);" onclick="floorCall(3,'down');down.play()"></div>
						<div class="callbttn"></div>
						<div class="callbttn"><img class="callbttnimg" id="call3" src="images/calledbttndarkup.png" alt="Floor 2 Call Up" onmouseover="this.src='images/calledbttnup.png'" onmouseout="callbttn_mouseout(3);" onclick="floorCall(2,'up'); up.play()"></div>
						<div class="callbttn"><img class="callbttnimg" id="call2" src="images/calledbttndarkdown.png" alt="Floor 2 Call Down" onmouseover="this.src='images/calledbttndown.png'" onmouseout="callbttn_mouseout(2);" onclick="floorCall(2,'down'); down.play()"></div>
						<div class="callbttn"></div>
						<div class="callbttn"><img class="callbttnimg" id="call1" src="images/calledbttndarkup.png" alt="Floor 1 Call Up" onmouseover="this.src='images/calledbttnup.png'" onmouseout="callbttn_mouseout(1);" onclick="floorCall(1,'up'); up.play()"></div>
					</div>
					<div class="statuscolumnright">
						<div class="indicator">
							<img class="indicator_large" id="floor3indicator" src="images/indicatorOff.png" alt="Location Indicator">
							
							<img class="indicator_small" id="floor2_3indicator_3" src="images/indicatorOff.png" alt="Location Indicator">
							<img class="indicator_small" id="floor2_3indicator_2" src="images/indicatorOff.png" alt="Location Indicator">
							<img class="indicator_small" id="floor2_3indicator_1" src="images/indicatorOff.png" alt="Location Indicator">
							
							<img class="indicator_large" id="floor2indicator" src="images/indicatorOff.png" alt="Location Indicator">
							
							<img class="indicator_small" id="floor1_2indicator_3" src="images/indicatorOff.png" alt="Location Indicator">
							<img class="indicator_small" id="floor1_2indicator_2" src="images/indicatorOff.png" alt="Location Indicator">
							<img class="indicator_small" id="floor1_2indicator_1" src="images/indicatorOff.png" alt="Location Indicator">
							
							<img class="indicator_large" id="floor1indicator" src="images/indicatorOff.png" alt="Location Indicator">
						</div>
					</div>
                </div>
            </main>
			
			<footer>
				This page is part of Engineering Project VI. &copy Matt H. 2020
			</footer>
        </body>
</html>