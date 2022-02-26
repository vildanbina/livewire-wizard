<div class="w-1/3">
    <div class="relative mb-2">
        @if(!$loop->first)
            <div class="absolute" style="width: calc(100% - 2.5rem - 1rem); top: 50%; transform: translate(-50%, -50%)">
                <div class="bg-gray-200 rounded flex-1">
                    <div class="bg-green-300 rounded py-1 w-{{ $this->stepIsGreaterOrEqualThan($stepInstance->getOrder()) ? 'full' : '0' }}"></div>
                </div>
            </div>
        @endif

        <div class="w-10 mx-auto">
            <x-button.circle
                :positive="$this->stepIsGreaterOrEqualThan($stepInstance->getOrder())"
                wire:click="setStep({{ $stepInstance->getOrder() }})"
                icon="{{ $stepInstance->icon() }}"
            />
        </div>
    </div>
    <div class="text-xs text-center md:text-base">{{ $stepInstance->title() }}</div>
</div>
