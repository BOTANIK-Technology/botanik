@section('critical-scripts')
    <link href="{{ asset('css/timetable.css') }}" rel="stylesheet">
    <script>
        // Загружаем данные из базы
        let init = () => {
            let checked = @json($checked);
            for (let item in checked) {
                setCookie(item, JSON.stringify(checked[item]));
            }
        }
        init();

    </script>
@endsection

<div class="timetable_box">
    <div class="timetable_box-head">
        <div class="timetable_box-fields">
            <select id="month_picker" class="timetable_box-select">
                @foreach($allMonth as $key => $month)
                    <option value="{{$key}}" {{$current_month == $key ? 'selected' : ''}}>{{$month}}</option>
                @endforeach
            </select>
            <select id="year_picker" class="timetable_box-select">
                @foreach($yearList as $year)
                    <option value="{{$year}}" {{$current_year == $year ? 'selected' : ''}}>{{$year}}</option>
                @endforeach
            </select>
        </div>
        <div class="timetable_box-btns">
            <button id="save_month" class="timetable_box-btn btn btn-info">{{__('Сохранить')}}</button>
        </div>


        <div class="timetable_box-btns">
            <button id="select-all" class="timetable_box-btn btn">{{__('Выбрать всё')}}</button>
            <button id="clear-all" class="timetable_box-btn btn btn-warning">{{__('Очистить всё')}}</button>
        </div>
    </div>
    <div class="timetable_box-wrap">
        <div class="timetable_box-table timetable grid">
            <div class="row-1 border-right-main border-bottom-main day"></div>
            @foreach($allMonthDays[$current_year][$current_month] as $key => $day)
                {{--                <div class="{{$day['day'] == 'Saturday' || $day['day'] == 'Sunday' ? 'week-end' : ''}}">--}}
                <div
                    class="day-row day border-bottom-main user-select-none cnt border-right {{$day['day'] == 'Saturday' || $day['day'] == 'Sunday' ? 'week_end' : ''}}"
                    style="grid-column:{{$loop->index+2}}">
                    <p class="week_day_cell">{{$daysWeek[strtolower($day['day']) ]}}</p>
                    <p class="number_cell"><strong>{{$day['number']}}</strong></p>
                </div>
                @foreach($times as $time)
                    <div id="{{$key}}-{{$time}}"
                         class="checkbox user-select-none {{$loop->index+2 == 2 ? '' : 'border-top'}} {{$day['day'] == 'Saturday' || $day['day'] == 'Sunday' ? 'week_end' : ''}} border-right"
                         style="grid-column:{{$loop->parent->index+2}};grid-row:{{$loop->index+2}}"
                         data-day="{{$key}}"
                         data-time="{{$time}}">
                    </div>
                    {{--                </div>--}}
                @endforeach
            @endforeach
            @foreach($times as $time)
                <div class="timetable_box-time border-right-main border-top col-1 time user-select-none cnt"
                     style="grid-row:{{$loop->index + 2}}">{{$time}}</div>
            @endforeach
        </div>
    </div>
</div>
@section('modal-styles')
    <style>
        .timetable .user-select-none.week_end {
            background-color: rgba(173, 216, 230, .2);
        }

        .timetable .user-select-none.week_end.checked {
            background-color: #084887;
        }

        .timetable_box {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: start;
            -ms-flex-pack: start;
            justify-content: flex-start;
            width: 100%;
            position: relative;
        }

        .timetable_box-head {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: end;
            -ms-flex-align: end;
            align-items: flex-end;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            width: 100%;
            padding: 0 20px;
            margin-bottom: 20px;
        }

        @media screen and (max-width: 900px) {
            .timetable_box-head {
                padding: 0;
            }
        }

        .timetable_box-head:last-child {
            margin-bottom: 0;
        }

        .timetable_box-btns {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: stretch;
            -ms-flex-align: stretch;
            align-items: stretch;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
        }

        .timetable_box-btn {
            margin: 0;
            margin-right: 15px;
        }

        .timetable_box-btn:last-child {
            margin-right: 0;
        }

        .timetable_box-btn#select-all {
            margin: 0;
            margin-right: 15px;
        }

        .timetable_box-btn#select-all:last-child {
            margin-right: 0;
        }


        .timetable_box-fields {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: stretch;
            -ms-flex-align: stretch;
            align-items: stretch;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
        }

        .timetable_box-fields select.timetable_box-select {
            width: 150px;
            margin-right: 15px;
        }

        .timetable_box-fields select.timetable_box-select:last-child {
            margin-right: 0;
        }

        .timetable_box-wrap {
            border: 1px solid #CEB5CE;
            border-radius: 5px;
            background: #fff;
            -webkit-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 0 20px;
        }

        @media screen and (max-width: 900px) {
            .timetable_box-wrap {
                max-width: 700px;
                max-height: 400px;
                margin: 0;
                overflow: auto;
            }
        }

        .timetable_box-wrap::-webkit-scrollbar {
            width: 10px;
            height: 10px;
            background-color: #FCF1EF;
        }

        .timetable_box-wrap::-webkit-scrollbar-thumb {
            background-color: #FFC2C2;
        }

        .timetable_box-table {
            -ms-grid-columns: auto;
            grid-template-columns: auto;
            width: auto;
            border: none;
            border-radius: 0;
            -webkit-box-shadow: none;
            box-shadow: none;
            margin: 0;
        }

        .timetable_box-table .day {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            min-width: 30px;
            height: auto;
            background: #fff;
            padding: 5px;
            position: -webkit-sticky;
            position: sticky;
            top: 0;
        }

        .timetable_box-time {
            background: #fff;
            position: -webkit-sticky;
            position: sticky;
            left: 0;
        }

        .btn-info {
            color: black;
        }
    </style>

@endsection
