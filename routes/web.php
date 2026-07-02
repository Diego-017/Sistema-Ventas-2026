<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController,
    ProductoController, VentaController, ClienteController,
    ReporteController, CompraController, CajaController,
    ConfiguracionController, UsuarioController,
    CategoriaController, ProveedorController,
    SubcategoriaController, InventarioController,
    CreditoController, CotizacionController,
    ImpresoraController
};

// ── AUTH ────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware('auth.session')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/perfil',  [AuthController::class, 'perfil'])->name('perfil');
    Route::post('/perfil', [AuthController::class, 'actualizarPerfil'])->name('perfil.update');

    // ── VENTAS ──────────────────────────────────────────────────
    Route::get('/ventas',                [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/nueva',          [VentaController::class, 'nueva'])->name('ventas.nueva');
    Route::post('/ventas',               [VentaController::class, 'store'])->name('ventas.guardar');
    Route::get('/ventas/{id}',           [VentaController::class, 'show'])->name('ventas.ver');
    Route::patch('/ventas/{id}/anular',  [VentaController::class, 'anular'])->name('ventas.anular');
    Route::get('/ventas/por-vendedor',   [VentaController::class, 'porVendedor'])->name('ventas.vendedor');

    // ── CLIENTES ────────────────────────────────────────────────
    Route::get('/clientes',              [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/{id}',         [ClienteController::class, 'show'])->name('clientes.show');
    Route::post('/clientes',             [ClienteController::class, 'store'])->name('clientes.guardar');
    Route::put('/clientes/{id}',         [ClienteController::class, 'update'])->name('clientes.actualizar');
    Route::delete('/clientes/{id}',      [ClienteController::class, 'destroy'])->name('clientes.eliminar');
    Route::get('/clientes/buscar',       [ClienteController::class, 'search'])->name('clientes.buscar');

    // ── PRODUCTOS ───────────────────────────────────────────────
    Route::get('/productos',             [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/buscar',      [ProductoController::class, 'search'])->name('productos.buscar');
    Route::get('/productos/crear',       [ProductoController::class, 'create'])->name('productos.crear');
    Route::post('/productos',            [ProductoController::class, 'store'])->name('productos.guardar');
    Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.editar');
    Route::put('/productos/{id}',        [ProductoController::class, 'update'])->name('productos.actualizar');
    Route::delete('/productos/{id}',     [ProductoController::class, 'destroy'])->name('productos.eliminar');

    // ── CATEGORÍAS / SUBCATEGORÍAS ──────────────────────────────
    Route::get('/categorias',                  [CategoriaController::class,    'index'])->name('categorias.index');
    Route::post('/categorias',                 [CategoriaController::class,    'store'])->name('categorias.guardar');
    Route::put('/categorias/{id}',             [CategoriaController::class,    'update'])->name('categorias.actualizar');
    Route::delete('/categorias/{id}',          [CategoriaController::class,    'destroy'])->name('categorias.eliminar');

    Route::get('/subcategorias',               [SubcategoriaController::class, 'index'])->name('subcategorias.index');
    Route::post('/subcategorias',              [SubcategoriaController::class, 'store'])->name('subcategorias.guardar');
    Route::put('/subcategorias/{id}',          [SubcategoriaController::class, 'update'])->name('subcategorias.actualizar');
    Route::delete('/subcategorias/{id}',       [SubcategoriaController::class, 'destroy'])->name('subcategorias.eliminar');
    Route::get('/subcategorias/por-categoria/{id}', [SubcategoriaController::class, 'porCategoria'])->name('subcategorias.por_categoria');

    // ── INVENTARIO ──────────────────────────────────────────────
    Route::get('/inventario/compras',               [CompraController::class,     'index'])->name('compras.index');
    Route::get('/inventario/compras/crear',          [CompraController::class,     'create'])->name('compras.crear');
    Route::post('/inventario/compras',               [CompraController::class,     'store'])->name('compras.guardar');
    Route::get('/inventario/compras/{id}',           [CompraController::class,     'show'])->name('compras.ver');
    Route::patch('/inventario/compras/{id}/anular',  [CompraController::class,     'anular'])->name('compras.anular');
    Route::get('/inventario/stock',                  [InventarioController::class, 'stock'])->name('inventario.stock');
    Route::get('/inventario/stock-lote',             [InventarioController::class, 'stockLote'])->name('inventario.stock_lote');
    Route::get('/inventario/traslados',              [InventarioController::class, 'traslados'])->name('inventario.traslados');
    Route::post('/inventario/traslados',             [InventarioController::class, 'storeTraslado'])->name('inventario.traslados.store');

    // ── PROVEEDORES ─────────────────────────────────────────────
    Route::get('/proveedores',           [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::post('/proveedores',          [ProveedorController::class, 'store'])->name('proveedores.guardar');
    Route::put('/proveedores/{id}',      [ProveedorController::class, 'update'])->name('proveedores.actualizar');
    Route::delete('/proveedores/{id}',   [ProveedorController::class, 'destroy'])->name('proveedores.eliminar');

    // ── CUENTAS POR COBRAR ──────────────────────────────────────
    Route::get('/creditos',              [CreditoController::class, 'index'])->name('creditos.index');
    Route::get('/creditos/{id}',         [CreditoController::class, 'show'])->name('creditos.show');
    Route::post('/creditos/{id}/pago',   [CreditoController::class, 'registrarPago'])->name('creditos.pago');

    // ── COTIZACIONES ─────────────────────────────────────────────
    Route::get('/cotizaciones',                      [CotizacionController::class, 'index'])->name('cotizaciones.index');
    Route::get('/cotizaciones/nueva',                [CotizacionController::class, 'nueva'])->name('cotizaciones.nueva');
    Route::post('/cotizaciones',                     [CotizacionController::class, 'store'])->name('cotizaciones.store');
    Route::get('/cotizaciones/{id}',                 [CotizacionController::class, 'show'])->name('cotizaciones.show');
    Route::post('/cotizaciones/{id}/convertir',      [CotizacionController::class, 'convertir'])->name('cotizaciones.convertir');

    // ── IMPRESORAS ───────────────────────────────────────────────
    Route::get('/impresoras',            [ImpresoraController::class, 'index'])->name('impresoras.index');
    Route::post('/impresoras',           [ImpresoraController::class, 'store'])->name('impresoras.guardar');
    Route::put('/impresoras/{id}',       [ImpresoraController::class, 'update'])->name('impresoras.actualizar');
    Route::delete('/impresoras/{id}',    [ImpresoraController::class, 'destroy'])->name('impresoras.eliminar');
    Route::get('/impresoras/ticket/{id}',[ImpresoraController::class, 'ticket'])->name('impresoras.ticket');

    // ── CAJA ─────────────────────────────────────────────────────
    Route::get('/caja',                  [CajaController::class, 'index'])->name('caja.index');
    Route::post('/caja/abrir',           [CajaController::class, 'abrir'])->name('caja.abrir');
    Route::post('/caja/cerrar',          [CajaController::class, 'cerrar'])->name('caja.cerrar');
    Route::post('/caja/gasto',           [CajaController::class, 'registrarGasto'])->name('caja.gasto');
    Route::get('/caja/corte/{id}',       [CajaController::class, 'corte'])->name('caja.corte');
    Route::get('/caja/historial',        [CajaController::class, 'historial'])->name('caja.historial');

    // ── REPORTES ─────────────────────────────────────────────────
    Route::get('/reportes',              [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/exportar',     [ReporteController::class, 'exportCsv'])->name('reportes.exportar');
    Route::get('/reportes/hoja-conteo',  [ReporteController::class, 'hojaConteo'])->name('reportes.hoja_conteo');
    Route::get('/reportes/kardex/{id}',  [ReporteController::class, 'kardex'])->name('reportes.kardex');

    // ── ADMIN ─────────────────────────────────────────────────────
    Route::get('/usuarios',               [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios',              [UsuarioController::class, 'store'])->name('usuarios.guardar');
    Route::put('/usuarios/{id}',          [UsuarioController::class, 'update'])->name('usuarios.actualizar');
    Route::patch('/usuarios/{id}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');

    Route::get('/configuracion',          [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion',         [ConfiguracionController::class, 'update'])->name('configuracion.update');
});
