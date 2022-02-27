<?php

namespace Vildanbina\LivewireWizard\Concerns;

use Arr;

trait HasSteps
{
    public int $activeStep = 0;
    public array $steps = [];

    public function stepIs($step): bool
    {
        return $this->activeStep == $step;
    }

    public function stepIsGreaterOrEqualThan($step): bool
    {
        return $this->activeStep >= $step;
    }

    public function stepIsLessOrEqualThan($step): bool
    {
        return $this->activeStep <= $step;
    }

    public function goToNextStep($step = null): void
    {
        $this->setStep($this->nextStep($step));
    }

    public function setStep($step): void
    {
        $this->callHook('beforeSetStep', $this->activeStep, $step);

        if ($this->hasPrevStep($step)) {
            $this->stepsValidation($this->prevStep($step));
        }

        $this->getCurrentStep()->callHook('onStepOut');

        $this->activeStep = $step;

        $this->getCurrentStep()->callHook('onStepIn');

        $this->callHook('afterSetStep', $this->activeStep, $step);
    }

    public function hasPrevStep($step = null): bool
    {
        $step ??= $this->activeStep;
        return Arr::has($this->steps(), (int) $step - 1);
    }

    public function prevStep($step = null): int
    {
        $step ??= $this->activeStep;
        return $this->hasPrevStep($step) ? $step - 1 : $step;
    }

    public function nextStep($step = null): int
    {
        $step ??= $this->activeStep;
        return $this->hasNextStep() ? $step + 1 : $step;
    }

    public function hasNextStep($step = null): bool
    {
        $step ??= $this->activeStep;
        return Arr::has($this->steps(), $step + 1);
    }

    public function stepIsGreaterThan($step): bool
    {
        return $this->activeStep > $step;
    }

    public function totalSteps(): int
    {
        return count($this->steps());
    }

    public function goToPrevStep($step = null): void
    {
        $this->setStep($this->prevStep($step));
    }

    public function stepIsLessThan($step): bool
    {
        return $this->activeStep < $step;
    }
}
