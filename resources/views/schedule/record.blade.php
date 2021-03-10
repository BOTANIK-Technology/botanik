@if($record->isEmpty())

    @include('schedule.empty', ['main' => $main, 'i' => $i, 'row' => $row])

@else

    <div
        class="{{$i == 13 ? '' : 'border-right'}}{{isset($main) && $main == true ? ' border-bottom-main bg-service' : ' border-bottom'}}"
        style="grid-column:{{$i}};grid-row:{{$row}}"
    >
        <div class="record flex justify-content-between align-items-center">
            <span>{{$record->count() > 1 ? __('Група'): $record->first()->telegramUser->first_name}}</span>

            <div class="flex align-items-center justify-content-center">
                <div class="more-icon" data-id="{{$record->first()->id}}"></div>
                <div id="menu-{{$record->first()->id}}" class="flex justify-content-around align-items-center more-menu service hide">
                    <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'view', 'id' => $record->first()->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $time])}}"><div class="view-icon"></div></a>
                    <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'edit', 'id' => $record->first()->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $time])}}"><div class="edit-icon"></div></a>
                    <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'delete', 'id' => $record->first()->service_id, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date, 'time' => $time])}}"><div class="delete-icon"></div></a>
                    <div><div data-id="{{$record->first()->id}}" class="more-menu-close pointer color">x</div></div>
                </div>
            </div>

        </div>
    </div>

@endif

