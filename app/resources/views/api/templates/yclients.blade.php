<div class="api">
    <h2>Yclients</h2>
    <div class="input-block">
        {{--        520831 --}}
        <label for="yclients_company_id">ID компании</label>
        <input id="yclients_company_id" name="company_id" type="text" class="inp" placeholder="Введите ID компании" value="{{$config->company_id ?? ''}}">
    </div>
    <div class="input-block">
{{--        520831 --}}
        <label for="yclients_token">Bearer токен</label>
        <input id="yclients_token" name="token" type="text" class="inp" placeholder="Введите токен" value="{{$config->partner_token ?? ''}}">
    </div>
    <div class="input-block">
        <label for="yclients_login">Логин</label>
        <input id="yclients_login" name="login" type="text" class="inp" placeholder="Введите логин" value="{{$config->login ?? ''}}">
    </div>
    <div class="input-block">
        <label for="yclients_password">Пароль</label>
        <input id="yclients_password" name="password" type="password" class="inp" placeholder="Введите пароль" value="{{$config->password ?? ''}}">
    </div>
    <button
        class="btn-primary"
        data-url="{{route('api.update', ['business' => $slug, 'slug' => 'yclients'])}}"
        id="yclients_update"
    >
        Сохранить
    </button>
</div>

<div class="api">
    <div class="input-block">
        <label for="method">Клиенты</label>
        <div class="buttons">
            <button
                name="method"
                class="btn"
                data-url="{{route('api.call', ['business' => $slug, 'slug' => 'yclients', 'method' => 'getClients'])}}"
                id="yclients_sendClients"
            >
                Отправить
            </button>

            <button
                name="method"
                class="btn"
                data-url="{{route('api.call', ['business' => $slug, 'slug' => 'yclients', 'method' => 'sendClients'])}}"
                id="yclients_getClients"
            >
                Загрузить
            </button>
        </div>
    </div>
</div>
