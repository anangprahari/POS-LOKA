@if (Session::has('success'))
    @php
        $success = Session::get('success');
        $message = is_array($success) ? (isset($success['message']) ? $success['message'] : '') : $success;
    @endphp

    <div class="alert alert-success">
        {{ $message }}
    </div>
@endif