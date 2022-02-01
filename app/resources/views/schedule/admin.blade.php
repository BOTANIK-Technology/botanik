@slot('header')
    <header class="flex align-items-center">
        <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'create'])}}"
           class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}
            записать клиента</a>
    </header>
@endslot
<div class="schedule-main grid">

    @include('schedule.calendar')


    <div class="">
        <div class="types flex justify-content-around align-self-center">
            @foreach($types as $type_link)
                <a class="hashtag text-decoration-none{{$current_type === $type_link->id ? ' active' : ''}}"
                   href="{{route('schedule', ['business' => $slug, 'current_type' => $type_link->id, 'date' => $date, 'current_month' => $current_month])}}">{{$type_link->type}}</a>
            @endforeach
        </div>
        <table>
            <tr>
            <th>{{__('Время записи')}}</th>
            <th>{{__('Специалист')}}</th>
            <th>{{__('Услуга')}}</th>
            <th>{{__('Группа')}}</th>
            <th>{{__('Клиент')}}</th>
            <th>{{__('Действие')}}</th>
            </tr>
            @foreach($records as $item)

                <tr>
                    <td>{{$item->time}}</td>
                    <td>{{$item->user->name}}</td>
                    <td>{{$item->service->name}}</td>
                    <td>{{$item->service->group ? $item->service->group->quantity : ''}}</td>
                    <td>{{$item->telegramUser->getFio()}}</td>
                    <td>
                        <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'view', 'id' => $item->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $item->time])}}"><div class="view-icon"></div></a>
                        <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'edit', 'id' => $item->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $item->time])}}"><div class="edit-icon"></div></a>
                        <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'delete', 'id' => $item->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $item->time])}}"><div class="delete-icon"></div></a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

<style>
    .timetable.admin {
        max-width: 900px;
        grid-template-columns: 10% repeat(23, 1fr);
    }

    .schedule-main {
        grid-template-columns: 15% 1fr 900px 10.5%;
    }

    .timetable > .bg-service {
        max-width: 102px;
        height: 35px;
    }
</style>
