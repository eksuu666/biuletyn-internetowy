<h2 id="contact-heading">Napisz do redakcji</h2>
<form action="{{ route('contact-us.message') }}" method="post" role="form">
    @csrf
    @honeypot
    @if(session()->has('errors'))
        <div class="alert alert-danger">
            <strong>Błąd!</strong>
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Imię</label>
                <input class="form-control" name="name" id="name" type="text" autocomplete="name" value="{{ old('name') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="subject">Temat</label>
                <input class="form-control" name="subject" id="subject" value="{{ old('subject') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input class="form-control" name="email" id="email" type="email" autocomplete="email" value="{{ old('email') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="phone">Telefon (opcjonalnie)</label>
                <input class="form-control" name="phone" id="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="message">Wiadomość</label>
        <textarea class="form-control" name="message" id="message" rows="6" required>{{ old('message') }}</textarea>
    </div>
    <button class="portal-button" type="submit">Wyślij wiadomość</button>
</form>
