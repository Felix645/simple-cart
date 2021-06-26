<?php


namespace Neon\Cart;


class ItemRepository
{
    /**
     * Collection which holds all cart items.
     *
     * @var ItemCollection
     */
    private $all;

    /**
     * Array of collections which hold items by their item ID.
     *
     * @var ItemCollection[]
     */
    private $by_id = [];

    /**
     * ItemRepository constructor.
     */
    public function __construct()
    {
        $this->all = new ItemCollection();
    }

    /**
     * Adds an item to the repository.
     *
     * @param Item $item
     *
     * @return void
     */
    public function addToRepository(Item $item)
    {
        $this->all->addToCollection($item);

        if( empty($this->by_id[$item->id()]) || !$this->by_id[$item->id()] instanceof ItemCollection) {
            $this->by_id[$item->id()] = new ItemCollection();
        }

        $this->by_id[$item->id()]->addToCollection($item);
    }

    /**
     * Gets collection of all cart items.
     *
     * @return ItemCollection
     */
    public function getAll() : ItemCollection
    {
        return $this->all;
    }

    /**
     * Gets array of collections by item id.
     *
     * @return ItemCollection[]
     */
    public function getAllByIdGroup() : array
    {
        return $this->by_id;
    }

    /**
     * Gets the item collection with the given item id.
     * Returns NULL if nothing was found.
     *
     * @param $item_id
     *
     * @return ItemCollection|null
     */
    public function getById($item_id) : ?ItemCollection
    {
        return $this->by_id[$item_id] ?? null;
    }

    /**
     * Removes the item with the given uuid from ALL collections.
     *
     * @param $uuid
     *
     * @return void
     */
    public function remove($uuid)
    {
        $this->all->remove($uuid);

        foreach($this->by_id as $collection) {
            $collection->remove($uuid);
        }
    }

    /**
     * Removes the first item with given item ID from all collections.
     *
     * @param $item_id
     *
     * @return string|null
     */
    public function removeByItemId($item_id) : ?string
    {
        $uuid = $this->all->hasItemId($item_id);

        if( is_null($uuid) ) {
            return null;
        }

        if( isset($this->by_id[$item_id]) ) {
            $this->by_id[$item_id]->remove($uuid);
            if( empty($this->by_id[$item_id]->all()) ) {
                unset($this->by_id[$item_id]);
            }
        }

        return $uuid;
    }

    /**
     * Destroy the whole collection which resembles the given item ID.
     * Returns all removed uuid's as an array.
     * Returns an empty array if no items with the given item ID exist.
     *
     * @param $item_id
     *
     * @return array
     */
    public function destroyIdCollection($item_id) : array
    {
        if( isset($this->by_id[$item_id]) ) {
            $uuids = $this->by_id[$item_id]->getUuIDs();
            unset($this->by_id[$item_id]);

            foreach( $uuids as $uuid ) {
                $this->all->remove($uuid);
            }
        }

        return $uuids ?? [];
    }
}