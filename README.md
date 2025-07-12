# simple-cart
Simple session based shopping cart handler

# Simple Usage

```php
<?php

use Neon\Cart;

// Create a new cart instance
$cart = Cart::instance('my_instance_key');

// Adding items to cart 
$product_id = 1;
$cart->add(item_id: $product_id, quantity: 3);
Cart::instance('my_instance_key')->add(item_id: $product_id, quantity: 3);

// Removing items from cart
// Removes two instances of the item with the id 1 from the cart
$cart->removeByItemId(item_id: 1, quantity: 2);
Cart::instance('my_instance_key')->removeByItemId(item_id: 1, quantity: 2);

// Fetching the whole cart content
$content = $cart->content();
$content = Cart::instance('my_instance_key')->content();

// Destroying the whole cart content
$cart->destroy();
Cart::instance('my_instance_key')->destroy();

// Removing all instances of a given item ID
$cart->destroyByItemId(1);
Cart::instance('my_instance_key')->destroyByItemId(1);

// Counting all items in the cart
$count = $cart->count();
$count = Cart::instance('my_instance_key')->count();
```
