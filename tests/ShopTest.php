<?php
/**
 * High level tests of the whole ecommerce module.
 *
 * @package shop
 * @subpackage tests
 */
class ShopTest extends FunctionalTest {

	static $fixture_file = 'shop/tests/fixtures/shop.yml';
	static $disable_theme = true;
	static $use_draft_site = true;

	function setUp() {
		parent::setUp();
		$this->assertTrue(class_exists('Payment'), 'Payment module is installed.');
	}

	function testExampleConfig(){
		require_once(SHOP_PATH.DIRECTORY_SEPARATOR.'example_config.php');
		//TODO: test each configuration
	}

	function testCanViewCheckoutPage() {
		$this->get('checkout');
		//TODO: check order hasn't started
	}
	
	function testFindLink() {
		$this->checkoutpage = $this->objFromFixture('CheckoutPage', 'checkout');
		$this->checkoutpage->publish('Stage','Live');
		$link = CheckoutPage::find_link();
		$this->assertEquals(Director::baseURL() . 'checkout/', $link, 'find_link() returns the correct link to checkout.');
	}

	function testCanViewProductPage() {
		$p1a = $this->objFromFixture('Product', 'tshirt');
		$p2a = $this->objFromFixture('Product', 'socks');
		$this->get(Director::makeRelative($p1a->Link()));
		$this->get(Director::makeRelative($p2a->Link()));
	}
	
	function testCanViewProductCategoryPage() {
		$clothing = $this->objFromFixture('ProductCategory', 'clothing');
		$musicplayers = $this->objFromFixture('ProductCategory', 'musicplayers');
		$this->get(Director::makeRelative($clothing->Link()));
		$this->get(Director::makeRelative($musicplayers->Link()));
	}

	function old_testCanViewAccountPage() {
		/* If we're not logged in we get directed to the log-in page */
		$this->get('account/');
		$this->assertPartialMatchBySelector('p.message', array(
			"You'll need to login before you can access the account page. If you are not registered, you won't be able to access it until you make your first order, otherwise please enter your details below.", ));

		/* But if we're logged on you can see */
		$this->session()->inst_set('loggedInAs', $this->idFromFixture('Member', 'member'));
		$this->get('account/');
		$this->assertPartialMatchBySelector('#PastOrders h3', array('Your Order History'));
	}

	static function setConfiguration(){
		$ds = DIRECTORY_SEPARATOR;
		include(BASE_PATH.$ds.SHOP_DIR.$ds.'tests'.$ds.'test_config.php');
	}

}
