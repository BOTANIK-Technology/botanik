<footer class="{{$class ?? ''}}">
    <div class="author-tm">
        {!! file_get_contents(public_path('images/botanik.svg')) !!}
    </div>
    <div class="copyright flex direction-column">
        <span>Copyright © 2020 BOTANIK technology.</span>
        <span>All rights reserved.</span>
    </div>
    <div class="author-link">
        <a href="https://botanik-technology.com">{{__('botanik-technology.com')}}</a>
    </div>
</footer>
