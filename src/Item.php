<?php


namespace Neon\Cart;


class Item
{
    /**
     * Internal uuid.
     *
     * @var string
     */
    private $internal_id;

    /**
     * Item ID.
     *
     * @var mixed
     */
    private $item_id;

    /**
     * Price of the item.
     *
     * @var numeric|int
     */
    private $price;

    /**
     * Item options array.
     *
     * @var array
     */
    private $options;

    /**
     * Item constructor.
     *
     * @param string $internal_id
     * @param mixed $item_id
     * @param numeric|int $price
     * @param array $options
     */
    public function __construct(string $internal_id, $item_id, $price, array $options = [])
    {
        $this->internal_id = $internal_id;
        $this->item_id = $item_id;
        $this->price = $price;
        $this->options = $options;
    }

    /**
     * Gets the items uuid.
     *
     * @return string
     */
    public function uuid() : string
    {
        return $this->internal_id;
    }

    /**
     * Gets the item id.
     *
     * @return mixed
     */
    public function id()
    {
        return $this->item_id;
    }

    /**
     * Gets the item price.
     *
     * @return float|int|string
     */
    public function price()
    {
        return $this->price;
    }

    /**
     * Gets the item options.
     *
     * @return array
     */
    public function options() : array
    {
        return $this->options;
    }

    /**
     * Updates the item price.
     *
     * @param $price
     *
     * @return $this
     */
    public function updatePrice($price) : Item
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Updates the item options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function updateOptions(array $options) : item
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }
}