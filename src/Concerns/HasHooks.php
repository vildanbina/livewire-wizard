<?php

namespace Vildanbina\LivewireWizard\Concerns;

trait HasHooks
{
    public function callHook(string $hook, ...$args): void
    {
        if (!method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}(...$args);
    }
}
