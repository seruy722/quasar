<table>
    <thead>
    <tr>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Дата
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Тип
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Деберц
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            М
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            В
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            За к
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            За м
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Очки
        </th>
{{--        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">--}}
{{--            Пол--}}
{{--        </th>--}}
{{--        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">--}}
{{--            Ск--}}
{{--        </th>--}}
{{--        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">--}}
{{--            Опл--}}
{{--        </th>--}}
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Кат
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Фак
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Прим
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Оп вл
        </th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">
            Кол
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        @php
            $things = json_decode($elem->things)
        @endphp
        <tr style="background-color: #fffe0b;">
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ date('d-m-Y', strtotime($elem->created_at)) }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->type ? 'Оплата' : 'Долг'}}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->code_client_name }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->place === 0 && $elem->type ? null : $elem->place }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->kg === 0 && $elem->type ? null : $elem->kg }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->for_kg === 0 && $elem->type ? null : $elem->for_kg }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->for_place === 0 && $elem->type ? null : $elem->for_place }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ round($elem->sum) }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->category_name }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->fax_name }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem->notation }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ @$things[0]->title }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ @$things[0]->count }}</td>
        </tr>
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
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-size: 10px;font-weight:bold;">{{ $collection->sum('place') }}</td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-size: 10px;font-weight:bold;">{{ $collection->sum('kg') }}</td>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-size: 10px;font-weight:bold;">{{ $collection->sum('sum') }}</td>
        <td></td>
    </tr>
    </tbody>
</table>
