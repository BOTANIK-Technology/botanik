@section('critical-scripts')
    <link href="{{ asset('css/timetable.css') }}" rel="stylesheet">
@endsection

<button id="select-all" class="btn">{{__('Выбрать всё')}}</button>
<button id="clear-all" class="btn btn-warning">{{__('Очистить всё')}}</button>
<div class="year_input">
    <select id="year_picker">
        @foreach($yearList as $year)
            <option value="{{$year}}" {{date('Y') == $year ? 'selected' : ''}}>{{$year}}</option>
        @endforeach
    </select>
</div>
<div class="month_input">
    <select id="month_picker">
        @foreach($allMonth as $key => $month)
            <option value="{{$key}}" {{$current_month == $key ? 'selected' : ''}}>{{$month}}</option>
        @endforeach
    </select>
</div>
<div class="timetable grid">
    <div class="row-1 border-right-main border-bottom-main day"></div>
    @foreach($daysMonth as $key => $day)
        <div class="day border-bottom-main user-select-none cnt {{$loop->index+2 == 8 ? '' : 'border-right'}}"
             style="grid-column:{{$loop->index+2}}">
            <p>{{$daysWeek[strtolower($day['day']) ]}}</p>
            <p>{{$day['number']}}</p>
        </div>
        @foreach($times as $time)
            <div id="{{$key}}-{{$time}}" class="checkbox user-select-none
                    {{$loop->index+2 == 2 ? '' : 'border-top'}}
            {{$loop->parent->index+2 == 8 ? '' : 'border-right'}}"
                 style="grid-column:{{$loop->parent->index+2}};grid-row:{{$loop->index+2}}" data-day="{{$key}}"
                 data-time="{{$time}}">
            </div>
        @endforeach
    @endforeach
    @foreach($times as $time)
        <div class="border-right-main border-top col-1 time user-select-none cnt"
             style="grid-row:{{$loop->index + 2}}">{{$time}}</div>
    @endforeach
</div>
