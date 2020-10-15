<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Models\AccessControlList;
use Cache;
use App\Models\Suggestion;
use App\Models\User;

use DB;

class SuggestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:admin,suggestion')->only('index', 'show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function suggestion(){
        $active_class = "new";
        if(isset($_GET['tab'])) $active_class=$_GET['tab'];
        $new_suggestion_count = Suggestion::where('status','N')->count();
        $suggestions = Suggestion::orderBy('created_at','desc')->get();

        return view('admin.suggestion',compact('new_suggestion_count','suggestions','active_class'));
    }

    public function updateStarred(){
        DB::transaction(function(){
            $suggestion = Suggestion::find(Input::get('id'));
            $suggestion->update_by = auth()->user()->username;
            $suggestion->is_starred = Input::get('is_starred');

            if(!$suggestion->save()){
                return response()->json(array(
                    'success'       => false, 
                    'msg'           => "Unable to update suggestion", 
                ), 200);
            }
        });
        return response()->json(array(
            'success'       => true, 
            'msg'           => "Suggestion has been updated!", 
        ), 200);
    }

    public function updateAsRead(Request $request) {
        foreach ($request->add_to_read as $key => $value) {
            $suggestion = Suggestion::find($value);
            $suggestion->update_by = auth()->user()->username;
            $suggestion->status = 'R';

            if(!$suggestion->save()){
                return response()->json(array(
                    'success'       => false, 
                    'msg'           => "Unable to update suggestion", 
                ), 200);
            }
        }

        return response()->json(array(
            'success'       => true, 
            'msg'           => "Suggestion/s marked as read!", 
        ), 200);
    }

    public function updateAsUnread(Request $request) {
        foreach ($request->add_to_unread as $key => $value) {
            $suggestion = Suggestion::find($value);
            $suggestion->update_by = auth()->user()->username;
            $suggestion->status = 'N';

            if(!$suggestion->save()){
                return response()->json(array(
                    'success'       => false, 
                    'msg'           => "Unable to update suggestion", 
                ), 200);
            }
        }

        return response()->json(array(
            'success'       => true, 
            'msg'           => "Suggestion/s marked as unread!", 
        ), 200);
    }


    public function getInfo($id) {
        $suggestion = Suggestion::find($id);
        $suggestion->update_by = auth()->user()->username;
        $suggestion->status = 'R';
        if(!$suggestion->save()){
            return response()->json(array(
                'success'       => false, 
                'msg'           => "Unable to open suggestion", 
            ), 200);
        }

        return response()->json(array(
            'success'       => true, 
            'title'           => $suggestion->title, 
            'description'           => $suggestion->description, 
        ), 200);
    }



}
