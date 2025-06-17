<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

        $router->get('/check-login', ['uses' => 'AuthController@checkLoginStatus']);
        $router->get('/user-permissions', ['uses' => 'AuthController@getUserPermissions']);
        
        //Hr Rapor
        $router->get('hr_rapor', 'HrRaporController@index');
        $router->get('hr_rapor/{id}', 'HrRaporController@show');
        $router->post('hr_rapor', 'HrRaporController@store');
        $router->put('hr_rapor/{id}', 'HrRaporController@update');
        $router->delete('hr_rapor/{id}', 'HrRaporController@destroy');

        //Master kriteria Rapor
        $router->get('kriteria-rapor', 'MstKriteriaRaporController@index');
        $router->post('kriteria-rapor', 'MstKriteriaRaporController@store');
        $router->get('kriteria-rapor/{id}', 'MstKriteriaRaporController@show');
        $router->put('kriteria-rapor/{id}', 'MstKriteriaRaporController@update');
        $router->delete('kriteria-rapor/{id}', 'MstKriteriaRaporController@destroy');
        $router->put('kriteria-rapor/{id}/toggle-status', 'MstKriteriaRaporController@toggleStatus');


        //Detail Kriteria
        $router->get('detail-kriteria-rapor', 'MstDtlKriteriaRaporController@index');
        $router->post('detail-kriteria-rapor', 'MstDtlKriteriaRaporController@store');
        $router->get('detail-kriteria-rapor/{id}', 'MstDtlKriteriaRaporController@show');
        $router->put('detail-kriteria-rapor/{id}', 'MstDtlKriteriaRaporController@update');
        $router->put('detail-kriteria-rapor/{id}/toggle-status', 'MstDtlKriteriaRaporController@toggleStatus');
        $router->delete('detail-kriteria-rapor/{id}', 'MstDtlKriteriaRaporController@destroy');

        //Master Tanda Tangan
        $router->get('signature-master/active', 'SignatureMasterController@getActive');
        $router->get('signature-master', 'SignatureMasterController@index');
        $router->get('signature-master/{id}', 'SignatureMasterController@show');
        $router->post('signature-master', 'SignatureMasterController@store');
        $router->put('signature-master/{id}', 'SignatureMasterController@update');
        $router->delete('signature-master/{id}', 'SignatureMasterController@destroy');

        // Bobot PK SOBA routes
        $router->get('/bobot-pk-soba', ['uses' => 'BobotPkSobaController@index']);
        $router->post('/bobot-pk-soba', ['uses' => 'BobotPkSobaController@store']);
        $router->get('/bobot-pk-soba/{id}', ['uses' => 'BobotPkSobaController@show']);
        $router->put('/bobot-pk-soba/{id}', ['uses' => 'BobotPkSobaController@update']);
        $router->delete('/bobot-pk-soba/{id}', ['uses' => 'BobotPkSobaController@destroy']);


    $router->get('/temp-pos-detail', ['uses' => 'TempPosDetailController@getDetails']);
    $router->get('/temp-pos-detail/result/{profileId}', ['uses' => 'TempPosDetailController@getResultByProfileId']);
    $router->delete('/temp-pos-detail/delete/{profileId}', ['uses' => 'TempPosDetailController@deleteByProfileId']);

    // temppos
    $router->get('/tempt_report_soba_pk', ['uses' => 'TemptReportSobaPkController@paging']);
    $router->post('/tempt_report_soba_pk', ['uses' => 'TemptReportSobaPkController@store']);
    $router->delete('/tempt_report_soba_pk/{id}', ['uses' => 'TemptReportSobaPkController@destroy']);

    $router->get('/tempt_report_soba_pk/{id}', ['uses' => 'TemptReportSobaPkController@show']);
    $router->put('/tempt_report_soba_pk/{id}', ['uses' => 'TemptReportSobaPkController@update']);

    $router->get('/temptreportsobapkalldata', ['uses' => 'TemptReportSobaPkController@getAllData']);

    $router->post('/temptReportSobaPk/storeBulky', ['uses' => 'TemptReportSobaPkController@storeBulky']);

    $router->get('/tempt_report_soba_pk', 'TemptReportSobaPkController@getTempReportSobaPK');


    // Users routes
        $router->get('/users', ['uses' => 'UsersController@paging']);
        $router->post('/users', ['uses' => 'UsersController@store']);
        $router->get('/users/{id}', ['uses' => 'UsersController@show']);
        $router->put('/users/{id}', ['uses' => 'UsersController@update']);
        $router->delete('/users/{id}', ['uses' => 'UsersController@destroy']);
        $router->get('/usersalldata', ['uses' => 'UsersController@getAllData']);

    $router->post('/users', ['uses' => 'UsersController@store']);
    $router->put('/users/{id}', ['uses' => 'UsersController@update']);

        $router->get('/api/users/password/{id}', ['uses' => 'UsersController@showPassword']);

    // Tambahkan route untuk login
    $router->post('/login', ['uses' => 'AuthController@login']);
    $router->post('/logout', ['uses' => 'AuthController@logout']);
    $router->get('/check-login', ['uses' => 'AuthController@checkLoginStatus']);

    //profiles
    $router->get('/profiles', ['uses' => 'ProfilesController@paging']);
        $router->post('/profiles', ['uses' => 'ProfilesController@store']);
        $router->delete('/profiles/{id}', ['uses' => 'ProfilesController@destroy']);

    $router->get('/profiles/{id}', ['uses' => 'ProfilesController@show']);
        $router->put('/profiles/{id}', ['uses' => 'ProfilesController@update']);

    $router->get('/profilesalldata', ['uses' => 'ProfilesController@getAllData']);

        $router->post('/profiles/storeBulky', ['uses' => 'ProfilesController@storeBulky']);

    //Untuk urutan NIK (nomor induk karyawan)
        $router->get('/lastNIK/{cabang_id}', ['uses' => 'ProfilesController@getLastNIK']);

    $router->get('/getProfileId/{profile_name}', ['uses' => 'ProfilesController@getProfileId']);
        $router->put('/profiles/update/{id}', ['uses' => 'ProfilesController@update']);

    $router->get('/profiles/by-cabang/{cabang_id}', ['uses' => 'ProfilesController@getProfilesByCabang']);


        //hr_cabang 
        $router->get('/hr_cabang', ['uses' => 'HrCabangController@paging']);
        $router->post('/hr_cabang', ['uses' => 'HrCabangController@store']);
        $router->delete('/hr_cabang/{id}', ['uses' => 'HrCabangController@destroy']);

        $router->get('/hr_cabang/{id}', ['uses' => 'HrCabangController@show']);
        $router->put('/hr_cabang/{id}', ['uses' => 'HrCabangController@update']);

        $router->get('/cabangalldata', ['uses' => 'HrCabangController@getAllData']);

    //hr _jabatan
    $router->get('/hr_jabatan', ['uses' => 'HrJabatanController@paging']);
    $router->post('/hr_jabatan', ['uses' => 'HrJabatanController@store']);
    $router->delete('/hr_jabatan/{id}', ['uses' => 'HrJabatanController@destroy']);

    $router->get('/hr_jabatan/{id}', ['uses' => 'HrJabatanController@show']);
    $router->put('/hr_jabatan/{id}', ['uses' => 'HrJabatanController@update']);

    $router->get('/jabatanalldata', ['uses' => 'HrJabatanController@getAllData']);


    //hr _unit
    $router->get('/hr_unit', ['uses' => 'HrUnitController@paging']);
    $router->post('/hr_unit', ['uses' => 'HrUnitController@store']);
    $router->delete('/hr_unit/{id}', ['uses' => 'HrUnitController@destroy']);

    $router->get('/hr_unit/{id}', ['uses' => 'HrUnitController@show']);
    $router->put('/hr_unit/{id}', ['uses' => 'HrUnitController@update']);

    $router->get('/unitalldata', ['uses' => 'HrUnitController@getAllData']);

    //hr _organisasi
    $router->get('/hr_organisasi', ['uses' => 'HrOrganisasiController@paging']);
    $router->post('/hr_organisasi', ['uses' => 'HrOrganisasiController@store']);
    $router->delete('/hr_organisasi/{id}', ['uses' => 'HrOrganisasiController@destroy']);

    $router->get('/hr_organisasi/{id}', ['uses' => 'HrOrganisasiController@show']);
    $router->put('/hr_organisasi/{id}', ['uses' => 'HrOrganisasiController@update']);

    //hr _bank
    $router->get('/hr_bank', ['uses' => 'HrBankController@paging']);
    $router->post('/hr_bank', ['uses' => 'HrBankController@store']);
    $router->delete('/hr_bank/{id}', ['uses' => 'HrBankController@destroy']);

    $router->get('/hr_bank/{id}', ['uses' => 'HrBankController@show']);
    $router->put('/hr_bank/{id}', ['uses' => 'HrBankController@update']);

    $router->get('/bankalldata', ['uses' => 'HrBankController@getAllData']);


    //hr _sallary
    $router->get('/hr_sallary', ['uses' => 'HrSallaryController@paging']);
    $router->post('/hr_sallary', ['uses' => 'HrSallaryController@store']);
    $router->delete('/hr_sallary/{id}', ['uses' => 'HrSallaryController@destroy']);

    $router->get('/hr_sallary/{id}', ['uses' => 'HrSallaryController@show']);
    $router->put('/hr_sallary/{id}', ['uses' => 'HrSallaryController@update']);

    $router->get('/sallaryalldata', ['uses' => 'HrSallaryController@getAllData']);


    //hr _bpjs
    $router->get('/hr_bpjs', ['uses' => 'HrBpjsController@paging']);
    $router->post('/hr_bpjs', ['uses' => 'HrBpjsController@store']);
    $router->delete('/hr_bpjs/{id}', ['uses' => 'HrBpjsController@destroy']);

    $router->get('/hr_bpjs/{id}', ['uses' => 'HrBpjsController@show']);
    $router->put('/hr_bpjs/{id}', ['uses' => 'HrBpjsController@update']);

    $router->get('/bpjsalldata', ['uses' => 'HrBpjsController@getAllData']);

    $router->get('/bpjs/profile/{profiles_id}', ['uses' => 'HrBpjsController@getByProfileId']);
    $router->put('/bpjs/profile/{profiles_id}', ['uses' => 'HrBpjsController@updateByProfileId']);

    //hr_npwp
    $router->get('/hr_npwp', ['uses' => 'HrNpwpController@paging']);
    $router->post('/hr_npwp', ['uses' => 'HrNpwpController@store']);
    $router->delete('/hr_npwp/{id}', ['uses' => 'HrNpwpController@destroy']);

    $router->get('/hr_npwp/{id}', ['uses' => 'HrNpwpController@show']);
    $router->put('/hr_npwp/{id}', ['uses' => 'HrNpwpController@update']);

    $router->get('/npwpalldata', ['uses' => 'HrNpwpController@getAllData']);

    $router->get('/npwp/profile/{profiles_id}', ['uses' => 'HrNpwpController@getByProfileId']);
    $router->put('/npwp/profile/{profiles_id}', ['uses' => 'HrNpwpController@updateByProfileId']);

    //hr_rek_karyawan
    $router->get('/hr_rek_karyawan', ['uses' => 'HrRekKarController@paging']);
    $router->post('/hr_rek_karyawan', ['uses' => 'HrRekKarController@store']);
    $router->delete('/hr_rek_karyawan/{id}', ['uses' => 'HrRekKarController@destroy']);

    $router->get('/hr_rek_karyawan/{id}', ['uses' => 'HrRekKarController@show']);
    $router->put('/hr_rek_karyawan/{id}', ['uses' => 'HrRekKarController@update']);

    $router->get('/RekKaralldata', ['uses' => 'HrRekKarController@getAllData']);

    $router->get('/rekkar/profile/{profiles_id}', ['uses' => 'HrRekKarController@getByProfileId']);
    $router->put('/rekkar/profile/{profiles_id}', ['uses' => 'HrRekKarController@updateByProfileId']);

    //hr_kode_lokasi
    $router->get('/hr_kode_lokasi', ['uses' => 'HrKodeLokasiController@paging']);
    $router->post('/hr_kode_lokasi', ['uses' => 'HrKodeLokasiController@store']);
    $router->delete('/hr_kode_lokasi/{id}', ['uses' => 'HrKodeLokasiController@destroy']);

    $router->get('/hr_kode_lokasi/{id}', ['uses' => 'HrKodeLokasiController@show']);
    $router->put('/hr_kode_lokasi/{id}', ['uses' => 'HrKodeLokasiController@update']);

    $router->get('/kodelokasialldata', ['uses' => 'HrKodeLokasiController@getAllData']);



    //profiles_detail
    $router->get('/profiles_detail', ['uses' => 'ProfilesDetailController@paging']);
    $router->post('/profiles_detail', ['uses' => 'ProfilesDetailController@store']);
    $router->delete('/profiles_detail/{id}', ['uses' => 'ProfilesDetailController@destroy']);

    $router->get('/profiles_detail/{id}', ['uses' => 'ProfilesDetailController@show']);
    $router->put('/profiles_detail/{id}', ['uses' => 'ProfilesDetailController@update']);

    $router->get('/profiledetailalldata', ['uses' => 'ProfilesDetailController@getAllData']);

    $router->get('/profiledetaildata', ['uses' => 'ProfilesDetailController@getProfileDetailData']);

    $router->post('/takeout', ['uses' => 'ProfilesDetailController@setTakeOut']);

    $router->get('/check-five-year', ['uses' => 'ProfilesDetailController@checkFiveYear']);
    $router->post('/takeout-five-year', ['uses' => 'ProfilesDetailController@takeoutFiveYear']);


    //hr_kontrak
    $router->get('/hr_kontrak', ['uses' => 'HrKontrakController@paging']);
    $router->post('/hr_kontrak', ['uses' => 'HrKontrakController@store']);
    $router->delete('/hr_kontrak/{id}', ['uses' => 'HrKontrakController@destroy']);

    $router->get('/hr_kontrak/{id}', ['uses' => 'HrKontrakController@show']);
    $router->put('/hr_kontrak/{id}', ['uses' => 'HrKontrakController@update']);

    $router->get('/kontrakalldata', ['uses' => 'HrKontrakController@getAllData']);

    // Route untuk menghitung jumlah kontrak berdasarkan profile_id dan tipe
    $router->get('/kontrak-count', ['uses' => 'HrKontrakController@getKontrakCount']);

    // Rute untuk mendapatkan nomor kontrak terakhir berdasarkan tipe
    $router->post('/hr_kontrak/getLastContractNumber', ['uses' => 'HrKontrakController@getLastContractNumber']);

    $router->get('/lastExitDate', ['uses' => 'HrKontrakController@getLastExitDate']);

    $router->get('/totalContractYears', ['uses' => 'HrKontrakController@getTotalContractYears']);

    $router->get('/check-expiring', ['uses' => 'HrKontrakController@checkExpiringContracts']);

    $router->post('/hr_kontrak/storeBulky', ['uses' => 'HrKontrakController@storeBulky']);



    //doc_list
    $router->get('/doc_list', ['uses' => 'DocListController@paging']);
    $router->post('/doc_list', ['uses' => 'DocListController@store']);
    $router->delete('/doc_list/{id}', ['uses' => 'DocListController@destroy']);

    $router->get('/doc_list/{id}', ['uses' => 'DocListController@show']);
    $router->put('/doc_list/{id}', ['uses' => 'DocListController@update']);

    $router->get('/doclistalldata', ['uses' => 'DocListController@getAllData']);

    //users
    $router->get('/users', ['uses' => 'UsersController@paging']);
    $router->post('/users', ['uses' => 'UsersController@store']);
    $router->delete('/users/{id}', ['uses' => 'UsersController@destroy']);

    $router->get('/users/{id}', ['uses' => 'UsersController@show']);
    $router->put('/users/{id}', ['uses' => 'UsersController@update']);

        });

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

