<?php

namespace App\Http\Controllers\CompanySettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanySettings\CreateTicketIssueTypeRequest;
use App\Http\Requests\CompanySettings\CreateTicketReasonRequest;
use App\Models\Product;
use App\Models\TicketReason;
use App\Models\TicketType as TicketIssueType;
use App\Services\CompanySettings\TicketConfigDependencies;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketConfigController extends Controller
{
    public function deleteTicketReason(
        int $productId,
        int $ticketReasonId) : JsonResponse
    {
        $ticketReason = TicketReason::find($ticketReasonId);
        $ticketReason->status = TicketReason::STATUS_DELETED;
        $ticketReason->save();

        return response()->json(null, 204);
    }

    public function deleteTicketIssueType(
        int $productId,
        int $ticketIssueTypeId) : JsonResponse
    {
        $ticketIssueType = TicketIssueType::find($ticketIssueTypeId);
        $ticketIssueType->status = TicketIssueType::STATUS_DELETED;
        $ticketIssueType->save();

        $ticketIssueType->ticketReasons()->update([
            'status' => TicketReason::STATUS_DELETED
        ]);

        return response()->json(null, 204);
    }

    public function getTicketConfiguration(int $productId) : JsonResponse
    {
        $productColumns = ['id', 'name', 'code', 'display_picture', 'company_id'];
        $product = Product::select($productColumns)
            ->with(['ticketIssueTypes' => function($query) {
                $query
                    ->select(['id', 'description', 'product_id', 'status'])
                    ->with(['ticketReasons' => function($query) {
                        $query
                            ->with('department')
                            ->isActive()
                            ->orderBy('description');
                    }])
                    ->isActive()
                    ->orderBy('description');
            }])
            ->withTicketAssignees()
            ->findOrFail($productId);

        $product->departmentsGroups = $product->userTypes->groupBy('company_id');
        $globalIssueTypes = TicketIssueType::select(['id', 'description', 'product_id'])
            ->with(['ticketReasons' => function ($query) {
                $query
                    ->with('department')
                    ->isActive()
                    ->orderBy('description');
            }])
            ->where('company_id', $product->company_id)
            ->where('product_id', null)
            ->isActive()
            ->orderBy('description')
            ->get();

        $product->display_picture = Storage::url($product->display_picture);
        $product->ticket_issue_types = $product
            ->ticketIssueTypes
            ->merge($globalIssueTypes);

        unset($product->ticketIssueTypes);

        return response()->json($product->toArray(), 200);
    }

    public function showConfiguration(int $companyId)
    {
        $ticketConfigDependencies = new TicketConfigDependencies($companyId);
        $products = Product::select(['id', 'name'])
            ->isActive()
            ->where('parent_id', -1)
            ->whereCompany($companyId)
            ->orderBy('name')
            ->get();

        $productColumns = ['id', 'name', 'code', 'display_picture'];
        $productsWithConfig = Product::select($productColumns)
            ->where(function($query) {
                $query
                    ->whereHas('ticketIssueTypes')
                    ->orWhereHas('ticketReasons');
            })
            ->whereCompany($companyId)
            ->orderBy('name')
            ->get();

        return view('admin.companysettings.ticketConfig')->with([
            'companyId' => $companyId,
            'products' => $products,
            'productsWithConfig' => $productsWithConfig,
            'tcDependencies' => $ticketConfigDependencies
        ]);
    }

    public function storeTicketIssueType(
        int $companyId,
        int $productId,
        CreateTicketIssueTypeRequest $request) : JsonResponse
    {

        $ticketIssueType = TicketIssueType::create([
            'description' => $request->description,
            'company_id' => $companyId,
            'product_id' => $productId,
            'create_by' => Auth::user()->username,
            'update_by' => Auth::user()->username,
        ]);

        return response()->json($ticketIssueType->toArray(), 201);
    }

    public function storeTicketReason(
        int $companyId,
        int $productId,
        CreateTicketReasonRequest $request) : JsonResponse
    {
        $ticketReason = TicketReason::create([
            'description' => $request->description,
            'company_id' => $companyId,
            'department_id' => $request->department,
            'product_id' => $productId,
            'ticket_type_id' => $request->ticket_issue_type,
            'ticket_priority_code' => $request->ticket_priority,
            'create_by' => Auth::user()->username,
            'update_by' => Auth::user()->username,
        ]);

        $ticketReason->load('department');

        return response()->json($ticketReason->toArray(), 201);
    }

    public function updateTicketIssueType(
        int $ticketIssueTypeId,
        EditTicketIssueTypeRequest $request) : JsonResponse
    {
        $ticketReason = TicketReason::findOrFail($ticketIssueTypeId);
        $ticketReason->update([
            'description' => $request->description,
            'update_by' => Auth::user()->username,
        ]);

        return response()->json($ticketIssueType->toArray(), 201);
    }

    public function updateTicketReason(
        int $ticketReasonId,
        EditTicketReasonRequest $request) : JsonResponse
    {
        $ticketReason = TicketReason::find($ticketReasonId);
        $ticketReason->update([
            'description' => $request->description,
            'department_id' => $request->department,
            'ticket_type_id' => $request->ticket_issue_type,
            'ticket_priority_code' => $request->ticket_priority,
            'update_by' => Auth::user()->username,
        ]);

        return response()->json($ticketReason->toArray(), 201);
    }
}