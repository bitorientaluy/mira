@if ($errors->any())
    <div class="alerta alerta-error" role="alert">
        <span class="icono" aria-hidden="true">error</span>
        <div>
            <strong>Revisá los datos</strong>
            <ul>
                {{-- Sin repetidos: lat y lng comparten el mismo mensaje --}}
                @foreach (array_unique($errors->all()) as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
