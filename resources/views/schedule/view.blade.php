<div class="view">
    <p class="black-text">{{$client_rec->telegramUser->last_name.' '.$client_rec->telegramUser->first_name.' '.$client_rec->telegramUser->middle_name}}</p>
    <a href="tel:{{$client_rec->telegramUser->phone}}" class="text-decoration-none"><p class="black-text">{{$client_rec->telegramUser->phone}}</p></a>
</div>
@slot('buttons')
    <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
@endslot
