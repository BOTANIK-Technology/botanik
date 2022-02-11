@section('modal-scripts')
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/schedule/edit.js')}}"></script>
@endsection

<div class="margin">
    @foreach($window_records as $record)
        <div class="m20px-0">
            <div class="grid create">
                <label>{{$record->getService()->name}}</label>

                <label for="client-{{$record->id}}">
                    <input id="client-{{$record->id}}" class="inp active" value="{{$record->telegramUser->getFio()}}" disabled>
                </label>

                <label for="phone-{{$record->id}}">
                    <input id="phone-{{$record->id}}" class="inp active" value="{{$record->telegramUser->phone}}" disabled>
                </label>

            </div>

            <div class="line margin"></div>

            <div class="flex justify-content-around create">

                <div class="flex justify-content-around create">

                    @include('schedule.user_calendar')

                </div>
                <div class="flex justify-content-around create">

                    @include('schedule.user_times')

                </div>

            </div>

            <button
                type="button"
                id="edit-{{$record->id}}"
                data-id="{{$record->id}}"
                data-href="{{route('schedule.update', ['business' => $slug, 'id' => $record->id])}}"
                class="btn-primary"
                onclick="update({{$record->id}}, '{{route('schedule.update', ['business' => $slug, 'id' => $record->id])}}')"
            >
                {{ __('Изменить') }}
            </button>
        </div>

    @endforeach
</div>

<a href="{{route('schedule', ['business' => $slug, 'current_type' => $current_type, 'date' => $date, 'current_month' => $current_month])}}" id="refresh-modal"></a>
