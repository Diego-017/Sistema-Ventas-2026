@extends('layouts.main')

@section('title', 'Nueva Venta')

@section('content')

<style>
/* ============================================================
   DIGITALSPOS - NUEVA VENTA
   POS MODERNO + F2 FACTURACIÓN
   ============================================================ */

:root{
    --pos-dark:#18202b;
    --pos-blue:#2563d4;
    --pos-blue-light:#eef4ff;
    --pos-green:#079455;
    --pos-orange:#e88719;
    --pos-red:#d9534f;
    --pos-border:#dfe6ef;
    --pos-bg:#f5f7fb;
    --pos-text:#1f2937;
    --pos-muted:#64748b;
}

/* ============================================================
   BASE
   ============================================================ */

.pos-page{
    background:var(--pos-bg);
    min-height:calc(100vh - 60px);
    padding:18px 22px 24px;
    color:var(--pos-text);
}

.pos-page *{
    box-sizing:border-box;
}

/* ============================================================
   CABECERA
   ============================================================ */

.pos-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:15px;
    margin-bottom:14px;
}

.pos-title{
    display:flex;
    align-items:center;
    gap:10px;
    margin:0;
    font-size:22px;
    font-weight:800;
    color:#18243a;
}

.pos-title-icon{
    font-size:22px;
}

.pos-top-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    justify-content:flex-end;
}

.pos-action{
    height:36px;
    min-width:112px;
    border:1px solid #cbd5e1;
    background:#fff;
    border-radius:6px;
    font-size:12px;
    font-weight:800;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    transition:.18s ease;
    text-decoration:none!important;
    cursor:pointer;
}

.pos-action:hover{
    transform:translateY(-1px);
    box-shadow:0 4px 10px rgba(15,23,42,.10);
}

.pos-action-f2{
    background:#eef4ff;
    border-color:#9ab8f0;
    color:#1554b3;
}

.pos-action-f8{
    background:#edfff5;
    border-color:#91d7b3;
    color:#16834c;
}

.pos-action-f6{
    background:#fff1f3;
    border-color:#efb0ba;
    color:#b33a49;
}

.pos-action-f4{
    background:#fff;
    border-color:#b9c2cf;
    color:#475569;
}

/* ============================================================
   GRID PRINCIPAL
   ============================================================ */

.pos-grid{
    display:grid;
    grid-template-columns:minmax(0,1fr) 330px;
    gap:14px;
    align-items:start;
}

.pos-left{
    min-width:0;
}

.pos-card{
    background:#fff;
    border:1px solid var(--pos-border);
    border-radius:8px;
    box-shadow:0 2px 8px rgba(15,23,42,.035);
    overflow:visible;
}

.pos-card-body{
    padding:14px;
}

.pos-search-card{
    margin-bottom:12px;
}

/* ============================================================
   BUSCADOR
   ============================================================ */

.pos-search-grid{
    display:grid;
    grid-template-columns:minmax(0,1fr) 190px;
    gap:14px;
    align-items:end;
}

.pos-label{
    display:block;
    margin-bottom:6px;
    font-size:11px;
    font-weight:800;
    color:#4b5563;
}

.pos-search-wrap{
    position:relative;
}

.pos-search-icon{
    position:absolute;
    left:12px;
    top:50%;
    transform:translateY(-50%);
    font-size:15px;
    color:#64748b;
    z-index:2;
}

#searchProducto{
    height:40px;
    border:1px solid #cbd5e1;
    border-radius:6px;
    padding-left:36px;
    font-size:12px;
    box-shadow:none;
}

#searchProducto:focus{
    border-color:#4c83e5;
    box-shadow:0 0 0 3px rgba(76,131,229,.12);
}

.pos-search-results{
    position:absolute;
    left:0;
    right:0;
    top:44px;
    background:#fff;
    border:1px solid #dbe2ea;
    border-radius:6px;
    box-shadow:0 10px 25px rgba(15,23,42,.14);
    z-index:1000;
    overflow:hidden;
}

.pos-search-results:empty{
    display:none;
}

.search-item{
    padding:10px 12px;
    border-bottom:1px solid #eef2f7;
    font-size:12px;
    cursor:pointer;
}

.search-item:last-child{
    border-bottom:0;
}

.search-item:hover{
    background:#f5f8ff;
}

.pos-select{
    height:40px!important;
    border:1px solid #cbd5e1!important;
    border-radius:6px!important;
    font-size:12px!important;
}

/* ============================================================
   TABLA
   ============================================================ */

.pos-table-card{
    margin-bottom:12px;
    overflow:hidden;
}

.pos-table-wrap{
    overflow-x:auto;
}

.table-pos{
    width:100%;
    border-collapse:collapse;
    margin:0;
    min-width:900px;
}

.table-pos thead th{
    background:var(--pos-dark);
    color:#fff;
    border:0;
    padding:9px 7px;
    font-size:10px;
    font-weight:800;
    white-space:nowrap;
}

.table-pos tbody td{
    padding:9px 7px;
    border-bottom:1px solid #edf1f5;
    vertical-align:middle;
    font-size:11px;
    color:#263244;
    background:#fff;
}

.table-pos tbody tr:hover td{
    background:#fbfdff;
}

.product-img{
    width:34px;
    height:34px;
    object-fit:cover;
    border-radius:5px;
    border:1px solid #e2e8f0;
    background:#f8fafc;
}

.product-name{
    font-weight:700;
    color:#172033;
    line-height:1.15;
}

.product-sku{
    font-size:9px;
    color:#64748b;
    margin-top:3px;
}

.product-category{
    font-size:10px;
    color:#334155;
}

.stock-value{
    font-weight:700;
    color:#475569;
}

.qty-control{
    display:inline-flex;
    height:27px;
    border:1px solid #d5dde7;
    border-radius:5px;
    overflow:hidden;
    background:#fff;
}

.qty-btn{
    width:27px;
    border:0;
    background:#f8fafc;
    color:#334155;
    font-weight:800;
    cursor:pointer;
}

.qty-btn:hover{
    background:#edf2f7;
}

.qty-input{
    width:32px;
    border:0;
    border-left:1px solid #e5eaf0;
    border-right:1px solid #e5eaf0;
    text-align:center;
    font-size:11px;
    background:#fff;
}

.price-cell{
    white-space:nowrap;
}

.discount-control{
    display:flex;
    align-items:center;
    gap:4px;
}

.discount-input{
    width:58px;
    height:26px;
    border:1px solid #d5dde7;
    border-radius:4px;
    text-align:center;
    font-size:10px;
}

.discount-type{
    height:26px;
    border:1px solid #d5dde7;
    border-radius:4px;
    font-size:10px;
    background:#fff;
}

.subtotal{
    font-weight:800;
    white-space:nowrap;
}

.action-icon{
    width:28px;
    height:28px;
    padding:0!important;
    border-radius:5px!important;
    display:inline-flex!important;
    align-items:center;
    justify-content:center;
}

.pos-table-total{
    height:40px;
    display:flex;
    justify-content:flex-end;
    align-items:center;
    padding:0 13px;
    background:#fff;
    border-top:1px solid #edf1f5;
    font-size:11px;
}

.pos-table-total strong{
    font-size:13px;
    color:#1e293b;
}

