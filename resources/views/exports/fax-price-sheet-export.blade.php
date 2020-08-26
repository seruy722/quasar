<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Кл</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">М</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">В</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">За к</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">За м</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Сум</th>
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
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ round($elem->sum) }}</td>
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
