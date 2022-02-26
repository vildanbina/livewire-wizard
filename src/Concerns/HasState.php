<?php

namespace Vildanbina\LivewireWizard\Concerns;

trait HasState
{
    public array $state = [];

    public function getState(): array
    {
        return $this->state;
    }

    public function setState(array $state): static
    {
        $this->state = $state;
        return $this;
    }

    public function mergeState(array $state): static
    {
        $this->state = array_merge($this->state, $state);
        return $this;
    }

    public function putState($key, $value = null, $default = null): static
    {
        data_set($this->state, $key, $value, $default);
        return $this;
    }
}
