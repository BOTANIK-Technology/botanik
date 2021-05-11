@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{asset('js/login.js')}}"></script>
@endsection

@section('content')
@php
    if (!$prefix = Route::getCurrentRoute()->parameter('business'))
        $prefix = 'a-level';
@endphp

    @include('auth.layouts.login')

@endsection

@section('modal')
    @if(isset($email))
        @component('modal')
            @slot('buttons')
                <button type="button" class="btn-primary" onclick="closeModal()">
                    {{ __('ОК') }}
                </button>
            @endslot
            <p>{{__('Новый пароль успешно отправлен')}}</p><br>
            <p>{{__('на Вашу почту')}} <b>{{$email}}</b></p>
        @endcomponent
    @endif
    @error('email')
        @component('modal')
            @slot('buttons')
                <button type="button" class="btn-primary" onclick="closeModal()">
                    {{ __('Продолжить') }}
                </button>
            @endslot
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @endcomponent
    @enderror
@endsection
