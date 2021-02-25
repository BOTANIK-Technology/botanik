@section('scripts')

@endsection

<div class="grid create" id="create-block">

    <label for="client">
        <select id="client">
            <option>{{__('ID, фамилия и имя клиента')}}</option>
            @foreach($create_clients as $client)
                <option value="{{$client->id}}">ID: {{$client->id}}; Номер: {{$client->phone}}; Имя: {{$client->first_name}}</option>
            @endforeach
        </select>
    </label>

    <div class="line"></div>

    <label for="service">
        <select id="service">
            <option>{{__('Услуга *')}}</option>
            @foreach($create_services as $service)
                <option value="{{$service->id}}">{{$service->name}}</option>
            @endforeach
        </select>
    </label>

    <label for="address">
        <select id="address">
            <option>{{__('Адрес *')}}</option>
            @foreach($create_addresses as $address)
                <option value="{{$address->id}}">{{$address->name}}</option>
            @endforeach
        </select>
    </label>

    <label id="master">
        <select id="master">
            <option>{{__('Специалист')}}</option>
            @foreach($create_users as $user)
                <option value="{{$user->id}}">{{$user->name}}</option>
            @endforeach
        </select>
    </label>

    <div class="line"></div>

    <label id="time">
        <select id="time">
            <option>{{__('Время')}}</option>
            @foreach($times as $t)
                <option value="{{$t}}">{{$t}}</option>
            @endforeach
        </select>
    </label>

    <label id="date">
        <input type="text" placeholder="{{__('01.01.2020')}}">
    </label>

</div>

@slot('buttons')
    <button type="button" id="create" data-href="{{route('schedule.create', ['business' => $slug])}}" class="btn-primary">
        {{ __('Создать') }}
    </button>
    <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
@endslot
