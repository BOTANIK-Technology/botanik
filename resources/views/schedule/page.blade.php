@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/schedule.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script>
        let url = '{{url()->current()}}';
        let baseUrl = '{{url('/').'/'.$slug.'/'.'schedule/'}}';
        @if (isset($modal) && isset($id))
        let endurl = '/{{$modal}}/{{$id}}';
        @endif
    </script>
    <script src="{{asset('js/schedule/page.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        @role('master')

            @include('schedule.master')

        @else

            @include('schedule.admin')

        @endrole
    @endcomponent
@endsection

@if (isset($modal))
    @section('modal')
        @component('modal')
            @include('schedule.'.$modal)
        @endcomponent
    @endsection
@endif
