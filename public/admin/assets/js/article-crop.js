(() => {
    const editor = document.querySelector('[data-crop-editor]');
    const file = document.querySelector('#article-image');
    if (!editor || !file) return;
    const x = editor.querySelector('#image-focus-x');
    const y = editor.querySelector('#image-focus-y');
    const images = editor.querySelectorAll('[data-crop-preview]');
    const update = () => images.forEach(img => {img.style.objectPosition = `${x.value}% ${y.value}%`;});
    x.addEventListener('input', update);
    y.addEventListener('input', update);
    let objectUrl;
    file.addEventListener('change', () => {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        if (!file.files[0] || !file.files[0].type.startsWith('image/')) return;
        objectUrl = URL.createObjectURL(file.files[0]);
        images.forEach(img => {img.src = objectUrl;});
        editor.querySelector('.editorial-crop-previews').hidden = false;
        update();
    });
    update();
})();
