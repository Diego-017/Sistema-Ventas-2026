@extends('layouts.main')
@section('title','Categorías')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0"><i class="fa-solid fa-tags"></i> Administrar Categorías</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Clasificación de familias de productos y departamentos</p>
  </div>
  <div>
    <a href="{{ route('categorias.crear') }}" class="btn btn-primary" style="font-weight:800; display:flex; align-items:center; gap:6px">
      + Nueva Categoría
    </a>
  </div>
</div>

<div class="card" style="border-radius:10px">
  <div class="card-body p-0">
    <div style="overflow-x:auto">
      <table class="table" id="catTable" style="margin:0">
        <thead>
          <tr>
            <th>Nombre de Categoría</th>
            <th>Descripción</th>
            <th>Productos Registrados</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categorias as $c)
          <tr>
            <td><strong style="color:#38bdf8">{{ $c->nombre }}</strong></td>
            <td class="text-muted">{{ $c->descripcion ?? '—' }}</td>
            <td>
              <span class="badge badge-{{ $c->productos_count > 0 ? 'success':'warning' }}">
                {{ $c->productos_count }} producto(s)
              </span>
            </td>
            <td class="actions" style="text-align:right; white-space:nowrap">
              <a href="{{ route('categorias.editar', $c->id) }}" class="btn btn-sm btn-primary" title="Editar Categoría"><i class="fa-solid fa-pen"></i> Editar</a>
              <a href="{{ route('categorias.confirmar_eliminar', $c->id) }}" class="btn btn-sm btn-danger" title="Eliminar Categoría"><i class="fa-solid fa-trash"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted" style="padding:2.5rem">Sin categorías aún.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
