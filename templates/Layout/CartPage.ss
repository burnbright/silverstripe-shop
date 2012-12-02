<% require ThemedCSS(checkout) %>
<h1 class="pagetitle">$Title</h1>
<div class="typography">
	<% if Content %>
		$Content
	<% end_if %>
</div>
<% if Cart %>
	<% control Cart %>
		<% include Cart %>
	<% end_control %>
	<% control ModifierForms %>
		$Me
	<% end_control %>
<% else %>
	<p class="message warning"><% _t('CartPage.ss.CARTEMPTY','Your cart is empty.') %></p>
<% end_if %>
<div class="cartfooter">
	<% if ContinueLink %>
		<a class="continuelink button" href="$ContinueLink">
			<% _t('CartPage.ss.CONTINUE','Continue Shopping') %>
		</a>
	<% end_if %>
	<% if Cart %>
		<% if Cart.CheckoutLink %>
			<a class="checkoutlink button" href="$Cart.CheckoutLink">
				<% _t('CartPage.ss.PROCEEDTOCHECKOUT','Proceed to Checkout') %>
			</a>
		<% end_if %>
	<% end_if %>
</div>