/* ============================================================
   CLIENTE + NOTAS
   ============================================================ */

.pos-bottom-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}

.pos-section-title{
    font-size:12px;
    font-weight:800;
    color:#26364e;
    margin:0;
    padding:11px 13px;
    border-bottom:1px solid #edf1f5;
}

.pos-section-body{
    padding:12px 13px;
}

.pos-form-grid{
    display:grid;
    grid-template-columns:1.15fr .85fr;
    gap:10px;
}

.pos-form-group{
    margin-bottom:9px;
}

.pos-form-group:last-child{
    margin-bottom:0;
}

.pos-form-label{
    display:block;
    font-size:10px;
    color:#64748b;
    margin-bottom:4px;
    font-weight:700;
}

.pos-input{
    height:32px!important;
    padding:5px 9px!important;
    font-size:11px!important;
    border:1px solid #d5dde7!important;
    border-radius:5px!important;
    background:#fff;
}

.pos-input:focus{
    border-color:#4c83e5!important;
    box-shadow:0 0 0 2px rgba(76,131,229,.10)!important;
}

.pos-client-extra-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.pos-client-editable{
    background:#fff!important;
}

.pos-note{
    height:88px!important;
    resize:none;
}

.pos-footer{
    display:grid;
    grid-template-columns:1fr 1fr;
    margin-top:12px;
    background:#fff;
    border:1px solid #e1e7ef;
    border-radius:6px;
    padding:10px 13px;
    font-size:10px;
    color:#64748b;
}

.pos-footer strong{
    color:#334155;
}

/* ============================================================
   RESUMEN
   ============================================================ */

.pos-right{
    min-width:0;
}

.pos-summary{
    background:#fff;
    border:1px solid #dfe6ef;
    border-radius:8px;
    box-shadow:0 3px 12px rgba(15,23,42,.06);
    overflow:hidden;
}

.pos-summary-title{
    font-size:14px;
    font-weight:800;
    color:#1e293b;
    padding:13px;
    border-bottom:1px solid #edf1f5;
}

.pos-stat-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:9px;
    padding:12px 13px 9px;
}

.pos-stat{
    min-height:61px;
    border:1px solid #d9e1ea;
    border-radius:6px;
    padding:8px 9px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.pos-stat-label{
    font-size:9px;
    font-weight:800;
    text-transform:uppercase;
    color:#64748b;
    margin-bottom:3px;
}

.pos-stat-value{
    font-size:18px;
    font-weight:800;
}

.pos-stat.green{
    border-color:#72d2a3;
    background:#f7fffb;
}

.pos-stat.green .pos-stat-value{
    color:#079455;
}

.pos-stat.orange{
    border-color:#f3bd83;
    background:#fffaf4;
}

.pos-stat.orange .pos-stat-value{
    color:#e88719;
}

.pos-stat.blue{
    border-color:#9db9f7;
    background:#f8faff;
}

.pos-stat.blue .pos-stat-value{
    color:#2463d4;
}

.pos-stat.purple{
    border-color:#c5a6f4;
    background:#fbf8ff;
}

.pos-stat.purple .pos-stat-value{
    color:#7043d7;
}

.pos-summary-body{
    padding:0 13px 13px;
}

.pos-summary-line{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
    padding:7px 0;
    border-bottom:1px dashed #dfe5ed;
    font-size:11px;
}

.pos-summary-line span:last-child{
    font-weight:700;
}

.pos-total-line{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 0 11px;
    border-top:1px solid #e5eaf0;
    margin-top:4px;
}

.pos-total-line span:first-child{
    font-size:15px;
    font-weight:800;
}

.pos-total-line span:last-child{
    font-size:20px;
    font-weight:900;
    color:#2463d4;
}

.pos-config-title{
    font-size:12px;
    font-weight:800;
    color:#26364e;
    margin:5px 0 10px;
    padding-top:10px;
    border-top:1px solid #edf1f5;
}

.pos-final-btn{
    width:100%;
    height:42px;
    border:0;
    border-radius:6px;
    background:#1b2432;
    color:#fff;
    font-size:12px;
    font-weight:800;
    cursor:pointer;
    margin-top:6px;
    transition:.18s ease;
}

.pos-final-btn:hover{
    background:#111827;
    transform:translateY(-1px);
}

/* ============================================================
   F2 - OVERLAY
   ============================================================ */

.modal-f2-fullscreen{
    position:fixed;
    inset:0;
    z-index:99999;
    display:none;
    align-items:flex-end;
    justify-content:center;
    background:rgba(10,18,32,.52);
    backdrop-filter:blur(3px);
    overflow:hidden;
}

/*
 * El panel entra DESDE ABAJO.
 */
.modal-f2-panel{
    width:100%;
    height:100vh;
    max-height:100vh;
    background:#fff;
    display:flex;
    flex-direction:column;
    transform:translateY(100%);
    opacity:0;
    box-shadow:0 -15px 50px rgba(0,0,0,.22);
}

.modal-f2-fullscreen.is-open .modal-f2-panel{
    animation:f2SlideUp .48s cubic-bezier(.18,.82,.25,1) forwards;
}

.modal-f2-fullscreen.is-closing .modal-f2-panel{
    animation:f2SlideDown .28s cubic-bezier(.55,.05,.68,.19) forwards;
}

@keyframes f2SlideUp{
    from{
        transform:translateY(100%);
        opacity:.6;
    }
    to{
        transform:translateY(0);
        opacity:1;
    }
}

@keyframes f2SlideDown{
    from{
        transform:translateY(0);
        opacity:1;
    }
    to{
        transform:translateY(100%);
        opacity:.6;
    }
}

/* ============================================================
   F2 HEADER
   ============================================================ */

.modal-f2-header{
    min-height:78px;
    padding:10px 22px;
    background:#f5f6f8;
    border-bottom:1px solid #e1e5eb;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-shrink:0;
}

.modal-f2-title{
    color:#e05d26;
    font-weight:900;
    font-size:19px;
    letter-spacing:.4px;
    margin:0;
}

.modal-f2-breadcrumb{
    color:#94a3b8;
    font-size:11px;
    margin-top:2px;
}

.top-values-bar{
    display:flex;
    align-items:center;
    gap:6px;
}

.val-box{
    min-width:105px;
    padding:7px 10px;
    border-radius:5px;
    text-align:center;
    border:1px solid #dfe5eb;
    background:#fff;
}

.val-box small{
    display:block;
    color:#94a3b8;
    font-size:8px;
    font-weight:800;
}

.val-box span{
    font-weight:900;
    font-size:16px;
}

.val-box.green{
    background:#effff7;
    border-color:#b7efd5;
}

.val-box.green span{
    color:#079455;
}

.val-box.red{
    background:#fff5f3;
    border-color:#f6c4ba;
}

.val-box.red span{
    color:#e05d26;
}

.f2-close{
    width:40px;
    height:40px;
    border:1px solid #d7dee7;
    background:#fff;
    border-radius:7px;
    color:#64748b;
    font-size:24px;
    cursor:pointer;
    line-height:1;
    transition:.15s ease;
}

.f2-close:hover{
    background:#f1f5f9;
    color:#1e293b;
}

/* ============================================================
   F2 BODY
   ============================================================ */

.modal-f2-body{
    flex:1;
    min-height:0;
    display:grid;
    grid-template-columns:minmax(420px,.95fr) minmax(480px,1.05fr);
    gap:20px;
    padding:18px 22px 22px;
    background:#f5f6f8;
    overflow:auto;
}

/* ============================================================
   F2 IZQUIERDA - PAGOS
   ============================================================ */

.f2-payment-column{
    min-width:0;
}

.f2-total-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.card-pay-cyan,
.card-pay-orange{
    min-height:108px;
    border-radius:6px;
    padding:18px 15px;
    text-align:center;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    box-shadow:0 2px 6px rgba(0,0,0,.06);
}

.card-pay-cyan{
    background:#d8f5f7;
    border:1px solid #a7e5e8;
    color:#159ca1;
}

.card-pay-cyan .f2-card-label,
.card-pay-orange .f2-card-label{
    display:block;
    font-size:10px;
    font-weight:800;
    letter-spacing:.4px;
    margin-bottom:5px;
    opacity:.8;
}

.card-pay-cyan .f2-money{
    font-size:32px;
    font-weight:900;
}

.card-pay-orange{
    background:#fff0d9;
    border:1px solid #f3ce9d;
    color:#d77c12;
}

.card-pay-orange .f2-money{
    font-size:32px;
    font-weight:900;
}

.f2-coin{
    font-size:23px;
    margin-bottom:2px;
}

/* ============================================================
   F2 CONTADO / CREDITO
   ============================================================ */

.toggle-contado-credito{
    display:flex;
    width:100%;
    margin-top:12px;
    margin-bottom:12px;
    border:1px solid #cbd5e1;
    border-radius:6px;
    overflow:hidden;
    background:#fff;
}

.btn-toggle-img2{
    flex:1;
    height:44px;
    padding:8px;
    text-align:center;
    font-weight:900;
    font-size:12px;
    background:#fff;
    color:#64748b;
    border:0;
    cursor:pointer;
    transition:.15s ease;
}

.btn-toggle-img2:first-child{
    border-right:1px solid #cbd5e1;
}

.btn-toggle-img2.active{
    background:#eef4ff;
    color:#2563d4;
    box-shadow:inset 0 -3px 0 #2563d4;
}

/* ============================================================
   F2 FORMULARIO DE PAGO
   ============================================================ */

.f2-payment-card{
    background:#fff;
    border:1px solid #dbe2ea;
    border-radius:7px;
    padding:15px;
    box-shadow:0 2px 6px rgba(0,0,0,.035);
}

.f2-payment-section{
    margin-bottom:13px;
}

.f2-payment-section:last-child{
    margin-bottom:0;
}

.f2-payment-label{
    display:block;
    font-size:11px;
    color:#596579;
    font-weight:900;
    margin-bottom:5px;
    text-transform:uppercase;
}

.f2-payment-input{
    width:100%;
    height:42px;
    border:1px solid #cbd5e1;
    border-radius:6px;
    padding:7px 11px;
    font-size:13px;
    outline:none;
}

.f2-payment-input:focus{
    border-color:#4c83e5;
    box-shadow:0 0 0 3px rgba(76,131,229,.10);
}

.f2-payment-input-small{
    height:37px;
    font-size:12px;
}

.f2-payment-row{
    display:grid;
    grid-template-columns:1fr 1.5fr;
    gap:7px;
}

/* ============================================================
   F2 DERECHA - VENTA
   ============================================================ */

.f2-sale-column{
    min-width:0;
    display:flex;
    flex-direction:column;
    min-height:0;
}

.f2-sale-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:10px;
}

