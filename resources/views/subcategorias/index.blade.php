@extends('layouts.main')
@section('title','Subcategorías')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">📂 Administrar Subcategorías</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Subclasificación específica agrupada por categoría principal</p>
  </div>
  <div>
    <a href="{{ route('subcategorias.crear') }}" class="btn btn-primary" style="font-weight:800; display:flex; align-items:center; gap:6px">
      + Nueva Subcategoría
    </a>
  </div>
</div>

<div class="card" style="border-radius:10px">
  <div class="card-body p-0">
    <div style="overflow-x:auto">
      <table class="table" id="subTable" style="margin:0">
        <thead>
          <tr>
            <th>Subcategoría</th>
            <th>Categoría Padre</th>
            <th>Descripción</th>
            <th>Productos</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subcategorias as $s)
          <tr>
            <td><strong style="color:#38bdf8">{{ $s->nombre }}</strong></td>
            <td><span class="badge badge-info">{{ $s->categoria->nombre ?? '—' }}</span></td>
            <td class="text-muted">{{ $s->descripcion ?? '—' }}</td>
            <td>
              <span class="badge badge-{{ $s->productos_count > 0 ? 'success':'warning' }}">
                {{ $s->productos_count }} producto(s)
              </span>
            </td>
            <td class="actions" style="text-align:right; white-space:nowrap">
              <a href="{{ route('subcategorias.editar', $s->id) }}" class="btn btn-sm btn-primary" title="Editar Subcategoría">✏️ Editar</a>
              <a href="{{ route('subcategorias.confirmar_eliminar', $s->id) }}" class="btn btn-sm btn-danger" title="Eliminar Subcategoría">🗑️</a>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted" style="padding:2.5rem">Sin subcategorías aún.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
