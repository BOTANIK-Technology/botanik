@if(!$record->isEmpty())

    <div
        class="{{$i == 13 ? '' : 'border-right'}}{{isset($main) && $main == true ? ' border-bottom-main bg-service' : ' border-bottom'}}"
        style="grid-column:{{$i}};grid-row:{{$row}}"
    >
        <div class="record flex justify-content-between align-items-center">
            <span>{{$record->count() > 1 ? __('Група'): $record->telegramUser->first_name}}</span>

            <div class="flex align-items-center justify-content-center">
                <div class="more-icon" data-id="{{$time_i.$row}}"></div>
                <div id="menu-{{$record->id}}" class="flex justify-content-around align-items-center more-menu service hide">
                    <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'view', 'id' => $record->id])}}"><div class="view-icon"></div></a>
                    <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'edit', 'id' => $record->id])}}"><div class="edit-icon"></div></a>
                    <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'delete', 'id' => $record->id])}}"><div class="delete-icon"></div></a>
                    <div><div data-id="{{$record->id}}" class="more-menu-close pointer color">x</div></div>
                </div>
            </div>

        </div>
    </div>

@else

    @include('schedule.empty', ['main' => $main, 'i' => $i, 'row' => $row])

@endif

