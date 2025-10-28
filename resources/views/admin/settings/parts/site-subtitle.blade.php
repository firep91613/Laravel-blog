<label for="{{ $setting->slug }}" class="form__label">{{ $setting->name }}:</label>
<input id="{{ $setting->slug }}" name="{{ $setting->slug }}" type="text" value="{{ old('name', $setting->value) }}" class="form__input">

