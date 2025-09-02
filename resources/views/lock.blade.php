<h2>This paste is password protected.</h2>
<form method="POST" action="{{ route('pastes.unlock', $paste) }}">
    @csrf
    <input type="password" name="password" required>
    <button type="submit">Unlock</button>
</form>