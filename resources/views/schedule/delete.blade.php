<div class="delete text-align-center">
    {{__('Вы действительно')}}<br>
    {{__('хотите удалить клиента')}}<br>
    <b>{{$client_rec->telegramUser->last_name.' '.$client_rec->telegramUser->first_name.' '.$client_rec->telegramUser->middle_name}}</b><br>
    {{__('из таблицы записей')}}?
</div>
@slot('buttons')
    <button type="button" id="delete-schedule" class="btn-primary" onclick="deleteSchedule()">
        {{ __('Удалить') }}
    </button>
    <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
@endslot
