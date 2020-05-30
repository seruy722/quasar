<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Код</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Клиент</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Мест</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Вес</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Категория</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Магазин</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Примечания</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Опись вложения</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">К-тво</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection[0] as $elem)
        @php
            $things = json_decode($elem->things)
        @endphp
        @if($elem->brand == 1)
            <tr>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->code_client_name }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->category_name }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->shop }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ $elem->notation }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ @$things[0]->title }}</td>
                <td style="font-weight:bold;border: 1px solid black;text-align: center;">{{ @$things[0]->count }}</td>
            </tr>
        @else
            <tr>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->code_place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->code_client_name }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->place }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->kg }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->category_name }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->shop }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ $elem->notation }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ @$things[0]->title }}</td>
                <td style="border: 1px solid black;text-align: center;">{{ @$things[0]->count }}</td>
            </tr>
        @endif
        @if(isset($things[0]))
            @foreach($things as $thing)
                @if (!$loop->first)
                    <tr>
                        <td style="border: 1px solid black;text-align: center;"></td>
                        <td style="border: 1px solid black;text-align: center;"></td>
                        <td style="border: 1px solid black;text-align: center;"></td>
                        <td style="border: 1px solid black;text-align: center;"></td>
                        <td style="border: 1px solid black;text-align: center;"></td>
                        <td style="border: 1px solid black;text-align: center;"></td>
                        <td style="border: 1px solid black;text-align: center;"></td>
                        <td style="border: 1px solid black;text-align: center;">{{ $thing->title }}</td>
                        <td style="border: 1px solid black;text-align: center;">{{ $thing->count }}</td>
                    </tr>
                @endif
            @endforeach
        @endif
    @endforeach
    <tr>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{$collection[0]->sum('place')}}</td>
        <td style="border: 1px solid black;text-align: center;font-weight:bold;">{{$collection[0]->sum('kg')}}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>

    @foreach($collection[1] as $category)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{$category->name}}</td>
            <td style="border: 1px solid black;text-align: center;">{{$category->place}}</td>
            <td style="border: 1px solid black;text-align: center;">{{$category->kg}}</td>
        </tr>
    @endforeach
    <tr>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;"></td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{$collection[1]->sum('place')}}</td>
        <td style="border: 1px solid black;text-align: center;font-weight: bold;">{{$collection[1]->sum('kg')}}</td>
    </tr>

    </tbody>
</table>
