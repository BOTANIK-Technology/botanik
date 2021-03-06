@section('modal-scripts')
    <script src="{{asset('js/request.js')}}"></script>
    <script src="{{asset('js/schedule/edit.js')}}"></script>
@endsection

<div class="edit">
    <select id="edit-months" class="border-color">
        @foreach($calendar as $m)
            <option value="{{$loop->index}}" @if($date) @foreach($m as $d) {{$d == $date ? 'selected' : ''}} @endforeach @endif>{{$m[0]}}</option>
        @endforeach
    </select>
    @foreach($calendar as $m)
        <select
            id="edit-days-{{$loop->index}}" class="border-color @if($date) @php foreach($m as $d){if($d==$date){$ok=true;break;}else{$ok=false;}} @endphp {{$ok ? '' : 'hide'}} @else {{$loop->first ? '' : 'hide'}} @endif">
            @foreach($m as $k => $d)
                @if (!$loop->first)
                    <option value="{{$d}}" {{$d == $date ? 'selected' : ''}}>{{$k}}</option>
                @endif
            @endforeach
        </select>
    @endforeach
    <select id="time-edit">
        @foreach($times as $time)
            <option value="{{$time}}" {{$time == $client_rec->time ? 'selected' : ''}}>{{$time}}</option>
        @endforeach
    </select>
</div>
@slot('buttons')
    <button type="button" id="edit-schedule" class="btn-primary">
        {{ __('Сохранить') }}
    </button>
    <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
@endslot
