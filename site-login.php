<?php

use \Hcode\Page;
use \Hcode\Model\Products;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app->get("/login", function(){

		$page = new Page();

		$page->setTpl("login", [
			"error"=>User::getError(),
			"errorRegister"=>User::getErrorRegister(),
			"registerValues"=>(isset($_SESSION["registerValues"])) ? $_SESSION["registerValues"] : [
				"name"=> "", 
				"email"=> "", 
				"phone"=> ""
			]
		]);
	});

	$app->post("/login", function(){
		try {
			User::login($_POST["login"], $_POST["password"]);
		} catch (Exception $e) {
			User::setError($e->getMessage());
		}

		header("Location: /checkout");
		exit;
	});

	$app->get("/logout", function(){
		User::logout();

		header("Location: /login");
		exit;
	});

?>