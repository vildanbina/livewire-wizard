<?php

namespace Vildanbina\LivewireWizard;

use Closure;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Str;
use Vildanbina\LivewireWizard\Components\Step;
use Vildanbina\LivewireWizard\Concerns\HasHooks;
use Vildanbina\LivewireWizard\Concerns\HasState;
use Vildanbina\LivewireWizard\Concerns\HasSteps;
use Vildanbina\LivewireWizard\Contracts\WizardForm;

abstract class WizardComponent extends Component implements WizardForm
{
    use HasSteps;
    use HasHooks;
    use HasState;

    public bool $saveStepState = true;
    public null|array|Model $model = null;
    protected array $cachedSteps = [];

    public function __construct($id = null)
    {
        parent::__construct($id);

        if ($this->saveStepState) {
            $this->queryString[] = 'activeStep';
        }
    }

    public function resetForm(): void
    {
        $this->callHook('beforeResetForm');

        $this->setStep(array_key_first($this->steps()));
        $this->mount();

        $this->callHook('afterResetForm');
    }

    public function steps(): array
    {
        if (property_exists($this, 'steps')) {
            return $this->steps;
        }

        return [];
    }

    public function mount()
    {
        $this->callHook('beforeMount', ...func_get_args());

        if (method_exists($this, 'model')) {
            $this->model = $this->model();
        }

        $this->stepClasses(function (Step $step) {
            
            if (method_exists($this, 'model')) {
                $step->setModel($this->model);
            }
            
            if (method_exists($step, 'mount')) {
                $step->mount();
            }

            if ($step->getOrder() < $this->activeStep && !$step->isValid()) {
                $this->setStep($step->getOrder());
            }
        });

        $this->callHook('afterMount', ...func_get_args());
    }

    protected function stepClasses(null|Closure $callback = null): array
    {

        if (filled($this->cachedSteps)) {
            return collect($this->cachedSteps)
                ->each(fn(Step $step, $index) => value($callback, $step, $index))
                ->toArray();
        }

        if (filled($this->steps())) {
            $this->cachedSteps = collect($this->steps())
                ->map(function ($step, $index) use ($callback) {
                    if (class_exists($step) && is_subclass_of($step, Step::class)) {
                        $stepInstance = $step::make($this);

                        if (is_null($stepInstance->getOrder())) {
                            $stepInstance->setOrder($index);
                        }

                        return $stepInstance;
                    }
                    return null;
                })
                ->filter()
                ->sortBy('order')
                ->values()
                ->toArray();

            if ($callback instanceof Closure) {
                return $this->stepClasses($callback);
            }
        }

        return $this->cachedSteps;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function updated($name, $value): void
    {
        $this->callHooksStep('updated', $name, $value);
    }

    private function callHooksStep($hook, $name, $value): void
    {
        $stepInstance = $this->getCurrentStep();
        $name         = Str::of($name);

        $propertyName     = $name->studly()->before('.');
        $keyAfterFirstDot = $name->contains('.') ? $name->after('.')->__toString() : null;
        $keyAfterLastDot  = $name->contains('.') ? $name->afterLast('.')->__toString() : null;

        $beforeMethod = $hook . $propertyName;

        $beforeNestedMethod = $name->contains('.')
            ? $hook . $name->replace('.', '_')->studly()
            : false;

        $stepInstance->callHook($beforeMethod, $value, $keyAfterFirstDot);

        if ($beforeNestedMethod) {
            $stepInstance->callHook($beforeNestedMethod, $value, $keyAfterLastDot);
        }
    }

    public function getCurrentStep(): ?Step
    {
        return $this->getStepInstance($this->activeStep);
    }

    public function getStepInstance($step): ?Step
    {
        if (($stepInstance = data_get($this->stepClasses(), $step)) && !$stepInstance instanceof Step) {
            throw new Exception(get_class($stepInstance) . ' must bee ' . Step::class . ' instance');
        }

        return $stepInstance;
    }

    public function updating($name, $value): void
    {
        $this->callHooksStep('updating', $name, $value);
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');

        $this->stepsValidation();

        $this->callHook('afterValidate');

        $state = $this->mutateStateBeforeSave($this->getState());

        $this->callHook('beforeSave');

        $this->stepClasses(function (Step $stepInstance) use ($state) {
            if (method_exists($stepInstance, 'save')) {
                $stepInstance->save($state);
            }
        });

        $this->callHook('afterSave');
    }

    protected function stepsValidation($step = null): void
    {
        [$rules, $messages, $attributes] = [[], [], []];
        $step = $step ?? max(array_keys($this->steps()));

        $this->stepClasses(function (Step $stepInstance) use ($step, &$rules, &$messages, &$attributes) {

            if (method_exists($stepInstance, 'validate') && $stepInstance->getOrder() <= $step) {
                $stepValidate = $stepInstance->validate();
                $stepInstance->validationFails = !$stepInstance->isValid();

                $rules      = array_merge($rules, $stepValidate[0] ?? []);
                $messages   = array_merge($messages, $stepValidate[1] ?? []);
                $attributes = array_merge($attributes, $stepValidate[2] ?? []);
            }
        });

        if (filled($rules)) {
            $this->validate($rules, $messages, $attributes);
        }
    }

    public function mutateStateBeforeSave(array $state = []): array
    {
        return $state;
    }

    public function render(): View
    {
        return view('livewire-wizard::wizard', [
            'stepInstances' => $this->stepClasses(),
        ]);
    }
}
