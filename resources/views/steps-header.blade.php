<div class="w-full py-6">
    <div class="flex">
        @foreach($stepInstances as $stepInstance)
            @include('livewire-wizard::step-header')
        @endforeach
    </div>
</div>
