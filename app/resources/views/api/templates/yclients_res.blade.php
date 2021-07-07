@section('modal')
    @component('modal')
        <div class="view">
            @if($result)
                <p class="data"><b>{{__('Синхронизация клиентов')}}</b></p>
                <div class="line"></div>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{count($result['clients']['create'])}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{count($result['clients']['update'])}}</b></span>
                    <span class="label">{{__('Выгружено в YClients')}}</span>
                    <span class="data"><b>{{count($result['clients']['upload'])}}</b></span>
                </div>
                <div class="line"></div>
                <p class="data"><b>{{__('Синхронизация специалистов')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{count($result['staff']['create'])}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{count($result['staff']['update'])}}</b></span>
                    <span class="label">{{__('Выгружено в YClients')}}</span>
                    <span class="data"><b>{{count($result['staff']['upload'])}}</b></span>
                </div>
                <div class="line"></div>
                <p class="data"><b>{{__('Синхронизация типов (категорий) услуг')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{count($result['services_types']['create'])}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{count($result['services_types']['update'])}}</b></span>
                    <span class="label">{{__('Выгружено в YClients')}}</span>
                    <span class="data"><b>{{count($result['services_types']['upload'])}}</b></span>
                </div>
                <div class="line"></div>

                <p class="data"><b>{{__('Синхронизация услуг')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{count($result['services']['create'])}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{count($result['services']['update'])}}</b></span>
                    <span class="label">{{__('Выгружено в YClients')}}</span>
                    <span class="data"><b>{{count($result['services']['upload'])}}</b></span>
                </div>
                <div class="line"></div>

                <p class="data"><b>{{__('Синхронизация записей')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{count($result['records']['create'])}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{count($result['records']['update'])}}</b></span>
                    <span class="label">{{__('Выгружено в YClients')}}</span>
                    <span class="data"><b>{{count($result['records']['upload'])}}</b></span>
                </div>
                <div class="line"></div>

                <p class="data"><b>{{__('Синхронизация каталогов')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{count($result['categories']['create'])}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{count($result['categories']['update'])}}</b></span>
                    <span class="label">{{__('Выгружено в YClients')}}</span>
                    <span class="data"><b>{{count($result['categories']['upload'])}}</b></span>
                </div>
                <div class="line"></div>

                <p class="data"><b>{{__('Синхронизация товаров')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{count($result['products']['create'])}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{count($result['products']['update'])}}</b></span>
                    <span class="label">{{__('Выгружено в YClients')}}</span>
                    <span class="data"><b>{{count($result['products']['upload'])}}</b></span>
                </div>
                <div class="line"></div>
            @endif
        </div>
        <a href="/{{$slug}}/partner-api" id="refresh-modal"></a>
    @endcomponent
@endsection
