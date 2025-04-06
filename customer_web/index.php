<?php

	include_once("config.php");

	$class 		= isset($_GET["mod"]) ? trim($_GET["mod"]) : "";
	$method		= isset($_GET["act"]) ? trim($_GET["act"]) : "";

	$method		= ($method == 'query') ? 'user_query' : $method;

	if(!file_exists(ROOT_CLASS .VERSION."/class." . strtolower($class) . ".php")){ // If no class file found, return error
		$myrai->api_status 	= 0;
		$myrai->api_message	= "Invalid end point";
	}else{
		
		require_once(ROOT_CLASS.VERSION."/class." . strtolower($class) . ".php");
		
		if(!method_exists($class, $method)){ // If no method found, return error
			$helper->api_status 	= 0;
			$helper->api_message	= "Invalid method";
		}else{ 
			
			$API = new $class("JSON_ARRAY", $pdo); // Create class instance
			$API->{$method}(); // call method
			echo $API->response(); // Final response
			exit; // Return from here since the required class and its method have been executed				
			
		}
	}

	// If API could not be performed successfully
	$helper->responseType = "JSON_ARRAY";
	echo $helper->response();
