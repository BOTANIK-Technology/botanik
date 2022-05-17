<footer class="{{$class ?? ''}}">
    <div class="author-tm">
        {!! file_get_contents(public_path('images/botanik.svg')) !!}
    </div>
    <div class="copyright flex direction-column">
        <span>Copyright © {{date("Y")}} BOTANIK technology.</span>
        <span>All rights reserved.</span>
    </div>
    <div class="author-link">
        <a href="https://botanik-technology.com.ua">{{__('botanik-technology.com.ua')}}</a>
    </div>
</footer>
