<?php

use \Hcode\Page;
use \Hcode\Model\Products;
use \Hcode\Model\User;
use \Hcode\Model\Category;;

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

?>