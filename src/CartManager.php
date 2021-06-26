<?php


namespace Neon\Cart;


use DateTime;
use Neon\Cart\Exceptions\SessionException;


class CartManager
{
    /**
     * Session key constant used to identifiy the cart.
     *
     * @var string
     */
    private const SESSID = 'pag_simple_cart';

    /**
     * Session key to identifiy cart items.
     *
     * @var string
     */
    private const ITEM_KEY = 'item_id';

    /**
     * Key to identifiy the price variable.
     *
     * @var string
     */
    private const PRICE_KEY = 'price';

    /**
     * Key to identifiy the options variable.
     *
     * @var string
     */
    private const OPTIONS_KEY = 'options';

    /**
     * Static collection of cart instances.
     *
     * @var array
     */
    private static $instances = [];

    /**
     * Active cart instance.
     *
     * @var null|CartManager
     */
    private static $active_instance = null;

    /**
     * Default cart instance.
     *
     * @var null|CartManager
     */
    private static $default_instance = null;

    /**
     * Instance identifier string.
     *
     * @var string|null
     */
    private $instance_string;

    /**
     * Item Repository instance.
     *
     * @var ItemRepository
     */
    private $item_repository;

    /**
     * Gets the instance with given instance identifier.
     *
     * @param string|null $instance
     *
     * @throws SessionException
     *
     * @return CartManager
     */
    public static function getInstance(?string $instance = null) : CartManager
    {
        if( is_null($instance) ) {
            return self::getActiveInstance();
        }

        if( 'default' === $instance ) {
            return self::getDefaultInstance();
        }

        return self::$instances[$instance] ?? self::getNewInstance($instance);
    }

    /**
     * CartManager constructor.
     *
     * @param string|null $instance
     *
     * @throws SessionException
     */
    private function __construct(?string $instance = null)
    {
        $this->instance_string = $instance;

        if( !$this->checkSessionStatus() ) {
            throw new SessionException();
        }

        $this->item_repository = new ItemRepository();

        if( !empty($_SESSION[self::SESSID][$this->instance_string ?? 'default']) ) {
            $this->buildFromSession();
        }
    }

    /**
     * Finds an item with the given uuid.
     *
     * @param string $uuid
     *
     * @return Item|null
     */
    public function find(string $uuid) : ?Item
    {
        return $this->item_repository->getAll()->find($uuid);
    }

    /**
     * Adds a new item to the cart.
     *
     * @param $item_id
     * @param int $quantity
     * @param null $price
     * @param array $options
     *
     * @return $this
     */
    public function add($item_id, int $quantity = 1, $price = null, array $options = []) : CartManager
    {
        for($i = 0; $i < $quantity; $i++) {
            $uuid = $this->generateInternalID($item_id);
            $item = new Item($uuid, $item_id, $price, $options);

            $this->item_repository->addToRepository($item);
            $this->addToSession($item);
        }

        return $this;
    }

    /**
     * Gets the cart content.
     *
     * @return ItemCollection|null
     */
    public function content() : ?ItemCollection
    {
        return $this->item_repository->getAll();
    }

    /**
     * Gets all items grouped by item id.
     *
     * @return array
     */
    public function contentByIdGroup() : array
    {
        return $this->item_repository->getAllByIdGroup();
    }

    /**
     * Gets item collection with given item id.
     *
     * @param $item_id
     *
     * @return ItemCollection|null
     */
    public function contentById($item_id) : ?ItemCollection
    {
        return $this->item_repository->getById($item_id);
    }

    /**
     * Removes the item with the given uuid from the cart.
     *
     * @param $uuid
     *
     * @return $this
     */
    public function remove($uuid) : CartManager
    {
        $this->item_repository->remove($uuid);
        $this->removeFromSession($uuid);

        return $this;
    }

    /**
     * Removes <quantity> amount of items of the given item id
     *
     * @param $item_id
     * @param int $quantity
     *
     * @return $this
     */
    public function removeByItemId($item_id, int $quantity = 1) : CartManager
    {
        for($i = 0; $i < $quantity; $i++) {
            $uuid = $this->item_repository->removeByItemId($item_id);
            $this->removeFromSession($uuid);
        }

        return $this;
    }

