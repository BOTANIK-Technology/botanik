@extends('layouts.app')

@section('critical-scripts')
    {{--<script src="{{ asset('js/app.js') }}" defer></script>--}}
@endsection

@section('styles')
    {{--<link href="{{asset('css/app.css')}}" rel="stylesheet">--}}
@endsection

@section('content')
    @component('layouts.content')
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">

                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            @role('owner')
                                {{ __('Добро пожаловать, владелец!') }}
                                <a href="{{route('service', Route::getCurrentRoute()->parameter('business'))}}">Сервисы</a>
                                <br>
                            @endrole
                            @role('admin')
                                {{ __('Добро пожаловать, администратор!') }}
                                <br>
                            @endrole
                            @role('master')
                                {{ __('Добро пожаловать, мастер!') }}
                                <a href="{{route('schedule', Route::getCurrentRoute()->parameter('business'))}}">Расписание</a><br>
                            @endrole
                            {{ __('Вы залогинены!') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcomponent
@endsection
