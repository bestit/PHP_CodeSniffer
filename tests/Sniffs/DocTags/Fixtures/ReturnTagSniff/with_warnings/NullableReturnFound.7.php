<?php

class NullableReturnFound {
    /**
     * @return bool
     */
    public function foo(): ?bool
    {
        return true;
    }
}