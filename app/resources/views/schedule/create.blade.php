@section('modal-scripts')
    <link rel="stylesheet" href="{{asset('css/simplepicker.css')}}">
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/schedule/create.js')}}"></script>
    <script src="{{asset('js/schedule/schedule_win.js')}}"></script>
    <script src="{{asset('js/simplepicker.js')}}"></script>
@endsection

<div class="grid create">
    <input id="url_slug" type="hidden" value="{{$slug}}" name="url_slug">
    <input id="token_id" type="hidden" name="_token" value="{{ csrf_token() }}" />
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

    <label id="address_label" for="address" style="display: none;">
        <select id="address">
            <option value="">{{__('Адрес *')}}</option>
            @foreach($create_addresses as $address)
                <option value="{{$address->id}}">{{$address->address}}</option>
            @endforeach
        </select>
    </label>

    <label id="master_label" for="master" style="display: none;">
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

    @include('schedule.user_calendar')

</div>
<div class="flex justify-content-around create">

    @include('schedule.user_times')

</div>

@slot('buttons')
    <button type="button" id="create" data-href="{{route('schedule.create', ['business' => $slug])}}" class="btn-primary">
        {{ __('Создать') }}
    </button>
    <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
@endslot
