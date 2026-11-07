{{-- Insignia con el color del estado. Uso: @include('partials.insignia-estado', ['estado' => $incidencia->estado]) --}}
<span class="insignia insignia-punto insignia-{{ \Illuminate\Support\Str::slug($estado->value) }}">{{ $estado->value }}</span>
