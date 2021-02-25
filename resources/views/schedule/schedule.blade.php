@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/schedule.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script>
        let url = '{{url()->current()}}';
        let baseUrl = '{{url('/').'/'.$slug.'/'.'schedule/'}}';
        @if (isset($modal) && isset($id))
            let endurl = '/{{$modal}}/{{$id}}';
        @endif
    </script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/schedule.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        @role('master')

            <div class="schedule-pers flex">
                <div class="date-sort flex direction-column">
                    <select id="months" class="border-color">
                        <option value="">{{__('Выберите месяц')}}</option>
                        @foreach($calendar as $m)
                            <option value="{{$loop->index}}" @if($date) @foreach($m as $d) {{$d == $date ? 'selected' : ''}} @endforeach @endif>{{$m[0]}}</option>
                        @endforeach
                    </select>
                    @foreach($calendar as $m)
                        <select id="days-{{$loop->index}}" class="border-color @if($date) @php foreach($m as $d){if($d==$date){$ok=true;break;}else{$ok=false;}} @endphp {{$ok ? '' : 'hide'}} @else {{$loop->first ? '' : 'hide'}} @endif">
                            <option value="">{{__('Выберите день')}}</option>
                            @foreach($m as $k => $d)
                                @if (!$loop->first)
                                    <option value="{{$d}}" {{$d == $date ? 'selected' : ''}}>{{$k}}</option>
                                @endif
                            @endforeach
                        </select>
                    @endforeach
                </div>

                @if ($schedule)

                    @foreach($schedule as $times)
                    <div class="timetable pers grid">
                        <div class="row-1 border-right-main border-bottom-main day"></div>
                        <div class="day border-bottom-main cnt" style="grid-column:2">{{$address}}</div>
                            @foreach($times as $time)
                                <div class="border-right-main {{$loop->index == 0 ? '' : 'border-top'}} col-1 time cnt" style="grid-row:{{$loop->iteration+1}}">{{$time}}</div>
                                <div class="checkbox col-2 {{$loop->index == 0 ? '' : 'border-top'}}" style="grid-row:{{$loop->iteration+1}}">
                                    @if($records)
                                        @foreach($records as $record)
                                            @if($time == $record->time)
                                                <div class="record flex justify-content-between align-items-center">
                                                    <span>{{$record->telegramUser->first_name}}</span>
                                                    <div class="flex">
                                                        <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'view', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/view-d.svg')) !!}</a>
                                                        <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'edit', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/edit-d.svg')) !!}</a>
                                                        <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'delete', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/delete.svg')) !!}</a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                    </div>
                    @endforeach

                @else

                    <h2>Выходной</h2>

                @endif

            </div>

        @else

            @slot('header')
                <header class="flex align-items-center">
                    <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'create'])}}" class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}записать клиента</a>
                </header>
            @endslot
            <div class="schedule-main grid">
                <div class="date-sort flex direction-column">
                    <select id="months" class="border-color">
                        <option value="">{{__('Выберите месяц')}}</option>
                        @foreach($calendar as $m)
                            <option value="{{$loop->index}}" @if($date) @foreach($m as $d) {{$d == $date ? 'selected' : ''}} @endforeach @endif>{{$m[0]}}</option>
                        @endforeach
                    </select>
                    @foreach($calendar as $m)
                        <select id="days-{{$loop->index}}" class="border-color @if($date) @php foreach($m as $d){if($d==$date){$ok=true;break;}else{$ok=false;}} @endphp {{$ok ? '' : 'hide'}} @else {{$loop->first ? '' : 'hide'}} @endif">
                            <option value="">{{__('Выберите день')}}</option>
                            @foreach($m as $k => $d)
                                @if (!$loop->first)
                                    <option value="{{$d}}" {{$d == $date ? 'selected' : ''}}>{{$k}}</option>
                                @endif
                            @endforeach
                        </select>
                    @endforeach
                </div>
                <div class="prev align-self-center">
                    @if ($services->previousPageUrl())
                        <a href="{{$services->previousPageUrl()}}">{!! file_get_contents(public_path('images/prev.svg')) !!}</a>
                    @endif
                </div>
                <div class="timetable grid">
                    <div class="row-1 border-right-main border-bottom-main day"></div>
                    @php $i = 2 @endphp
                    @foreach($services as $service)
                        <div class="day border-bottom-main cnt {{$i == 6 ? '' : 'border-right'}}" style="grid-column:{{$i}}">{{$service->type}}</div>
                        @php $j = 2 @endphp
                        @foreach($times as $time)
                            <div id="j{{$j}}i{{$i}}" class="checkbox {{$j == 2 ? '' : 'border-top'}} {{$i == 6 ? '' : 'border-right'}}" style="grid-column:{{$i}};grid-row:{{$j}}">
                                @foreach($records as $record)
                                    @if($service->id == $record->service->type_service_id && $time == $record->time)
                                        <div class="record flex justify-content-between align-items-center">
                                            <span>{{$record->telegramUser->first_name}}</span>
                                            <div class="flex">
                                                <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'view', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/view.svg')) !!}</a>
                                                <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'edit', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/edit.svg')) !!}</a>
                                                <a href="{{route('window.schedule', ['business' => $slug, 'date' => $date, 'modal' => 'delete', 'id' => $record->id])}}" class="flex">{!! file_get_contents(public_path('images/delete.svg')) !!}</a>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @php $j++ @endphp
                        @endforeach
                        @php $i++ @endphp
                    @endforeach
                    @php $j = 2; @endphp
                    @foreach($times as $time)
                        <div class="border-right-main border-top col-1 time cnt" style="grid-row:{{$j}}">{{$time}}</div>
                        @php $j++ @endphp
                    @endforeach
                </div>
                <div class="next align-self-center">
                    @if ($services->nextPageUrl())
                        <a href="{{$services->nextPageUrl()}}">{!! file_get_contents(public_path('images/next.svg')) !!}</a>
                    @endif
                </div>
            </div>

        @endrole
    @endcomponent
