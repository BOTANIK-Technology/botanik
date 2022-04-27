@section('scripts')
    <script src="{{asset('js/service/page.js')}}"></script>
    <script src="{{asset('js/cookie.js')}}"></script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/service/type.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        @slot('class')
            modal-edit
        @endslot

        <div class="flex direction-column edit-type">
            <input type="hidden" value="{{$slug}}" id="edit_type_slug">
            <input type="hidden" value="{{$w_type->id}}" id="edit_type_id">
            <div><input type="text" value="{{$w_type->type}}" placeholder="{{__('Название типа услуги')}}" id="edit_type_name"></div>
        </div>
        <div class="line"></div>
        @slot('buttons')
            <button type="button" id="edit-type" class="btn-primary">
                {{ __('Сохранить') }}
            </button>
        @endslot
    @endcomponent
@endsection
