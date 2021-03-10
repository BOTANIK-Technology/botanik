@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/api.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/api/page.js')}}"></script>
    <script src="{{asset('js/api/beauty.js')}}"></script>
    <script src="{{asset('js/api/yclients.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        <div class="content">
            @foreach($apis as $api)
                <div class="line"></div>
                @include('api.templates.'.$api->slug, ['config' => json_decode($api->config)])
            @endforeach
        </div>

    @endcomponent
@endsection
