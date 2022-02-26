<?php

namespace Vildanbina\LivewireWizard;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireWizardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-wizard')
            ->hasConfigFile()
            ->hasViews();
    }
}
