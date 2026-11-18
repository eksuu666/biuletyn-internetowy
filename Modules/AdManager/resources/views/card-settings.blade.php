<div class="form-group col-lg-6">
    <label for="card-size">Rozmiar kafelka na głównej</label>
    <select class="form-control" id="card-size" name="card_size">
        <option value="small" @selected(old('card_size', $ad->card_size ?? 'small') === 'small')>Mały - jedna kolumna</option>
        <option value="large" @selected(old('card_size', $ad->card_size ?? 'small') === 'large')>Duży - dwie kolumny</option>
    </select>
    <p class="help-block">Dotyczy miejsca „Kafelki między artykułami”. Grafika reklamy jest wyświetlana w całości.</p>
</div>
<div class="form-group col-lg-6">
    <label for="ad-sponsor-name">Nazwa reklamodawcy (opcjonalnie)</label>
    <input class="form-control" id="ad-sponsor-name" name="sponsor_name" maxlength="120" value="{{ old('sponsor_name', $ad->sponsor_name ?? '') }}">
</div>
