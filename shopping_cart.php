<?php

class shopping_cart
{
    protected $cust_id;
    private $items = [];

    public function __construct()
    {
        $this->cust_id = md5('shopping_cart');
        $this->read();
    }

    public function getProducts()
    {
        return $this->items;
    }

    public function isEmpty()
    {
        return empty(array_filter($this->items));
    }

    public function getAllProducts()
    {
        $total = 0;
        foreach ($this->items as $items) {
            foreach ($items as $item) {
                    $total+=1;
            }
        }
        return $total;
    }

    public function delete()
    {
        $this->items = [];
        $this->write();
    }

    public function addProducts($id)
    {
        $quantity = 1;

        if (isset($this->items[$id])) {
            foreach ($this->items[$id] as $index => $item) {
                $this->items[$id][$index]['quantity'] += $quantity;
                $this->write();
                return true;
            }
        }
        $this->items[$id][] = [
            'id'         => $id,
            'quantity'   => $quantity,
        ];
        $this->write();
        return true;
    }
	
    public function remove($id)
    {
        if (!isset($this->items[$id])) {
            return false;
        }
        foreach ($this->items[$id] as $index => $item) {
            unset($this->items[$id][$index]);
            $this->write();
            return true;
        }
        return false;
    }

    public function destroy()
    {
        $this->items = [];
        setcookie($this->cust_id, '', -1);
    }

    private function read()
    {
        if (isset($_COOKIE[$this->cust_id])) {
            $this->items = json_decode($_COOKIE[$this->cust_id], true);
        } else {
            $this->items = json_decode('[]', true);
        }
    }

    private function write()
    {
        setcookie($this->cust_id, json_encode(array_filter($this->items)), time() + 604800);
    }
}

