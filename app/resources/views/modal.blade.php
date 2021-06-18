<div class="modal hide {{ $class ?? '' }}" id="{{ $name ?? 'modal' }}" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content @if(isset($buttons) || isset($footer)) add-footer @endif">
            <div class="modal-header flex justify-content-between">
                <div class="back align-self-center">{{ $header ?? '' }}</div><button type="button" class="close" id="modal-close-btn">×</button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if(isset($buttons) || isset($footer))
                <div class="modal-footer text-align-center">
                    {{ $buttons ?? '' }}
                </div>
            @endif
        </div>
    </div>
</div>