// $router->group(['prefix' => 'api'], function () use ($router) {

//     //Hr Rapor
//     $router->get('hr_rapor', 'HrRaporController@index');
//     $router->get('hr_rapor/{id}', 'HrRaporController@show');
//     $router->post('hr_rapor', 'HrRaporController@store');
//     $router->put('hr_rapor/{id}', 'HrRaporController@update');
//     $router->delete('hr_rapor/{id}', 'HrRaporController@destroy');

//     //Master kriteria Rapor
//     $router->get('kriteria-rapor', 'MstKriteriaRaporController@index');
//     $router->post('kriteria-rapor', 'MstKriteriaRaporController@store');
//     $router->get('kriteria-rapor/{id}', 'MstKriteriaRaporController@show');
//     $router->put('kriteria-rapor/{id}', 'MstKriteriaRaporController@update');
//     $router->delete('kriteria-rapor/{id}', 'MstKriteriaRaporController@destroy');
//     $router->put('kriteria-rapor/{id}/toggle-status', 'MstKriteriaRaporController@toggleStatus');


//     //Detail Kriteria
//     $router->get('detail-kriteria-rapor', 'MstDtlKriteriaRaporController@index');
//     $router->post('detail-kriteria-rapor', 'MstDtlKriteriaRaporController@store');
//     $router->get('detail-kriteria-rapor/{id}', 'MstDtlKriteriaRaporController@show');
//     $router->put('detail-kriteria-rapor/{id}', 'MstDtlKriteriaRaporController@update');
//     $router->put('detail-kriteria-rapor/{id}/toggle-status', 'MstDtlKriteriaRaporController@toggleStatus');
//     $router->delete('detail-kriteria-rapor/{id}', 'MstDtlKriteriaRaporController@destroy');

