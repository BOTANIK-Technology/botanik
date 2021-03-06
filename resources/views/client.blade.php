@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/client.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script>
        let url = '{{url()->current()}}';
        let urlParams = '?sort={{$sort}}&load={{$load}}';
    </script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/client.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            <header class="@phone grid phone-head @else flex align-items-center @endphone">
                <span class="sort">Сортировка: </span>
                <span class="type-sort">
                    <span class="{{$sort == 'price_desc' || $sort == 'price_asc' ? 'active' : ''}}">по стоимости</span>
                    <a href="{{route('client', ['business' => $slug, 'sort' => 'price_desc', 'load' => $load])}}"><div class="arrow {{$sort == 'price_desc' ? 'active' : ''}}"></div></a>
                    <a href="{{route('client', ['business' => $slug, 'sort' => 'price_asc', 'load' => $load])}}"><div class="arrow down {{$sort == 'price_asc' ? 'active' : ''}}"></div></a>
                </span>

                <span class="type-sort">
                    <span class="{{$sort == 'frequency_desc' || $sort == 'frequency_asc' ? 'active' : ''}}">по частоте посещений</span>
                    <a href="{{route('client', ['business' => $slug, 'sort' => 'frequency_desc', 'load' => $load])}}"><div class="arrow {{$sort == 'frequency_desc' ? 'active' : ''}}"></div></a>
                    <a href="{{route('client', ['business' => $slug, 'sort' => 'frequency_asc', 'load' => $load])}}"><div class="arrow down {{$sort == 'frequency_asc' ? 'active' : ''}}"></div></a>
                </span>

                <span class="type-sort">
                    <span class="{{$sort == 'visit_desc' || $sort == 'visit_asc' ? 'active' : ''}}">последнее посещение</span>
                    <a href="{{route('client', ['business' => $slug, 'sort' => 'visit_desc', 'load' => $load])}}"><div class="arrow {{$sort == 'visit_desc' ? 'active' : ''}}"></div></a>
                    <a href="{{route('client', ['business' => $slug, 'sort' => 'visit_asc', 'load' => $load])}}"><div class="arrow down {{$sort == 'visit_asc' ? 'active' : ''}}"></div></a>
                </span>

                <span class="search-form">
                    <form class="flex justify-content-between" method="GET" action="{{route('client', ['business' => $slug, 'sort' => 'visit_desc'])}}">
                        <input type="text" class="inp {{isset($search) ? 'active' : ''}}" id="search" name="search" placeholder="#Поиск.." value="{{$search ?? ''}}">
                        <button class="search cover background-none" type="submit"></button>
                    </form>
                </span>

            </header>
        @endslot

        <div class="flex direction-column timetable">
            <div id="titles" class="client-item border-bottom-main">
                @foreach($titles as $title)
                    <div class="cnt {{$loop->index == 9 ? '' : ($loop->index == 0 ? 'border-right-main' : 'border-right')}}">{{$title}}</div>
                @endforeach
            </div>
            @if($clients)
                @foreach($clients as $client)
                    <div id="{{$client->id}}" class="client-item client {{$loop->index == 0 ? '' : 'border-top'}}">
                        <div class="text-align-center client-data border-right-main pointer">{{$client->id}}</div>
                        <input id="last-name-{{$client->id}}" class="border-right" type="text" value="{{$client->last_name ?? ''}}" disabled>
                        <input id="first-name-{{$client->id}}" class="border-right" type="text" value="{{$client->first_name ?? ''}}" disabled>
                        <input id="middle-name-{{$client->id}}" class="border-right" type="text" value="{{$client->middle_name ?? ''}}" disabled>
                        <input id="username-{{$client->id}}" class="border-right" type="text" value="{{$client->username ?? ''}}" disabled>
                        <input id="phone-{{$client->id}}" class="border-right" type="text" value="{{$client->phone ?? ''}}" disabled>
                        <input id="email-{{$client->id}}" class="border-right email" type="text" value="{{$client->email ?? ''}}" disabled>
                        <input id="age-{{$client->id}}" class="border-right" type="text" value="{{$client->age ?? ''}}" disabled>
                        <input id="sex-{{$client->id}}" class="border-right" type="text" value="@if (isset($client->sex)){{$client->sex == 1 ? 'мужской' : 'женский'}} @endif" disabled>
                        <input id="bonus-{{$client->id}}" class="border-right-none" type="text" value="{{$client->bonus ?? 0}}" disabled>
                    </div>
                @endforeach
            @endif
        </div>

        @include('layouts.load', ['count' => $countClients, 'plus' => 15, 'inputs' => ['sort' => $sort], 'load' => $load, 'route' => 'client'])

    @endcomponent
