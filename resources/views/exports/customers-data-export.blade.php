<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Клиент</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Имя</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Телефон</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Город</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Способ доставки</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Отделение</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->client_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->phone }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->city_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->delivery_method_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->department }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