//     //Master Tanda Tangan
//     $router->get('signature-master/active', 'SignatureMasterController@getActive');
//     $router->get('signature-master', 'SignatureMasterController@index');
//     $router->get('signature-master/{id}', 'SignatureMasterController@show');
//     $router->post('signature-master', 'SignatureMasterController@store');
//     $router->put('signature-master/{id}', 'SignatureMasterController@update');
//     $router->delete('signature-master/{id}', 'SignatureMasterController@destroy');

//     // Bobot PK SOBA routes
//     $router->get('/bobot-pk-soba', ['uses' => 'BobotPkSobaController@index']);
//     $router->post('/bobot-pk-soba', ['uses' => 'BobotPkSobaController@store']);
//     $router->get('/bobot-pk-soba/{id}', ['uses' => 'BobotPkSobaController@show']);
//     $router->put('/bobot-pk-soba/{id}', ['uses' => 'BobotPkSobaController@update']);
//     $router->delete('/bobot-pk-soba/{id}', ['uses' => 'BobotPkSobaController@destroy']);


//     $router->get('/temp-pos-detail', ['uses' => 'TempPosDetailController@getDetails']);
//     $router->get('/temp-pos-detail/result/{profileId}', ['uses' => 'TempPosDetailController@getResultByProfileId']);
//     $router->delete('/temp-pos-detail/delete/{profileId}', ['uses' => 'TempPosDetailController@deleteByProfileId']);

