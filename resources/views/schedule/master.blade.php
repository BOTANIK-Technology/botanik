<div class="schedule-pers flex">

    @include('schedule.calendar')

    @if ($schedule)

        @foreach($schedule as $times)
            <div class="timetable pers grid">
                <div class="row-1 border-right-main border-bottom-main day"></div>
                <div class="day border-bottom-main cnt" style="grid-column:2">{{$address}}</div>
                @foreach($times as $time)
                    <div class="border-right-main {{$loop->index == 0 ? '' : 'border-top'}} col-1 time cnt" style="grid-row:{{$loop->iteration+1}}">{{$time}}</div>
                    <div class="checkbox col-2 {{$loop->index == 0 ? '' : 'border-top'}}" style="grid-row:{{$loop->iteration+1}}">
                        @if($records)
                            @foreach($records as $record)
                                @if($time == $record->time)
                                    <div class="record flex justify-content-between align-items-center">
                                        <span>{{$record->telegramUser->first_name}}</span>
                                        <div class="flex">
                                            <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'view', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/view-d.svg')) !!}</a>
                                            <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'edit', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/edit-d.svg')) !!}</a>
                                            <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'delete', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/delete.svg')) !!}</a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach

    @else

        <h2>{{__('Выходной')}}</h2>

    @endif
</div>
