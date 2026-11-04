@props([
    'show' => 'showConfirmModal',
    'action' => null,
    'actionBinding' => 'confirmRoute',
    'method' => 'POST',
    'title',
    'message' => null,
    'submit' => 'Ya, Hapus',
    'cancel' => 'Batal',
    'submitClass' => 'btn-danger',
    'icon' => 'trash',
    'maxWidth' => '',
])

@php
    $iconBg = $icon === 'warning' ? '#FFF8E7' : '#FAD7D2';
    $iconColor = $icon === 'warning' ? '#D97706' : '#C0392B';
    $boxClass = trim('modal-box confirm-dialog '.$maxWidth);
@endphp

<div
    x-show="{{ $show }}"
    x-cloak
    {{ $attributes->merge(['class' => 'modal-overlay']) }}
    style="display:none;"
    @keydown.escape.window="{{ $show }} = false"
>
    <div
        x-show="{{ $show }}"
        x-transition
        class="{{ $boxClass }}"
        @click.away="{{ $show }} = false"
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
        @if($message) aria-describedby="confirm-modal-desc" @endif
    >
        <form @if($action) action="{{ $action }}" @else x-bind:action="{{ $actionBinding }}" @endif method="POST">
            @csrf
            @if(strtoupper($method) !== 'POST')
                @method($method)
            @endif

            <div class="confirm-dialog__header">
                <button type="button" class="confirm-dialog__close" @click="{{ $show }} = false" aria-label="Tutup dialog">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body confirm-dialog__body">
                <div class="confirm-dialog__hero">
                    <div class="confirm-dialog__icon" style="background:{{ $iconBg }};">
                        @if($icon === 'warning')
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:{{ $iconColor }};" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @else
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:{{ $iconColor }};" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        @endif
                    </div>
                    <div class="confirm-dialog__copy">
                        <h3 class="confirm-dialog__title" id="confirm-modal-title">{{ $title }}</h3>
                        @if($message)
                            <p class="confirm-dialog__message" id="confirm-modal-desc">{{ $message }}</p>
                        @endif
                    </div>
                </div>

                @if($slot->isNotEmpty())
                    <div class="confirm-dialog__slot">
                        {{ $slot }}
                    </div>
                @endif
            </div>

            <div class="modal-footer confirm-dialog__footer">
                <button type="button" @click="{{ $show }} = false" class="btn-secondary">{{ $cancel }}</button>
                <button type="submit" class="{{ $submitClass }}">{{ $submit }}</button>
            </div>
        </form>
    </div>
</div>
