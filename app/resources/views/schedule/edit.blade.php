@section('modal-scripts')
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/schedule/schedule_win.js')}}"></script>
    <script>
        let service_id = {{$record->service_id}};
        let address_id = {{$record->address_id}};
        let master_id = {{$record->user_id}};
        let month = '{{$month}}';
        let date = '{{$record->date}}';
        let time = '{{$record->time}}';
    </script>
    <script src="{{asset('js/schedule/edit.js')}}"></script>
@endsection

<input id="url_slug" type="hidden" value="{{$slug}}" name="url_slug">
<input id="token_id" type="hidden" name="_token" value="{{ csrf_token() }}"/>
<div class="margin">
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

                @include('schedule.user_calendar')

            </div>
            <div class="flex justify-content-around create">

                @include('schedule.user_times')

            </div>

            <div class="flex justify-content-around create" id="payments-block">
                <div class="pay-block hide" id="block-cash_pay">
                    <input class="pay-input" type="radio" name="pay_type">
                    <label for="cash_pay">{{__('Наличными')}}</label>
                </div>
                <div class="pay-block hide" id="block-bonus_pay">
                    <input class="pay-input" type="radio" name="pay_type">
                    <label for="bonus_pay">{{__('Бонусами')}}</label>
                </div>
                <div class="pay-block hide" id="block-online_pay">
                    <input class="pay-input" type="radio" name="pay_type">
                    <label for="online_pay">{{__('Бонусами')}}</label>
                </div>
            </div>

            <button
                type="button"
                id="action"
                data-id="{{$record->id}}"
                data-href="{{route('schedule.update', ['business' => $slug, 'id' => $record->id])}}"
                class="btn-primary"
            >
                {{ __('Изменить') }}
            </button>
        </div>
</div>

<a href="{{route('schedule', ['business' => $slug, 'current_type' => $current_type, 'date' => $date, 'current_month' => $current_month])}}" id="refresh-modal"></a>