//     // temppos
//     $router->get('/tempt_report_soba_pk', ['uses' => 'TemptReportSobaPkController@paging']);
//     $router->post('/tempt_report_soba_pk', ['uses' => 'TemptReportSobaPkController@store']);
//     $router->delete('/tempt_report_soba_pk/{id}', ['uses' => 'TemptReportSobaPkController@destroy']);

//     $router->get('/tempt_report_soba_pk/{id}', ['uses' => 'TemptReportSobaPkController@show']);
//     $router->put('/tempt_report_soba_pk/{id}', ['uses' => 'TemptReportSobaPkController@update']);

//     $router->get('/temptreportsobapkalldata', ['uses' => 'TemptReportSobaPkController@getAllData']);

//     $router->post('/temptReportSobaPk/storeBulky', ['uses' => 'TemptReportSobaPkController@storeBulky']);

//     $router->get('/tempt_report_soba_pk', 'TemptReportSobaPkController@getTempReportSobaPK');


//     // Users routes
//     $router->get('/users', ['uses' => 'UsersController@paging']);
//     $router->post('/users', ['uses' => 'UsersController@store']);
//     $router->get('/users/{id}', ['uses' => 'UsersController@show']);
//     $router->put('/users/{id}', ['uses' => 'UsersController@update']);
//     $router->delete('/users/{id}', ['uses' => 'UsersController@destroy']);
//     $router->get('/usersalldata', ['uses' => 'UsersController@getAllData']);

