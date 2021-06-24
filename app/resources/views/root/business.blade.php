@extends('root.layouts.app')

@section('styles')
    <link href="{{ asset('css/root/business.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/img-label.js')}}"></script>
    <script src="{{asset('js/root/business.js')}}"></script>
@endsection

@section('content')
    @component('root.layouts.content')
        <div class="create-block">
            <div class="grid create-form">

                <label for="business-name">Название бизнеса<span style="color:red;">*</span></label>
                <input type="text" placeholder="Введите текст" id="business-name" name="business_name" class="inp">

                <label for="bot-name">Название бота<span style="color:red;">*</span></label>
                <input type="text" placeholder="Введите текст" id="bot-name" name="bot_name" class="inp">

                <label for="slug">SLUG<span style="color:red;">*</span></label>
                <input type="text" placeholder="Введите текст" id="slug" name="slug" class="inp">

                <label for="img">Логотип</label>
                <label for="img" class="image-logo flex align-items-center" id="img-label">
                    <input type="file" accept="image/*" id="img" name="logo">
                </label>

                <label for="package">Пакет<span style="color:red;">*</span></label>
                <div class="flex pack">
                    @if ($packages)
                        @foreach($packages as $package)
                            <input type="radio" name="package" value="{{$package->id}}" id="{{mb_strtolower($package->name)}}" @if ($loop->last) checked @endif>
                            <label for="{{mb_strtolower($package->name)}}">
                                {{$package->name}}
                            </label>
                        @endforeach
                    @else
                        Пакетов нет!
                    @endif
                </div>

                <label for="name">ФИО основателя<span style="color:red;">*</span></label>
                <div class="flex justify-content-between name">
                    <label for="second-name"></label><input type="text" name="last_name" placeholder="Введите фамилию" id="second-name" class="inp">
                    <input type="text" name="first_name" placeholder="Введите имя" id="first-name" class="inp">
                    <input type="text" name="middle_name" placeholder="Введите отчество" id="middle-name" class="inp">
                </div>

                <label for="pass">Пароль<span style="color:red;">*</span><br>
                    <span style="font-size: 12px;color: #999;">Пароль должен содержать не менее 6-ти латинских символов и цифр</span>
                </label>
                <div class="flex justify-content-between pass">
                    <input type="password" name="password" placeholder="Введите пароль" id="pass" class="inp">
                    <input type="password" name="password_confirmation" placeholder="Повторите пароль" id="re-pass" class="inp">
                </div>

                <label for="email">Почта основателя<span style="color:red;">*</span></label>
                <input type="email" id="email" name="email" placeholder="Введите email" class="inp">

                <label for="pay-token">Платёжный токен</label>
                <input type="text" id="pay-token" name="pay_token" placeholder="API key" class="inp">

                <label for="tg-token">Telegram токен<span style="color:red;">*</span></label>
                <input type="text" id="tg-token" name="tg_token" placeholder="API key" class="inp">

                <label for="catalog-on">Каталог<span style="color:red;">*</span></label>
                <div class="flex pack">
                    <input type="radio" name="catalog" value="1" id="catalog-on">
                    <label for="catalog-on">
                        ON
                    </label>
                    <input type="radio" name="catalog" value="0" id="catalog-off" checked>
                    <label for="catalog-off">
                        OFF
                    </label>
                </div>

                <button
                    class="btn-primary"
                    data-storage="{{route('api.storage')}}"
                    data-href="{{route('root.business.create')}}"
                    id="create"
                >
                    {{__('Создать')}}
                </button>

            </div>
        </div>
    @endcomponent
@endsection
