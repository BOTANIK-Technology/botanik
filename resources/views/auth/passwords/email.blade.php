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
    @component('modal')
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email', ['business' => $prefix]) }}">
                @csrf

                <div class="flex reset direction-column align-items-center">
                    <label for="email" class="email-lab full-width">{{ __('Введите почту, указанную при создании аккаунта,  для отправки нового пароля') }}</label>

                    <div class="form-group email">
                        <input id="email" type="email" class="form-control email @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary">
                        {{ __('Отправить') }}
                    </button>
                </div>

            </form>
        </div>
    @endcomponent
@endsection
