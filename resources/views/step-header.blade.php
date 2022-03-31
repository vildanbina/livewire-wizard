@php
    $isStepValid = $stepInstance->isValid();
    $isFailedStep = $stepInstance->validationFails;
    $stepIsGreaterOrEqualThan = $this->stepIsGreaterOrEqualThan($stepInstance->getOrder());
@endphp
<div class="w-1/3">
    <div class="relative mb-2">
        @if(!$loop->first)
            <div class="absolute" style="width: calc(100% - 2.5rem - 1rem); top: 50%; transform: translate(-50%, -50%)">
                <div class="bg-gray-200 rounded flex-1">
                    <div
                        @class([
                            'rounded py-0.5',
                            'bg-green-300' => $stepIsGreaterOrEqualThan && !$isFailedStep,
                            'bg-red-300' => $isFailedStep,
                            'w-full' => $isFailedStep || $stepIsGreaterOrEqualThan,
                            'w-0' =>  !($isFailedStep || $stepIsGreaterOrEqualThan)
                        ])
                    ></div>
                </div>
            </div>
        @endif

        <div class="grid place-items-center">
            <x-button.circle
                :positive="$stepIsGreaterOrEqualThan && !$isFailedStep"
                :negative="$isFailedStep"
                wire:click="setStep({{ $stepInstance->getOrder() }})"
                icon="{{ $stepInstance->icon() }}"
            />
        </div>
    </div>
    <div class="text-xs text-center md:text-base">{{ $stepInstance->title() }}</div>
</div>
