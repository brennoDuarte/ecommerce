<?php
 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
/*
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
*/
$app = new Slim();

$app->config('debug', true);

require_once("site.php");
require_once("site-login.php");
require_once("site-categories.php");
require_once("site-forgot.php");
require_once("site-cart.php");
require_once("site-profile.php");
require_once("site-checkout.php");
require_once("site-order.php");
require_once("functions.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-forgot.php");
require_once("admin-categories.php");
require_once("admin-products.php");
require_once("admin-orders.php");

$app->run();

 ?>