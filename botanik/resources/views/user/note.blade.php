@section('modal')
    @component('modal')

        <div class="delete text-align-center">
            {{__('Специалист успешно')}}<br>
            {{__('отправлен на')}} <b>{{__('модерацию')}}</b>.<br>
            {{__('Ожидайте уведомления')}}<br>
            {{__('о подтверждении.')}}
        </div>
        @slot('buttons')
            <button type="button" onclick="closeModal()" class="btn-primary">
                {{ __('Ок') }}
            </button>
            <a href="{{route('user', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
        @endslot

    @endcomponent
@endsection