<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Spatie\Activitylog\Models\Activity;


class ActivityLogController extends Controller
{
    use HttpResponses;


    public function getLogs(Request $request)
    {
        // $activityLogs = Activity::with('causer:id,name')->get();
        //get all activity logs with causer name only
        // $activityLogs = Activity::with('causer:id,name')->select('id','description','causer_id','properties','created_at', 'causer_id','causer.name')->get();
        
        $activityLogs = Activity::join('users as causer', 'activity_log.causer_id', '=', 'causer.id')
        ->select('activity_log.id', 'activity_log.description', 'activity_log.causer_id','activity_log.created_at', 'causer.name as causer_name','activity_log.event', 'activity_log.properties')
        ->orderBy('activity_log.created_at', 'desc')->get();

        
        if(!$activityLogs){
            return $this->error('Activity logs not found', 404);
        }
        if(auth()->user()->is_admin ==1){
            return $this->success($activityLogs,200,'all Activity Logs');
        }else{
            $activityLogs = Activity::join('users as causer', 'activity_log.causer_id', '=', 'causer.id')
        ->select('activity_log.id', 'activity_log.description', 'activity_log.causer_id','activity_log.created_at', 'causer.name as causer_name','activity_log.event', 'activity_log.properties')
        ->where('activity_log.causer_id', auth()->user()->id)
        ->orderBy('activity_log.created_at', 'desc')
        ->get();
            return $this->success($activityLogs,200,'all Activity Logs');
        }
    }
    // get file from storage
    public function getFile($file_name)
    {
        $file_path = storage_path('app/public/'.$file_name);
        if(file_exists($file_path)){
            return response()->download($file_path);
        }else{
            return $this->error('File not found', 404);
        }
    }
}
