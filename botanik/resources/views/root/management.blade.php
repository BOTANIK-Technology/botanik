@extends('root.layouts.app')

@section('styles')
    <link href="{{ asset('css/root/management.css') }}" rel="stylesheet">
    @if(isset($modal) && $modal == 'edit')
        <link href="{{ asset('css/root/business.css') }}" rel="stylesheet">
    @endif
@endsection

@section('scripts')
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/root/management.js')}}"></script>
    @if(isset($modal) && $modal == 'edit')
        <script src="{{asset('js/root/business.js')}}"></script>
    @endif
@endsection

@section('content')
    @component('root.layouts.content')

        <div class="table grid">
            @if (isset($table) && $table)
                @phone
                    @foreach ($table as $item)
                        <div class="flex align-items-center justify-content-center num">
                            {{$loop->iteration}}
                        </div>
                        <div class="flex align-items-center text">
                            {{$item->name}}
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <div class="more-icon" data-id="{{$item->id}}"></div>
                            <div id="menu-{{$item->id}}" class="more-menu hide">
                                <ul>
                                    <li><a href="{{route('root.window.management', ['modal' => 'view',   'id' => $item->id, 'load' => $load])}}"><div class="view-icon"  ></div><span class="more-menu-text pur">Просмотр</span></a></li>
                                    <li><a href="{{route('root.window.management', ['modal' => 'edit',   'id' => $item->id, 'load' => $load])}}"><div class="edit-icon"  ></div><span class="more-menu-text pur">Редактировать</span></a></li>
                                    <li><a href="{{route('root.window.management', ['modal' => 'chart',  'id' => $item->id, 'load' => $load])}}"><div class="chart-icon" ></div><span class="more-menu-text pur">Статистика</span></a></li>
                                    <li><a href="{{route('root.window.management', ['modal' => 'pause',  'id' => $item->id, 'load' => $load])}}"><div class="pause-icon" ></div><span class="more-menu-text pur">Остановить</span></a></li>
                                    <li><a href="{{route('root.window.management', ['modal' => 'delete', 'id' => $item->id, 'load' => $load])}}"><div class="delete-icon"></div><span class="more-menu-text red">Удалить</span></a></li>
                                </ul>
                                <div data-id="{{$item->id}}" class="more-menu-close"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach ($table as $item)
                        <div class="flex align-items-center justify-content-center num">
                            {{$loop->iteration}}
                        </div>
                        <div class="flex align-items-center text">
                            {{$item->name}}
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('root.window.management', ['modal' => 'view', 'id' => $item->id, 'load' => $load])}}"><div class="view-icon"></div></a>
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('root.window.management', ['modal' => 'edit', 'id' => $item->id, 'load' => $load])}}"><div class="edit-icon"></div></a>
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('root.window.management', ['modal' => 'chart', 'id' => $item->id, 'load' => $load])}}"><div class="chart-icon icon"></div></a>
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('root.window.management', ['modal' => 'pause', 'id' => $item->id, 'load' => $load])}}"><div class="pause-icon icon"></div></a>
                        </div>
                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('root.window.management', ['modal' => 'delete', 'id' => $item->id, 'load' => $load])}}"><div class="delete-icon"></div></a>
                        </div>
                    @endforeach
                @endphone
            @endif
        </div>

        @if ($countItems > $load)
            <form method="GET" action="{{route('service', ['load' => $load])}}">
                <input type="hidden" name="load" value="{{$load + 5}}">
                <button class="btn load-more"><i class="load-icon cover"></i>Загрузить ещё</button>
            </form>
        @endif

    @endcomponent
@endsection

