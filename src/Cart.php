<?php


namespace Neon\Cart;


use Neon\Cart\Exceptions\BadMethodCallException;
use Neon\Cart\Exceptions\SessionException;


/**
 * Class Cart
 * @package PAG\Lib\SimpleCart
 *
 * @method static Item find(string $uuid)
 * @method static CartManager add(mixed $item_id, int $quantity = 1, mixed $price = null, array $options = [])
 * @method static CartManager destroy()
 * @method static CartManager destroyByItemId(mixed $item_id)
 * @method static null|ItemCollection content()
 * @method static array contentByIdGroup()
 * @method static null|ItemCollection contentById(mixed $item_id)
 * @method static CartManager remove(string $uuid)
 * @method static CartManager removeByItemId(mixed $item_id, int $quantity = 1)
 * @method static int count()
 * @method static int countByItemId()
 * @method static float total()
 *
 * @uses \Neon\Cart\CartManager::find()
 * @uses \Neon\Cart\CartManager::add()
 * @uses \Neon\Cart\CartManager::destroy()
 * @uses \Neon\Cart\CartManager::destroyByItemId()
 * @uses \Neon\Cart\CartManager::content()
 * @uses \Neon\Cart\CartManager::contentByIdGroup()
 * @uses \Neon\Cart\CartManager::contentById()
 * @uses \Neon\Cart\CartManager::remove()
 * @uses \Neon\Cart\CartManager::removeByItemId()
 * @uses \Neon\Cart\CartManager::count()
 * @uses \Neon\Cart\CartManager::countByItemId()
 * @uses \Neon\Cart\CartManager::total()
 */
class Cart
{
    /**
     * @param $method
     * @param $arguments
     *
     * @throws BadMethodCallException|SessionException
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $instance = CartManager::getInstance();

        if( !is_callable([$instance, $method]) ) {
            throw new BadMethodCallException(self::class, $method);
        }

        return $instance->$method(...$arguments);
    }

    /**
     * Gets a specific cart instance.
     *
     * @param string $instance
     *
     * @throws Exceptions\SessionException
     *
     * @return CartManager
     */
    public static function instance(string $instance) : CartManager
    {
        return CartManager::getInstance($instance);
    }
}