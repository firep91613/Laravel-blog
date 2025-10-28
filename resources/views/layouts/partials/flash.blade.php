@if (session('status'))
    <div class="alert__message alert__message_status">
        {{ session('status') }}
        <span class="alert__close">&times;</span>
    </div>
@endif

@if (session('success'))
    <div class="alert__message alert__message_success">
        {{ session('success') }}
        <span class="alert__close">&times;</span>
    </div>
@endif

@if (session('error'))
    <div class="alert__message alert__message_danger">
        {{ session('error') }}
        <span class="alert__close">&times;</span>
    </div>
@endif

@if (session('info'))
    <div class="alert__message alert__message_info">
        {{ session('info') }}
        <span class="alert__close">&times;</span>
    </div>
@endif

