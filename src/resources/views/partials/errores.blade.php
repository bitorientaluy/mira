@if ($errors->any())
    <div class="alerta alerta-error" role="alert">
        <span class="icono" aria-hidden="true">error</span>
        <div>
            <strong>Revisá los datos</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