//     $router->post('/users', ['uses' => 'UsersController@store']);
//     $router->put('/users/{id}', ['uses' => 'UsersController@update']);

//     $router->get('/api/users/password/{id}', ['uses' => 'UsersController@showPassword']);

//     // Tambahkan route untuk login
//     $router->post('/login', ['uses' => 'AuthController@login']);
//     $router->post('/logout', ['uses' => 'AuthController@logout']);
//     $router->get('/check-login', ['uses' => 'AuthController@checkLoginStatus']);

//     //profiles
//     $router->get('/profiles', ['uses' => 'ProfilesController@paging']);
//     $router->post('/profiles', ['uses' => 'ProfilesController@store']);
//     $router->delete('/profiles/{id}', ['uses' => 'ProfilesController@destroy']);

//     $router->get('/profiles/{id}', ['uses' => 'ProfilesController@show']);
//     $router->put('/profiles/{id}', ['uses' => 'ProfilesController@update']);

//     $router->get('/profilesalldata', ['uses' => 'ProfilesController@getAllData']);

//     $router->post('/profiles/storeBulky', ['uses' => 'ProfilesController@storeBulky']);

//     //Untuk urutan NIK (nomor induk karyawan)
//     $router->get('/lastNIK/{cabang_id}', ['uses' => 'ProfilesController@getLastNIK']);

