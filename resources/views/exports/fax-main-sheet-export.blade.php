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
    @foreach($collection[0] as $elem)
        @if($elem->brand == 1)
            <tr>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;color: #ff1a17;">{{ $elem->for_kg ? $elem->for_kg : null }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;color: #ff1a17;">{{ $elem->for_place ? $elem->for_place : null }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->sum }}</td>
            </tr>
        @else
            <tr>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="border: 1px solid black;text-align: center;color: #ff1a17;font-weight:bold;">{{ $elem->for_kg ? $elem->for_kg : null }}</td>
                <td style="border: 1px solid black;text-align: center;color: #ff1a17;font-weight: bold;">{{ $elem->for_place ? $elem->for_place : null }}</td>
                <td style="border: 1px solid black;text-align: center;font-weight:bold;">{{  round($elem->sum) }}</td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;"></td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;"></td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{ $collection[0]->sum('place') }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;">{{ $collection[0]->sum('kg') }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;"></td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;background-color: #fffe0b">{{ round($collection[0]->sum('sum')) }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;background-color: #5ccd61">{{ round($collection[0]->sum('sum') - $collection[1]->sum('sum')) }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;background-color: #fffe0b">{{ round($collection[1]->sum('sum'), 1) }}</td>
    </tr>
    <tr></tr>
    <tr></tr>

    @foreach($collection[1] as $category)
        <tr>
            <td>{{ $category->price }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $category->name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $category->place }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $category->kg }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ round($category->kg * $category->price, 1) }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;"></td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{ $collection[1]->sum('place') }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{ $collection[1]->sum('kg') }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{  round($collection[1]->sum('sum'), 1) }}</td>
    </tr>
    <tr></tr>
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Клиент</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Мест</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Вес</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Сумма</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Город</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection[0] as $elem)
        @if($elem->brand == 1)
            <tr>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ round($elem->sum) }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->city_name }}</td>
            </tr>
        @else
            <tr>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="border: 1px solid black;text-align: center;font-weight:bold;">{{  round($elem->sum) }}</td>
                <td style="border: 1px solid black;text-align: center;font-weight:bold;">{{  $elem->city_name }}</td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;"></td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{ $collection[0]->sum('place') }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;">{{ $collection[0]->sum('kg') }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;background-color: #fffe0b">{{ round($collection[0]->sum('sum')) }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;"></td>
    </tr>
    </tbody>
</table>
