<form method="post" action="{{route('api.update', ['business' => $slug, 'slug' => 'beauty'])}}" class="api">
    @csrf
    <h2>Beauty PRO</h2>
    <div class="input-block">
        <label for="beauty_application_id">application_id</label>
        <input id="beauty_application_id" name="application_id" type="text" class="inp" placeholder="Введите текст" value="{{$config->partner_token ?? ''}}">
    </div>
    <div class="input-block">
        <label for="beauty_database_code">database_code</label>
        <input id="beauty_database_code" name="database_code" type="text" class="inp" placeholder="Введите текст" value="{{$config->partner_token ?? ''}}">
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
