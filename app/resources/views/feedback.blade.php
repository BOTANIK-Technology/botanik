@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/feedback.css') }}" rel="stylesheet">
@endsection

@section('scripts')

@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            <header class="flex align-items-center">
                <a href="{{route('review', ['business' => $slug])}}" class="hashtag {{Route::currentRouteNamed('review') ? 'active' : ''}}">{{__('Отзывы')}}</a>
                @if ($package == 'pro')
                    <a href="{{route('feedback', ['business' => $slug])}}" class="hashtag {{Route::currentRouteNamed('feedback') ? 'active' : ''}}">{{__('Обратная связь')}}</a>
                @endif
                <span class="sort">Сортировка: </span>
                <span>
                    <span class="{{$sort == 'asc' || $sort == 'desc' ? 'active' : ''}}">по дате</span>
                    <a href="{{route(Route::currentRouteName(), ['business' => $slug, 'sort' => 'desc'])}}"><div class="arrow {{$sort == 'desc' ? 'active' : ''}}"></div></a>
                    <a href="{{route(Route::currentRouteName(), ['business' => $slug, 'sort' => 'asc'])}}"><div class="arrow down {{$sort == 'asc' ? 'active' : ''}}"></div></a>
                </span>
            </header>
        @endslot

        <div class="table grid {{Route::currentRouteNamed('review') ? 'review' : 'feedback'}} @phone phone @endphone">
            @if (isset($table) && $table)
            @if (Route::currentRouteName() == 'review')
                @phone
                    @foreach ($table as $item)
                        <div class="flex align-items-center justify-content-center num">
                            {{$loop->iteration}}
                        </div>
                        <div class="flex direction-column justify-content-center text preview">
                            <span class="lite-text">
                                {{\Carbon\Carbon::parse($item->created_at)->format('Y-m-d')}}
                            </span>
                            {{$item->text}}
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route(Route::currentRouteName(), ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort])}}">{!! file_get_contents(public_path('images/view.svg')) !!}</a>
                        </div>
                    @endforeach
                @else
                    @foreach ($table as $item)
                        <div class="flex align-items-center justify-content-center num">
                            {{$loop->iteration}}
                        </div>
                        <div class="flex align-items-center text preview">
                            {{$item->text}}
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route(Route::currentRouteName(), ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort])}}"><div class="view-icon"></div></a>
                        </div>
                        <div class="flex align-items-center justify-content-center date">
                            {{\Carbon\Carbon::parse($item->created_at)->format('Y-m-d')}}
                        </div>
                    @endforeach
                @endphone
            @else
                @phone
                    @foreach ($table as $item)
                        <div class="flex align-items-center justify-content-center">{{__('Имя')}}</div>
                        <div class="flex align-items-center justify-content-center text">
                            {{$item->telegramUser->first_name}}
                        </div>
                        <div class="flex align-items-center justify-content-center">{{__('Услуга')}}</div>
                        <div class="flex align-items-center justify-content-center text ellipsis">
                            {{$item->service->name}}
                        </div>
                        <div class="flex align-items-center justify-content-center">{{__('Специалист')}}</div>
                        <div class="flex align-items-center justify-content-center text ellipsis">
                            {{$item->user->name ?? '-'}}
                        </div>
                        <div class="flex align-items-center justify-content-center">{{__('Оценка')}}</div>
                        <div class="flex align-items-center justify-content-center">
                            @for($i=1; $i<6; $i++)
                                <div class="star {{$item->stars >= $i ? 'star-full' : 'star-empty'}}"></div>
                            @endfor
                        </div>
                        <div class="flex align-items-center justify-content-center">{{__('Дата')}}</div>
                        <div class="flex align-items-center justify-content-center">
                            {{\Carbon\Carbon::parse($item->created_at)->format('Y-m-d')}}
                        </div>
                        <div class="flex align-items-center justify-content-center date two-col">
                            <a class="flex align-items-center" href="{{route(Route::currentRouteName(), ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort])}}"><div class="view-icon"></div><span>{{__('ПОДРОБНЕЕ')}}</span></a>
                        </div>
                    @endforeach
                @else
                    <div></div>
                    <div class="flex align-items-center justify-content-center">{{__('Имя')}}</div>
                    <div class="flex align-items-center justify-content-center">{{__('Услуга')}}</div>
                    <div class="flex align-items-center justify-content-center">{{__('Специалист')}}</div>
                    <div class="flex align-items-center justify-content-center">{{__('Оценка')}}</div>
                    <div class="flex align-items-center justify-content-center">{{__('Дата')}}</div>
                    <div class="flex align-items-center justify-content-center"></div>
                    @foreach ($table as $item)
                        <div class="flex align-items-center justify-content-center num border-right-main">
                            {{$loop->iteration}}
                        </div>
                        <div class="flex align-items-center justify-content-center text">
                            {{$item->telegramUser->first_name}}
                        </div>
                        <div class="flex align-items-center justify-content-center text">
                            {{$item->service->name}}
                        </div>
                        <div class="flex align-items-center justify-content-center text">
                            {{$item->user->name ?? '-'}}
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            @for($i=1; $i<6; $i++)
                                <div class="star {{$item->stars >= $i ? 'star-full' : 'star-empty'}}"></div>
                            @endfor
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            {{\Carbon\Carbon::parse($item->created_at)->format('Y-m-d')}}
                        </div>
                        <div class="flex align-items-center justify-content-center date">
                            <a href="{{route(Route::currentRouteName(), ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort])}}"><div class="view-icon"></div></a>
                        </div>
                    @endforeach
                @endphone
            @endif
            @endif
        </div>

        @include('layouts.load', ['count' => $countItems, 'inputs' => ['sort' => $sort], 'load' => $load, 'route' => $route])

    @endcomponent
