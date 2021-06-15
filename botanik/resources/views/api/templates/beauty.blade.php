<div class="api">
    <h2>Beauty PRO</h2>
    <div class="input-block">
        <label for="beauty_application_id">application_id</label>
        <input id="beauty_application_id" type="text" class="inp" placeholder="Введите текст" value="{{$config->application_id ?? ''}}">
    </div>
    <div class="input-block">
        <label for="beauty_application_secret">application_secret</label>
        <input id="beauty_application_secret" type="text" class="inp" placeholder="Введите текст" value="{{$config->application_secret ?? ''}}">
    </div>
    <div class="input-block">
        <label for="beauty_database_code">database_code</label>
        <input id="beauty_database_code" type="text" class="inp" placeholder="Введите текст" value="{{$config->database_code ?? ''}}">
    </div>
    <button
        class="btn-primary"
        data-url="{{route('api.update', ['business' => $slug, 'slug' => 'beauty'])}}"
        id="beauty_update"
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
                data-url="{{route('api.call', ['business' => $slug, 'slug' => 'beauty', 'method' => 'sendClients'])}}"
                id="beauty_sendClients"
            >
                Отправить
            </button>
            <button
                name="method"
                class="btn"
                data-url="{{route('api.call', ['business' => $slug, 'slug' => 'beauty', 'method' => 'getClients'])}}"
                id="beauty_getClients"
            >
                Загрузить
            </button>
        </div>
    </div>
</div>
