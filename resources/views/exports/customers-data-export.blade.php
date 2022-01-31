<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Деберц</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Им</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Тел</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Гор</th>
{{--        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Сп. доставки</th>--}}
{{--        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Отд</th>--}}
{{--        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Пол</th>--}}
{{--        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Кол кг</th>--}}
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->client_name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->name }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->phone }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem->city_name }}</td>
{{--            <td style="border: 1px solid black;text-align: center;">{{ $elem->delivery_method_name }}</td>--}}
{{--            <td style="border: 1px solid black;text-align: center;">{{ $elem->department }}</td>--}}
{{--            <td style="border: 1px solid black;text-align: center;">{{ $elem->sex }}</td>--}}
{{--            <td style="border: 1px solid black;text-align: center;">{{ $elem->count }}</td>--}}
        </tr>
    @endforeach
    </tbody>
</table>
