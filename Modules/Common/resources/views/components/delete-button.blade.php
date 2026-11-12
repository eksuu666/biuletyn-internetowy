<div>
    <form action="{{ $route }}" method="post">
        @csrf
        @method('delete')
        <button class="btn btn-sm btn-danger btn-icon round d-flex justify-content-center align-items-center"
                rel="tooltip" aria-label="Usuń" data-bs-original-title="Usuń">
            <i class="icon-trash fa-flip-horizontal"></i>
        </button>
    </form>
</div>
