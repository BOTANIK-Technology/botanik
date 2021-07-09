@section('modal')
    @component('modal')
        <div class="view">
            @if($result)
                <p class="data"><b>{{__('Синхронизация клиентов')}}</b></p>
                <div class="line"></div>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{$result['clients']['create']}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{$result['clients']['update']}}</b></span>
                    <span class="label">{{__('Выгружено в BeautyPro')}}</span>
                    <span class="data"><b>{{$result['clients']['upload']}}</b></span>
                </div>
                <div class="line"></div>
                <p class="data"><b>{{__('Синхронизация специалистов')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{$result['staff']['create']}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{$result['staff']['update']}}</b></span>
                    <span class="label">{{__('Выгружено в BeautyPro')}}</span>
                    <span class="data"><b>{{$result['staff']['upload']}}</b></span>
                </div>
                <div class="line"></div>
                <p class="data"><b>{{__('Синхронизация типов (категорий) услуг')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{$result['services_types']['create']}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{$result['services_types']['update']}}</b></span>
                    <span class="label">{{__('Выгружено в BeautyPro')}}</span>
                    <span class="data"><b>{{$result['services_types']['upload']}}</b></span>
                </div>
                <div class="line"></div>

                <p class="data"><b>{{__('Синхронизация услуг')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{$result['services']['create']}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{$result['services']['update']}}</b></span>
                    <span class="label">{{__('Выгружено в BeautyPro')}}</span>
                    <span class="data"><b>{{$result['services']['upload']}}</b></span>
                </div>
                <div class="line"></div>

                <p class="data"><b>{{__('Синхронизация записей')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{$result['records']['create']}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{$result['records']['update']}}</b></span>
                    <span class="label">{{__('Выгружено в BeautyPro')}}</span>
                    <span class="data"><b>{{$result['records']['upload']}}</b></span>
                </div>
                <div class="line"></div>

                <p class="data"><b>{{__('Синхронизация товаров')}}</b></p>
                <div class="grid">
                    <span class="label">{{__('Импортировано')}}</span>
                    <span class="data"><b>{{$result['products']['create']}}</b></span>
                    <span class="label">{{__('Обновлено')}}</span>
                    <span class="data"><b>{{$result['products']['update']}}</b></span>
                </div>
                <div class="line"></div>
            @endif
        </div>
        <a href="/{{$slug}}/partner-api" id="refresh-modal"></a>
    @endcomponent
@endsection
