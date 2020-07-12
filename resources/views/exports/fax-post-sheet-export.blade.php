<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Клиент</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Код</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Вес</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Сумма</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Имя</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Город</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Способ доставки</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Отделение</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Телефон</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Примечания</th>
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
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Способ доставки</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Мест</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Вес</th>
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