//     $router->get('/getProfileId/{profile_name}', ['uses' => 'ProfilesController@getProfileId']);
//     $router->put('/profiles/update/{id}', ['uses' => 'ProfilesController@update']);

//     $router->get('/profiles/by-cabang/{cabang_id}', ['uses' => 'ProfilesController@getProfilesByCabang']);


//     //hr_cabang 
//     $router->get('/hr_cabang', ['uses' => 'HrCabangController@paging']);
//     $router->post('/hr_cabang', ['uses' => 'HrCabangController@store']);
//     $router->delete('/hr_cabang/{id}', ['uses' => 'HrCabangController@destroy']);

//     $router->get('/hr_cabang/{id}', ['uses' => 'HrCabangController@show']);
//     $router->put('/hr_cabang/{id}', ['uses' => 'HrCabangController@update']);

//     $router->get('/cabangalldata', ['uses' => 'HrCabangController@getAllData']);

//     //hr _jabatan
//     $router->get('/hr_jabatan', ['uses' => 'HrJabatanController@paging']);
//     $router->post('/hr_jabatan', ['uses' => 'HrJabatanController@store']);
//     $router->delete('/hr_jabatan/{id}', ['uses' => 'HrJabatanController@destroy']);

//     $router->get('/hr_jabatan/{id}', ['uses' => 'HrJabatanController@show']);
//     $router->put('/hr_jabatan/{id}', ['uses' => 'HrJabatanController@update']);

//     $router->get('/jabatanalldata', ['uses' => 'HrJabatanController@getAllData']);


//     //hr _unit
//     $router->get('/hr_unit', ['uses' => 'HrUnitController@paging']);
//     $router->post('/hr_unit', ['uses' => 'HrUnitController@store']);
//     $router->delete('/hr_unit/{id}', ['uses' => 'HrUnitController@destroy']);

//     $router->get('/hr_unit/{id}', ['uses' => 'HrUnitController@show']);
//     $router->put('/hr_unit/{id}', ['uses' => 'HrUnitController@update']);

//     $router->get('/unitalldata', ['uses' => 'HrUnitController@getAllData']);

//     //hr _organisasi
//     $router->get('/hr_organisasi', ['uses' => 'HrOrganisasiController@paging']);
//     $router->post('/hr_organisasi', ['uses' => 'HrOrganisasiController@store']);
//     $router->delete('/hr_organisasi/{id}', ['uses' => 'HrOrganisasiController@destroy']);

