<?php

namespace BestIt\Sniffs\Functions\NoSimplePropertyMethodSniff;

class CorrectClass
{
    /**
     * @phpcsSuppress BestIt.Functions.NoSimplePropertyMethod.ShouldUseTypedPropertyDirectly
     * @var string
     */
    public string $ignore = 'baz';

    public $with = 'foo';

    public string $without = 'bar';

    public function getIgnore(): void
    {
        return $this->ignore;
    }

    public function setIgnore(string $ignore): void
    {
        $this->ignore = $ignore;
    }

    public function getWith(): string
    {
        return $this->with;
    }

    public function setWith(string $with): self
    {
        $this->with = $with;

        return $this;
    }

    public function setWithout(string $with): self
    {
        $this->with = $with;

        return $this;
    }
}