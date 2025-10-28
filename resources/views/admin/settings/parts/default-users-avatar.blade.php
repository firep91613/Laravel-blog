<label class="input-file">
    <span type="text" class="input-file__text"></span>
    <input type="file" name="{{ $setting->slug }}" class="input-file__custom" data-old="{{ config('custom.symbolic_images_link') . $setting->value }}">
    <span class="input-file__btn">Выберите изображение:</span>
</label>

<script src="{{ asset('/js/image-changer.js') }}"></script>
<script>
    (new ImageChanger(
        document.querySelector('.input-file__custom'),
        50
    )).init();
</script>
