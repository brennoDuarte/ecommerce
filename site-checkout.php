<?php

	use \Hcode\Page;
	use \Hcode\Model\Products;
	use \Hcode\Model\User;
	use \Hcode\Model\Category;
	use \Hcode\Model\Cart;
	use \Hcode\Model\Address;
	use \Hcode\Model\Order;
	use \Hcode\Model\OrderStatus;

$app->get("/checkout", function(){
		User::verifyLogin(false);
		$address = new Address();
		$cart = Cart::getFromSession();

		if (isset($_GET["zipcode"])) {
			$_GET["zipcode"] = $cart->getdeszipcode();
		}

		if (isset($_GET["zipcode"])) {
			$address->loadFromCEP($_GET["zipcode"]);
			$cart->setdeszipcode($_GET["zipcode"]);
			$cart->save();
			$cart->getCalculateTotal();
		}

		if (!$address->getdesaddress()) $address->setdesaddress('');
		if (!$address->getdesnumber()) $address->setdesnumber('');
		if (!$address->getdescomplement()) $address->setdescomplement('');
		if (!$address->getdesdistrict()) $address->setdesdistrict('');
		if (!$address->getdescity()) $address->setdescity('');
		if (!$address->getdesstate()) $address->setdesstate('');
		if (!$address->getdescountry()) $address->setdescountry('');
		if (!$address->getdeszipcode()) $address->setdeszipcode('');

		$page= new Page();

		$page->setTpl("checkout", [
			"cart"=>$cart->getValues(),
			"address"=>$address->getValues(),
			"products"=>$cart->getProducts(),
			"error"=>Address::getError()
		]);
	});

	$app->post("/checkout", function(){
		User::verifyLogin(false);

		if (!isset($_POST["zipcode"]) || $_POST["zipcode"] === "") {
			Address::seError("Informe o CEP");

			header("Location: /checkout");
			exit;
		}
		/*
		if (!isset($_POST["desadress"]) || $_POST["desadress"] === "") {
			Address::setError("Informe o endereço");

			header("Location: /checkout");
			exit;
		}*/

		if (!isset($_POST["desdistrict"]) || $_POST["desdistrict"] === "") {
			Address::setError("Informe o bairro");

			header("Location: /checkout");
			exit;
		}

		if (!isset($_POST["descity"]) || $_POST["descity"] === "") {
			Address::setError("Informe a cidade");

			header("Location: /checkout");
			exit;
		}

		if (!isset($_POST["desstate"]) || $_POST["desstate"] === "") {
			Address::setError("Informe o estado");

			header("Location: /checkout");
			exit;
		}

		if (!isset($_POST["descountry"]) || $_POST["descountry"] === "") {
			Cart::setError("Informe o país");

			header("Location: /checkout");
			exit;
		}

		$user = User::getFromSession();

		$address = new Address();

		$_POST["deszipcode"] = $_POST["zipcode"];
		$_POST["idperson"] = $user->getidperson();

		$address->setData($_POST);
		$address->save();

		$cart = Cart::getFromSession();
		$cart->getCalculateTotal();

		$order = new Order();
		$order->setData([
			"idcart"=>$cart->getidcart(),
			"idaddress"=>$address->getidaddress(),
			"iduser"=>$user->getiduser(),
			"idstatus"=>OrderStatus::EM_ABERTO,
			"vltotal"=>$cart->getvltotal()
		]);

		$order->save();

		header("Location: /order/".$order->getidorder());
		exit;
	});

?>