<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\Templates\SaveWorkflowRequest;
use App\Models\Product;
use App\Models\SubTaskTemplateHeader as TaskTemplate;
use App\Services\Products\Workflow\WorkflowDependencies;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkflowTemplateController extends Controller
{
    public function show(Request $request)
    {
        $productsGroup = Product::select('id', 'name', 'company_id')
            ->with('partnerCompany:id,company_name,partner_id')
            ->isActive()
            ->whereHas('partnerCompany')
            ->where('parent_id', -1)
            ->whereCompany(Auth::user()->company_id)
            ->get()
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id');
        
        return view('products.workflowTemplateN')->with([
            'productId' => $request->product_id,
            'productsGroup' => $productsGroup
        ]);
    }

    /** 
     * @todo Make asynchronous
     */
    public function save(SaveWorkflowRequest $request)
    {
        $identifierArray = ['product_id' => $request->product_id];
        $taskTemplate = TaskTemplate::updateOrCreate($identifierArray, [
            'name' => $request->task_name,
            'description' => $request->task_description,
            'create_by' => Auth::user()->username,
            'update_By' => Auth::user()->username,
        ]);

        $taskTemplate->subtaskTemplates()->delete();
        $taskTemplate->subtaskTemplates()->createMany($request->subtasks);

        $url  = route('products.templates.workflow.show');
        $url .= "?product_id={$taskTemplate->product_id}";

        return redirect($url);
    }
    
    public function getProductWorkflowTemplate($productId) : JsonResponse
    {
        $product = Product::with('subProducts')
            ->withTicketAssignees()
            ->with('taskTemplate')
            ->find($productId);

        $product->display_picture = Storage::url($product->display_picture);
        $workflowDependencies = new WorkflowDependencies($product);

        return response()->json([
            'product' => $product,
            'workflow_dependencies' => $workflowDependencies,
        ]);
    }
}
