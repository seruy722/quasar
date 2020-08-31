<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Деберц</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Бал</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ $elem['code_client_name'] }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ round($elem['sum']) }}</td>
        </tr>
    @endforeach

    <tr>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ round(array_sum(array_column($collection, 'sum'))) }}</td>
    </tr>
    </tbody>
</table>
