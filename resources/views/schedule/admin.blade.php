@slot('header')
    <header class="flex align-items-center">
        <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'create', 'date' => $date])}}"
           class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}
            записать клиента</a>
    </header>
@endslot
<div class="schedule-main grid">

    @include('schedule.calendar')


    <div class="schedule_table-box">
        <div class="schedule_table-nav types flex justify-content-around align-self-center">
            <a class="hashtag text-decoration-none {{$current_type == 0 ? 'active' : ''}}"
               href="{{route('schedule', ['business' => $slug, 'current_type' => 0, 'date' => $date, 'current_month' => $current_month])}}">{{__('Все')}}</a>
            @foreach($types as $type_link)
                <a class="hashtag text-decoration-none {{$current_type == $type_link->id ? 'active' : ''}}"
                   href="{{route('schedule', ['business' => $slug, 'current_type' => $type_link->id, 'date' => $date, 'current_month' => $current_month])}}">{{$type_link->type}}</a>
            @endforeach
        </div>
        <table class="schedule_table">
            <thead>
            <tr>
                <th colspan="2" >{{__('Время записи')}}</th>
                <th rowspan="2">{{__('Специалист')}}</th>
                <th rowspan="2">{{__('Услуга')}}</th>
                <th rowspan="2">{{__('Группа')}}</th>
                <th rowspan="2">{{__('Клиент')}}</th>
                <th rowspan="2">{{__('Действие')}}</th>
            </tr>
            <tr>
                <th>{{__('от')}}</th>
                <th>{{__('до')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($records as $item)
                <tr>
                    <td class="schedule_table-time">{{$item->time}}</td>
                    <td class="schedule_table-time">{{$item->finishTime}}</td>
                    <td>{{$item->user ? $item->user->name : ''}}</td>
                    <td>{{$item->service->name}}</td>
                    <td>{{$item->service->group ? $item->service->group->quantity : ''}}</td>
                    <td>{{$item->telegramUser->getFio()}}</td>
                    <td>
                        <div class="schedule_table-btns">
                            <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'view', 'id' => $item->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $item->time])}}"
                               class="schedule_table-btn">
                                <div class="view-icon"></div>
                            </a>
                            <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'edit', 'id' => $item->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $item->time])}}"
                               class="schedule_table-btn">
                                <div class="edit-icon"></div>
                            </a>
                            <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'delete', 'id' => $item->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $item->time])}}"
                               class="schedule_table-btn">
                                <div class="delete-icon"></div>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @section('modal-styles')
        <style>
            .timetable.admin {
                max-width: 900px;
                grid-template-columns: 10% repeat(23, 1fr);
            }

            .schedule-main {
                grid-template-columns: 15% 1fr 90px 10.5%;
            }

            .timetable > .bg-service {
                max-width: 102px;
                height: 35px;
            }

            .schedule_table {
                font-size: 12px;
                line-height: 1.2;
                border: 1px solid #CEB5CE;
            }

            .schedule_table-box {
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
                margin-left: 40px;
            }

            .schedule_table-nav {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                -webkit-box-align: start;
                -ms-flex-align: start;
                align-items: flex-start;
                -webkit-box-pack: center;
                -ms-flex-pack: center;
                justify-content: center;
                width: 100%;
                text-align: center;
                margin-bottom: 10px;
            }

            .schedule_table-nav > * {
                margin-right: 40px;
            }

            .schedule_table-nav > *:last-child {
                margin-right: 0;
            }

            .schedule_table thead {
                border-bottom: 1px solid #CEB5CE;
                background: #FCF1EF;
            }

            .schedule_table thead tr th {
                vertical-align: middle;
                font-weight: 600;
                color: #9A13CA;
                text-align: center;
                border: 1px solid #CEB5CE;
                padding: 8px 16px;
            }

            .schedule_table thead tr th:last-child {
                border-right: none;
            }

            .schedule_table tbody tr {
                border-bottom: 1px solid #CEB5CE;
            }

            .schedule_table tbody tr:last-child {
                border-bottom: none;
            }

            .schedule_table tbody tr td {
                vertical-align: middle;
                font-weight: 500;
                color: #4F4F4F;
                border-right: 1px solid #CEB5CE;
                padding: 10px;
            }

            .schedule_table tbody tr td:last-child {
                border-right: none;
            }

            .schedule_table tbody tr td.schedule_table-time {
                font-weight: 600;
                text-align: center;
            }

            .schedule_table-btns {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
            }

            .schedule_table-btn {
                margin-right: 10px;
            }

            .schedule_table-btn:last-child {
                margin-right: 0;
            }
        </style>
@endsection
