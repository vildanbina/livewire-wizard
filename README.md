[![Latest Stable Version](http://poser.pugx.org/vildanbina/livewire-wizard/v)](https://packagist.org/packages/vildanbina/livewire-wizard)
[![Total Downloads](http://poser.pugx.org/vildanbina/livewire-wizard/downloads)](https://packagist.org/packages/vildanbina/livewire-wizard)
[![Latest Unstable Version](http://poser.pugx.org/vildanbina/livewire-wizard/v/unstable)](https://packagist.org/packages/vildanbina/livewire-wizard)
[![License](http://poser.pugx.org/vildanbina/livewire-wizard/license)](https://packagist.org/packages/vildanbina/livewire-wizard)
[![PHP Version Require](http://poser.pugx.org/vildanbina/livewire-wizard/require/php)](https://packagist.org/packages/vildanbina/livewire-wizard)

A dynamic Laravel Livewire component for multi steps form.

![Multi steps form](https://user-images.githubusercontent.com/51203303/155848196-e3569891-cb63-499d-8079-a63a30925b77.png)

## Installation

You can install the package via composer:

``` bash
composer require vildanbina/livewire-wizard
```

For UI design this package require [WireUI package](https://livewire-wireui.com) for details.

## Alpine

Livewire Wizard requires [Alpine](https://github.com/alpinejs/alpine). You can use the official CDN to quickly include Alpine:

```html
<!-- Alpine v2 -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

<!-- Alpine v3 -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

## TailwindCSS

The base modal is made with TailwindCSS. If you use a different CSS framework I recommend that you publish the modal template and change the markup to include the required classes for your CSS framework.

```shell
php artisan vendor:publish --tag=livewire-wizard-views
```

## Usage

### Creating a wizard form

You can create livewire component `php artisan make:livewire UserWizard` to make the initial Livewire component. Open your component class and make sure it extends the `WizardComponent` class:

```php
<?php

namespace App\Http\Livewire;

use Vildanbina\LivewireWizard\WizardComponent;
use App\Models\User;

class UserWizard extends WizardComponent
{
    // My custom class property
    public $userId;
    
    /*
     * Will return App\Models\User instance or will create empty User (based on $userId parameter) 
     */
    public function model()
    {
        return User::findOrNew($this->userId);
    }
}
```

When you need to display wizard form, based on above example we need to pass `$userId` value and to display wizard form:

```html 
<livewire:user-wizard user-id="3"/>
```

Or when you want to create new user, let blank `user-id` attribute, or don't put that.

When you want to reset form, ex. To reset to the first step, and clear filled fields. You can use:

```php
$wizardFormInstance->resetForm();
```

When you want to have current step instance. You can use:

```php
$wizardFormInstance->getCurrentStep();
```

When you want to go to specific step. You can use:

```php
$wizardFormInstance->setStep($step);
```

Or, you want to go in the next step:

```php
$wizardFormInstance->goToNextStep();
```

Or, you want to go in the prev step:

```php
$wizardFormInstance->goToPrevStep();
```

### Creating a wizard step

You can create wizard form step. Open or create your step class (at `App\Steps` folder) and make sure it extends the `Step` class:

```php
<?php

namespace App\Steps;

use Vildanbina\LivewireWizard\Components\Step;
use Illuminate\Validation\Rule;

class General extends Step
{
    // Step view located at resources/views/steps/general.blade.php 
    protected string $view = 'steps.general';

    /*
     * Initialize step fields
     */
    public function mount()
    {
        $this->mergeState([
            'name'                  => $this->model->name,
            'email'                 => $this->model->email,
        ]);
    }
    
    /*
    * Step icon 
    */
    public function icon(): string
    {
        return 'check';
    }

    /*
     * When Wizard Form has submitted
     */
    public function save($state)
    {
        $user = $this->model;

        $user->name     = $state['name'];
        $user->email    = $state['email'];
        
        $user->save();
    }

    /*
     * Step Validation
     */
    public function validate()
    {
        return [
            [
                'state.name'     => ['required', Rule::unique('users', 'name')->ignoreModel($this->model)],
                'state.email'    => ['required', Rule::unique('users', 'email')->ignoreModel($this->model)],
            ],
            [],
            [
                'state.name'     => __('Name'),
                'state.email'    => __('Email'),
            ],
        ];
    }

    /*
     * Step Title
     */
    public function title(): string
    {
        return __('General');
    }
}
```

In Step class, you can use livewire hooks example:

```php
use Vildanbina\LivewireWizard\Components\Step;

class General extends Step
{
    public function onStepIn($name, $value)
    {
        // Something you want
    }

    public function onStepOut($name, $value)
    {
        // Something you want
    }

    public function updating($name, $value)
    {
        // Something you want
    }

    public function updatingState($name, $value)
    {
        // Something you want
    }
    
    public function updated($name, $value)
    {
        // Something you want
    }

    public function updatedState($name, $value)
    {
        // Something you want
    }
}
```

Each step need to have view, you can pass view path in `$view` property.

After create step class, you need to put that step to wizard form:

```php
<?php

namespace App\Http\Livewire;

use App\Steps\General;
use Vildanbina\LivewireWizard\WizardComponent;

class UserWizard extends WizardComponent
{
    public array $steps = [
        General::class,
        // Other steps...
    ];
   
    ...
}
```

## Building Tailwind CSS for production

Because some classes are dynamically build and to compile js you should add some classes to the purge safelist so your `tailwind.config.js` should look something like this:

```js
module.exports = {
    presets: [
        require("./vendor/wireui/wireui/tailwind.config.js")
    ],
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",

        "./vendor/vildanbina/livewire-wizard/resources/views/*.blade.php",
        "./vendor/wireui/wireui/resources/**/*.blade.php",
        "./vendor/wireui/wireui/ts/**/*.ts",
        "./vendor/wireui/wireui/src/View/**/*.php"
    ],
    plugins: [
        require("@tailwindcss/forms"),
    ],
};

```

If you haven't installed `tailwindcss/forms` plugin, install it: `npm install -D @tailwindcss/forms`

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please e-mail vildanbina@gmail.com to report any security vulnerabilities instead of the issue tracker.

## Credits

- [Vildan Bina](https://github.com/vildanbina)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
