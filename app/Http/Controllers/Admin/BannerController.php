<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Departments\DepartmentListService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Banners\BannerStoreRequest;
use App\Http\Requests\Banners\BannerUpdateRequest;
use App\Models\Banner;
use App\Models\BannerViewer;
use App\Models\PartnerCompany;
use App\Models\UserType;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:announcement,view')->only('index');
        $this->middleware('access:announcement,create')->only('create', 'store');
        $this->middleware('access:announcement,edit')->only('edit', 'update');
        $this->middleware('access:announcement,delete')->only('destroy');
    }

    public function close($id)
    {
        session()->push('read_banners', $id);
        return response()->json(null, 200);
    }

    public function create()
    {
        $bannerTypes = Banner::TYPES;
        $companies = PartnerCompany::whereHas('partner', function($query) {
                return $query->where('status', 'A')
                    ->where('partner_type_id', 7);
            })
            ->orderBy('company_name')
            ->get();

        $departmentGroups = UserType::isActive()
            ->isNonSystem()
            ->with('partnerCompany')
            ->orderBy('description')
            ->get()
            ->sortBy('partnerCompany.company_name')
            ->groupBy('company_id');

        $userGroups = User::isActive()
            ->with('partnerCompany')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->sortBy('partnerCompany.company_name')
            ->groupBy('company_id');

        return view('admin.banners.create')->with([
            'bannerTypes' => $bannerTypes,
            'companies' => $companies,
            'departmentGroups' => $departmentGroups,
            'userGroups' => $userGroups
        ]);
    }

    public function destroyMany(Request $request)
    {
        if (isset($request->banners)) {
            $banners = Banner::with('bannerViewers')
                ->whereIn('id', $request->banners)
                ->get();
                
            foreach ($banners as $banner) {
               $banner->update(['status' => Banner::STATUS_DELETED]);
               $banner->bannerViewers()->update([
                   'status' => BannerViewer::STATUS_DELETED
               ]);
            }
        }

        return redirect(route('admin.banners.index'))->with([
            'success' => 'Banner/s successfully deleted'
        ]);
    }

    public function edit($id)
    {
        $banner = Banner::with('bannerViewers')->find($id);
        $baseViewerType = $banner->bannerViewers()->first()->viewer_type == 'A' ? 'A' : 'S';

        $bannerTypes = Banner::TYPES;
        $companies = PartnerCompany::whereHas('partner', function($query) {
            return $query->where('status', 'A')
                ->where('partner_type_id', 7);
            })
            ->orderBy('company_name')
            ->get();
            
        $departmentGroups = UserType::isActive()
            ->isNonSystem()
            ->with('partnerCompany')
            ->orderBy('description')
            ->get()
            ->sortBy('partnerCompany.company_name')
            ->groupBy('company_id');

        $userGroups = User::isActive()
            ->with('partnerCompany')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->sortBy('partnerCompany.company_name')
            ->groupBy('company_id');

        return view('admin.banners.edit')->with([
            'banner' => $banner,
            'bannerTypes' => $bannerTypes,
            'baseViewerType' => $baseViewerType,
            'companies' => $companies,
            'departmentGroups' => $departmentGroups,
            'userGroups' => $userGroups
        ]);
    }

    public function index()
    {
        $banners = Banner::active()->get();
        $bannerTotalCount = Banner::active()->count();
        $bannerShowingCount = Banner::active()->showing()->count();
        $bannerUpcomingCount = Banner::active()->upcoming()->count();
        return view('admin.banners.index')->with([
            'banners' => $banners,
            'bannerTotalCount' => $bannerTotalCount,
            'bannerShowingCount' => $bannerShowingCount,
            'bannerUpcomingCount' => $bannerUpcomingCount,
            'bannerEndedCount' => $bannerTotalCount - 
                                  $bannerShowingCount -
                                  $bannerUpcomingCount
        ]);
    }
    
    public function store(BannerStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $banner = Banner::create([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'starts_at' => "{$request->starts_at}:00",
                'ends_at' => "{$request->ends_at}:00",
                'create_by' => auth()->user()->username,
                'update_by' => auth()->user()->username,
            ]);

            if ($request->viewer_type == 'A') {
                $bannerViewerData[] = [
                    'banner_id' => $banner->id,
                    'viewer_type' => 'A',
                    'viewer_id' => null,
                    'create_by' => auth()->user()->username,
                    'update_by' => auth()->user()->username,
                ];
            } else if ($request->viewer_type == 'S') {
                $bannerViewerData = $this->makeBannerViewerData($banner->id, $request);
            }

            $banner->bannerViewers()->createMany($bannerViewerData);
            DB::commit();

            $message = 'Banner successfully created';
            if (isset($request->create_another)) {
                return redirect(route('admin.banners.create'))->with([
                    'success' => $message
                ]);
            } else {
                return redirect(route('admin.banners.index'))->with([
                    'success' => $message
                ]);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect(route('admin.banners.create'))->with([
                'failed' => 'There was an error processing you request. ' . 
                            'Please try again later!'
            ]);
        }
    }

    public function update(BannerUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $banner = Banner::find($id);
            $banner->update([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'starts_at' => "{$request->starts_at}:00",
                'ends_at' => "{$request->ends_at}:00",
                'create_by' => auth()->user()->username,
                'update_by' => auth()->user()->username,
            ]);

            $banner->bannerViewers()->delete();
            if ($request->viewer_type == 'A') {
                $bannerViewerData[] = [
                    'banner_id' => $banner->id,
                    'viewer_type' => 'A',
                    'viewer_id' => null,
                    'create_by' => auth()->user()->username,
                    'update_by' => auth()->user()->username,
                ];

            } else if ($request->viewer_type == 'S') {
                $bannerViewerData = $this->makeBannerViewerData($banner->id, $request);
            }

            $banner->bannerViewers()->createMany($bannerViewerData);
            DB::commit();

            $message = 'Banner successfully updated';
            if (isset($request->continue_updating)) {
                return redirect(route('admin.banners.edit', $banner->id))->with([
                    'success' => $message
                ]);
            } else {
                return redirect(route('admin.banners.index'))->with([
                    'success' => $message
                ]);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect(route('admin.banners.edit', $banner->id))->with([
                'failed' => 'There was an error processing you request. ' . 
                            'Please try again later!'
            ]);
        }
    }

    /**
     * Private Functions
     */
    private function makeBannerViewerData($bannerId, Request $request)
    {
        foreach (BannerViewer::VIEWER_TYPES as $i => $bannerViewerType) {
            $bannerViewerType = strtolower($bannerViewerType);
            if (isset($request->$bannerViewerType)) {
                foreach ($request->$bannerViewerType as $bannerViewer) {
                    $bannerViewerData[] = [
                        'banner_id' => $bannerId,
                        'viewer_type' => $i,
                        'viewer_id' => $bannerViewer,
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username,
                    ];
                }
            }
        }

        return $bannerViewerData;
    }
}
