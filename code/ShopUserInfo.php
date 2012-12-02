<?php
/**
 * Collects and stores data about the user
 */
class ShopUserInfo extends Object{

	private static $singleton = null;
	protected static function singleton(){
		if(!self::$singleton){
			self::$singleton = new ShopUserInfo();
		}
		return self::$singleton;
	}

	protected function setLocation($address){
		if($address instanceof Address){
			$address = $address->toMap();
		}
		$address = new Address(Convert::raw2sql($address));
		Session::set("UserInfo.Location",$address);
		$this->extend("onAfterSetLocation",$address);
	}

	static function set_location($address){
		ShopUserInfo::singleton()->setLocation($address);
	}

	static function get_location(){
		return Session::get("UserInfo.Location");
	}	

}