.f2-sale-title{
    margin:0;
    color:#687386;
    font-size:13px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.3px;
}

.btn-finalizar-img2{
    background:#2457d6;
    color:#fff;
    border:0;
    font-weight:900;
    font-size:13px;
    padding:11px 22px;
    border-radius:5px;
    box-shadow:0 4px 10px rgba(37,87,214,.25);
    cursor:pointer;
    transition:.16s ease;
}

.btn-finalizar-img2:hover{
    background:#1946b7;
    transform:translateY(-1px);
}

.f2-sale-card{
    background:#fff;
    border:1px solid #dbe2ea;
    border-radius:7px;
    overflow:hidden;
    box-shadow:0 2px 6px rgba(0,0,0,.035);
}

.f2-sale-table-wrap{
    overflow:auto;
    max-height:calc(100vh - 180px);
}

.f2-sale-table{
    width:100%;
    border-collapse:collapse;
}

.f2-sale-table thead{
    background:#f8fafc;
}

.f2-sale-table th{
    padding:10px 12px;
    color:#687386;
    font-size:10px;
    font-weight:900;
    text-transform:uppercase;
    border-bottom:1px solid #e3e8ef;
    white-space:nowrap;
}

.f2-sale-table td{
    padding:12px;
    border-bottom:1px solid #edf1f5;
    font-size:12px;
    color:#334155;
}

.f2-sale-table tbody tr:last-child td{
    border-bottom:0;
}

.f2-sale-product{
    font-weight:800;
    color:#26364e;
}

.f2-sale-price{
    font-weight:700;
    color:#64748b;
}

.f2-delete{
    width:32px;
    height:32px;
    border:0;
    border-radius:6px;
    background:#fff0f1;
    color:#d9534f;
    cursor:pointer;
}

