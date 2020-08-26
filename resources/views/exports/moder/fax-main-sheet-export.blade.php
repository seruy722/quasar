<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Кл</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">М</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">В</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Кат</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection[0] as $elem)
        @if($elem->brand == 1)
            <tr>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->category }}</td>
            </tr>
        @else
            <tr>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="border: 1px solid black;text-align: center;">{{  $elem->category }}</td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;"></td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{ $collection[0]->sum('place') }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;">{{ $collection[0]->sum('kg') }}</td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;"></td>
    </tr>
    </tbody>
</table>
