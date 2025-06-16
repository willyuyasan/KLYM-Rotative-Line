
<x-filament-panels::page>

    <form wire:submit.prevent="submmit">

        <div class="space--y-2">
            {{ $this->form }}
            
            <?php /*
            <x-filament::button type="submmit">
                Generate
            </x-filament::button>
            */?>

        </div>

    </form>

    @php
    //var_dump($response);
    @endphp

    <div class="space--y-2">
        {{ $this->table }}
    </div>

</x-filament-panels::page>







