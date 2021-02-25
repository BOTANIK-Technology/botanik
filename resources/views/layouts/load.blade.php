<div class="load-block">
    @if($count > $load)
        <form method="GET" action="{{route($route, $route_params ?? ['business' => $slug])}}">
            @if(isset($inputs) && is_array($inputs))
                @foreach($inputs as $k => $input)
                    <input type="hidden" name="{{$k}}" value="{{$input}}">
                @endforeach
            @endif
            <input type="hidden" name="load" value="{{$load + ($plus ?? 5)}}">
            <button class="btn load-more"><i class="load-icon cover"></i>{{__('Загрузить ещё')}}</button>
        </form>
    @endif
</div>