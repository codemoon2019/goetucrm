<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\BaseService;
use App\Contracts\Constant;
use App\Models\Company;
use App\Services\Utility\DataTableUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends BaseController
{

    private $service;

    /**
     * use BaseService
     *
     * CompanyController constructor.
     * @param BaseService $baseService
     */
    public function __construct(BaseService $baseService)
    {
        $this->service = $baseService;
        $this->middleware('access:company,view')->only('index', 'view');
        $this->middleware('access:company,add')->only('create', 'store');
        $this->middleware('access:company,edit')->only('edit', 'update');
        $this->middleware('access:company,delete')->only('destroy');
    }

    /**
     * List all entries
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index()
    {
        if (request()->expectsJson()) {
            $filterColumn = [];
            $orderColumn = DataTableUtility::orderColumn(request()->all());
            $searchColumn = DataTableUtility::searchColumn(request()->all());
            $pageInfo = ["pageSize" => request()->get("perPage")];
            $pageData = $this->service->fetch(Company::class, $pageInfo, $filterColumn, $orderColumn, $searchColumn, []);
            return response()->json($pageData);
        } else {
            $this->initSetting(Constant::MODULE_ADMIN_COMPANY);
            return view("admin.company.list");
        }
    }

    /**
     * Show create form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view("admin.company.create");
    }

    /**
     * Show edit form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $company = Company::find($id);
        if (!isset($company)) {
            abort(404);
        }

        return view("admin.company.edit", compact("company"));
    }

    /**
     * Destroy
     *
     * @param $id
     */
    public function destroy($id)
    {
        Company::destroy($id);
    }

    /**
     * Store company information
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validateStore($request);

        // Company Logo
        $companyLogo = "";
        if (!$request->has("company_logo_path_value") && $request->hasFile("company_logo_path")) {
            $logFile = $request->file("company_logo_path");
            $companyLogo = Storage::putFileAs("images", $logFile, $logFile->getClientOriginalName(), "public");
        }

        $data = [
            "company_name" => $request->get("name"),
            "powered_by_link" => $request->get("powered_by_link"),
            "logo_path" => $companyLogo
        ];

        $this->service->createModel(Company::class, $data);

        return response()->redirectTo('/admin/company');

    }

    /**
     * Update company information
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validateUpdate($request, $id);

        // Company Logo
        $companyLogo = "";
        if (!$request->has("company_logo_path_value") && $request->hasFile("company_logo_path")) {
            $logFile = $request->file("company_logo_path");
            $companyLogo = Storage::putFileAs("images", $logFile, $logFile->getClientOriginalName(), "public");
        }

        $data = [
            "company_name" => $request->get("name"),
            "powered_by_link" => $request->get("powered_by_link"),
            "logo_path" => $companyLogo
        ];

        $this->service->updateModel(Company::class, $id, $data);

        return back();

    }

    /**
     * Validate Store Request
     *
     * @param $request
     */
    protected function validateStore(Request $request)
    {
        $this->validate($request, [
                "name" => "required|max:50",
                "powered_by_link" => "required|max:120",
                "company_logo_path" => "required"
            ]
        );
    }

    /**
     * Validate Update Request
     *
     * @param Request $request
     * @param $id
     */
    protected function validateUpdate(Request $request, $id)
    {
        $company = Company::find($id);
        if (!isset($company)) {
            abort(404);
        }

        $this->validate($request, [
                "name" => "required|max:50",
                "powered_by_link" => "required|max:120",
                "company_logo_path" => "required"
            ]
        );
    }

}
