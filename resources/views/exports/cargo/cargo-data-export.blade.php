<table>
    <thead>
    <tr>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Дата</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Тип</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Деберц</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">М</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">В</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">За к</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">За м</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Очки</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Пол</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Ск</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Опл</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Кат</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Фак</th>
        <th style="font-size: 10px;font-weight:bold;border: 1px solid black;text-align: center;vertical-align: center;">Прим</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr style="background-color: #fffe0b;">
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ date('d-m-Y', strtotime($elem['created_at'])) }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['type'] ? 'Оплата' : 'Долг'}}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['code_client_name'] }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['place'] === 0 && $elem['type'] ? null : $elem['place'] }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['kg'] === 0 && $elem['type'] ? null : $elem['kg'] }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['for_kg'] === 0 && $elem['type'] ? null : $elem['for_kg'] }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['for_place'] === 0 && $elem['type'] ? null : $elem['for_place'] }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ round($elem['sum']) }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['get_pay_user_name'] }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ round($elem['sale']) }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['paid'] ? 'Да' : null }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['category_name'] }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['fax_name'] }}</td>
            <td style="font-size: 10px;border: 1px solid black;text-align: center;">{{ $elem['notation'] }}</td>
        </tr>
    @endforeach

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-size: 10px;font-weight:bold;">{{ array_sum(array_column($collection, 'place')) }}</td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-size: 10px;font-weight:bold;">{{ array_sum(array_column($collection, 'kg')) }}</td>
        <td></td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-size: 10px;font-weight:bold;">{{ round(array_sum(array_column($collection, 'sum'))) }}</td>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-size: 10px;font-weight:bold;">{{ round(array_sum(array_column($collection, 'sale'))) }}</td>
    </tr>
    </tbody>
</table>