@endsection

@if (isset($modal))
@section('modal')
@component('modal')
        @if ($modal === 'delete')
            <div class="delete text-align-center">
                Вы действительно<br>
                хотите удалить клиента<br>
                <b>{{$current->getFio()}}</b>?
            </div>
            @slot('buttons')
                <button type="button" id="delete-client" data-id="{{$current->id}}" class="btn-primary">
                    {{ __('Удалить') }}
                </button>
                <a href="{{route('client', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot
        @elseif($modal === 'block')
            <div class="delete text-align-center">
                {{__('Вы действительно')}}<br>
                @if ($current->status)
                {{__('хотите заблокировать клиента')}}<br>
                @else
                {{__('хотите разблокировать клиента')}}<br>
            @endif
            <b>{{$current->getFio()}}</b> ?
            </div>
            @slot('buttons')
                <button type="button" id="block-client" data-id="{{$current->id}}" class="btn-primary">
                    @if ($current->status)
                        {{ __('Заблокировать') }}
                    @else
                        {{ __('Разблокировать') }}
                    @endif

                </button>
                <a href="{{route('client', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot
        @elseif($modal === 'history')
            <div class="grid timetable history-table">
                @foreach($labels as $label)
                    <div class="cnt border-bottom-main {{$loop->index == 6 ? '' : ($loop->index == 0 ? 'border-right-main' : 'border-right')}}">{{$label}}</div>
                @endforeach
                @foreach($visits as $visit)
                        @foreach($visit as $data)
                            <div class="flex justify-content-center align-items-center {{$loop->parent->index == 0 ? '' : 'border-top'}} {{$loop->index == 6 ? '' : ($loop->index == 0 ? 'border-right-main' : 'border-right')}}">
                                <p class="client-data">{{$data}}</p>
                            </div>
                        @endforeach
                @endforeach
            </div>

            @slot('buttons')
                <a href="{{route('client', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot
        @elseif($modal === 'statistic')
            @slot('class')
                modal-stat
            @endslot
            <div class="statistic flex direction-column align-items-start">
                <div>Посещения</div>
                <span><b>{{$stat['visit']}}</b></span>
                <div>Частота</div>
                <span><b>{{$stat['freq']}} / месяц</b></span>
                <div>Потраченная сумма</div>
                <span><b>{{$stat['money']}} ₴</b></span>
                <div>Потраченные бонусы</div>
                <span><b>{{$stat['bonus']}}</b></span>
            </div>
            @slot('buttons')
                <div class="line"></div>
                <div class="flex justify-content-around padding-top">
                    <div class="flex direction-column">
                        <a class="text-decoration-none hashtag {{$time == 'month' ? 'active' : ''}}" href="{{route('window.client', ['business' => $slug, 'sort' => $sort, 'id' =>$current->id, 'load' => $load, 'modal' => $modal, 'time' => 'month'])}}">за месяц</a>
                        <a class="text-decoration-none hashtag {{$time == 'year'  ? 'active' : ''}}" href="{{route('window.client', ['business' => $slug, 'sort' => $sort, 'id' =>$current->id, 'load' => $load, 'modal' => $modal, 'time' => 'year'])}}">за год</a>
                    </div>
                    <div class="flex direction-column">
                        <a class="text-decoration-none hashtag {{$time == 'half'  ? 'active' : ''}}" href="{{route('window.client', ['business' => $slug, 'sort' => $sort, 'id' =>$current->id, 'load' => $load, 'modal' => $modal, 'time' => 'half'])}}">за поглода</a>
                        <a class="text-decoration-none hashtag {{$time == 'all'   ? 'active' : ''}}" href="{{route('window.client', ['business' => $slug, 'sort' => $sort, 'id' =>$current->id, 'load' => $load, 'modal' => $modal, 'time' => 'all'])}}">за всё время</a>
                    </div>
                </div>
                <a href="{{route('client', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot
        @endif
@endcomponent
@endsection
@endif
