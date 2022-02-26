<div>
    @if($this->hasPrevStep())
        <x-button dark label="Back" wire:click="goToPrevStep" spinner="goToPrevStep"/>
    @endif
    @if($this->hasNextStep())
        <x-button primary wire:click="goToNextStep" spinner="goToNextStep" label="Next"/>
    @else
        <x-button primary type="submit" spinner="submit" label="Submit"/>
    @endif
</div>
