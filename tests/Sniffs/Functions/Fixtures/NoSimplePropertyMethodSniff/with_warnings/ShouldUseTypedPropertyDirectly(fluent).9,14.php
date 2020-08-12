<?php

namespace BestIt\Sniffs\Functions\NoSimplePropertyMethodSniff;

class ShouldUseTypedPropertyDirectlyInsteadSetter
{
    public string $without = 'bar';

    public function getWithout(): string
    {
        return $this->without;
    }

    public function setWithout(string $with): self
    {
        $this->without = $with;

        return $this;
    }
}