@extends('layouts.app')
@section('styles')
    <link href="{{ asset('css/user.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            <header class="flex align-items-center">
                <a  href="{{route('window.user', ['business' => $slug, 'modal' => 'create', 'sort' => $sort, 'load' => $load])}}" class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}{{__('добавить специалиста')}}</a>
                @if ($package == 'pro')
                    <a href="{{route('user', ['business' => $slug, 'sort' => 'admin', 'load' => $load])}}" class="hashtag {{$sort == 'admin' ? 'active' : ''}}">{{__('Администраторы')}}</a>
                @endif
                <a href="{{route('user', ['business' => $slug, 'sort' => 'master', 'load' => $load])}}" class="hashtag {{$sort == 'master' ? 'active' : ''}}">{{__('Специалисты')}}</a>
                @if (Auth::user()->hasRole('owner') && $package == 'pro')
                    <a href="{{route('user', ['business' => $slug, 'sort' => 'moder', 'load' => $load])}}" class="hashtag {{$sort == 'moder' ? 'active' : ''}}">{{__('Модерация')}}</a>
                @endif
            </header>
        @endslot
        @slot('popup')
            <div id="user-popup"></div>
        @endslot
        <div class="table grid" @if(Auth::user()->hasRole('admin')) style="grid-template-columns: 10% 30% 30% 10% 20% !important;" @endif >
            @if (isset($table) && $table)
                @phone
                @foreach ($table as $item)
                    <div class="flex align-items-center justify-content-center num">
                        {{$loop->iteration}}
                    </div>
                    <div class="flex direction-column justify-content-center text ellipsis">
                        {{$item->name}}
                        <span class="lite-text">
                            @if ($sort == 'master')
                                @foreach($item->services as $service)
                                    {{$service->typeServices->type}}{{' '}}
                                @endforeach
                            @else
                                {{$item->roles[0]->name}}
                            @endif
                        </span>
                    </div>
                    @if($sort == 'moder')
                        <div class="flex align-items-center justify-content-center">
                            <div class="more-icon" data-id="{{$item->id}}"></div>
                            <div id="menu-{{$item->id}}" class="more-menu hide">
                                <ul>
                                    <li><a href="{{route('window.user', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="view-icon"></div><span class="more-menu-text">Просмотр</span></a></li>
                                    <li><form method="get" action="{{route('manage.confirm', ['business' => $slug, 'sort' => $sort])}}"><input type="hidden" name="id" value="{{$item->id}}"><button class="add-b-icon bg-color-fff" type="submit"></button></form></li>
                                    <li><form method="get" action="{{route('manage.reject', ['business' => $slug, 'sort' => $sort])}}"><input type="hidden" name="id" value="{{$item->id}}"><button class="reject-icon bg-color-fff" type="submit"></button></form></li>
                                </ul>
                                <div data-id="{{$item->id}}" class="more-menu-close"></div>
                            </div>
                        </div>
                    @else
                        <div class="flex align-items-center justify-content-center">
                            <div class="more-icon" data-id="{{$item->id}}"></div>
                            <div id="menu-{{$item->id}}" class="more-menu hide">
                                <ul>
                                    <li><a href="{{route('window.user', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="view-icon"></div><span class="more-menu-text">{{__('Просмотр')}}</span></a></li>
                                    @if(Auth::user()->hasRole('owner'))
                                        <li><a href="{{route('window.user', ['business' => $slug, 'modal' => 'edit', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="edit-icon"></div><span class="more-menu-text">{{__('Редактировать')}}</span></a></li>
                                    @endif
                                    <li><a href="{{route('window.user', ['business' => $slug, 'modal' => 'delete', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="delete-icon"></div><span class="more-menu-text red">{{__('Удалить')}}</span></a></li>
                                </ul>
                                <div data-id="{{$item->id}}" class="more-menu-close"></div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                @foreach ($table as $item)
                    <div class="flex align-items-center justify-content-center num">
                        {{$loop->iteration}}
                    </div>
                    <div class="flex align-items-center text ellipsis">
                        {{$item->name}}
                    </div>
                    <div class="flex align-items-center text ellipsis">
                        @if ($sort == 'master')
                            @foreach($item->services as $service)
                                {{$service->name}}({{$service->typeServices->type}}){{count($item->services)-1 !== $loop->index ? ', ': ''}}
                            @endforeach
                        @else
                            {{$item->roles[0]->name}}
                        @endif
                    </div>
                    @if($sort == 'moder')
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('window.user', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort])}}">
                                <div class="view-icon"></div></a>
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <form method="get" action="{{route('manage.confirm', ['business' => $slug, 'sort' => $sort])}}">
                                <input type="hidden" name="id" value="{{$item->id}}">
                                <button class="add-b-icon bg-color-fff" type="submit"></button>
                            </form>
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <form method="get" action="{{route('manage.reject', ['business' => $slug, 'sort' => $sort])}}">
                                <input type="hidden" name="id" value="{{$item->id}}">
                                <button class="reject-icon bg-color-fff" type="submit"></button>
                            </form>
                        </div>
                    @else
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('window.user', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="view-icon"></div></a>
                        </div>
                        @if(Auth::user()->hasRole('owner'))
                            <div class="flex align-items-center justify-content-center">
                                <a href="{{route('window.user', ['business' => $slug, 'modal' => 'edit', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="edit-icon"></div></a>
                            </div>
                        @endif
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('window.user', ['business' => $slug, 'modal' => 'delete', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="delete-icon"></div></a>
                        </div>
                    @endif
                @endforeach
                @endphone
            @endif
        </div>

        @include('layouts.load', ['count' => $countUsers, 'inputs' => ['sort' => $sort], 'load' => $load, 'route' => 'user'])

    @endcomponent
@endsection

@if (isset($modal))
    @include('user.'.$modal)
@endif
