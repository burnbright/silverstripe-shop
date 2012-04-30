# Custom fields for orders, customers

You may want to store additional information for each customer.
In a nutshell you need to update your DataModel, and any places where the 
data is entered. This can generally be done by creating extensions.

First read the [silverstripe docs on this](http://doc.silverstripe.org/sapphire/en/reference/dataobjectdecorator)

## Customer

Customer fields are saved to both Members and Orders.

Here is an example extension:

	<?php
	class ExtendedCustomer extends DataObjectDecorator{
	
		function extraStatics(){
			return array(
				'db' => array(
					'MyExtraField' => 'Varchar'
				)
			);
		}
		
	}
	
To your _config.php file, add:

	Object::add_extension('Member','ExtendedCustomer');
	Object::add_extension('Order','ExtendedCustomer');
