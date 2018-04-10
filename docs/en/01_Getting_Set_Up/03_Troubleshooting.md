If you have a common question needing a common answer, hopefully you can find it here.

## Which version of SilverStripe / shop / Payment should I use?

This has been made much simpler with the introduction of [composer](https://getcomposer.org/).

## No products are showing up on my website!?

Here are a few things to check:

 * Are the products published? You may be viewing the website in the wrong mode. See if it makes a difference to view the site in draft or live mode. You can switch in the CMS.
 * Is the template correct? (try add ?showtemplate=1 to the url)
 * Do the products have prices?
 * Do the products have "show in menus" set?
 * Is there any kind of global allow purchase setting affecting things?

## How to customise forms - checkout, add product, cart, etc

If the form has been built well, it should have extension hooks that allow you to make adjustments by writing your own Extension class.

## How do I customise the country field? - change country list, remove field completely, set default

See the [customising docs](../02_Customisation/index.md).

## How do I set my site to use a different currency?

in `mysite\_config\config.yml`

```
# Example for EURO
SilverShop\Extension\ShopConfigExtension:
  base_currency: 'EUR'
SilverShop\ORM\FieldType\ShopCurrency:
  currency_symbol: '€'
```

then run `dev/build`

## I can't get payments to work? eg: PayPal, PaymentExpress, Other..

see [payment](06_Payment.md)

## How do I add shipping calculation to the checkout process? How do I customise fees for different locations / delivery zones?

The default shop module provides a few shipping [modifiers](../03_How_It_Works/Order_Modifiers.md). You can also have one custom built for your needs.

## How do I use in a different language?

Follow the [Silverstripe internationalisation guide](http://docs.silverstripe.org/en/developer_guides/i18n/)


The translation of the silvershop module is being done via Transifex. You can see the current translation progress on: [www.transifex.com/silvershop/silverstripe-shop](https://www.transifex.com/silvershop/silverstripe-shop). 
If your language isn't translated yet, we welcome your contribution on transifex! Just click the **"Help Translate "SilverShop"** button to get started.


## When are cart/order totals recalculated?

When changes are made to an order, the totals need to be recalculated. It is too expensive to do this on every single change.

Totals are recalculated on every request for carts. The recalculation is triggered by the Cart function,
which is for use in templates only. It will only recalculate once, so make sure recalculation isn't triggered early, producing incorrect results.

After an order is placed, recalculation will only occur on demand.
