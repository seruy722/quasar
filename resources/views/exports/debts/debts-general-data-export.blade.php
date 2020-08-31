<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Дата</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Тип</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Деберц</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Очки</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Ком</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Опл</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Прим</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Пол</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ date('d-m-Y', strtotime($elem->created_at)) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->type ? 'Оплата' : 'Долг'}}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->code_client_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ round($elem->sum) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ round($elem->commission) }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->paid ? 'Да' : $elem->type ? null : 'Нет' }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->notation }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->user_name }}</td>
        </tr>
    @endforeach

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ $collection->sum('sum') }}</td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ $collection->sum('commission') }}</td>
    </tr>
    </tbody>
</table>