    /**
     * Clears the whole cart.
     *
     * @return void
     */
    public function destroy()
    {
        unset($_SESSION[self::SESSID][$this->instance_string ?? 'default']);
        $this->item_repository = new ItemRepository();
    }

    /**
     * Removes all items with the given item id from the cart.
     *
     * @param $item_id
     *
     * @return void
     */
    public function destroyByItemId($item_id)
    {
        $uuids = $this->item_repository->destroyIdCollection($item_id);

        foreach( $uuids as $uuid ) {
            $this->removeFromSession($uuid);
        }
    }

    /**
     * Counts how many items are in the cart.
     *
     * @return int
     */
    public function count() : int
    {
        return $this->content()->count();
    }

    /**
     * Counts how many items of a given item id are in the cart.
     *
     * @return int
     */
    public function countByItemId() : int
    {
        return count($this->item_repository->getAllByIdGroup());
    }

    /**
     * Calculates the total of the cart.
     *
     * @return float
     */
    public function total() : float
    {
        return $this->content()->total();
    }

    /**
     * Builds the cart from the session.
     *
     * @return void
     */
    private function buildFromSession()
    {
        foreach( $_SESSION[self::SESSID][$this->instance_string ?? 'default'] as $uuid => $item ) {
            $this->addFromSessionInfo($uuid, $item);
        }
    }

    /**
     * Adds a new item from session array.
     *
     * @param string $uuid
     * @param array $item
     *
     * @return void
     */
    private function addFromSessionInfo(string $uuid, array $item)
    {
        $item = new Item(
            $uuid,
            $item[self::ITEM_KEY],
            $item[self::PRICE_KEY],
            $item[self::OPTIONS_KEY]
        );

        $this->item_repository->addToRepository($item);
    }

    /**
     * Adds a given item instance to the session.
     *
     * @param Item $item
     *
     * @return void
     */
    private function addToSession(Item $item)
    {
        $_SESSION[self::SESSID][$this->instance_string ?? 'default'][$item->uuid()] = [
            self::ITEM_KEY      => $item->id(),
            self::PRICE_KEY     => $item->price(),
            self::OPTIONS_KEY   => $item->options()
        ];
    }

    /**
     * Removes the item of the given uuid from the session.
     *
     * @param $uuid
     *
     * @return void
     */
    private function removeFromSession($uuid)
    {
        if( isset($_SESSION[self::SESSID][$this->instance_string ?? 'default'][$uuid]) ) {
            unset($_SESSION[self::SESSID][$this->instance_string ?? 'default'][$uuid]);
        }
    }

    /**
     * Generates the internal uuid for given item.
     *
     * @param $item_id
     *
     * @return string
     */
    private function generateInternalID($item_id) : string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $timestamp = (new DateTime('2000-01-01'))->getTimestamp();

        return md5($item_id . $randomString . $timestamp);
    }

    /**
     * Checks if a php session is active.
     *
     * @return bool
     */
    private function checkSessionStatus() : bool
    {
        if( php_sapi_name() === 'cli' ) {
            return false;
        }

        if( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE;
        }

        return !(session_id() === '');
    }

    /**
     * Gets a new CartManager instance.
     *
     * @param string $instance_string
     *
     * @throws SessionException
     *
     * @return CartManager
     */
    private static function getNewInstance(string $instance_string) : CartManager
    {
        $instance = new self($instance_string);
        self::$active_instance = $instance;
        self::$instances[$instance_string] = $instance;
        return $instance;
    }

    /**
     * Gets the current active cart instance.
     *
     * @return CartManager
     */
    private static function getActiveInstance() : CartManager
    {
        if( is_null(self::$active_instance) ) {
            $instance = new self();
            self::$active_instance = $instance;
            self::$default_instance = $instance;
        }

        return self::$active_instance;
    }

    /**
     * Gets the default cart instance.
     *
     * @return CartManager
     */
    private static function getDefaultInstance() : CartManager
    {
        if( is_null(self::$default_instance) ) {
            $instance = new self();
            self::$active_instance = $instance;
            self::$default_instance = $instance;
        }

        return self::$default_instance;
    }
}