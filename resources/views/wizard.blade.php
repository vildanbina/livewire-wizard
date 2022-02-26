<div>
    <x-errors/>
    <form wire:submit.prevent="save">
        @include('livewire-wizard::steps-header')
        <div class="container p-4">
            {{ $this->getCurrentStep() }}
        </div>

        @include('livewire-wizard::steps-footer')
    </form>
</div>
