<div class="w-full py-2">
    <div class="flex">
        @foreach($stepInstances as $stepInstance)
            @include('livewire-wizard::step-header')
        @endforeach
    </div>
</div>