@endsection

@if (isset($modal))
    @section('modal')
        @component('modal')

            @if (Route::currentRouteName() == 'review')

                <div class="flex justify-content-between">
                    <div>
                        <div class="info">
                            {{$content->telegramUsers->first_name}}
                        </div>
                        <div class="info phone">
                            {{$content->telegramUsers->phone}}
                        </div>
                    </div>
                    <div>
                        <span class="modal-date">{{\Carbon\Carbon::parse($content->created_at)->format('Y-m-d')}}</span>
                    </div>
                </div>

                <div class="line"></div>
                <div>
                    <p class="message">{{$content->text}}</p>
                </div>

                @slot('buttons')
                    <div class="flex justify-content-center">
                        @for($i=1; $i<6; $i++)
                            <div class="star {{$content->stars >= $i ? 'star-full' : 'star-empty'}}"></div>
                        @endfor
                    </div>
                    <a href="{{route(Route::currentRouteName(), ['business' => $slug, 'sort' => $sort])}}" id="refresh-modal"></a>
                @endslot
            @else
                <div class="modal-feedback">
                    <div class="flex direction-column">
                        <span class="modal-date">{{__('Клиент')}}</span>
                        <div class="flex justify-content-between">
                            <span class="info">{{$content->telegramUser->getFio()}}</span>
                            <span class="modal-date">{{\Carbon\Carbon::parse($content->created_at)->format('Y-m-d')}}</span>
                        </div>
                    </div>
                    <div class="flex direction-column">
                        <div class="flex justify-content-between">
                            <span class="modal-date">{{__('Специалист')}}</span>
                            <span class="modal-date">{{__('Услуга')}}</span>
                        </div>
                        <div class="flex justify-content-between">
                            <span class="info">{{$content->user->name ?? '-'}}</span>
                            <span class="info">{{$content->service->name}}</span>
                        </div>
                    </div>
                </div>
                <div class="line feedbacks"></div>
                <div class="flex justify-content-center">
                    @for($i=1; $i<6; $i++)
                        <div class="star {{$content->stars >= $i ? 'star-full' : 'star-empty'}}"></div>
                    @endfor
                </div>
                <div class="line feedbacks"></div>
                @slot('buttons')
                    <p class="message">{{$content->text}}</p>
                    <a href="{{route(Route::currentRouteName(), ['business' => $slug, 'sort' => $sort])}}" id="refresh-modal"></a>
                @endslot
            @endif
        @endcomponent
    @endsection
@endif