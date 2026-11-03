@if ($errors->any())
    <div class="alert alert-danger m-3" role="alert">{{ $errors->first() }}</div>
@endif
@foreach (['success', 'error', 'status'] as $message)
    @if (session($message))
        <div class="alert {{ $message === 'error' ? 'alert-danger' : 'alert-success' }} m-3" role="status">{{ session($message) }}</div>
    @endif
@endforeach
