<div class="load-block">
    @if($count > $load)
        <form method="GET" action="{{route($route, $route_params ?? ['business' => $slug])}}">
            @if(isset($inputs) && is_array($inputs))
                @foreach($inputs as $k => $input)
                    <input type="hidden" name="{{$k}}" value="{{$input}}">
                @endforeach
            @endif

            @if($view == "services")
                <input type="hidden" name="load" value="{{$load + ($plus ?? 5)}}">
            @endif

            @if($view == "types")
                <input type="hidden" name="load_types" value="{{$load_types + ($plus ?? 5)}}">
            @endif

            @if($view == "addresses")
                <input type="hidden" name="load_addresses" value="{{$load_addresses + ($plus ?? 5)}}">
            @endif

            <input type="hidden" name="view" value="{{$view ?? ""}}">
            <button style="background-color: #fff;" class="btn load-more"><i class="load-icon cover"></i>{{__('Загрузить ещё')}}</button>
        </form>
    @endif
</div>
