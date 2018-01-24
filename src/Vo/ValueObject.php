<?php

namespace App\Vo;

class ValueObject implements \JsonSerializable
{
    public function populate($object)
    {
        $attributes = get_object_vars($this);

        foreach ($attributes as $name => $value) {
            if (isset($object->$name)) {
                $this->$name = $object->$name;
            }
        }
    }

    public function populateFromArray(array $object)
    {
        $attributes = get_object_vars($this);

        foreach ($attributes as $name => $value) {
            if (isset($object[$name])) {
                $this->$name = $object[$name];
            }
        }
    }

    public function toArray()
    {
        $attributes = get_object_vars($this);
        $tab = [];
        foreach ($attributes as $name => $value) {
            $tab[$name] = $value;
        }

        return $tab;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
