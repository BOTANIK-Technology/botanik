@section('modal-scripts')
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/schedule/delete.js')}}"></script>
@endsection

<div class="delete text-align-center">
    {{__('Вы действительно')}}<br>
    {{__('хотите удалить клиента')}}<br>
    <b>{{$record->telegramUser->getFio()}}</b><br>
    {{__('из таблицы записей')}}?
</div>
@slot('buttons')
    <button type="button" id="delete-schedule" class="btn-primary" onclick="deleteSchedule()">
        {{ __('Удалить') }}
    </button>
    <a href="{{route('schedule', ['business' => $slug, 'date' => $date, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date])}}" id="refresh-modal"></a>
@endslot