@endsection

@if (isset($modal))
@section('modal')
    @component('modal')

        @if ($modal === 'view')
            <div class="view">
                <p class="black-text">{{$client_rec->telegramUser->last_name.' '.$client_rec->telegramUser->first_name.' '.$client_rec->telegramUser->middle_name}}</p>
                <a href="tel:{{$client_rec->telegramUser->phone}}" class="text-decoration-none"><p class="black-text">{{$client_rec->telegramUser->phone}}</p></a>
            </div>
            @slot('buttons')
                <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
            @endslot
        @elseif($modal === 'delete')
            <div class="delete text-align-center">
                {{__('Вы действительно')}}<br>
                {{__('хотите удалить клиента')}}<br>
                <b>{{$client_rec->telegramUser->last_name.' '.$client_rec->telegramUser->first_name.' '.$client_rec->telegramUser->middle_name}}</b><br>
                {{__('из таблицы запитей')}}?
            </div>
            @slot('buttons')
                <button type="button" id="delete-schedule" class="btn-primary" onclick="deleteSchedule()">
                    {{ __('Удалить') }}
                </button>
                <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
            @endslot

        @elseif($modal === 'edit')
            <div class="edit">
                <select id="edit-months" class="border-color">
                    @foreach($calendar as $m)
                        <option value="{{$loop->index}}" @if($date) @foreach($m as $d) {{$d == $date ? 'selected' : ''}} @endforeach @endif>{{$m[0]}}</option>
                    @endforeach
                </select>
                @foreach($calendar as $m)
                    <select id="edit-days-{{$loop->index}}" class="border-color @if($date) @php foreach($m as $d){if($d==$date){$ok=true;break;}else{$ok=false;}} @endphp {{$ok ? '' : 'hide'}} @else {{$loop->first ? '' : 'hide'}} @endif">
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
        @else

            <div class="grid create" id="create-block">
                <div id="clients">
                    <select id="client-selector">
                        <option value="none">Выберите клиента</option>
                        @foreach($clients as $client)
                            <option value="{{$client->id}}">ID: {{$client->id}}; Номер: {{$client->phone}}; Имя: {{$client->first_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div id="types">
                    <select id="type-selector" disabled>
                        <option value="none">Выберите тип услуги</option>
                        @foreach($type_services as $type)
                            <option value="{{$type->id}}">{{$type->type}}</option>
                        @endforeach
                    </select>
                </div>
                <div id="services"></div>
                <div id="addresses"></div>
                <div id="masters"></div>
                <div id="dates"></div>
                <div id="times"></div>
            </div>

            @slot('buttons')
                <button type="button" id="create" data-href="{{route('schedule.create', ['business' => $slug])}}" class="btn-primary">
                    {{ __('Создать') }}
                </button>
                <a href="{{route('schedule', ['business' => $slug, 'date' => $date])}}" id="refresh-modal"></a>
            @endslot
        @endif

    @endcomponent

@endsection
@endif