@slot('header')
    <header class="flex align-items-center">
        <a href="{{route('window.schedule', ['business' => $slug, 'modal' => 'create'])}}" class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}записать клиента</a>
    </header>
@endslot

<div class="schedule-main grid">

    @include('schedule.calendar')

    <div class="types flex justify-content-around align-self-center">
        @foreach($types as $type_link)
            <a class="hashtag text-decoration-none{{$current_type === $type_link->id ? ' active' : ''}}" href="{{route('schedule', ['business' => $slug, 'current_type' => $type_link->id, 'date' => $date, 'current_month' => $current_month])}}">{{$type_link->type}}</a>
        @endforeach
    </div>

    <div class="timetable admin grid">

        <div class="row-1 border-right-main border-bottom-main day"></div>

        @php $time_i = 2 @endphp
        @foreach($times as $time)

            <div class="day border-bottom-main time cnt{{$time_i == 13 ? '' : ' border-right'}}" style="grid-column:{{$time_i}}">{{$time}}</div>

            @php $row = 2 @endphp
            @foreach($services as $service)

                @if (!$service->users->isEmpty())

                    @include('schedule.empty', ['main' => true, 'i' => $time_i, 'row' => $row])

                    @foreach($service->users as $user)

                        @php $row++ @endphp
                        @include('schedule.record', ['record' => $user->records->where('date', $date)->where('time', $time), 'main' => false, 'i' => $time_i, 'row' => $row])

                    @endforeach

                @else

                    @include('schedule.record', ['record' => $service->records->where('date', $date)->where('time', $time), 'main' => true, 'i' => $time_i, 'row' => $row])

                @endif

                @php $row++ @endphp

            @endforeach

        @php $time_i++ @endphp
        @endforeach

        @php $row = 2 @endphp
        @foreach($services as $service_label)

            <div class="bg-service border-right-main border-bottom-main col-1 service-title cnt" style="grid-row:{{$row}}">
                {{$service_label->name}}
            </div>

            @if (!$service_label->users->isEmpty())

                @foreach($service_label->users as $user_label)

                    @php $row++ @endphp
                    <div class="border-right-main border-bottom-main col-1 time cnt" style="grid-row:{{$row}}">
                        {{$user_label->name}}
                    </div>

                @endforeach

            @endif

            @php $row++ @endphp

        @endforeach

    </div>

</div>