//     $router->get('/hr_organisasi/{id}', ['uses' => 'HrOrganisasiController@show']);
//     $router->put('/hr_organisasi/{id}', ['uses' => 'HrOrganisasiController@update']);

//     //hr _bank
//     $router->get('/hr_bank', ['uses' => 'HrBankController@paging']);
//     $router->post('/hr_bank', ['uses' => 'HrBankController@store']);
//     $router->delete('/hr_bank/{id}', ['uses' => 'HrBankController@destroy']);

//     $router->get('/hr_bank/{id}', ['uses' => 'HrBankController@show']);
//     $router->put('/hr_bank/{id}', ['uses' => 'HrBankController@update']);

//     $router->get('/bankalldata', ['uses' => 'HrBankController@getAllData']);


//     //hr _sallary
//     $router->get('/hr_sallary', ['uses' => 'HrSallaryController@paging']);
//     $router->post('/hr_sallary', ['uses' => 'HrSallaryController@store']);
//     $router->delete('/hr_sallary/{id}', ['uses' => 'HrSallaryController@destroy']);

//     $router->get('/hr_sallary/{id}', ['uses' => 'HrSallaryController@show']);
//     $router->put('/hr_sallary/{id}', ['uses' => 'HrSallaryController@update']);

//     $router->get('/sallaryalldata', ['uses' => 'HrSallaryController@getAllData']);


//     //hr _bpjs
//     $router->get('/hr_bpjs', ['uses' => 'HrBpjsController@paging']);
//     $router->post('/hr_bpjs', ['uses' => 'HrBpjsController@store']);
//     $router->delete('/hr_bpjs/{id}', ['uses' => 'HrBpjsController@destroy']);

//     $router->get('/hr_bpjs/{id}', ['uses' => 'HrBpjsController@show']);
//     $router->put('/hr_bpjs/{id}', ['uses' => 'HrBpjsController@update']);

//     $router->get('/bpjsalldata', ['uses' => 'HrBpjsController@getAllData']);

//     $router->get('/bpjs/profile/{profiles_id}', ['uses' => 'HrBpjsController@getByProfileId']);
//     $router->put('/bpjs/profile/{profiles_id}', ['uses' => 'HrBpjsController@updateByProfileId']);

//     //hr_npwp
//     $router->get('/hr_npwp', ['uses' => 'HrNpwpController@paging']);
//     $router->post('/hr_npwp', ['uses' => 'HrNpwpController@store']);
//     $router->delete('/hr_npwp/{id}', ['uses' => 'HrNpwpController@destroy']);

//     $router->get('/hr_npwp/{id}', ['uses' => 'HrNpwpController@show']);
//     $router->put('/hr_npwp/{id}', ['uses' => 'HrNpwpController@update']);

//     $router->get('/npwpalldata', ['uses' => 'HrNpwpController@getAllData']);

//     $router->get('/npwp/profile/{profiles_id}', ['uses' => 'HrNpwpController@getByProfileId']);
//     $router->put('/npwp/profile/{profiles_id}', ['uses' => 'HrNpwpController@updateByProfileId']);

//     //hr_rek_karyawan
//     $router->get('/hr_rek_karyawan', ['uses' => 'HrRekKarController@paging']);
//     $router->post('/hr_rek_karyawan', ['uses' => 'HrRekKarController@store']);
//     $router->delete('/hr_rek_karyawan/{id}', ['uses' => 'HrRekKarController@destroy']);

//     $router->get('/hr_rek_karyawan/{id}', ['uses' => 'HrRekKarController@show']);
//     $router->put('/hr_rek_karyawan/{id}', ['uses' => 'HrRekKarController@update']);

//     $router->get('/RekKaralldata', ['uses' => 'HrRekKarController@getAllData']);

//     $router->get('/rekkar/profile/{profiles_id}', ['uses' => 'HrRekKarController@getByProfileId']);
//     $router->put('/rekkar/profile/{profiles_id}', ['uses' => 'HrRekKarController@updateByProfileId']);

