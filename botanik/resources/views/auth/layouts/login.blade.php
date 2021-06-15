<div class="container">
    <div class="flex justify-content-center">
        <div class="card">
            <div class="card-header text-align-center"><img src="{{ $logotype ?? '/images/botanik-head.png' }}" class="logo"></div>

            <div class="card-body">
                <form method="POST" action="{{ route('login', $prefix) }}">
                    @csrf

                    <div class="form-group email">
                        <input id="email" type="email" onchange="line(this)" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{__('Логин')}}" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    </div>

                    <div class="form-group password">
                        <input id="password" type="password" onchange="line(this);eye(this)" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{__('Пароль')}}" required autocomplete="current-password">
                        <div id="eye" class="eye hide"></div>

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="restore">
                        @if (Route::has('password.request'))
                            <a class="btn-link" href="{{ route('password.request', $prefix) }}">
                                {{ __('Восстановить пароль') }}
                            </a>
                        @endif
                    </div>

                    <div class="form-group text-align-center">
                        <button type="submit" class="btn-primary">
                            {{ __('Войти') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>