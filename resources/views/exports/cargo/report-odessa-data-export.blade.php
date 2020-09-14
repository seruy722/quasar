<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Деберц</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Код</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">М</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">В</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Ф</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->code_client_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->fax_name }}</td>
        </tr>
    @endforeach

    <tr>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ $collection->sum('place') }}</td>
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
