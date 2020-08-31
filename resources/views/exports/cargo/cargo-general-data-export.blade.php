<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Дата</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Тип</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Деберц</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">М</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">В</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">За к</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">За м</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Очки</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Ск</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Опл</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Кат</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Фак</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Прим</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr style="background-color: #fffe0b;">
            <td style="border: 1px solid black;text-align: center;">{{ date('d-m-Y', strtotime($elem->created_at)) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->type ? 'Оплата' : 'Долг'}}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->code_client_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->place === 1 && $elem->type ? null : $elem->place }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->kg === 0 && $elem->type ? null : $elem->kg }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->for_kg === 0 && $elem->type ? null : $elem->for_kg }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->for_place === 0 && $elem->type ? null : $elem->for_place }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ round($elem->sum) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ round($elem->sale) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->paid ? 'Да' : $elem->type ? null : 'Нет' }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->category_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->fax_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->notation }}</td>
        </tr>
    @endforeach

    <tr>
        <td></td>
        <td></td>
        <td></td>
                <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ $collection->sum('place') }}</td>
                <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ $collection->sum('kg') }}</td>
        <td></td>
        <td></td>
                <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ round($collection->sum('sum')) }}</td>
                <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ round($collection->sum('sale')) }}</td>
    </tr>
    </tbody>
</table>
