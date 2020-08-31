<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Деберц</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Код</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">В</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Очки</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Им</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Гор</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Сп. доставки</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Отд</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Тел</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">При</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->code_client_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ round($elem->kg * $elem->for_kg + $elem->for_place) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->city_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->delivery_method_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->department }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->phone }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->notation }}</td>
        </tr>
    @endforeach

    <tr>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ $collection->sum('kg') }}</td>
    </tr>
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Город</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Мест</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Вес</th>
    </tr>
    </thead>
    <tbody>
    @php
        $countData = $collection->groupBy('city_name');
    @endphp
    @foreach($countData as $key => $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ $key ? $key : 'Неизвестно' }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ count($elem) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->sum('kg') }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight: bold;">{{ $collection->count() }}</td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight: bold;">{{ $collection->sum('kg') }}</td>
    </tr>
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Сп доставки</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">М</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">В</th>
    </tr>
    </thead>
    <tbody>
    @php
        $countData2 = $collection->groupBy('delivery_method_name');
    @endphp
    @foreach($countData2 as $key => $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ $key ? $key : null }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ count($elem) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->sum('kg') }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight: bold;">{{ $collection->count() }}</td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight: bold;">{{ $collection->sum('kg') }}</td>
    </tr>
    </tbody>
</table>
