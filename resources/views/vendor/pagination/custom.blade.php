@if ($paginator->hasPages())
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-top: 1px solid var(--border-light); background: var(--white); border-bottom-left-radius: var(--radius); border-bottom-right-radius: var(--radius);">
        <div style="font-size: 13px; color: var(--gray-500);">
            Menampilkan <span style="font-weight: 600; color: var(--text);">{{ $paginator->firstItem() }}</span> hingga <span style="font-weight: 600; color: var(--text);">{{ $paginator->lastItem() }}</span> dari <span style="font-weight: 600; color: var(--text);">{{ $paginator->total() }}</span> entri
        </div>
        <div style="display: flex; gap: 4px;">
            @if ($paginator->onFirstPage())
                <span class="btn-small outline" style="color: var(--gray-400); border-color: var(--border-light); cursor: not-allowed; opacity: 0.6;"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn-small outline" style="text-decoration: none;"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="btn-small outline" style="color: var(--gray-400); border-color: var(--border-light); cursor: default;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="btn-small teal" style="cursor: default;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="btn-small outline" style="text-decoration: none;">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn-small outline" style="text-decoration: none;"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="btn-small outline" style="color: var(--gray-400); border-color: var(--border-light); cursor: not-allowed; opacity: 0.6;"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
@endif
