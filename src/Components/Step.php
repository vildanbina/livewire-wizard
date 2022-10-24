<?php

namespace Vildanbina\LivewireWizard\Components;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component as ViewComponent;
use Vildanbina\LivewireWizard\Concerns\BelongsToLivewire;
use Vildanbina\LivewireWizard\Concerns\HasHooks;
use Vildanbina\LivewireWizard\Contracts\WizardForm;

abstract class Step extends ViewComponent implements Htmlable
{
    use BelongsToLivewire;
    use HasHooks;

    public ?int $order = null;
    public null|array|Model $model = null;
    protected string $view;
    public bool $validationFails = false;

    public function __construct(WizardForm $livewire)
    {
        $this
            ->setLivewire($livewire)
            ->setModel($livewire->getModel());
    }

    public static function make(WizardForm $livewire): static
    {
        return new static($livewire);
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): Step
    {
        $this->model = $model;
        return $this;
    }

    public function icon(): string
    {
        return 'check';
    }

    public function isValid(): bool
    {
        if (method_exists($this, 'validate')) {
            return !validator(['state' => $this->livewire->state], ...$this->validate())->fails();
        }

        return true;
    }

    public function setState(array $state = [])
    {
        $this->getLivewire()->state = $state;
    }

    public function mergeState(array $state = [])
    {
        $this->getLivewire()->mergeState($state);
    }

    public function putState($key, $value = null, $default = null)
    {
        $this->getLivewire()->putState($key, $value, $default);
    }

    abstract public function title(): string;

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function setView(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view($this->getView(), array_merge($this->data(), [
            'container' => $this,
        ]));
    }

    public function getView(): string
    {
        return $this->view;
    }
}
