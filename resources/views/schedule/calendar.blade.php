@php
$dateSub = (new \Carbon\Carbon($date) )->subMonth()->format('Y-m-d');
$dateAdd = (new \Carbon\Carbon($date) )->addMonth(1)->format('Y-m-d');
@endphp
<div class="mouth-select flex justify-content-between align-self-center">
    <a href="{{route('schedule', [   'business' => $slug,
                                    'current_month' => $prev_month,
                                    'current_type' => $current_type,
                                    'date' => $dateSub
                                    ]
                                    )}}">{!! file_get_contents(public_path('images/prev.svg')) !!}</a>
    <span class="month">{{$months[$current_month]}}</span>
    <a href="{{route('schedule', ['business' => $slug,
                                    'current_month' => $next_month,
                                    'current_type' => $current_type,
                                    'date' => $dateAdd
                                    ])}}">{!! file_get_contents(public_path('images/next.svg')) !!}</a>
</div>


<div class="date-sort flex direction-column">
    <div class="calendar border">
        <a href="#">Пн</a>
        <a href="#">Вт</a>
        <a href="#">Ср</a>
        <a href="#">Чт</a>
        <a href="#">Пт</a>
        <a href="#">Сб</a>
        <a href="#">Вс</a>
        @foreach($days as $d => $day)
            <a href="{{route('schedule', ['business' => $slug, 'current_type' => $current_type, 'date' => $d, 'current_month' => $current_month])}}" class="border color cnt{{$d == $date ? ' bg-main border-main active' : ''}}">{{$day}}</a>
        @endforeach
    </div>
</div>
