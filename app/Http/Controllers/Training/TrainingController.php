<?php

namespace App\Http\Controllers\Training;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\TrainingModule;
use App\Models\Product;
use DB;

class TrainingController extends Controller
{
    public function list(){
        $trainings = Training::where('status','A')->get();
        return view("training.module",compact('trainings'));
    }
    public function module($id){
        $trainings = Training::where('status','A')->get();
        $module = Training::where('status','A')->where('id',$id)->first();
        if(!isset($module))
        {
            return redirect('/training/training_list')->with('failed','Cannot find training module');
        }
        return view("training.module",compact('trainings','module'));
    }
    public function setup(){
        $trainings = Training::where('status','A')->get();
        foreach($trainings as $training){
            $training->productname = isset($training->product->name) ? $training->product->name : ''; 
        }
        return view("training.setup",compact('trainings'));
    }
    public function setupEdit($id){
        $training = Training::where('status','A')->where('id',$id)->first();
        if(!isset($training))
        {
            return redirect('/training/setup')->with('failed','Cannot find training module');
        }
        if(auth()->user()->company_id == -1){
            $products = Product::where('status','A')->where('parent_id',-1)->get();
        }else{
            $products = Product::where('status','A')->where('parent_id',-1)->where('company_id',auth()->user()->company_id)->get();
        }
        
        $postUrl = "/training/updateModule/".$id;
        $label = 'Edit';
        return view("training.setupEdit",compact('training','products','postUrl','label'));
    }
    public function setupCreate(){
        if(auth()->user()->company_id == -1){
            $products = Product::where('status','A')->where('parent_id',-1)->get();
        }else{
            $products = Product::where('status','A')->where('parent_id',-1)->where('company_id',auth()->user()->company_id)->get();
        }
        $postUrl = "/training/createModule";
        $label = 'New';
        return view("training.setupEdit",compact('products','postUrl','label'));
    }

    public function accessControl(){
        return view("training.accessControl");
    }
    public function accessControlEdit(){
        return view("training.accessControlEdit");
    }
    public function updateModule($id,Request $request){
        DB::transaction(function() use ($id,$request){
            $training = Training::find($id);
            $training->name = $request->training_name;
            $training->description = $request->training_desc;
            $training->product_id = $request->training_product;
            $training->update_by = auth()->user()->username;
            $training->save();

            $deletedRows = TrainingModule::where('training_id', $id)->delete();
            $details = $request->txtModuleList;
            $details = json_decode($details);
            foreach ($details as $detail) {
                $module = new TrainingModule;
                $module->training_id = $id;
                $module->name = $detail->name;
                $module->description = $detail->description;
                $module->module_code = $detail->code;
                $module->save();
            }
        });
        return redirect('/training/setup')->with('success','Module has been updated!');
    } 

    public function createModule(Request $request){
        DB::transaction(function() use ($request){
            $training = new Training;
            $training->name = $request->training_name;
            $training->description = $request->training_desc;
            $training->product_id = $request->training_product;
            $training->status = 'A';
            $training->update_by = auth()->user()->username;
            $training->save();

            $details = $request->txtModuleList;
            $details = json_decode($details);
            foreach ($details as $detail) {
                $module = new TrainingModule;
                $module->training_id = $training->id;
                $module->name = $detail->name;
                $module->description = $detail->description;
                $module->module_code = $detail->code;
                $module->save();
            }
        });
        return redirect('/training/setup')->with('success','Module has been created!');
    } 

}
