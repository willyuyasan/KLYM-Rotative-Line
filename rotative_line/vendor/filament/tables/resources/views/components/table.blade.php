@props([
    'footer' => null,
    'header' => null,
    'headerGroups' => null,
    'reorderable' => false,
    'reorderAnimationDuration' => 300,
])

<div style="max-height: 500px;">

<table
    {{ $attributes->class(['fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5']) }}
>
    @if ($header)
        <thead class="divide-y divide-gray-200 dark:divide-white/5">
            @if ($headerGroups)
                <tr class="bg-gray-100 dark:bg-transparent">
                    {{ $headerGroups }}
                </tr>
            @endif

            <tr class="bg-gray-50 dark:bg-white/5">
                {{ $header }}
            </tr>
        </thead>
    @endif
    
    <tbody
        @if ($reorderable)
            x-on:end.stop="$wire.reorderTable($event.target.sortable.toArray())"
            x-sortable
            data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
        @endif
        class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5"
    >
        {{ $slot }}
    </tbody>
    
    @if ($footer)
        <tfoot class="bg-gray-50 dark:bg-white/5">
            <tr>
                {{ $footer }}
            </tr>
        </tfoot>
    @endif

</table>

</div>

<style>
    table {
        position: relative;
    }

    table th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fafafa;
    }

    /*
    table th:nth-child(1) {
        left: 0;
        z-index: 3;
    }
    
    table tbody tr td:nth-child(1) {
        position: sticky;
        left: 0;
        z-index: 1;
        background: white;
        background-clip: padding-box;
    }
    */
</style>


