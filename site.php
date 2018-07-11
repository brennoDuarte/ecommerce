<?php
	use \Hcode\Page;
	use \Hcode\Model\Products;
	use \Hcode\Model\User;

	$app->get('/', function() {
	    $products = Products::listAll();

		$page = new Page();
		$page->setTpl("index", [
			"products"=>Products::checkList($products)
		]);

	});

	$app->get("/products/:desurl", function($desurl){
		$products = new Products();
		$products->getFromURL($desurl);

		$page = new Page();
		$page->setTpl("product-detail", [
			"product"=>$products->getValues(),
			"categories"=>$products->getCategories()
		]);
	});

	$app->post("/register", function(){
		$_SESSION["registerValues"] = $_POST;

		if (!isset($_POST["name"]) || $_POST["name"] == ""){
			User::setErrorRegister("Preencha o seu nome.");
			
			header("Location: /login");
			exit;
		}

		if (!isset($_POST["email"]) || $_POST["email"] == ""){
			User::setErrorRegister("Preencha o seu email.");
			
			header("Location: /login");
			exit;
		}

		if (!isset($_POST["password"]) || $_POST["password"] == ""){
			User::setErrorRegister("Preencha sua senha.");
			
			header("Location: /login");
			exit;
		}

		if (User::checkLoginExist($_POST["email"] === true)) {
			User::setErrorRegister("Este e-mail já está sendo usado");
			header("Location: /login");
			exit;
		}

		$user = new User();
		$user->setData([
			"inadmin"=>0,
			"deslogin"=>$_POST["email"],
			"desperson"=>$_POST["name"],
			"desemail"=>$_POST["email"],
			"despassword"=>$_POST["password"],
			"nrphone"=>$_POST["phone"]
		]);

		$user->save();

		User::login($_POST["email"], $_POST["password"]);
		header("Location: /checkout");
		exit;
	});
?>