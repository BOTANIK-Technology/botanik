@section('scripts')
    <script src="{{asset('js/service/page.js')}}"></script>
    <script src="{{asset('js/cookie.min.js')}}"></script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/service/edit.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        @slot('class')
            modal-edit
        @endslot

        <div class="flex direction-column edit-type">
            <input type="hidden" value="{{$slug}}" id="edit_addr_slug">
            <input type="hidden" value="{{$w_address->id}}" id="edit_addr_id">
            <div><input type="text" value="{{$w_address->address}}" placeholder="{{__('Адрес')}}" id="edit_addr_name"></div>
        </div>
        <div class="line"></div>
        @slot('buttons')
            <button type="button" id="edit-addr" class="btn-primary">
                {{ __('Сохранить') }}
            </button>
        @endslot
    @endcomponent
@endsection
