<table>
    <thead>
    <tr>
        <th>Код</th>
        <th>Клиент</th>
        <th>Мест</th>
        <th>Вес</th>
        <th>Категория</th>
        <th>Магазин</th>
        <th>Примечания</th>
        <th>Опись вложения</th>
        <th>К-тво</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection[0] as $elem)
        @php
            $things = json_decode($elem->things)
        @endphp
        <tr>
            <td>{{ $elem->code_place }}</td>
            <td>{{ $elem->code_client_name }}</td>
            <td>{{ $elem->place }}</td>
            <td>{{ $elem->kg }}</td>
            <td>{{ $elem->category_name }}</td>
            <td>{{ $elem->shop }}</td>
            <td>{{ $elem->notation }}</td>
            <td>{{ @$things[0]->title }}</td>
            <td>{{ @$things[0]->count }}</td>
        </tr>
        @if(isset($things[0]))
            @foreach($things as $thing)
                @if (!$loop->first)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $thing->title }}</td>
                        <td>{{ $thing->count }}</td>
                    </tr>
                @endif
            @endforeach
        @endif
    @endforeach
    <tr>
        <td></td>
        <td></td>
        <td style="font-weight: bold;">{{$collection[1]->sum('place')}}</td>
        <td style="font-weight: bold;">{{$collection[1]->sum('kg')}}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>

    @foreach($collection[1] as $category)
        <tr>
            <td>{{$category->name}}</td>
            <td>{{$category->place}}</td>
            <td>{{$category->kg}}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="font-weight: bold;">{{$collection[1]->sum('place')}}</td>
        <td style="font-weight: bold;">{{$collection[1]->sum('kg')}}</td>
    </tr>

    </tbody>
</table>