@if (isset($modal))
    @section('modal')
    @component('modal')

        @if ($modal === 'view')

            <div class="grid view-grid">

                <div class="col-1-2 flex align-items-center direction-column">
                    <img src="{{isset($business->img) ? asset('public/storage/'.$business->img) : asset("images/image-icon.svg")}}" class="view-logo" alt="business logotype">
                    <span class="view-title color"><b>{{$business->name}}</b></span>
                </div>

                <div class="flex direction-column view">
                    <span>Токен платежной системы</span>
                    <div>{{$business->pay_token}}</div>

                    <span>Токен телеграм бота</span>
                    <div>{{$business->token}}</div>

                    <span>SLUG</span>
                    <div>{{$business->slug}}</div>

                    <span>База данных</span>
                    <div>{{$business->db_name}}</div>
                </div>

                <div class="flex direction-column view">
                    <span>ФИО основателя</span>
                    <div>{{$business->owner->fio}}</div>

                    <span>Почта основателя</span>
                    <div>{{$business->owner->email}}</div>

                    <span>Пароль основателя</span>
                    <div>{{$business->owner->password}}</div>

                    <span>Ссылка на вход</span>
                    <div>{{URL::to('/').'/'.$business->slug.'/login'}}</div>

                    <span>Статус</span>
                    <div>{{$business->status ? 'активен' : 'заблокирован'}}</div>
                </div>

            </div>

            @slot('buttons')
                <a href="{{route('root.management', ['load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @elseif($modal === 'edit')

            <form class="text-align-left grid create-form" method="post" enctype="multipart/form-data" action="{{route('root.management.edit', ['id' => $business->id])}}">
                <label for="business-name">Название бизнеса</label>
                <input type="text" placeholder="Введите текст" id="business-name" name="business_name" class="inp" value="{{$business->name}}">

                <label for="bot-name">Название бота</label>
                <input type="text" placeholder="Введите текст" id="bot-name" name="bot_name" class="inp" value="{{$business->bot_name}}">

                <label for="img">Логотип</label>
                <label for="img" class="image-logo flex align-items-center" id="img-label" style="background-image:url('{{isset($business->img) ? asset('public/storage/'.$business->img) : asset("images/image-icon.svg")}}')">
                    <input type="file" id="img" name="logo">
                </label>
                <label for="package">Пакет</label>
                <div class="flex pack">
                    @if ($packages)
                        @foreach($packages as $package)
                            <input type="radio" name="package" value="{{$package->id}}" id="{{mb_strtolower($package->name)}}" {{$business->package_id == $package->id ? 'checked' : ''}}>
                            <label for="{{mb_strtolower($package->name)}}">
                                {{$package->name}}
                            </label>
                        @endforeach
                    @else
                        Пакетов нет!
                    @endif
                </div>
                <label for="pay-token">Платёжный токен</label>
                <input type="text" id="pay-token" name="pay_token" placeholder="API key" class="inp" value="{{$business->pay_token}}">

                <label for="tg-token">Telegram токен</label>
                <input type="text" id="tg-token" name="tg_token" placeholder="API key" class="inp" value="{{$business->token}}">

                <label for="catalog-on">Каталог</label>
                <div class="flex pack">
                    <input type="radio" name="catalog" value="1" id="catalog-on" {{$business->catalog == 1 ? 'checked' : ''}}>
                    <label for="catalog-on">
                        ON
                    </label>
                    <input type="radio" name="catalog" value="0" id="catalog-off" {{$business->catalog == 0 ? 'checked' : ''}}>
                    <label for="catalog-off">
                        OFF
                    </label>
                </div>

                @csrf

                <button type="submit" class="btn-primary">
                    {{ __('Изменить') }}
                </button>

                <a href="{{route('root.management', ['load' => $load])}}" id="refresh-modal"></a>
            </form>

            <form method="post" action="{{route('root.management.webhook', ['id' => $business->id])}}">
                @csrf
                <button type="submit">Rewrite webhook</button>
            </form>

        @elseif($modal === 'chart')
            <div class="grid view-grid">

                <div class="flex direction-column view row-2 col-1">
                    <span>Кол-во выполненых услуг</span>
                    <div>{{$chart['records']}}</div>

                    <span>Сумма прибыли</span>
                    <div>{{$chart['total']}} ₴</div>

                    <span>Созданные разделы информации</span>
                    <div>{{$chart['info']}}</div>

                    <span>Отзывы</span>
                    <div>{{$chart['reviews']}}</div>

                    <span>Feedback</span>
                    <div>{{$chart['feedback']}}</div>

                    <span>Жалобы</span>
                    <div>{{$chart['complaint']}}</div>
                </div>

                <div class="flex direction-column view row-2 col-2">
                    <span>Количество специалистов</span>
                    <div>{{$chart['users']}}</div>

                    <span>Количество администраторов</span>
                    <div>{{$chart['admins']}}</div>

                    <span>Количество услуг</span>
                    <div>{{$chart['services']}}</div>

                    <span>{{__('Количество адресов')}}</span>
                    <div>{{$chart['addrs']}}</div>
                </div>

            </div>

            @slot('buttons')
                <a href="{{route('root.management', ['load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @elseif($modal === 'pause')

            <div class="delete text-align-center">
                Текущий статус бизнеса<br>
                {{$business->name}} - <b>{{$business->status ? 'активен' : 'заблокирован'}}</b><br>
                изменить на статус "{{$business->status ? 'заблокирован' : 'активен'}}"?
            </div>

            @slot('buttons')
                <button type="button" id="confirm" data-id="{{$business->id}}" data-src="{{route('root.management.pause', ['id' => $business->id])}}" class="btn-primary">
                    {{ __('Изменить') }}
                </button>
                <a href="{{route('root.management', ['load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @elseif($modal === 'delete')

            <div class="delete text-align-center">
                Вы действительно хотите удалить<br>
                <b>{{$business->name}}</b><br>
                из системы?
            </div>

            @slot('buttons')
                <button type="button" id="confirm" data-id="{{$business->id}}" data-src="{{route('root.management.delete', ['id' => $business->id])}}" class="btn-primary">
                    {{ __('Удалить') }}
                </button>
                <a href="{{route('root.management', ['load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @endif
    @endcomponent
@endsection
@endif
