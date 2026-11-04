@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="custom-pagination-container">
        <div class="pagination-info">
            Halaman <strong>{{ $paginator->currentPage() }}</strong>
        </div>

        <div class="pagination-nav">
            @if ($paginator->onFirstPage())
                <span class="page-btn disabled" aria-disabled="true">
                    <i class="fa-solid fa-chevron-left"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-btn">
                    <i class="fa-solid fa-chevron-left"></i> Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-btn">
                    Selanjutnya <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-btn disabled" aria-disabled="true">
                    Selanjutnya <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
