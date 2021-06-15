@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/notice.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{asset('js/notice.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        <div class="notice-content">
            @if($notices)
                @foreach($notices as $notice)
                    <div class="grid notice-message">
                        <div class="border-right"><span>{!! $notice->message !!}</span></div>
                        <div class="border-right"><a href="{{route('deleteNotice', [$slug, $notice->id])}}">{!! file_get_contents(public_path('images/delete.svg')) !!}</a></div>
                        <div>{{\Carbon\Carbon::parse($notice->created_at)->format('Y-m-d')}}</div>
                    </div>
                @endforeach
            @else
                <span>{{__('Нет новых уведомлений.')}}</span>
            @endif
        </div>
    @endcomponent
@endsection