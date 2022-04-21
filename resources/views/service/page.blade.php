@extends('layouts.app')
@section('styles')
    <link href="{{ asset('css/service.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            <div id="add-icon" class="hide">
                <div class="add-b-icon pointer"></div>
            </div>
            <header class="flex align-items-center">
                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'create', 'load' => $load])}}"
                   class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}{{__('создать услугу')}}</a>
                <a href="{{route('service', ['business' => $slug, 'view' => 'services', 'load' => $load])}}"
                   class="hashtag {{$view == 'services' ? 'active' : ''}}">{{__('Услуги')}}</a>
                <a href="{{route('service', ['business' => $slug, 'view' => 'types', 'load_types' => $load_types??5])}}"
                   class="hashtag {{$view == 'types' ? 'active' : ''}}">{{__('Типы услуг')}}</a>
                <a href="{{route('service', ['business' => $slug, 'view' => 'addresses', 'load_addresses' => $load_addresses??5])}}"
                   class="hashtag {{$view == 'addresses' ? 'active' : ''}}">{{__('Адреса')}}</a>
            </header>
        @endslot

        @if ($view == 'services' && isset($services) && $services)
            @php $si = 1; @endphp
            <div class="table grid" style="grid-template-columns: 10% 45% 15% 10% 10% 10%;">
                @foreach ($services as $service)
                    @if ($si > $load) @break @endif
                    <div class="flex align-items-center justify-content-center num">
                        {{$loop->iteration}}
                    </div>
                    <div class="flex align-items-center text ellipsis">
                        {{$service->name}}
                    </div>
                    <div class="flex align-items-center text small ellipsis" >
                        @if($service->timetables->count())
                            Свой график
                        @else
                            Графики специалистов
                        @endif
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('window.service', ['business' => $slug, 'modal' => 'view', 'id' => $service->id, 'load' => $load])}}">
                            <div class="view-icon"></div>
                        </a>
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('window.service', ['business' => $slug, 'modal' => 'edit', 'id' => $service->id, 'load' => $load])}}">
                            <div class="edit-icon"></div>
                        </a>
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('window.service', ['business' => $slug, 'modal' => 'delete', 'id' => $service->id, 'load' => $load])}}">
                            <div class="delete-icon"></div>
                        </a>
                    </div>
                    @php $si++; @endphp
                @endforeach
            </div>
            @include('layouts.load', ['count' => $countService, 'view_type' => 'services', 'load' => $load, 'route' => 'service', 'route_params' => ['business' => $slug]])
        @elseif($view == 'types')
            @php
                $ti = 1;
                if(!isset($load_types)) {
                    $load_types = 5;
                    $countTypes = 0;
                }
                if(!isset($countTypes)) {
                    $countTypes = 0;
                }
            @endphp
            <div class="table grid" style="grid-template-columns: 10% 60% 15% 15%;">
                @foreach ($types as $type)
                    @if ($ti > $load_types) @break @endif
                    <div class="flex align-items-center justify-content-center num">
                        {{$loop->iteration}}
                    </div>
                    <div class="flex align-items-center text ellipsis">
                        {{$type->type}}
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('types.edit', ['business' => $slug, 'modal' => 'edit', 'id' => $type->id, 'load_types' => $load_types])}}">
                            <div class="edit-icon"></div>
                        </a>
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('types.delete', ['business' => $slug, 'modal' => 'delete', 'id' => $type->id, 'load_types' => $load_types])}}">
                            <div class="delete-icon"></div>
                        </a>
                    </div>
                    @php $ti++; @endphp
                @endforeach
            </div>
            @include('layouts.load', ['count' => $countTypes, 'view_type' => 'types', 'load' => $load_types, 'route' => 'service', 'route_params' => ['business' => $slug]])
        @elseif($view == 'addresses')
            @php
                $ai = 1;
                if(!isset($load_addresses)) {
                    $load_addresses = 5;
                }
                if(!isset($countAddresses)) {
                    $countAddresses = 0;
                }
            @endphp
            <div class="table grid" style="grid-template-columns: 10% 60% 15% 15%;">
                @foreach ($addresses as $address)
                    @if ($ai > $load_addresses) @break @endif
                    <div class="flex align-items-center justify-content-center num">
                        {{$loop->iteration}}
                    </div>
                    <div class="flex align-items-center text ellipsis">
                        {{$address->address}}
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('addresses.edit', ['business' => $slug, 'modal' => 'edit', 'id' => $address->id, 'load_addresses' => $load_addresses])}}">
                            <div class="edit-icon"></div>
                        </a>
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('addresses.delete', ['business' => $slug, 'modal' => 'delete', 'id' => $address->id, 'load_addresses' => $load_addresses])}}">
                            <div class="delete-icon"></div>
                        </a>
                    </div>
                    @php $ai++; @endphp
                @endforeach
            </div>
            @include('layouts.load', ['count' => $countAddresses, 'view_type' => 'services', 'load' => $load_addresses, 'route' => 'service', 'route_params' => ['business' => $slug]])
        @endif

    @endcomponent
@endsection

@if ($view == 'services' && isset($modal))
    @include('service.'.$modal)
@endif

@if ($view == 'types' && isset($modal))
    @include('type.' . $modal)
@endif

@if ($view == 'addresses' && isset($modal))
    @include('address.' . $modal)
@endif
