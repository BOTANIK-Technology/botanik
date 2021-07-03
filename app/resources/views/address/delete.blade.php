@section('scripts')
    <script>let url = '{{url()->current()}}';</script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/service/delete.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        <div class="delete">
            {{__('Вы действительно')}}<br>
            {{__(' хотите удалить адрес ')}}<br>
            <b>“{{$address->address}}”</b>?
        </div>
        @slot('buttons')
            <button type="button" id="delete" data-type-id="{{$address->id}}" class="btn-primary">
                {{ __('Удалить') }}
            </button>
            <a href="{{route('service', ['business' => $slug, 'load' => $load, 'view' => 'addresses'])}}" id="refresh-modal"></a>
        @endslot

    @endcomponent
@endsection