//     //hr_kode_lokasi
//     $router->get('/hr_kode_lokasi', ['uses' => 'HrKodeLokasiController@paging']);
//     $router->post('/hr_kode_lokasi', ['uses' => 'HrKodeLokasiController@store']);
//     $router->delete('/hr_kode_lokasi/{id}', ['uses' => 'HrKodeLokasiController@destroy']);

//     $router->get('/hr_kode_lokasi/{id}', ['uses' => 'HrKodeLokasiController@show']);
//     $router->put('/hr_kode_lokasi/{id}', ['uses' => 'HrKodeLokasiController@update']);

//     $router->get('/kodelokasialldata', ['uses' => 'HrKodeLokasiController@getAllData']);



//     //profiles_detail
//     $router->get('/profiles_detail', ['uses' => 'ProfilesDetailController@paging']);
//     $router->post('/profiles_detail', ['uses' => 'ProfilesDetailController@store']);
//     $router->delete('/profiles_detail/{id}', ['uses' => 'ProfilesDetailController@destroy']);

//     $router->get('/profiles_detail/{id}', ['uses' => 'ProfilesDetailController@show']);
//     $router->put('/profiles_detail/{id}', ['uses' => 'ProfilesDetailController@update']);

//     $router->get('/profiledetailalldata', ['uses' => 'ProfilesDetailController@getAllData']);

//     $router->get('/profiledetaildata', ['uses' => 'ProfilesDetailController@getProfileDetailData']);

//     $router->post('/takeout', ['uses' => 'ProfilesDetailController@setTakeOut']);

//     $router->get('/check-five-year', ['uses' => 'ProfilesDetailController@checkFiveYear']);
//     $router->post('/takeout-five-year', ['uses' => 'ProfilesDetailController@takeoutFiveYear']);


//     //hr_kontrak
//     $router->get('/hr_kontrak', ['uses' => 'HrKontrakController@paging']);
//     $router->post('/hr_kontrak', ['uses' => 'HrKontrakController@store']);
//     $router->delete('/hr_kontrak/{id}', ['uses' => 'HrKontrakController@destroy']);

//     $router->get('/hr_kontrak/{id}', ['uses' => 'HrKontrakController@show']);
//     $router->put('/hr_kontrak/{id}', ['uses' => 'HrKontrakController@update']);

//     $router->get('/kontrakalldata', ['uses' => 'HrKontrakController@getAllData']);

//     // Route untuk menghitung jumlah kontrak berdasarkan profile_id dan tipe
//     $router->get('/kontrak-count', ['uses' => 'HrKontrakController@getKontrakCount']);

//     // Rute untuk mendapatkan nomor kontrak terakhir berdasarkan tipe
//     $router->post('/hr_kontrak/getLastContractNumber', ['uses' => 'HrKontrakController@getLastContractNumber']);

//     $router->get('/lastExitDate', ['uses' => 'HrKontrakController@getLastExitDate']);

//     $router->get('/totalContractYears', ['uses' => 'HrKontrakController@getTotalContractYears']);

//     $router->get('/check-expiring', ['uses' => 'HrKontrakController@checkExpiringContracts']);

//     $router->post('/hr_kontrak/storeBulky', ['uses' => 'HrKontrakController@storeBulky']);



//     //doc_list
//     $router->get('/doc_list', ['uses' => 'DocListController@paging']);
//     $router->post('/doc_list', ['uses' => 'DocListController@store']);
//     $router->delete('/doc_list/{id}', ['uses' => 'DocListController@destroy']);

//     $router->get('/doc_list/{id}', ['uses' => 'DocListController@show']);
//     $router->put('/doc_list/{id}', ['uses' => 'DocListController@update']);

//     $router->get('/doclistalldata', ['uses' => 'DocListController@getAllData']);

//     //users
//     $router->get('/users', ['uses' => 'UsersController@paging']);
//     $router->post('/users', ['uses' => 'UsersController@store']);
//     $router->delete('/users/{id}', ['uses' => 'UsersController@destroy']);

//     $router->get('/users/{id}', ['uses' => 'UsersController@show']);
//     $router->put('/users/{id}', ['uses' => 'UsersController@update']);
// });