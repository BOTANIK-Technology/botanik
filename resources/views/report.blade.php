@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/report.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{asset('js/report.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            <header class="flex align-items-center">
                <a href="{{route('report', ['business' => $slug, 'sort' => 'today', 'types_page' => $types_page, 'addresses_page' => $addresses_page])}}" class="hashtag {{$sort == 'today' ? 'active' : ''}}">за день</a>
                <a href="{{route('report', ['business' => $slug, 'sort' => 'week', 'types_page' => $types_page, 'addresses_page' => $addresses_page])}}" class="hashtag {{$sort == 'week' ? 'active' : ''}}">за неделю</a>
                <a href="{{route('report', ['business' => $slug, 'sort' => 'month', 'types_page' => $types_page, 'addresses_page' => $addresses_page])}}" class="hashtag {{$sort == 'month' ? 'active' : ''}}">за месяц</a>
                <a href="{{route('report', ['business' => $slug, 'sort' => 'year', 'types_page' => $types_page, 'addresses_page' => $addresses_page])}}" class="hashtag {{$sort == 'year' ? 'active' : ''}}">за год</a>
                <a href="{{route('report', ['business' => $slug, 'sort' => 'all', 'types_page' => $types_page, 'addresses_page' => $addresses_page])}}" class="hashtag {{$sort == 'all' ? 'active' : ''}}">за всё время</a>
                <form class="flex justify-content-between align-items-center custom" action="{{route('report', ['business' => $slug, 'sort' => $sort, 'types_page' => $types_page, 'addresses_page' => $addresses_page])}}" method="GET">
                    <div class="flex align-items-center start-block">
                        <label for="start-date" class="hashtag {{$sort == 'custom' && $start_date ? 'active' : ''}}">от</label>
                        <input class="inp {{$start_date ? 'active' : ''}}" id="start-date" name="start_date" placeholder="дд.мм.гггг" value="{{$start_date ?? ''}}">
                    </div>
                    <div class="flex align-items-center end-block">
                        <label for="end-date" class="hashtag {{$sort == 'custom' && $end_date ? 'active' : ''}}">до</label>
                        <input class="inp {{$end_date ? 'active' : ''}}" id="end-date" name="end_date" placeholder="дд.мм.гггг" value="{{$end_date ?? ''}}">
                    </div>
                    <input type="hidden" value="custom" name="sort">
                    <button class="calendar-icon cover background-none" id="custom" type="submit"></button>
                </form>
            </header>

            <div class="flex direction-column align-items-center padding-top">


                @if ($services && !empty($reports))
                    <div id="services-block" class="grid report-main">

                        <div class="prev align-self-center col-1">
                            @if ($services->previousPageUrl())
                                <a href="{{route('report', ['business' => $slug, 'sort' => $sort, 'start_date' => $start_date, 'end_date' => $end_date, 'addresses_page' => $addresses_page, 'types_page' => $services->currentPage()-1])}}">{!! file_get_contents(public_path('images/prev.svg')) !!}</a>
                            @endif
                        </div>

                        <div id="services-table" class="timetable grid col-2">
                            <div class="row-1 border-right-main border-bottom-main day"></div>
                            @php $i = 2 @endphp
                            @foreach($services as $service)
                                <div class="row-1 day border-bottom-main cnt border-right" style="grid-column:{{$loop->iteration+1}}">{{$service->type}}</div>
                                @for($j = 2; $j < 5; $j++)
                                    <div  class="black-text border-right {{$j == 2 ? '' : 'border-top'}}" style="grid-column:{{$i}};grid-row:{{$j}}">
                                        @switch($j)
                                            @case(2)
                                                {{$reports['typesReports'][$service->id]['total'] ?? 0}}
                                                @break
                                            @case(3)
                                                {{$reports['typesReports'][$service->id]['records'] ?? 0}}
                                                @break
                                            @case(4)
                                                {{$reports['typesReports'][$service->id]['feeds'] ?? 0}}
                                                @break
                                        @endswitch
                                    </div>
                                @endfor
                                @php $i++ @endphp
                            @endforeach
                            @if ($i < 8)
                                @while($i < 8)

                                    <div class="row-1 day border-bottom-main cnt border-right" style="grid-column:{{$i}}">-</div>
                                    @for($j = 2; $j < 5; $j++)
                                        <div  class="black-text border-right {{$j == 2 ? '' : 'border-top'}}" style="grid-column:{{$i}};grid-row:{{$j}}">
                                            @switch($j)
                                                @case(2)
                                                    -
                                                @break
                                                @case(3)
                                                    -
                                                @break
                                                @case(4)
                                                    -
                                                @break
                                            @endswitch
                                        </div>
                                    @endfor

                                    @php $i++ @endphp

                                @endwhile
                            @endif
                            <div class="row-1 day border-bottom-main cnt" style="grid-column:8">Все услуги</div>
                            <div class="border-right-main col-1 time cnt row-2">Доход (₴)</div>
                            <div class="border-right-main border-top col-1 time cnt row-3">Активность записей</div>
                            <div class="border-right-main border-top col-1 time cnt row-4">Отзывы</div>
                            <div class="row-2 black-text" style="grid-column:8">{{$reports['total']}}</div>
                            <div class="row-3 border-top black-text" style="grid-column:8">{{$reports['records']}}</div>
                            <div class="row-4 border-top black-text" style="grid-column:8">{{$reports['feeds']}}</div>
                        </div>



                        <div class="next align-self-center col-3">
                            @if ($services->nextPageUrl())
                                <a href="{{route('report', ['business' => $slug, 'sort' => $sort, 'start_date' => $start_date, 'end_date' => $end_date, 'addresses_page' => $addresses_page, 'types_page' => $services->currentPage()+1])}}">{!! file_get_contents(public_path('images/next.svg')) !!}</a>
                            @endif
                        </div>

                    </div>
                @endif

                @if ($addresses && !empty($reports))
                    <div id="addresses-block" class="grid report-main">

                        <div class="prev align-self-center col-1">
                            @if ($addresses->previousPageUrl())
                                <a href="{{route('report', ['business' => $slug, 'sort' => $sort, 'start_date' => $start_date, 'end_date' => $end_date, 'types_page' => $types_page, 'addresses_page' => $addresses->currentPage()-1])}}">{!! file_get_contents(public_path('images/prev.svg')) !!}</a>
                            @endif
                        </div>

                        <div id="addresses-table" class="timetable grid col-2">
                            <div class="row-1 border-right-main border-bottom-main day"></div>
                            @php $i = 2 @endphp
                            @foreach($addresses as $address)
                                <div class="row-1 day border-bottom-main cnt border-right" style="grid-column:{{$loop->iteration+1}}">{{$address->address}}</div>
                                @for($j = 2; $j < 4; $j++)
                                    <div  class="black-text border-right {{$j == 2 ? '' : 'border-top'}}" style="grid-column:{{$i}};grid-row:{{$j}}">
                                        @switch($j)
                                            @case(2)
                                            {{$reports['addressesReports'][$address->id]['total'] ?? 0}}
                                            @break
                                            @case(3)
                                            {{$reports['addressesReports'][$address->id]['records'] ?? 0}}
                                            @break
                                        @endswitch
                                    </div>
                                @endfor
                                @php $i++ @endphp
                            @endforeach
                            @if ($i < 8)
                                @while($i < 8)

                                    <div class="row-1 day border-bottom-main cnt border-right" style="grid-column:{{$i}}">-</div>
                                    @for($j = 2; $j < 4; $j++)
                                        <div  class="black-text border-right {{$j == 2 ? '' : 'border-top'}}" style="grid-column:{{$i}};grid-row:{{$j}}">
                                            @switch($j)
                                                @case(2)
                                                -
                                                @break
                                                @case(3)
                                                -
                                                @break
                                            @endswitch
                                        </div>
                                    @endfor

                                    @php $i++ @endphp

                                @endwhile
                            @endif
                            <div class="row-1 day border-bottom-main cnt" style="grid-column:8">Все адреса</div>
                            <div class="border-right-main col-1 time cnt row-2">Доход (₴)</div>
                            <div class="border-right-main border-top col-1 time cnt row-3">Активность записей</div>
                            <div class="row-2 black-text" style="grid-column:8">{{$reports['total']}}</div>
                            <div class="row-3 border-top black-text" style="grid-column:8">{{$reports['records']}}</div>
                        </div>

                        <div class="next align-self-center col-3">
                            @if ($addresses->nextPageUrl())
                                <a href="{{route('report', ['business' => $slug, 'sort' => $sort, 'start_date' => $start_date, 'end_date' => $end_date, 'types_page' => $types_page, 'addresses_page' => $addresses->currentPage()+1])}}">{!! file_get_contents(public_path('images/next.svg')) !!}</a>
                            @endif
                        </div>

                    </div>

                    <a class="download btn pointer text-decoration-none" href="{{route('report.download', ['business' => $slug, 'sort' => $sort, 'start_date' => $start_date, 'end_date' => $end_date])}}" download="">Скачать отчет</a>
                @endif

                @if($catalog && $products)

                        <div id="catalog-block" class="grid report-main">

                            <div class="prev align-self-center col-1">
                                @if ($products->previousPageUrl())
                                    <a href="{{route('report', ['business' => $slug, 'sort' => $sort, 'start_date' => $start_date, 'end_date' => $end_date, 'types_page' => $types_page, 'addresses_page' => $addresses_page, 'products_page' => $products->currentPage()-1])}}">{!! file_get_contents(public_path('images/prev.svg')) !!}</a>
                                @endif
                            </div>

                            <div id="addresses-table" class="timetable grid col-2">
                                <div class="row-1 border-right-main border-bottom-main day"></div>
                                @php $i = 2 @endphp
                                @foreach($products as $product)
                                    <div class="row-1 day border-bottom-main cnt border-right" style="grid-column:{{$loop->iteration+1}}">{{$product->title}}</div>
                                    @for($j = 2; $j < 5; $j++)
                                        <div  class="black-text border-right {{$j == 2 ? '' : 'border-top'}}" style="grid-column:{{$i}};grid-row:{{$j}}">
                                            @switch($j)
                                                @case(2)
                                                    {{$productsReports['reports'][$product->id]['total'] ?? 0}}
                                                @break
                                                @case(3)
                                                    {{$productsReports['reports'][$product->id]['sales'] ?? 0}}
                                                @break
                                                @case(4)
                                                    {{$productsReports['reports'][$product->id]['visits'] ?? 0}}
                                                @break
                                            @endswitch
                                        </div>
                                    @endfor
                                    @php $i++ @endphp
                                @endforeach
                                @if ($i < 8)
                                    @while($i < 8)

                                        <div class="row-1 day border-bottom-main cnt border-right" style="grid-column:{{$i}}">-</div>
                                        @for($j = 2; $j < 5; $j++)
                                            <div  class="black-text border-right {{$j == 2 ? '' : 'border-top'}}" style="grid-column:{{$i}};grid-row:{{$j}}">
                                                @switch($j)
                                                    @case(2)
                                                        -
                                                    @break
                                                    @case(3)
                                                        -
                                                    @break
                                                    @case(4)
                                                        -
                                                    @break
                                                @endswitch
                                            </div>
                                        @endfor

                                        @php $i++ @endphp

                                    @endwhile
                                @endif
                                <div class="row-1 day border-bottom-main cnt" style="grid-column:8">Все товары</div>
                                <div class="border-right-main col-1 time cnt row-2">Доход (₴)</div>
                                <div class="border-right-main border-top col-1 time cnt row-3">Заказы</div>
                                <div class="border-right-main border-top col-1 time cnt row-4">{{__('Посещения')}}</div>
                                <div class="row-2 black-text" style="grid-column:8">{{$productsReports['total']}}</div>
                                <div class="row-3 border-top black-text" style="grid-column:8">{{$productsReports['sales']}}</div>
                                <div class="row-4 border-top black-text" style="grid-column:8">{{$productsReports['visits']}}</div>
                            </div>

                            <div class="next align-self-center col-3">
                                @if ($addresses->nextPageUrl())
                                    <a href="{{route('report', ['business' => $slug, 'sort' => $sort, 'start_date' => $start_date, 'end_date' => $end_date, 'types_page' => $types_page, 'addresses_page' => $addresses_page, 'products_page' => $products->currentPage()+1])}}">{!! file_get_contents(public_path('images/next.svg')) !!}</a>
                                @endif
                            </div>

                        </div>

                    <a class="download btn pointer text-decoration-none" href="{{route('report.catalog.download', ['business' => $slug, 'sort' => $sort, 'start_date' => $start_date, 'end_date' => $end_date])}}" download="">Скачать отчет</a>

                @endif
            </div>
        @endslot
    @endcomponent
@endsection