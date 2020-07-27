<table>
    <thead>
    <tr>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Клиент</th>
        <th style="font-weight:bold;border: 1px solid black;text-align: center;">Баланс</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $elem)
        <tr>
            <td style="border: 1px solid black;text-align: center;">{{ $elem['code_client_name'] }}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $elem['sum'] }}</td>
        </tr>
    @endforeach

    <tr>
        <td></td>
        <td style="border: 1px solid black;text-align: center;background-color: #fffe0b;font-weight:bold;">{{ round(array_sum(array_column($collection, 'sum'))) }}</td>
    </tr>
    </tbody>
</table>
