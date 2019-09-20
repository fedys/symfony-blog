<?php

namespace App\Collection;

trait CollectionTrait
{
    /**
     * Sets property and returns new instance.
     *
     * @param string $name
     * @param $value
     *
     * @return self
     */
    private function setProperty(string $name, $value): self
    {
        $clone = clone $this;
        $clone->$name = $value;

        return $clone;
    }
}
