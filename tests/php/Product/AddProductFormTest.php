<?php

namespace SilverShop\Core\Tests\Product;


use SilverShop\Core\Product\AddProductForm;
use SilverShop\Core\Product\ProductController;
use SilverStripe\Dev\SapphireTest;


class AddProductFormTest extends SapphireTest
{
    public static $fixture_file = "silvershop/tests/fixtures/shop.yml";

    public function testForm()
    {

        $controller = new ProductController($this->objFromFixture("Product", "socks"));
        $form = new AddProductForm($controller);
        $form->setMaximumQuantity(10);

        $this->markTestIncomplete("test can't go over max quantity");

        $data = array(
            'Quantity' => 4,
        );
        $form->addtocart($data, $form);

        $this->markTestIncomplete('check quantity');
    }
}