{* The shop basket page, served at /shop/cart/ when shop.ini
   [BasketSettings] BasketViewName is set to 'cart'.

   The markup lives in shop/basket.tpl and is included rather than copied, so
   a design that overrides shop/basket.tpl keeps working under either view
   name. Override this file instead when the cart page needs to differ.

   basket.tpl reads the view name from shop.ini itself, so nothing needs to be
   handed to it here. *}
{include uri='design:shop/basket.tpl' basket_view_name=$basket_view_name}
