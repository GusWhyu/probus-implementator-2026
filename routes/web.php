<?php

use App\Http\Controllers\accprofile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\MyrequsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UpdateSystemController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/',[HomeController::class,'index'])->middleware(['auth'])->name('home');

//Route Dashboard
Route::get('/dashboard',[HomeController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::get('/', [HomeController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::get('/activitylog', [HomeController::class, 'activitylog'])->middleware(['auth'])->name('activitylog');
Route::get('/mrq/{id}', [HomeController::class, 'morerq'])->middleware(['auth'])->name('morerequest');

// Route Request 
Route::get('/daftartiket', [HomeController::class, 'daftartiket'])->middleware(['auth'])->name('daftartiket');
Route::get('/inbox-manajemen', [HomeController::class, 'inbox'])->middleware(['auth'])->name('inbox');
Route::get('/laporan', [HomeController::class, 'laporan'])->middleware(['auth'])->name('laporan');
Route::get('/penilaian/{id}', [HomeController::class, 'penilaian'])->middleware(['auth'])->name('penilaian');
Route::post('/penilaian/{id}', [HomeController::class, 'storePenilaian'])->middleware(['auth'])->name('penilaian.store');
Route::get('/rapor/{id}', [HomeController::class, 'rapor'])->middleware(['auth'])->name('rapor');
Route::get('/rapor/{id}/download', [HomeController::class, 'downloadRapor'])->middleware(['auth'])->name('rapor.download');
Route::get('/riwayat/{id}', [HomeController::class, 'riwayat'])->middleware(['auth'])->name('riwayat');
Route::get('/getOutlet/{search?}', [RequestController::class, 'getOutlet'])->name('getOutlet');
Route::get('/detailrequest/{id}',[RequestController::class, 'detailrequest'])->middleware(['auth'])->name('detailrequest');
Route::get('/mrq', [RequestController::class, 'mrq'])->middleware(['auth'])->name('mrq');
Route::get('/createrq', [RequestController::class, 'createrq'])->middleware(['auth'])->name('createrq');
Route::get('/editrq/{id}', [RequestController::class, 'editrq'])->middleware(['auth'])->name('editrq');
Route::post('/createrq',[RequestController::class, 'store'])->middleware(['auth'])->name('storerq');
Route::post('/editrq/{id}', [RequestController::class, 'updaterq'])->middleware(['auth'])->name('updaterq');
Route::get('/deleteimg/{img}', [RequestController::class, 'deleteimg'])->middleware(['auth'])->name('deleteimg');
Route::get('/updatestatus/{id}/{stid}',[RequestController::class, 'updatestatus'])->middleware(['auth'])->name('updatestatus');
Route::post('/komentar/{id}', [RequestController::class, 'komentar'])->middleware(['auth'])->name('komentar');
Route::delete('/deletekomen/{id}',[RequestController::class, 'deletekomen'])->middleware(['auth'])->name('deletekomen');
Route::delete('/deletereq/{id}',[RequestController::class, 'delete'])->middleware(['auth'])->name('deletereq');
Route::post('/request/{id}/approve', [RequestController::class, 'approveRequest'])->middleware(['auth'])->name('request.approve');
Route::post('/request/{id}/reject', [RequestController::class, 'rejectRequest'])->middleware(['auth'])->name('request.reject');

//Route Update System
Route::get('/detailus/{id}', [UpdateSystemController::class, 'detailus'])->middleware(['auth'])->name('detailus');
Route::get('/mus', [UpdateSystemController::class, 'mus'])->middleware(['auth'])->name('mus');
Route::get('/createus/{id}', [UpdateSystemController::class, 'createus'])->middleware(['auth'])->name('createus');
Route::get('/editus/{id}',[UpdateSystemController::class, 'editus'])->middleware(['auth'])->name('editus');
Route::post('/createus', [UpdateSystemController::class, 'store'])->middleware(['auth'])->name('storeus');
Route::post('/editus/{id}', [UpdateSystemController::class, 'update'])->middleware(['auth'])->name('updateus');
Route::delete('/deleteus/{id}', [UpdateSystemController::class, 'delete'])->middleware(['auth'])->name('deleteus');

//Route My Request
Route::get('/myreq', [MyController::class, 'index'])->middleware(['auth'])->name('myreq');
Route::get('/myproc/{id}', [MyController::class, 'myproc'])->middleware(['auth'])->name('myproc');
Route::get('/myus',[MyController::class, 'myus'])->middleware(['auth'])->name('myus');

//Route User
Route::get('/user', [UserController::class, 'index'])->middleware(['auth'])->name('user');
Route::get('/registeracc',[UserController::class, 'registeracc'])->middleware(['auth'])->name('registeracc');
Route::post('/registeracc',[UserController::class, 'store'])->middleware(['auth'])->name('storeacc');
Route::get('/editacc/{id}',[UserController::class, 'editacc'])->middleware(['auth'])->name('editacc');
Route::post('/editacc/{id}',[UserController::class, 'edit'])->middleware(['auth'])->name('updateacc');
Route::get('/editpass/{id}', [UserController::class, 'editpass'])->middleware(['auth'])->name('editpass');
Route::delete('/deleteacc/{id}',[UserController::class, 'delete'])->middleware(['auth'])->name('deleteacc');
Route::post('/editpass/{id}', [UserController::class, 'updatepass'])->middleware(['auth'])->name('updatepass');

//Route Tag
Route::get('/tag', [TagController::class, 'index'])->middleware(['auth'])->name('tag');
Route::get('/createtag', [TagController::class, 'createtag'])->middleware(['auth'])->name('createtag');
Route::get('/edittag/{id}', [TagController::class, 'edittag'])->middleware(['auth'])->name('edittag');
Route::post('/createtag', [TagController::class, 'store'])->middleware(['auth'])->name('storetag');
Route::post('/edittag/{id}', [TagController::class, 'updatetag'])->middleware(['auth'])->name('updatetag');
Route::delete('/deletetag/{id}', [TagController::class, 'delete'])->middleware(['auth'])->name('deletetag');

//Route Kategori
Route::get('/kategori', [KategoriController::class, 'index'])->middleware(['auth'])->name('kategori');
Route::get('/createkategori', [KategoriController::class, 'createkategori'])->middleware(['auth'])->name('createkategori');
Route::get('/editkategori/{id}', [KategoriController::class, 'editkategori'])->middleware(['auth'])->name('editkategori');
Route::post('/createkategori', [KategoriController::class, 'store'])->middleware(['auth'])->name('storekategori');
Route::post('/editkategori/{id}', [KategoriController::class, 'updatekategori'])->middleware(['auth'])->name('updatekategori');
Route::delete('/deletekategori/{id}',[KategoriController::class, 'delete'])->middleware(['auth'])->name('deletekategori');

//Route Modul
Route::get('/modul', [ModulController::class, 'index'])->middleware(['auth'])->name('modul');
Route::get('/createmodul', [ModulController::class, 'create'])->middleware(['auth'])->name('createmodul');
Route::get('/editmodul/{id}', [ModulController::class, 'edit'])->middleware(['auth'])->name('editmodul');
Route::post('/createmodul', [ModulController::class, 'store'])->middleware(['auth'])->name('storemodul');
Route::post('/editmodul/{id}', [ModulController::class, 'update'])->middleware(['auth'])->name('updatemodul');
Route::delete('/deletemodul/{id}', [ModulController::class, 'delete'])->middleware(['auth'])->name('deletemodul');

//Route Status
Route::get('/status', [StatusController::class, 'index'])->middleware(['auth'])->name('status');
Route::get('/createstatus', [StatusController::class, 'createstatus'])->middleware(['auth'])->name('createstatus');
Route::get('/editstatus/{id}', [StatusController::class, 'editstatus'])->middleware(['auth'])->name('editstatus');
Route::post('/createstatus', [StatusController::class, 'store'])->middleware(['auth'])->name('storestatus');
Route::post('/editstatus/{id}', [StatusController::class, 'updatestatus'])->middleware(['auth'])->name('updatestatus');
Route::get('/deletestatus/{id}',[StatusController::class, 'delete'])->middleware(['auth'])->name('deletestatus');

//Route Client
Route::get('/client', [ClientController::class, 'index'])->middleware(['auth'])->name('client');
Route::get('/createclient', [ClientController::class, 'createclient'])->middleware(['auth'])->name('createclient');
Route::get('/editclient/{id}', [ClientController::class, 'editclient'])->middleware(['auth'])->name('editclient');
Route::post('/createclient', [ClientController::class, 'store'])->middleware(['auth'])->name('storeclient');
Route::post('/editclient/{id}', [ClientController::class, 'update'])->middleware(['auth'])->name('updateclient');
Route::delete('/deleteclient/{id}',[ClientController::class, 'delete'])->middleware(['auth'])->name('deleteclient');

//Route Account Profile
Route::get('/profile', [ProfileController::class, 'index'])->middleware(['auth'])->name('profile');
Route::get('/editprofile/{id}', [ProfileController::class, 'edit'])->middleware(['auth'])->name('editprofile');
Route::post('/editprofile/{id}', [ProfileController::class, 'updateprofile'])->middleware(['auth'])->name('updateprofile');
Route::get('/editpassacc', [ProfileController::class, 'editpass'])->middleware(['auth'])->name('editpassacc');
Route::post('/editpassacc', [ProfileController::class, 'updatepass'])->middleware(['auth'])->name('updatepassacc');

//Route My Request & Update System
Route::get('/myrequs', [MyrequsController::class, 'index'])->middleware(['auth'])->name('myrequs');
Route::get('/myrequest', [MyrequsController::class, 'myrequest'])->middleware(['auth'])->name('myrequest');
Route::get('/myupdatesystem', [MyrequsController::class, 'myupdatesystem'])->middleware(['auth'])->name('myupdatesystem');

//Route Notification
Route::get('/notifications/mark-as-read', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return back();
})->name('notifications.markAsRead');

Route::get('/demo-tiket', function () {
    return view('demo_tiket');
});

require __DIR__.'/auth.php';

