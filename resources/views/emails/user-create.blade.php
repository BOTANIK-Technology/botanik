{{__('Логин: ')}}<b>{{$login}}</b><br>
{{__('Пароль: ')}}<b>{{$password}}</b><br>
{{__('Ссылка на вход:') . URL::to('/').'/'.$slug.'/login'}}