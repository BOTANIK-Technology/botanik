<div class="mouth-select flex justify-content-between align-self-center">
    <a href="{{route('schedule', ['business' => $slug, 'current_month' => $prev_month, 'current_type' => $current_type, 'date' => $date])}}">{!! file_get_contents(public_path('images/prev.svg')) !!}</a>
    <span class="month">{{$mouths[$current_month]}}</span>
    <a href="{{route('schedule', ['business' => $slug, 'current_month' => $next_month, 'current_type' => $current_type, 'date' => $date])}}">{!! file_get_contents(public_path('images/next.svg')) !!}</a>
</div>

<div class="date-sort flex direction-column">
    <div class="calendar border">
        @foreach($days as $d => $day)
            <a href="{{route('schedule', ['business' => $slug, 'current_type' => $current_type, 'date' => $d, 'current_month' => $current_month])}}" class="border color cnt{{$d == $date ? ' bg-main border-main active' : ''}}">{{$day}}</a>
        @endforeach
    </div>
</div>
