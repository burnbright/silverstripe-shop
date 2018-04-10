# Multi-Step "Stepped" Checkout

Historically the checkout has only been based on a single OrderForm. Whilst it's nice to place an order in a single form submission, in most cases different parts of the checkout process rely on the data of others. In particular, to provide prices for shipping, we first need an address. To do this in a single form, we require the aid of javascript/ajax.

There is a multi-step version of the checkout available that can be enabled. See the code in `silvershop/code/checkout/steps`, which contains a number of decorators for `CheckoutPage`. Each step is stored in a class with an action to view the step, a form to gather data, and an action to process the form data.

To enable the multi-step checkout, define the steps in your config.yaml file. The config is a mapping from url `action` to `CheckoutStep`. For example:

```yaml
SilverShop\Page\CheckoutPage:
  steps:
    membership: 'SilverShop\Checkout\Step\Membership'
    contactdetails: 'SilverShop\Checkout\Step\ContactDetails'
    shippingaddress: 'SilverShop\Checkout\Step\AddressBook'
    billingaddress: 'SilverShop\Checkout\Step\AddressBook'
    shippingmethod: 'SilverShop\Checkout\Step\ShippingMethod'
    summary: 'SilverShop\Checkout\Step\Summary'
```

The above configuration is picked up by the `SteppedCheckout::setupSteps()` function in `silvershop/_config.php`, and it adds all of the steps as extensions to `CheckoutPage_Controller`, and make sure the index action (yoursite.tld/checkout/) is the first step.

Next, optionally replace your `CheckoutPage.ss` template with one that uses steps. You can find such a template in `silvershop/templates/Layout/SteppedCheckoutPage.ss` you could put this in your mysite/templates/Layout folder and rename it to `CheckoutPage.ss`.

You may notice that the `SteppedCheckoutPage.ss` template contains statements like:

```html
<% if IsFutureStep(contactdetails) %> ... <% end_if %>
<% if IsCurrentStep(contactdetails) %> ... <% end_if %>
<% if IsPastStep(contactdetails) %> ... <% end_if %>
```

These functions have been set up to enable a single page template to handle multiple actions. This approach is good for showing upcoming steps, the current form, and past data that the user has entered already.

You can also define individual templates if you like, eg:

```html
mysite/templates/Layout/CheckoutPage_contactdetails.ss
mysite/templates/Layout/CheckoutPage_shippingaddress.ss
mysite/templates/Layout/CheckoutPage_billingaddress.ss
mysite/templates/Layout/CheckoutPage_paymentmethod.ss
mysite/templates/Layout/CheckoutPage_summary.ss
```

(templates could also be in your theme)

## Configuring Steps

Steps can be configured using yaml config:

```yaml
SilverShop\Page\CheckoutPage:
  steps:
    'membership' : 'SilverShop\Checkout\Step\Membership'
    'contactdetails' : 'SilverShop\Checkout\Step\ContactDetails'
    'shippingaddress' : 'SilverShop\Checkout\Step\Address'
    'billingaddress' : 'SilverShop\Checkout\Step\Address'
    'paymentmethod' : 'SilverShop\Checkout\Step\PaymentMethod'
    'summary' : 'SilverShop\Checkout\Step\Summary'
```

Add/remove/reorder steps as you please, but keep in mind that the data from one step may be reliant on data from another.


## Additional Form Fields

When needing additional Form Fields in a Multi-Step Checkout, your best bet is to extend the CheckoutStep class and point this to a custom Checkout Component.  Below is an example to demonstrate.  Say, we need an Organisation name in the contact details because the eCommerce website will sell products B2B.

1) Adjust the yaml config:

```yaml
SilverShop\Page\CheckoutPage:
  steps:
    ...
    'contactdetails' : 'CheckoutStep_ContactDetailsCustom'
    ...
```

2) As recommended in [Customising_Fields](Custom_Fields), use a Data Extension to extend existing classes. In this example add Organisation to the Member and Order classes.

```php
class ExtendedCustomer extends DataExtension
{
    private static $db = array(
        'Organisation' => 'Varchar'
    );
    public function updateMemberFormFields(FieldList $fields) {
    	$fields->insertBefore(new TextField('Organisation'), 'FirstName');
    }
}
```

In your config.yml file:

```yaml
Member:
  extensions:
    - ExtendedCustomer
Order:
  extensions:
    - ExtendedCustomer
```

3) Copy CheckoutStep_ContactDetails.php and save under [mysite]/code as CheckoutStep_ContactDetailsCustom.php.  In addition, copy CustomerDetailsCheckoutComponent.php and save under [mysite]/code as CustomerDetailsCheckoutComponentCustom.php.

4) Open CheckoutStep_ContactDetailsCustom.php and rename the class to CheckoutStep_ContactDetailsCustom.  Change one line in the ContactDetailsForm function to point to our custom Checkout Component.

```php
$config->addComponent(new CustomerDetailsCheckoutComponentCustom());
```

5) Open CustomerDetailsCheckoutComponent.php and rename the class to CustomerDetailsCheckoutComponentCustom.  Update two functions as below.

Add the Organisation to the form fields:

```php
public function getFormFields(Order $order) {
	$fields = new FieldList(
		$organisation = TextField::create('Organisation'),
		...
```

Add Organisation to the getData function:

```php
public function getData(Order $order) {
	if($order->Organisation || $order->FirstName || $order->Surname || $order->Email){
		return array(
			'Organisation' => $order->Organisation,
			...
	if($member = Member::currentUser()){
		return array(
			'Organisation' => $member->Organisation,
			...
```

6) Flush the class manifest by adding ?flush=1 to your site url.
