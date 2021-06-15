@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/service.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            <div id="add-icon" class="hide"><div class="add-b-icon pointer"></div></div>
            <header class="flex align-items-center">
                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'create', 'load' => $load])}}" class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}{{__('создать услугу')}}</a>
            </header>
        @endslot
        <div class="table grid">
            @if (isset($types) && $types)
                @phone
                @foreach ($types as $type)
                    <div class="flex align-items-center justify-content-center num">
                        {{$loop->iteration}}
                    </div>
                    <div class="flex align-items-center text ellipsis">
                        {{$type->type}}
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <div class="more-icon" data-id="{{$type->id}}"></div>
                        <div id="menu-{{$type->id}}" class="more-menu hide">
                            <ul>
                                <li><a href="{{route('window.service', ['business' => $slug, 'modal' => 'view', 'id' => $type->id, 'load' => $load])}}"><div class="view-icon"></div><span class="more-menu-text">{{__('Просмотр')}}</span></a></li>
                                <li><a href="{{route('window.service', ['business' => $slug, 'modal' => 'edit', 'id' => $type->id, 'load' => $load])}}"><div class="edit-icon"></div><span class="more-menu-text">{{__('Редактировать')}}</span></a></li>
                                <li><a href="{{route('window.service', ['business' => $slug, 'modal' => 'delete', 'id' => $type->id, 'load' => $load])}}"><div class="delete-icon"></div><span class="more-menu-text red">{{__('Удалить')}}</span></a></li>
                            </ul>
                            <div data-id="{{$type->id}}" class="more-menu-close"></div>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach ($types as $type)
                    <div class="flex align-items-center justify-content-center num">
                        {{$loop->iteration}}
                    </div>
                    <div class="flex align-items-center text ellipsis">
                        {{$type->type}}
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('window.service', ['business' => $slug, 'modal' => 'view', 'id' => $type->id, 'load' => $load])}}"><div class="view-icon"></div></a>
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('window.service', ['business' => $slug, 'modal' => 'edit', 'id' => $type->id, 'load' => $load])}}"><div class="edit-icon"></div></a>
                    </div>
                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('window.service', ['business' => $slug, 'modal' => 'delete', 'id' => $type->id, 'load' => $load])}}"><div class="delete-icon"></div></a>
                    </div>
                @endforeach
                @endphone
            @endif
        </div>

        @include('layouts.load', ['count' => $countService, 'load' => $load, 'route' => 'service', 'route_params' => ['business' => $slug]])

    @endcomponent
@endsection

@if (isset($modal))
    @include('service.'.$modal)
@endif