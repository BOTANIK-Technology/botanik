@section('modal-scripts')
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/schedule/delete.js')}}"></script>
@endsection

<div class="margin">
    @foreach($window_records as $record)
        <div class="m20px-0">
            <div class="delete text-align-center">
                {{__('Вы действительно')}}<br>
                {{__('хотите удалить клиента')}}<br>
                <b>{{$record->telegramUser->getFio()}}</b><br>
                {{__('из таблицы записей')}}?
            </div>

            <button
                type="button"
                id="delete-{{$record->id}}"
                data-href="{{route('schedule.delete', ['business' => $slug, 'id' => $record->id])}}"
                class="btn-primary text-align-center"
                onclick="deleteSchedule('{{route('schedule.delete', ['business' => $slug, 'id' => $record->id])}}')"
            >
                {{ __('Удалить') }}
            </button>
        </div>
    @endforeach
</div>

<a href="{{route('schedule', ['business' => $slug, 'date' => $date, 'current_month' => $current_month, 'current_type' => $current_type, 'date' => $date])}}" id="refresh-modal"></a>
