<div class="w-full pb-6 pt-2">
    <div class="flex">
        @foreach($stepInstances as $stepInstance)
            @include('livewire-wizard::step-header')
        @endforeach
    </div>
</div>
