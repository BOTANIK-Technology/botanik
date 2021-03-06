@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/support.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{asset('js/img-label.js')}}"></script>
    <script src="{{asset('js/support.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')

        <div class="send-block">
            <form class="grid send-form" action="{{route('support.create', ['business' => $slug])}}" method="post" enctype="multipart/form-data">
                @csrf

                <label for="title" class="col-1">{{'Заголовок'}}</label>
                <input id="title" class="col-2 inp" type="text" name="title" placeholder="{{__('Введите текст')}}">

                <label for="img" class="col-1">{{__('Обложка')}}</label>
                <label for="img" class="image-logo flex align-items-center" id="img-label">
                    <input type="file" accept="image/*" name="image" id="img">
                </label>

                <label for="text" class="col-1 align-self-start">Текст</label>
                <textarea id="text" class="col-2 inp" name="text" placeholder="{{__('Введите текст')}}"></textarea>

                <input type="hidden" name="business_id" value="{{$business_id}}">
                <input type="hidden" name="user_id" value="{{$user_id}}">

                <button class="btn-primary col-2" type="submit">{{__('Отправить')}}</button>
            </form>
        </div>

    @endcomponent
@endsection

@if (isset($response))
    @section('modal')
        @component('modal')
            <p class="response">{{$response}}</p>
            <button type="button" onclick="closeModal()" class="btn-primary">
                {{ __('ОК') }}
            </button>
            <a href="{{route('support', ['business' => $slug])}}" id="refresh-modal"></a>
        @endcomponent
    @endsection
@endif
