<button class="btn btn-sm btn-secondary btn-icon round d-flex justify-content-center align-items-center copy-link-button"
        data-url="{{ $url }}"
        rel="tooltip" aria-label="Kopiuj odnośnik" data-bs-original-title="Kopiuj odnośnik">
    <i class="far fa-copy"></i>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.copy-link-button').forEach(button => {
            button.addEventListener('click', function () {
                const url = this.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(() => {
                    swal.fire({
                        icon: 'success',
                        title: 'Skopiowano!',
                        text: 'Odnośnik został skopiowany.',
                        confirmButtonText: 'OK'
                    });
                }).catch(err => {
                    Sweetalert2.fire({
                        icon: 'error',
                        title: 'Błąd!',
                        text: 'Kopiowanie odnośnika nie powiodło się.',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
    });
</script>
