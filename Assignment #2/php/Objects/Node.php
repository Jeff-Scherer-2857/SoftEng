<?php

class Node {
	// class variables
	public $CANID = 0;
	
	// class methods
	
	// constructor
	public function __construct($ID) {
		echo "New Node with CANID:" . $ID . "<br>";
		$this->CANID = $ID;
	}
	
	public function SetCANID($ID) {
		$this->CANID = $ID;
	}
	
	public function GetCANID() {
		return $this->CANID;
	}
	
	public function SendMessage($message) {
		//CanTX($CANID, $message);	// dummy CAN transmit function
		echo $message . " sent.<br>";
	}
	
	public function GetMessage() {
		$message = "";
		//$message .= CanRX($CANID);	// dummy CAN recieve function
		return $message;
	}
}

?>