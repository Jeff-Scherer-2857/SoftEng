<?php
	require_once __DIR__ . '/CarNode.php';
	require_once __DIR__ . '/FloorNode.php';
	require_once __DIR__ . '/ControllerNode.php';
	require_once __DIR__ . '/SuperNode.php';

	echo "Soft Eng Assignment 2<br>";
	
	echo "<br>CarNode: <br>";
	echo "------------------------------------------------------------<br>";
	
	
	$elevatorCar = CarNode::Instance(142536);
	echo "CarNode CAN ID is: " . $elevatorCar->GetCANID() . "<br>";
	
	$elevatorCar->SetCurrentFloor(3);
	echo "CarNode current floor is: " . $elevatorCar->GetCurrentFloor() . "<br>";
	
	echo "Attemping to create second CarNode...<br>";
	$newElevatorCar = CarNode::Instance(654321);
	echo "CarNode CAN ID is: " . $newElevatorCar->GetCANID() . "<br>";
	echo "Second CarNode current floor is: " . $newElevatorCar->GetCurrentFloor() . "<br>";
	

	echo "<br>FloorNode: <br>";
	echo "------------------------------------------------------------<br>";
	
	$floor1 = new FloorNode(100001);
	
	$floor2 = new FloorNode(100002);
	
	echo "Floor1 CAN ID is: " . $floor1->GetCANID() . "<br>";
	echo "Floor2 CAN ID is: " . $floor2->GetCANID() . "<br>";
	
	echo "Calling Floor2 down<br>";
	$floor2->SetCalledDown(0);
	$floor2->SetCalledDown(1);
	echo "Floor2 called up: " . $floor2->GetCalledUp() . "<br>";
	echo "Floor2 called down: " . $floor2->GetCalledDown() . "<br>";
	
	echo "<br>ControllerNode: <br>";
	echo "------------------------------------------------------------<br>";
	
	$controller = ControllerNode::Instance(111222);
	echo "ControllerNode CAN ID is: " . $controller->GetCANID() . "<br>";
	
	$controller->SetPosition(200);

	$controller->SetSpeed(-10);
	
	echo "ControllerNode Position is: " . $controller->GetPosition() . "<br>";
	echo "ControllerNode Speed is: " . $controller->GetSpeed() . "<br>";
	
	echo "Attemping to create second ControllerNode...<br>";
	$controller = ControllerNode::Instance(333445);

	echo "<br>SuperNode: <br>";
	echo "------------------------------------------------------------<br>";
	
	$supervisor = SuperNode::Instance(505050);
	echo "SupererNode CAN ID is: " . $supervisor->GetCANID() . "<br>";
	
	$supervisor = SuperNode::Instance(543210);
	echo "Got Supervisor DB handle: <br>";
	
	echo "Attemping to create second ControllerNode...<br>";
	$supervisor = SuperNode::Instance(202020);
	echo "SupererNode CAN ID is: " . $supervisor->GetCANID() . "<br>";
	
	
	echo "Done.";
?>