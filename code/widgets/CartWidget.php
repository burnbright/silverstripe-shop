<?php

/**
 * CartWidget displays the current contents of the user's cart.
 * @package shop
 * @subpackage widgets
 */

class CartWidget extends Widget{

	static $title = "Shopping Cart";
	static $cmsTitle = "Shopping Cart";
	static $description = "Displays the current contents of the user's cart.";

	function Cart(){
		return Controller::curr()->Cart();
	}
	
}