<?php

namespace QiuQiuX\SerialNo;

class Config
{

    protected $data = [];

    public function __construct(array $conf)
    {
        foreach ($conf as $key => $item) {
            $this->data[$key] = $item;
        }
    }

    public function __get($key)
    {
        return $this->data[$key];
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

//    public function offsetExists($offset)
//    {
//        return isset($this->data[$offset]);
//    }
//
//    public function offsetGet($offset)
//    {
//        return $this->data[$offset];
//    }
//
//
//    public function offsetSet($offset, $value)
//    {
//        $this->data[$offset] = $value;
//    }
//
//
//    public function offsetUnset($offset)
//    {
//        unset($this->data[$offset]);
//    }

}

 