@if ($paginator->hasPages())
    <div class="custom-pagination">
        @if ($paginator->onFirstPage())
            <span class="page-btn disabled">&laquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">&laquo;</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-btn disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">&raquo;</a>
        @else
            <span class="page-btn disabled">&raquo;</span>
        @endif
    </div>
@endif