.f2-delete:hover{
    background:#ffe0e3;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */

@media(max-width:1100px){

    .pos-grid{
        grid-template-columns:1fr;
    }

    .pos-right{
        order:2;
    }

    .pos-left{
        order:1;
    }

    .modal-f2-body{
        grid-template-columns:1fr;
    }

    .f2-sale-table-wrap{
        max-height:none;
    }
}

@media(max-width:720px){

    .pos-page{
        padding:12px;
    }

    .pos-header{
        align-items:flex-start;
        flex-direction:column;
    }

    .pos-top-actions{
        width:100%;
        justify-content:flex-start;
    }

    .pos-action{
        flex:1;
        min-width:90px;
    }

    .pos-search-grid{
        grid-template-columns:1fr;
    }

    .pos-bottom-grid{
        grid-template-columns:1fr;
    }

    .pos-form-grid{
        grid-template-columns:1fr;
    }

    .pos-client-extra-grid{
        grid-template-columns:1fr;
    }

    .pos-footer{
        grid-template-columns:1fr;
        gap:5px;
    }

    .modal-f2-header{
        align-items:flex-start;
        flex-direction:column;
        padding:12px;
    }

    .top-values-bar{
        width:100%;
        overflow-x:auto;
    }

    .val-box{
        min-width:90px;
    }

    .modal-f2-body{
        padding:12px;
        grid-template-columns:1fr;
    }

    .f2-total-grid{
        grid-template-columns:1fr 1fr;
    }

    .f2-payment-row{
        grid-template-columns:1fr;
    }
}
</style>


<!-- ============================================================
     NUEVA VENTA
     ============================================================ -->

<div class="pos-page">

    <!-- CABECERA -->
    <div class="pos-header">

        <h2 class="pos-title">
            <span class="pos-title-icon">🛒</span>
            Nueva Venta
        </h2>

        <div class="pos-top-actions">

            <button
                type="button"
                class="pos-action pos-action-f2"
                onclick="abrirModalPago()">
                🖨️ F2 Facturar
            </button>

            <button
                type="button"
                class="pos-action pos-action-f8"
                onclick="confirmarYGuardarVenta()">
                💾 F8 Guardar
            </button>

            <a
                href="{{ route('cotizaciones.nueva') }}"
                class="pos-action pos-action-f6">
                📄 F6 Cotización
            </a>

            <a
                href="{{ route('ventas.index') }}"
                class="pos-action pos-action-f4">
                🚪 F4 Salir
            </a>

        </div>
    </div>


    @if(!$cajaAbierta)

        <div class="alert alert-warning py-2 mb-3">
            ⚠️ No hay caja abierta.

            <a
                href="{{ route('caja.index') }}"
                class="btn btn-sm btn-primary ml-2">
                Abrir Caja
            </a>
        </div>

    @endif


    <div class="pos-grid">

        <!-- ====================================================
             IZQUIERDA
             ==================================================== -->

        <main class="pos-left">

            <!-- BUSCAR PRODUCTO -->
            <section class="pos-card pos-search-card">

                <div class="pos-card-body">

                    <div class="pos-search-grid">

                        <div>

                            <label class="pos-label">
                                Buscar y agregar producto
                            </label>

                            <div class="pos-search-wrap">

                                <span class="pos-search-icon">
                                    🔍
                                </span>

                                <input
                                    type="search"
                                    id="searchProducto"
                                    class="form-control"
                                    placeholder="Buscar y agregar producto (Nombre, SKU...)"
                                    autocomplete="off"
                                    autofocus>

                                <div
                                    id="searchResults"
                                    class="pos-search-results">
                                </div>

                            </div>

                        </div>


                        <div>

                            <label class="pos-label">
                                Tipo impresión
                            </label>

                            <select
                                id="tipoImpresion"
                                class="form-control pos-select">

                                <option>DOC. VENTA</option>
                                <option>TICKET</option>
                                <option>FACTURA</option>

                            </select>

                        </div>

                    </div>

                </div>

            </section>


            <!-- TABLA PRODUCTOS -->
            <section class="pos-card pos-table-card">

                <div class="pos-table-wrap">

                    <table class="table-pos">

                        <thead>

                            <tr>

                                <th style="width:32px">#</th>

                                <th style="width:52px">
                                    Imagen
                                </th>

                                <th>
                                    Producto (SKU)
                                </th>

                                <th>
                                    Categoría
                                </th>

                                <th>
                                    Stock
                                </th>

                                <th>
                                    Cantidad
                                </th>

                                <th>
                                    Precio Unit.
                                </th>

                                <th>
                                    Descuento
                                </th>

                                <th>
                                    Impuesto
                                </th>

                                <th>
                                    Subtotal
                                </th>

                                <th class="text-center">
                                    Acciones
                                </th>

                            </tr>

                        </thead>

                        <tbody id="cartBody">

                            <tr id="emptyCart">

                                <td
                                    colspan="11"
                                    class="text-center text-muted py-4">

                                    No hay productos en el carrito.

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

                <div class="pos-table-total">

                    Total:

                    <strong
                        class="ml-2"
                        id="lblTotalTabla">
                        $0.00
                    </strong>

                </div>

            </section>


            <!-- CLIENTE + NOTAS -->
            <div class="pos-bottom-grid">

                <!-- DATOS CLIENTE -->
                <section class="pos-card">

                    <h6 class="pos-section-title">
                        👤 Datos del Cliente
                    </h6>

                    <div class="pos-section-body">

                        <div class="pos-form-grid">

                            <div class="pos-form-group">

                                <label class="pos-form-label">
                                    Cliente
                                </label>

                                <select
                                    id="clienteSelect"
                                    class="form-control pos-input">

                                    <option value="">
                                        CLIENTES VARIOS
                                    </option>

                                    @foreach($clientes as $c)

                                        <option
                                            value="{{ $c->id }}"
                                            data-nombre="{{ e($c->nombre ?? '') }}"
                                            data-documento="{{ e($c->documento ?? $c->nit ?? $c->dui ?? '') }}"
                                            data-nrc="{{ e($c->nrc ?? '') }}"
                                            data-direccion="{{ e($c->direccion ?? '') }}"
                                            data-telefono="{{ e($c->telefono ?? '') }}">

                                            {{ $c->nombre }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="pos-form-group">

                                <label class="pos-form-label">
                                    Documento / DUI / NIT
                                </label>

                                <input
                                    type="text"
                                    id="documentoCliente"
                                    class="form-control pos-input pos-client-editable"
                                    placeholder="Documento del cliente">

                            </div>

                        </div>


                        <div class="pos-client-extra-grid">

                            <div class="pos-form-group">

                                <label class="pos-form-label">
                                    NRC
                                </label>

                                <input
                                    type="text"
                                    id="nrcCliente"
                                    class="form-control pos-input pos-client-editable"
                                    placeholder="NRC">

                            </div>


                            <div class="pos-form-group">

                                <label class="pos-form-label">
                                    Teléfono
                                </label>

                                <input
                                    type="text"
                                    id="telefonoCliente"
                                    class="form-control pos-input pos-client-editable"
                                    placeholder="0000-0000">

                            </div>

                        </div>


                        <div class="pos-form-group">

                            <label class="pos-form-label">
                                Dirección
                            </label>

                            <input
                                type="text"
                                id="direccionCliente"
                                class="form-control pos-input pos-client-editable"
                                placeholder="Dirección del cliente">

                        </div>

                    </div>

                </section>


                <!-- NOTAS -->
                <section class="pos-card">

                    <h6 class="pos-section-title">

                        📝 Observaciones / Notas

                        <span class="text-muted">
                            (Opcionales)
                        </span>

                    </h6>

                    <div class="pos-section-body">

                        <textarea
                            id="notasInput"
                            class="form-control pos-input pos-note"
                            placeholder="Agregar notas u observaciones de la venta..."></textarea>

                    </div>

                </section>

            </div>


            <!-- FOOTER -->
            <div class="pos-footer">

                <div>

                    Productos:

                    <strong id="cantProductos">
                        0
                    </strong>

                    &nbsp;&nbsp;

                    Cantidad total:

                    <strong id="cantTotalUnidades">
                        0
                    </strong>

                </div>


                <div class="text-right">

                    Percepción (0%):
                    <strong>$0.00</strong>

                    &nbsp;&nbsp;&nbsp;

                    Retención (0%):
                    <strong>$0.00</strong>

                </div>

            </div>

        </main>


        <!-- ====================================================
             RESUMEN DERECHO
             ==================================================== -->

        <aside class="pos-right">

            <div class="pos-summary">

                <div class="pos-summary-title">
                    🧾 Resumen de Pago
                </div>


                <div class="pos-stat-grid">

                    <div class="pos-stat green">

                        <span class="pos-stat-label">
                            Gravado
                        </span>

                        <span
                            class="pos-stat-value"
                            id="lblGravado">
                            $0.00
                        </span>

                    </div>


                    <div class="pos-stat orange">

                        <span class="pos-stat-label">
                            IVA (13%)
                        </span>

                        <span
                            class="pos-stat-value"
                            id="lblIva">
                            $0.00
                        </span>

                    </div>


                    <div class="pos-stat blue">

                        <span class="pos-stat-label">
                            Venta Exenta
                        </span>

                        <span class="pos-stat-value">
                            $0.00
                        </span>

                    </div>


                    <div class="pos-stat purple">

                        <span class="pos-stat-label">
                            Total
                        </span>

                        <span
                            class="pos-stat-value"
                            id="lblTotal">
                            $0.00
                        </span>

                    </div>

                </div>


                <div class="pos-summary-body">

                    <div class="pos-summary-line">

                        <span>
                            Gravado
                        </span>

                        <span id="summaryGravado">
                            $0.00
                        </span>

                    </div>


                    <div class="pos-summary-line">

                        <span>
                            Venta Exenta
                        </span>

                        <span>
                            $0.00
                        </span>

                    </div>


                    <div class="pos-summary-line">

                        <span>
                            IVA (13%)
                        </span>

                        <span id="summaryIva">
                            $0.00
                        </span>

                    </div>


                    <div class="pos-summary-line">

                        <span>
                            Descuento Global ($)
                        </span>

                        <input
                            id="descuentoGlobal"
                            type="number"
                            class="form-control pos-input text-right"
                            value="0"
                            min="0"
                            step="0.01"
                            style="width:100px">

                    </div>


                    <div class="pos-summary-line">

                        <span>
                            Retención ($)
                        </span>

                        <input
                            id="retencionGlobal"
                            type="number"
                            class="form-control pos-input text-right"
                            value="0"
                            min="0"
                            step="0.01"
                            style="width:100px">

                    </div>


                    <div class="pos-total-line">

                        <span>
                            Total:
                        </span>

                        <span id="summaryTotal">
                            $0.00
                        </span>

                    </div>


                    <!-- CONFIGURACIÓN PDF -->
                    <div class="pos-config-title">
                        Configuración de Impresión / PDF
                    </div>


                    <div class="pos-form-group">

                        <label class="pos-form-label">
                            Nombre / Razón Social del Cliente:
                        </label>

                        <input
                            id="razonSocial"
                            type="text"
                            class="form-control pos-input"
                            placeholder="Nombre o razón social del cliente...">

                    </div>


                    <div class="pos-form-group">

                        <label class="pos-form-label">
                            Tipo de Documento:
                        </label>

                        <select
                            id="tipoDocumento"
                            class="form-control pos-input">

                            <option>
                                FACTURA DE CONSUMO FINAL
                            </option>

                            <option>
                                CREDITO FISCAL
                            </option>

                        </select>

                    </div>


                    <div class="pos-form-group">

                        <label class="pos-form-label">
                            Forma de Pago en Ticket:
                        </label>

                        <select
                            id="formaPagoTicket"
                            class="form-control pos-input">

                            <option>
                                EFECTIVO
                            </option>

                            <option>
                                TARJETA
                            </option>

                            <option>
                                TRANSFERENCIA
                            </option>

                        </select>

                    </div>


                    <button
                        type="button"
                        class="pos-final-btn"
                        onclick="abrirModalPago()">

                        Emitir Documento y Finalizar [F10]

                    </button>

                </div>

            </div>

        </aside>

    </div>

</div>


<!-- ============================================================
     FORMULARIO REAL DE VENTA
     ============================================================ -->

<form
    id="ventaForm"
    method="POST"
    action="{{ route('ventas.guardar') }}">

    @csrf

    <input
        type="hidden"
        name="items"
        id="itemsInput">

    <input
        type="hidden"
        name="subtotal"
        id="subtotalInput">

    <input
        type="hidden"
        name="descuento"
        id="descuentoInput"
        value="0">

    <input
        type="hidden"
        name="total"
        id="totalInput">

    <input
        type="hidden"
        name="tipo_venta"
        id="tipoVentaInput"
        value="contado">

    <input
        type="hidden"
        name="metodo_pago"
        id="metodoPagoInput"
        value="efectivo">

    <input
        type="hidden"
        name="efectivo_recibido"
        id="efectivoRecibidoInput"
        value="0">

    <input
        type="hidden"
        name="cliente_id"
        id="clienteIdHidden">

    <input
        type="hidden"
        name="razon_social_cliente"
        id="razonSocialHidden">

    <input
        type="hidden"
        name="documento_cliente"
        id="documentoClienteHidden">

    <input
        type="hidden"
        name="nrc_cliente"
        id="nrcClienteHidden">

    <input
        type="hidden"
        name="direccion_cliente"
        id="direccionClienteHidden">

    <input
        type="hidden"
        name="telefono_cliente"
        id="telefonoClienteHidden">

    <input
        type="hidden"
        name="tipo_documento"
        id="tipoDocumentoHidden">

    <input
        type="hidden"
        name="forma_pago_ticket"
        id="formaPagoTicketHidden">

    <input
        type="hidden"
        name="notas"
        id="notasHidden">

</form>


<!-- ============================================================
     F2 FACTURAR
     ============================================================ -->

<div
    id="modalPagoF2"
    class="modal-f2-fullscreen">

    <div class="modal-f2-panel">

        <!-- HEADER F2 -->

        <div class="modal-f2-header">

            <div>

                <h4 class="modal-f2-title">
                    VENTAS
                </h4>

                <div class="modal-f2-breadcrumb">
                    Dashboard &gt; Ventas
                </div>

            </div>


            <div class="top-values-bar">

                <div class="val-box green">

                    <small>
                        GRAVADO $
                    </small>

                    <span id="mGravado">
                        0.00
                    </span>

                </div>


                <div class="val-box red">

                    <small>
                        IVA $
                    </small>

                    <span id="mIva">
                        0.00
                    </span>

                </div>


                <div class="val-box green">

                    <small>
                        SUBTOTAL $
                    </small>

                    <span id="mSubtotal">
                        0.00
                    </span>

                </div>


                <div class="val-box red">

                    <small>
                        RETENCIÓN $
                    </small>

                    <span id="mRetencion">
                        0.00
                    </span>

                </div>


                <div class="val-box red">

                    <small>
                        A PAGAR $
                    </small>

                    <span id="mAPagar">
                        0.00
                    </span>

                </div>


                <button
                    type="button"
                    class="f2-close"
                    onclick="cerrarModalPago()">

                    &times;

                </button>

            </div>

        </div>


        <!-- BODY F2 -->

        <div class="modal-f2-body">


            <!-- ================================================
                 COLUMNA PAGOS
                 ================================================ -->

            <div class="f2-payment-column">


                <div class="f2-total-grid">

                    <div class="card-pay-cyan">

                        <span class="f2-card-label">
                            A PAGAR
                        </span>

                        <span
                            class="f2-money"
                            id="cAPagar">
                            $ 0.00
                        </span>

                    </div>


                    <div class="card-pay-orange">

                        <span class="f2-coin">
                            🪙
                        </span>

                        <span class="f2-card-label">
                            CAMBIO
                        </span>

                        <span
                            class="f2-money"
                            id="cCambio">
                            0.00
                        </span>

                    </div>

                </div>


                <!-- CONTADO / CREDITO -->

                <div class="toggle-contado-credito">

                    <button
                        type="button"
                        id="bContado"
                        class="btn-toggle-img2 active"
                        onclick="setTipoModal('contado')">

                        CONTADO

                    </button>


                    <button
                        type="button"
                        id="bCredito"
                        class="btn-toggle-img2"
                        onclick="setTipoModal('credito')">

                        CREDITO

                    </button>

                </div>


                <!-- PAGOS -->

                <div class="f2-payment-card">


                    <!-- EFECTIVO -->

                    <div class="f2-payment-section">

                        <label class="f2-payment-label">
                            EFECTIVO
                        </label>

                        <input
                            type="number"
                            id="mEfectivo"
                            class="f2-payment-input"
                            placeholder="Monto Efectivo"
                            step="0.01"
                            min="0"
                            oninput="calcCambioImg2()">

                    </div>


                    <!-- TARJETA -->

                    <div class="f2-payment-section">

                        <label class="f2-payment-label">
                            TARJETA
                        </label>

                        <div class="f2-payment-row">

                            <input
                                type="number"
                                id="mTarjeta"
                                class="f2-payment-input f2-payment-input-small"
                                placeholder="Monto Tarjeta"
                                min="0"
                                step="0.01">

                            <input
                                type="text"
                                id="mTarjetaAutorizacion"
                                class="f2-payment-input f2-payment-input-small"
                                placeholder="Número de transacción o autorización">

                        </div>

                    </div>


                    <!-- TRANSFERENCIA -->

                    <div class="f2-payment-section">

                        <label class="f2-payment-label">
                            TRANSFERENCIA
                        </label>

                        <div class="f2-payment-row">

                            <input
                                type="number"
                                id="mTransferencia"
                                class="f2-payment-input f2-payment-input-small"
                                placeholder="Monto Transferencia"
                                min="0"
                                step="0.01">

                            <input
                                type="text"
                                id="mTransferenciaAutorizacion"
                                class="f2-payment-input f2-payment-input-small"
                                placeholder="Número de transacción o autorización">

                        </div>

                    </div>


                </div>

            </div>


            <!-- ================================================
                 COLUMNA VENTA
                 ================================================ -->

            <div class="f2-sale-column">

                <div class="f2-sale-header">

                    <h6 class="f2-sale-title">
                        Venta
                    </h6>

                    <button
                        type="button"
                        class="btn-finalizar-img2"
                        onclick="confirmarYGuardarVenta()">

                        FINALIZAR ✓

                    </button>

                </div>


                <div class="f2-sale-card">

                    <div class="f2-sale-table-wrap">

                        <table class="f2-sale-table">

                            <thead>

                                <tr>

                                    <th>
                                        DESCRIPCIÓN
                                    </th>

                                    <th class="text-center">
                                        CANTIDAD
                                    </th>

                                    <th class="text-right">
                                        SUBTOTAL
                                    </th>

                                    <th class="text-center">
                                        ACCIÓN
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="modalBodyImg2">

                                <tr>

                                    <td
                                        colspan="4"
                                        class="text-center text-muted py-3">

                                        Sin productos

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


@push('scripts')

<script>

/* ============================================================
   CARRITO
   ============================================================ */

const cart = {};

let timer;


/* ============================================================
   TECLAS RÁPIDAS
   ============================================================ */

document.addEventListener('keydown', function(e){

    if(e.key === 'F2'){

        e.preventDefault();

        abrirModalPago();

    }

    else if(e.key === 'F8'){

        e.preventDefault();

        confirmarYGuardarVenta();

    }

    else if(e.key === 'F10'){

        e.preventDefault();

        confirmarYGuardarVenta();

    }

    else if(e.key === 'Escape'){

        cerrarModalPago();

    }

});


/* ============================================================
   CLIENTE
   ============================================================ */

document
    .getElementById('clienteSelect')
    .addEventListener('change', function(){

        const option =
            this.options[this.selectedIndex];

        const get = name =>
            option.dataset[name] || '';


        document.getElementById('razonSocial').value =
            get('nombre');

        document.getElementById('documentoCliente').value =
            get('documento');

        document.getElementById('nrcCliente').value =
            get('nrc');

        document.getElementById('direccionCliente').value =
            get('direccion');

        document.getElementById('telefonoCliente').value =
            get('telefono');

    });


/* ============================================================
   BUSCAR PRODUCTOS
   ============================================================ */

document
    .getElementById('searchProducto')
    .addEventListener('input', function(){

        clearTimeout(timer);

        timer = setTimeout(
            () => doSearch(this.value.trim()),
            300
        );

    });


async function doSearch(term){

    const box =
        document.getElementById('searchResults');


    if(term.length < 2){

        box.innerHTML = '';

        return;

    }


    try{

        const res = await fetch(
            `{{ route('productos.buscar') }}?q=${encodeURIComponent(term)}`
        );

        const list = await res.json();


        if(!list.length){

            box.innerHTML =
                '<div class="search-item text-muted">Sin resultados</div>';

            return;

        }


        box.innerHTML = list.map(p => `

            <div
                class="search-item"
                onclick='addToCart(${JSON.stringify(p)})'>

                <strong>
                    ${escapeHtml(p.nombre)}
                </strong>

                <span
                    style="
                        float:right;
                        font-weight:800;
                        color:#2563d4;
                    ">

                    $${parseFloat(p.precio_venta).toFixed(2)}

                </span>

                <br>

                <small class="text-muted">

                    SKU:
                    ${escapeHtml(p.sku || '—')}

                    |

                    Stock:
                    ${p.stock}

                </small>

            </div>

        `).join('');

    }
    catch(err){

        box.innerHTML =
            '<div class="search-item text-danger">No fue posible buscar productos.</div>';

    }

}


/* ============================================================
   SEGURIDAD HTML
   ============================================================ */

function escapeHtml(value){

    return String(value).replace(
        /[&<>'"]/g,
        c => ({
            '&':'&amp;',
            '<':'&lt;',
            '>':'&gt;',
            "'":'&#39;',
            '"':'&quot;'
        }[c])
    );

}


/* ============================================================
   AGREGAR PRODUCTO
   ============================================================ */

function addToCart(p){

    document.getElementById('searchResults').innerHTML = '';

    document.getElementById('searchProducto').value = '';

    document.getElementById('searchProducto').focus();


    if(p.stock <= 0){

        alert('Sin stock disponible.');

        return;

    }


    if(cart[p.id]){

        if(cart[p.id].cantidad >= p.stock){

            alert(
                'Stock insuficiente. Máximo disponible: ' +
                p.stock
            );

            return;

        }

        cart[p.id].cantidad++;

    }
    else{

        cart[p.id] = {
            ...p,
            cantidad:1
        };

    }


    renderCart();

}


/* ============================================================
   ELIMINAR PRODUCTO
   ============================================================ */

function removeFromCart(id){

    delete cart[id];

    renderCart();

}


/* ============================================================
   CANTIDAD
   ============================================================ */

function updateQty(id,val){

    cart[id].cantidad =
        Math.min(
            Math.max(
                1,
                parseInt(val) || 1
            ),
            cart[id].stock
        );

    renderCart();

}


/* ============================================================
   RENDER CARRITO
   ============================================================ */

function renderCart(){

    const ids = Object.keys(cart);

    const tbody =
        document.getElementById('cartBody');

    const modalTbody =
        document.getElementById('modalBodyImg2');


    if(!ids.length){

        tbody.innerHTML = `

            <tr id="emptyCart">

                <td
                    colspan="11"
                    class="text-center text-muted py-4">

                    No hay productos en el carrito.

                </td>

            </tr>

        `;


        modalTbody.innerHTML = `

            <tr>

                <td
                    colspan="4"
                    class="text-center text-muted py-3">

                    Sin productos

                </td>

            </tr>

        `;


        document.getElementById(
            'cantProductos'
        ).textContent = '0';


        document.getElementById(
            'cantTotalUnidades'
        ).textContent = '0';


        recalc(0);

        return;

    }


    let totalUnits = 0;

    let idx = 1;


    /* TABLA PRINCIPAL */

    tbody.innerHTML = ids.map(id => {

        const i = cart[id];

        totalUnits += i.cantidad;


        const img =
            i.imagen
                ? '/storage/' + i.imagen
                : 'https://via.placeholder.com/40';


        const subtotal =
            parseFloat(i.precio_venta) *
            i.cantidad;


        return `

            <tr>

                <td>
                    ${idx++}
                </td>


                <td>

                    <img
                        src="${img}"
                        class="product-img"
                        alt="">

                </td>


                <td>

                    <div class="product-name">
                        ${escapeHtml(i.nombre)}
                    </div>

                    <div class="product-sku">
                        ${escapeHtml(i.sku || 'SKU-N/A')}
                    </div>

                </td>


                <td class="product-category">

                    ${escapeHtml(
                        i.categoria
                            ? i.categoria.nombre
                            : 'General'
                    )}

                </td>


                <td class="stock-value">
                    ${i.stock}
                </td>


                <td>

                    <div class="qty-control">

                        <button
                            type="button"
                            class="qty-btn"
                            onclick="updateQty(
                                ${id},
                                ${i.cantidad - 1}
                            )">

                            −

                        </button>


                        <input
                            type="text"
                            class="qty-input"
                            value="${i.cantidad}"
                            readonly>


                        <button
                            type="button"
                            class="qty-btn"
                            onclick="updateQty(
                                ${id},
                                ${i.cantidad + 1}
                            )">

                            +

                        </button>

                    </div>

                </td>


                <td class="price-cell">

                    $${parseFloat(
                        i.precio_venta
                    ).toFixed(2)}

                </td>


                <td>

                    <div class="discount-control">

                        <input
                            type="number"
                            class="discount-input"
                            value="0"
                            min="0"
                            step="0.01">

                        <select
                            class="discount-type">

                            <option>%</option>
                            <option>$</option>

                        </select>

                    </div>

                </td>


                <td>
                    13%
                </td>


                <td class="subtotal">

                    $${subtotal.toFixed(2)}

                </td>


                <td class="text-center">

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger action-icon"
                        onclick="removeFromCart(${id})">

                        🗑

                    </button>

                </td>

            </tr>

        `;

    }).join('');


    /* TABLA F2 */

    modalTbody.innerHTML = ids.map(id => {

        const i = cart[id];

        const subtotal =
            parseFloat(i.precio_venta) *
            i.cantidad;


        return `

            <tr>

                <td>

                    <div class="f2-sale-product">

                        ${escapeHtml(i.nombre)}

                    </div>

                    <small class="text-muted">

                        ${escapeHtml(i.sku || '')}

                    </small>

                </td>


                <td class="text-center">

                    ${i.cantidad}

                </td>


                <td class="text-right f2-sale-price">

                    $${subtotal.toFixed(2)}

                </td>


                <td class="text-center">

                    <button
                        type="button"
                        class="f2-delete"
                        onclick="removeFromCart(${id})">

                        🗑

                    </button>

                </td>

            </tr>

        `;

    }).join('');


    document.getElementById(
        'cantProductos'
    ).textContent = ids.length;


    document.getElementById(
        'cantTotalUnidades'
    ).textContent = totalUnits;


    const subtotalTotal =
        ids.reduce(
            (sum,id) =>
                sum +
                (
                    parseFloat(
                        cart[id].precio_venta
                    ) || 0
                ) *
                cart[id].cantidad,
            0
        );


    recalc(subtotalTotal);

}


/* ============================================================
   RECALCULAR
   ============================================================ */

function recalc(sub){

    const descuento =
        parseFloat(
            document.getElementById(
                'descuentoGlobal'
            ).value
        ) || 0;


    const retencion =
        parseFloat(
            document.getElementById(
                'retencionGlobal'
            ).value
        ) || 0;


    const base =
        Math.max(
            0,
            sub - descuento
        );


    const iva =
        base * 0.13;


    const gravado =
        base - iva;


    const total =
        Math.max(
            0,
            base - retencion
        );


    const money =
        value =>
            '$' + Number(value).toFixed(2);


    /* RESUMEN PRINCIPAL */

    document.getElementById(
        'lblGravado'
    ).textContent =
        money(gravado);


    document.getElementById(
        'lblIva'
    ).textContent =
        money(iva);


    document.getElementById(
        'lblTotal'
    ).textContent =
        money(total);


    document.getElementById(
        'lblTotalTabla'
    ).textContent =
        money(sub);


    document.getElementById(
        'summaryGravado'
    ).textContent =
        money(gravado);


    document.getElementById(
        'summaryIva'
    ).textContent =
        money(iva);


    document.getElementById(
        'summaryTotal'
    ).textContent =
        money(total);


    /* F2 */

    document.getElementById(
        'mGravado'
    ).textContent =
        gravado.toFixed(2);


    document.getElementById(
        'mIva'
    ).textContent =
        iva.toFixed(2);


    document.getElementById(
        'mSubtotal'
    ).textContent =
        base.toFixed(2);


    document.getElementById(
        'mRetencion'
    ).textContent =
        retencion.toFixed(2);


    document.getElementById(
        'mAPagar'
    ).textContent =
        total.toFixed(2);


    document.getElementById(
        'cAPagar'
    ).textContent =
        '$ ' + total.toFixed(2);


    document.getElementById(
        'descuentoInput'
    ).value =
        descuento.toFixed(2);


    calcCambioImg2();

}


/* ============================================================
   DESCUENTO / RETENCION
   ============================================================ */

document
    .getElementById('descuentoGlobal')
    .addEventListener(
        'input',
        () => {

            const ids =
                Object.keys(cart);


            const subtotal =
                ids.reduce(
                    (sum,id) =>
                        sum +
                        (
                            parseFloat(
                                cart[id].precio_venta
                            ) || 0
                        ) *
                        cart[id].cantidad,
                    0
                );


            recalc(subtotal);

        }
    );


document
    .getElementById('retencionGlobal')
    .addEventListener(
        'input',
        () => {

            const ids =
                Object.keys(cart);


            const subtotal =
                ids.reduce(
                    (sum,id) =>
                        sum +
                        (
                            parseFloat(
                                cart[id].precio_venta
                            ) || 0
                        ) *
                        cart[id].cantidad,
                    0
                );


            recalc(subtotal);

        }
    );


/* ============================================================
   ABRIR F2
   ============================================================ */

function abrirModalPago(){

    if(!Object.keys(cart).length){

        alert(
            'Carrito vacío. Agrega al menos un producto.'
        );

        return;

    }


    const modal =
        document.getElementById(
            'modalPagoF2'
        );


    modal.classList.remove(
        'is-closing'
    );


    modal.style.display = 'flex';


    /*
     * Fuerza el navegador a reconocer
     * el estado inicial antes de animar.
     */

    void modal.offsetWidth;


    requestAnimationFrame(() => {

        modal.classList.add(
            'is-open'
        );

    });


    setTimeout(() => {

        const efectivo =
            document.getElementById(
                'mEfectivo'
            );

        if(
            document
                .getElementById(
                    'tipoVentaInput'
                )
                .value === 'contado'
        ){

            efectivo.focus();

        }

    },500);

}


/* ============================================================
   CERRAR F2
   ============================================================ */

function cerrarModalPago(){

    const modal =
        document.getElementById(
            'modalPagoF2'
        );


    if(
        modal.style.display === 'none' ||
        !modal.style.display
    ){

        return;

    }


    modal.classList.remove(
        'is-open'
    );


    modal.classList.add(
        'is-closing'
    );


    setTimeout(() => {

        modal.style.display = 'none';

        modal.classList.remove(
            'is-closing'
        );

    },300);

}


/* ============================================================
   CONTADO / CREDITO
   ============================================================ */

function setTipoModal(tipo){

    document.getElementById(
        'tipoVentaInput'
    ).value = tipo;


    document
        .getElementById('bContado')
        .classList.toggle(
            'active',
            tipo === 'contado'
        );


    document
        .getElementById('bCredito')
        .classList.toggle(
            'active',
            tipo === 'credito'
        );


    if(tipo === 'credito'){

        document.getElementById(
            'mEfectivo'
        ).value = '0';

        document.getElementById(
            'cCambio'
        ).textContent = '0.00';

    }
    else{

        document
            .getElementById(
                'mEfectivo'
            )
            .focus();

    }

}


/* ============================================================
   CAMBIO
   ============================================================ */

function calcCambioImg2(){

    const total =
        parseFloat(
            document.getElementById(
                'mAPagar'
            ).textContent
        ) || 0;


    const efectivo =
        parseFloat(
            document.getElementById(
                'mEfectivo'
            ).value
        ) || 0;


    const cambio =
        Math.max(
            0,
            efectivo - total
        );


    document.getElementById(
        'cCambio'
    ).textContent =
        cambio.toFixed(2);


    document.getElementById(
        'efectivoRecibidoInput'
    ).value =
        efectivo.toFixed(2);

}


/* ============================================================
   SINCRONIZAR DATOS DEL CLIENTE
   ============================================================ */

function sincronizarDatosCliente(){

    /*
     * IMPORTANTE:
     * Estos valores son los que realmente se mandan
     * al controlador y posteriormente al PDF.
     */

    document.getElementById(
        'clienteIdHidden'
    ).value =
        document.getElementById(
            'clienteSelect'
        ).value;


    document.getElementById(
        'razonSocialHidden'
    ).value =
        document.getElementById(
            'razonSocial'
        ).value.trim();


    document.getElementById(
        'documentoClienteHidden'
    ).value =
        document.getElementById(
            'documentoCliente'
        ).value.trim();


    document.getElementById(
        'nrcClienteHidden'
    ).value =
        document.getElementById(
            'nrcCliente'
        ).value.trim();


    document.getElementById(
        'direccionClienteHidden'
    ).value =
        document.getElementById(
            'direccionCliente'
        ).value.trim();


    document.getElementById(
        'telefonoClienteHidden'
    ).value =
        document.getElementById(
            'telefonoCliente'
        ).value.trim();


    document.getElementById(
        'tipoDocumentoHidden'
    ).value =
        document.getElementById(
            'tipoDocumento'
        ).value;


    document.getElementById(
        'formaPagoTicketHidden'
    ).value =
        document.getElementById(
            'formaPagoTicket'
        ).value;


    document.getElementById(
        'notasHidden'
    ).value =
        document.getElementById(
            'notasInput'
        ).value.trim();

}


/* ============================================================
   FINALIZAR / GUARDAR
   ============================================================ */

function confirmarYGuardarVenta(){

    const ids =
        Object.keys(cart);


    if(!ids.length){

        alert(
            'El carrito está vacío.'
        );

        return;

    }


    /*
     * Si estamos en contado,
     * verificamos el pago.
     */

    const tipoVenta =
        document.getElementById(
            'tipoVentaInput'
        ).value;


    const total =
        parseFloat(
            document.getElementById(
                'mAPagar'
            ).textContent
        ) || 0;


    const efectivo =
        parseFloat(
            document.getElementById(
                'mEfectivo'
            ).value
        ) || 0;


    /*
     * No bloqueamos crédito.
     */

    if(
        tipoVenta === 'contado' &&
        efectivo > 0 &&
        efectivo < total
    ){

        const tarjeta =
            parseFloat(
                document.getElementById(
                    'mTarjeta'
                ).value
            ) || 0;


        const transferencia =
            parseFloat(
                document.getElementById(
                    'mTransferencia'
                ).value
            ) || 0;


        const pagado =
            efectivo +
            tarjeta +
            transferencia;


        if(pagado < total){

            alert(
                'El monto recibido es menor que el total de la venta.'
            );

            return;

        }

    }


    /* ITEMS */

    const items =
        ids.map(id => ({

            id:cart[id].id,

            cantidad:cart[id].cantidad,

            precio:cart[id].precio_venta

        }));


    document.getElementById(
        'itemsInput'
    ).value =
        JSON.stringify(items);


    /* SUBTOTAL */

    document.getElementById(
        'subtotalInput'
    ).value =
        document
            .getElementById(
                'lblTotalTabla'
            )
            .textContent
            .replace('$','')
            .trim();


    /* TOTAL */

    document.getElementById(
        'totalInput'
    ).value =
        document
            .getElementById(
                'summaryTotal'
            )
            .textContent
            .replace('$','')
            .trim();


    /* CLIENTE */

    sincronizarDatosCliente();


    /* MÉTODO DE PAGO */

    const formaPago =
        document.getElementById(
            'formaPagoTicket'
        ).value;


    document.getElementById(
        'metodoPagoInput'
    ).value =
        formaPago.toLowerCase();


    /*
     * Evitamos doble envío.
     */

    const form =
        document.getElementById(
            'ventaForm'
        );


    if(form.dataset.enviando === '1'){

        return;

    }


    form.dataset.enviando = '1';


    /*
     * Deshabilitar botones para
     * evitar facturación duplicada.
     */

    document
        .querySelectorAll(
            '.btn-finalizar-img2, .pos-final-btn'
        )
        .forEach(btn => {

            btn.disabled = true;

            btn.style.opacity = '.65';

        });


    form.submit();

}


/* ============================================================
   CERRAR F2 AL HACER CLICK FUERA
   ============================================================ */

document
    .getElementById('modalPagoF2')
    .addEventListener(
        'click',
        function(e){

            if(
                e.target === this
            ){

                cerrarModalPago();

            }

        }
    );


/* ============================================================
   INICIALIZAR
   ============================================================ */

document.addEventListener(
    'DOMContentLoaded',
    function(){

        /*
         * Estado inicial.
         */

        recalc(0);

    }
);

</script>

@endpush

@endsection