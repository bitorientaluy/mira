{{-- Paginación simple con el estilo propio (la de Laravel trae clases de Tailwind) --}}
@if ($paginator->hasPages())
    <nav class="paginacion" aria-label="Paginación">
        @if ($paginator->onFirstPage())
            <span class="boton boton-secundario" aria-disabled="true">
                <span class="icono" aria-hidden="true">chevron_left</span> Anterior
            </span>
        @else
            <a class="boton boton-secundario" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <span class="icono" aria-hidden="true">chevron_left</span> Anterior
            </a>
        @endif

        <span class="texto-suave texto-chico">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="boton boton-secundario" href="{{ $paginator->nextPageUrl() }}" rel="next">
                Siguiente <span class="icono" aria-hidden="true">chevron_right</span>
            </a>
        @else
            <span class="boton boton-secundario" aria-disabled="true">
                Siguiente <span class="icono" aria-hidden="true">chevron_right</span>
            </span>
        @endif
    </nav>
@endif
