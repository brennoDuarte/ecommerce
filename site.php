<?php
	use \Hcode\Page;
	use \Hcode\Model\Products;
	use \Hcode\Model\User;
	use \Hcode\Model\Category;
	use \Hcode\Model\Cart;

	$app->get('/', function() {
	    $products = Products::listAll();

		$page = new Page();
		$page->setTpl("index", [
			"products"=>Products::checkList($products)
		]);

	});

	$app->get("/categories/:idcategory", function($idcategory){
		User::verifyLogin();
		$page = (isset($_GET["page"])) ? (int)$_GET["page"] : 1;
		$category = new Category();

		$category->get((int)$idcategory);
		$pagination = $category->getProductsPage($page);

		$pages = [];
		for ($i=1; $i <= $pagination["pages"]; $i++) { 
			array_push($pages, [
				"link"=>"/categories".$category->getidcategory()."?page=".$i,
				"page"=>$i
			]);
		}

		$page = new Page();
		$page->setTpl("category", [
			"category"=>$category->getValues(),
			"products"=>$pagination["data"],
			"pages"=>$pages
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

	$app->get("/cart", function(){
		$cart = Cart::getFromSession();

		$page = new Page();

		$page->setTpl("cart");
	});
?>