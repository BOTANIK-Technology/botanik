@section('modal-scripts')
    <link rel="stylesheet" href="{{asset('css/simplepicker.css')}}">
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/schedule/create.js')}}"></script>
    <script src="{{asset('js/simplepicker.js')}}"></script>
@endsection

<div class="grid create">

    <label for="client">
        <select id="client">
            <option value="">{{__('ID, фамилия и имя клиента')}}</option>
            @foreach($create_clients as $client)
                <option value="{{$client->id}}">{{$client->id}}. {{$client->first_name}} {{$client->last_name}}</option>
            @endforeach
        </select>
    </label>

</div>

<div class="line margin"></div>

<div class="grid create">

    <label for="service">
        <select id="service">
            <option value="">{{__('Услуга *')}}</option>
            @foreach($create_services as $service)
                <option value="{{$service->id}}">{{$service->name}}</option>
            @endforeach
        </select>
    </label>

    <label for="address">
        <select id="address">
            <option value="">{{__('Адрес *')}}</option>
            @foreach($create_addresses as $address)
                <option value="{{$address->id}}">{{$address->address}}</option>
            @endforeach
        </select>
    </label>

    <label for="master">
        <select id="master">
            <option value="">{{__('Специалист')}}</option>
            @foreach($create_users as $user)
                <option value="{{$user->id}}">{{$user->name}}</option>
            @endforeach
        </select>
    </label>

</div>

<div class="line margin"></div>

<div class="flex justify-content-around create">

    <button id="select_datetime" class="btn-primary" type="button">Дата и время</button>

    <label for="time">
        <input style="width: 50px;" readonly id="time" type="text" class="inp" placeholder="00:00">
    </label>

    <label for="date">
        <input style="width: 80px;" readonly id="date" type="text" class="inp" placeholder="{{__('01.01.2020')}}">
    </label>

</div>

@slot('buttons')
    <button type="button" id="create" data-href="{{route('schedule.create', ['business' => $slug])}}" class="btn-primary">
        {{ __('Создать') }}
    </button>
    <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
@endslot
