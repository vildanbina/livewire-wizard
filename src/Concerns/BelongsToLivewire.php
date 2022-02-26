<?php

namespace Vildanbina\LivewireWizard\Concerns;

use Vildanbina\LivewireWizard\Contracts\WizardForm;

trait BelongsToLivewire
{
    protected WizardForm $livewire;

    public function setLivewire(WizardForm $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): WizardForm
    {
        return $this->livewire;
    }
}
