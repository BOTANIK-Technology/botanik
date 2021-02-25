@section('scripts')
    <script>
        let note = '{{Auth::user()->hasRole('admin') ? route('window.user', ['business' => $slug, 'sort' => $sort, 'modal' => 'note', 'load' => $load]) : ''}}';
    </script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/user/page.js')}}"></script>
    <script src="{{asset('js/user/delete.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        <div class="delete text-align-center">
            {{__('Вы действительно  хотите удалить')}} <span class="lowercase">{{$user->roles[0]->name}}{{__('а')}}</span><br>
            <b>{{$user->name}}</b><br>
            {{__(' из системы бизнеса?')}}
        </div>
        @slot('buttons')
            <button type="button" id="delete-user" class="btn-primary">
                {{ __('Удалить') }}
            </button>
            <a href="{{route('user', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
        @endslot

    @endcomponent
@endsection