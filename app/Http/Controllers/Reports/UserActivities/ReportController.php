<?php

namespace App\Http\Controllers\Reports\UserActivities;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\UserActivities\GenerateReportRequest;
use App\Models\UserType;
use App\Models\User;
use App\Services\Reports\UserActivity\ExcelReportFormatter;
use App\Services\Reports\UserActivity\ReportFormatterFactory;
use App\Services\Reports\UserActivity\ReportGenerator;
use App\Services\Reports\UserActivity\WebReportFormatter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:reports,user activities report')->only('index');
    }
    
    public function index()
    {
        $userTypes = UserType::isActive()
            ->isSystem()
            ->orderBy('description')
            ->find([4, 5, 6, 8, 11, 13]);

        $departmentsUsers = User::isActive()->analyticsUsers()->orderBy('first_name')->get();
        $userTypesUsers = User::isActive()
            ->analyticsUsers()
            ->with('partner.merchantBranches.connectedUser')
            ->whereHas('partner', function($query) {
                $query->where('partner_type_id', '<>', 9);
            })
            ->where('company_id', '<>', '-1')
            ->whereCompany(auth()->user()->company_id)
            ->orderBy('first_name')
            ->get();

        $departments = UserType::isActive()
            ->isNonSystem()
            ->with(['users' => function($query) {
                $query->orderBy('first_name');
            }])
            ->with('partnerCompany')
            ->where('company_id', '<>', '-1')
            ->whereCompany(auth()->user()->company_id)
            ->orderBy('description')
            ->get();

        $departmentGroups = $departments
            ->sortBy('partnerCompany.company_name')
            ->groupBy('company_id');

        return view('reports.userActivities.index')->with([
            'departments' => $departments,
            'departmentsUsers' => $departmentsUsers,
            'departmentGroups' => $departmentGroups,
            'userTypes' => $userTypes,
            'userTypesUsers' => $userTypesUsers,
        ]);
    }

    public function report(
        GenerateReportRequest $request,
        ReportFormatterFactory $rfFactory)
    {
        $reportGenerator = new ReportGenerator(
            $request->display_by,
            $request->resource_type, 
            $request->resource_id, 
            $request->start_date, 
            $request->end_date,
            $request->company_id ?? null);
        
        $report = $reportGenerator->generate();
        $reportFormatter = $rfFactory->make($request->report_type);

        return $reportFormatter->format($report);
    }
}
