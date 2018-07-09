<?php

namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\User;

class Address extends Model{

	const ERROR = "AddressError";
	
	public static function getCep($nrcep){
		$nrcep = str_replace("-", "", $nrcep);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$nrcep/json/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$data =json_decode(curl_exec($ch), true);
		curl_close($ch);

		return $data;
	}

	public function loadFromCEP($nrcep){
		$data = Address::getCep($nrcep);

		if (isset($data["logradouro"]) && $data["logradouro"]) {
			$this->setdesaddress($data["logradouro"]);
			$this->setdescomplement($data["complemento"]);
			$this->setdesdistrict($data["bairro"]);
			$this->setdescity($data["localidade"]);
			$this->setdesstate($data["uf"]);
			$this->setdescountry("Brasil");
			$this->setnrzipcode($nrcep);
		}
		
	}

	public function save(){
		$sql = new Sql();

		$results = $sql->select("CALL sp_address_save(:idaddress, :idperson:, :desaddress, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
			":idaddress"=>$this->getidaddress(),
			":idperson"=>$this->getidperson(),
			":desaddress"=>utf8_encode($this->getdesaddress()),
			":descomplement"=>utf8_encode($this->getdescomplement()),
			":descity"=>utf8_encode($this->getdescity()),
			":desstate"=>utf8_encode($this->getdesstate()),
			":descountry"=>$this->getdescountry(),
			":deszipcode"=>$this->getdeszipcode(),
			":desdistrict"=>$this->getdesdistrict()
		]);

		if (count($results) > 0) {
			$this->setData($results[0]);
		}
	}

	public static function setError($msg){
		$_SESSION[Address::ERROR] = $msg;
	}

	public static function getError(){
		$msg = (isset($_SESSION[Address::ERROR])) && $_SESSION[Address::ERROR] ? $_SESSION[Address::ERROR] : "";
		Address::clearError();
		return $msg;
	}

	public static function clearError(){
		$_SESSION[Address::ERROR] = NULL;
	}
}

?>