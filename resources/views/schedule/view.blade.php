<div class="view">
    <p class="black-text">{{$record->telegramUser->getFio()}}</p>
    <a href="tel:{{$record->telegramUser->phone}}" class="text-decoration-none"><p class="black-text">{{$record->telegramUser->phone}}</p></a>
</div>
@slot('buttons')
    <a href="{{route('schedule', ['business' => $slug, 'date' => $date, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date])}}" id="refresh-modal"></a>
@endslot
