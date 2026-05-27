<?php

use App\Http\Controllers\Admin\CashController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComandaController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\OrderTrackingController;
use App\Http\Controllers\Store\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Loja Pública
|--------------------------------------------------------------------------
*/
Route::prefix('loja')->name('store.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    Route::get('/produto/{product:slug}', [StoreController::class, 'product'])->name('product');

    // Carrinho
    Route::get('/carrinho', [CartController::class, 'index'])->name('cart');
    Route::post('/carrinho/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/carrinho/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/carrinho/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/carrinho/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Rastreamento
    Route::get('/pedido/{code}', [OrderTrackingController::class, 'show'])->name('tracking');
});

// Redirecionar raiz para a loja
Route::get('/', fn() => redirect()->route('store.index'));

/*
|--------------------------------------------------------------------------
| Admin — Autenticado
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produtos
    Route::resource('produtos', ProductController::class)->names([
        'index'   => 'products.index',
        'create'  => 'products.create',
        'store'   => 'products.store',
        'edit'    => 'products.edit',
        'update'  => 'products.update',
        'destroy' => 'products.destroy',
    ])->parameters(['produtos' => 'product']);
    Route::patch('/produtos/{product}/disponibilidade', [ProductController::class, 'toggleAvailable'])->name('products.toggle');

    // Categorias
    Route::resource('categorias', CategoryController::class)->names([
        'index'   => 'categories.index',
        'create'  => 'categories.create',
        'store'   => 'categories.store',
        'edit'    => 'categories.edit',
        'update'  => 'categories.update',
        'destroy' => 'categories.destroy',
    ])->parameters(['categorias' => 'category']);

    // Pedidos
    Route::prefix('pedidos')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{order}', [OrderController::class, 'cancel'])->name('cancel');
    });

    // Mesas
    Route::prefix('mesas')->name('tables.')->group(function () {
        Route::get('/', [TableController::class, 'index'])->name('index');
        Route::post('/', [TableController::class, 'store'])->name('store');
        Route::patch('/{table}', [TableController::class, 'update'])->name('update');
        Route::patch('/{table}/status', [TableController::class, 'updateStatus'])->name('status');
        Route::delete('/{table}', [TableController::class, 'destroy'])->name('destroy');
    });

    // Comandas
    Route::prefix('comandas')->name('comandas.')->group(function () {
        Route::get('/', [ComandaController::class, 'index'])->name('index');
        Route::post('/', [ComandaController::class, 'store'])->name('store');
        Route::get('/{comanda}', [ComandaController::class, 'show'])->name('show');
        Route::post('/{comanda}/item', [ComandaController::class, 'addItem'])->name('add-item');
        Route::delete('/{comanda}/item/{item}', [ComandaController::class, 'removeItem'])->name('remove-item');
        Route::patch('/{comanda}/fechar', [ComandaController::class, 'close'])->name('close');
        Route::delete('/{comanda}', [ComandaController::class, 'cancel'])->name('cancel');
    });

    // Caixa
    Route::prefix('caixa')->name('cash.')->group(function () {
        Route::get('/', [CashController::class, 'index'])->name('index');
        Route::post('/abrir', [CashController::class, 'openSession'])->name('open');
        Route::patch('/fechar', [CashController::class, 'closeSession'])->name('close');
    });

    // Cozinha
    Route::prefix('cozinha')->name('kitchen.')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('index');
        Route::patch('/pedido/{order}/status', [KitchenController::class, 'updateOrderStatus'])->name('order-status');
        Route::patch('/item/{item}/status', [KitchenController::class, 'updateItemStatus'])->name('item-status');
    });

    // Delivery
    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('index');
        Route::patch('/{order}/status', [DeliveryController::class, 'updateStatus'])->name('status');
    });

    // Clientes
    Route::prefix('clientes')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::patch('/{customer}', [CustomerController::class, 'update'])->name('update');
    });

    // Estoque
    Route::prefix('estoque')->name('stock.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::post('/', [StockController::class, 'store'])->name('store');
        Route::post('/{item}/movimento', [StockController::class, 'movement'])->name('movement');
    });

    // Relatórios
    Route::get('/relatorios', [ReportController::class, 'index'])->name('reports.index');

    // Configurações
    Route::get('/configuracoes', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/configuracoes', [SettingController::class, 'update'])->name('settings.update');
});
