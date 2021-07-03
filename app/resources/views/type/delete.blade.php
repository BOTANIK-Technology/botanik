@section('scripts')
    <script>let url = '{{url()->current()}}';</script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/service/delete.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        <div class="delete">
            {{__('Вы действительно')}}<br>
            {{__(' хотите удалить тип услуги ')}}<br>
            <b>“{{$type->type}}”</b>?
        </div>
        @slot('buttons')
            <button type="button" id="delete" data-type-id="{{$type->id}}" class="btn-primary">
                {{ __('Удалить') }}
            </button>
            <a href="{{route('service', ['business' => $slug, 'load' => $load, 'view' => 'types'])}}" id="refresh-modal"></a>
        @endslot

    @endcomponent
@endsection
