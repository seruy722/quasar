<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Клиент</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Мест</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Вес</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">За кг</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">За место</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Сумма</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        @if($elem->brand == 1)
            <tr>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->code }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;color: #ff1a17;">{{ $elem->for_kg ? $elem->for_kg : null }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;color: #ff1a17;">{{ $elem->for_place ? $elem->for_place : null }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->sum }}</td>
            </tr>
        @else
            <tr>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->code }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="border: 1px solid black;text-align: center;color: #ff1a17;font-weight:bold;">{{ $elem->for_kg ? $elem->for_kg : null }}</td>
                <td style="border: 1px solid black;text-align: center;color: #ff1a17;font-weight:bold;">{{ $elem->for_place ? $elem->for_place : null }}</td>
                <td style="border: 1px solid black;text-align: center;font-weight:bold;">{{ round($elem->sum) }}</td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td style="font-weight: bold;border: 1px solid black;text-align: center;"></td>
        <td style="font-weight: bold;border: 1px solid black;text-align: center;">{{ $collection->sum('place') }}</td>
        <td style="font-weight: bold;border: 1px solid black;text-align: center;">{{ $collection->sum('kg') }}</td>
        <td style="font-weight: bold;border: 1px solid black;text-align: center;"></td>
        <td style="font-weight: bold;border: 1px solid black;text-align: center;"></td>
        <td style="font-weight: bold;border: 1px solid black;text-align: center;background-color: #fffe0b">{{ round($collection->sum('sum')) }}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    </tbody>
</table>
