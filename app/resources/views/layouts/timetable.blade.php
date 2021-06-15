@section('critical-scripts')
    <link href="{{ asset('css/timetable.css') }}" rel="stylesheet">
@endsection
<button id="select-all" class="btn">{{__('Выбрать всё')}}</button>
<div class="timetable grid">
    <div class="row-1 border-right-main border-bottom-main day"></div>
    @foreach($days as $key => $day)
        <div class="day border-bottom-main user-select-none cnt {{$loop->index+2 == 8 ? '' : 'border-right'}}" style="grid-column:{{$loop->index+2}}">{{$day}}</div>
        @foreach($times as $time)
            <div id="{{$key}}-{{$time}}" class="checkbox user-select-none {{$loop->index+2 == 2 ? '' : 'border-top'}} {{$loop->parent->index+2 == 8 ? '' : 'border-right'}}" style="grid-column:{{$loop->parent->index+2}};grid-row:{{$loop->index+2}}" data-day="{{$key}}" data-time="{{$time}}"></div>
        @endforeach
    @endforeach
    @foreach($times as $time)
        <div class="border-right-main border-top col-1 time user-select-none cnt" style="grid-row:{{$loop->index + 2}}">{{$time}}</div>
    @endforeach
</div>
