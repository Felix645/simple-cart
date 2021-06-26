<?php


namespace Neon\Cart;


class ItemCollection
{
    /**
     * Collection of items.
     *
     * @var Item[]
     */
    private $items_list = [];

    /**
     * Adds an item to the collection.
     *
     * @param Item $item
     *
     * @return void
     */
    public function addToCollection(Item $item)
    {
        $this->items_list[$item->uuid()] = $item;
    }

    /**
     * Gets all items from the collection.
     *
     * @return Item[]
     */
    public function all() : array
    {
        return $this->items_list;
    }

    /**
     * Finds an item with given uuid from the collection.
     *
     * @param $uuid
     *
     * @return Item|null
     */
    public function find($uuid) : ?Item
    {
        return $this->items_list[$uuid] ?? null;
    }

    /**
     * Counts how many items are in the collection.
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->items_list);
    }

    /**
     * Checks if collection is empty or not.
     *
     * @return bool
     */
    public function empty() : bool
    {
        return empty($this->items_list);
    }

    /**
     * Removes the item with the given uuid from the collection.
     *
     * @param $uuid
     *
     * @return void
     */
    public function remove($uuid)
    {
        if( isset($this->items_list[$uuid]) ) {
            unset($this->items_list[$uuid]);
        }
    }

    /**
     * Checks if items with the given item ID are present within the collection.
     * Returns the uuid of the first item found.
     * Returns NULL if nothing was found.
     *
     * @param $item_id
     *
     * @return string|null
     */
    public function hasItemId($item_id) : ?string
    {
        foreach( $this->items_list as $item ) {
            if( $item_id === $item->id() ) {
                return $item->uuid();
            }
        }

        return null;
    }

    /**
     * Gets an array of all uuid's in the collection.
     *
     * @return array
     */
    public function getUuIDs() : array
    {
        return array_keys($this->items_list);
    }

    /**
     * Calculates the total price of the cart.
     *
     * @return float|int
     */
    public function total()
    {
        $total = 0;

        foreach( $this->items_list as $item ) {
            $total += $item->price() ?? 0;
        }

        return $total;
    }
}