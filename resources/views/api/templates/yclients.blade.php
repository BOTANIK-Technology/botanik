<form method="post" action="{{route('api.update', ['business' => $slug, 'slug' => 'yclients'])}}" class="api">
    @csrf
    <h2>Yclients</h2>
    <div class="input-block">
        <label for="yclients_login">Логин</label>
        <input id="yclients_login" name="login" type="text" class="inp" placeholder="Введите логин" value="{{$config->partner_token ?? ''}}">
    </div>
    <div class="input-block">
        <label for="yclients_password">Пароль</label>
        <input id="yclients_password" name="password" type="password" class="inp" placeholder="Введите пароль" value="{{$config->partner_token ?? ''}}">
    </div>
    <button class="btn-primary">Сохранить</button>
</form>

<form method="post" action="{{route('api.call', ['business' => $slug])}}" class="api">
    @csrf
    <div class="input-block">
        <label for="method">Клиенты</label>
        <div class="buttons">
            <button name="method" value="sendClients" class="btn">Отправить</button>
            <button name="method" value="getClients" class="btn">Загрузить</button>
        </div>
    </div>
</form>
