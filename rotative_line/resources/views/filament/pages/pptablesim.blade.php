<table class="table table-striped table-bordered table-hover">

    <x-filament::input.wrapper>

    <thead>
        <tr>
                <th>Couta</th>
                <th>Fecha de inicio de cuota</th>
                <th>Fecha de fin de cuota</th>
                <th>Dias de la cuota</th>
                <th>Valor cuota</th>
                <th>Valor cuota a capital</th>
                <th>Valor cuota a interes</th>
                <th>Balance dueda capital</th>
            
        </tr>
    </thead>

    </x-filament::input.wrapper>

    <x-filament::input.wrapper>

    <tbody>

        @foreach($response as $res1)

        <tr>
            @foreach($res1 as $res2)
            <td>{{ $res2 }}</td>
            @endforeach
        </tr>

        @endforeach

    </tbody>

    </x-filament::input.wrapper>

</table>