<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
//    public static function getUserQuotaUsed($userid) {
//        $user = User::find($userid);
//
//        $userQuota = $user->leftJoin('files', 'files.user_id' ,'=', 'users.id')
//            ->groupBy('disk_quota')
//            ->selectRaw('ifnull((sum(files.file_size) / 1024 / 1024), 0) disk_usage, disk_quota')
//            ->where('users.id', $userid)
//            ->first();
//
//        return $userQuota;
//    }


//    public function user() {
//        return $this->belongsTo('App\User');
//    }

//    public function getUserDiskQuota(Request $request) {
//        $id = $request->input('userId');
//
//        $userQuota = QuotaHelper::getUserQuotaUsed($id);
//
//        return $userQuota->toJson();
//    }


//Route::post('/upload', ['uses' => 'FileController@uploadFile'])->name('file.upload');
//Route::post('/explorer/files', ['uses' => 'FileController@getUserFiles'])->name('explorer.files');
//Route::post('/explorer/folders', ['uses' => 'FolderController@getUserFolders'])->name('explorer.folders');
//Route::post('/explorer/folders/parent', ['uses' => 'FolderController@getParentFolderId'])->name('explorer.folder.parent');
//Route::get('/explorer/files/download/{id?}', ['uses' => 'FileController@downloadFile'])->name('explorer.download');
//Route::get('/explorer/files/delete/{id?}', ['uses' => 'FileController@deleteFile'])->name('explorer.delete');
//Route::post('/explorer/folders/create', ['uses' => 'FolderController@createFolder'])->name('explorer.folder.create');
//Route::post('/explorer/files/rename', ['uses' => 'FileController@renameFile'])->name('explorer.file.rename');
//Route::post('/explorer/folders/rename', ['uses' => 'FolderController@renameFolder'])->name('explorer.folder.rename');
//Route::post('/explorer/files/move', ['uses' => 'FileController@moveFile'])->name('explorer.file.move');
//Route::post('/explorer/folders/get-breadcrumb', ['uses' => 'FolderController@getFolderBreadcrumb'])->name('explorer.folder.getBreadcrumb');


}
