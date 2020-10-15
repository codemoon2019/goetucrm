<?php

namespace App\Http\Controllers\Developers;

use App\Models\Access;
use App\Models\ApiKey;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Developers\ApiKeyStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:developers,view api keys')->only('index');
        $this->middleware('access:developers,create api keys')->only('store');
    }

    public function delete($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $apiKey->status = ApiKey::STATUS_DELETED;
        $apiKey->save();

        return redirect(route('developers.apiKeys.index'))->with([
            'success' => 'Successfully deleted API Key'
        ]);
    }

    public function index()
    {
        if (Access::hasPageAccess('admin', 'super admin access', true)) {
            $apiKeys = ApiKey::with('user')->active()->get();
            $userGroups = User::isActive()
                ->with('partnerCompany')
                ->whereRaw("FIND_IN_SET('4', user_type_id) <> 0")
                ->orWhereRaw("FIND_IN_SET('5', user_type_id) <> 0")
                ->orWhereRaw("FIND_IN_SET('11', user_type_id) <> 0")
                ->orWhereRaw("FIND_IN_SET('6', user_type_id) <> 0")
                ->orWhereRaw("FIND_IN_SET('13', user_type_id) <> 0")
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
                ->sortBy('partnerCompany.company_name')
                ->groupBy('company_id');
        } else {
            $apiKeys = auth()->user()->apiKeys()->active()->get();
            $userGroups = [];
        }

        return view('developers.apiKeys.index')->with([
            'apiKeys' => $apiKeys,
            'key' => Str::random(15),
            'userGroups' => $userGroups,
        ]);
    }

    public function store(ApiKeyStoreRequest $request)
    {
        $user = User::findOrFail($request->user_id ?? auth()->user()->id);
        $apiKey = ApiKey::create([
            'project_name' => $request->project_name,
            'key' => $request->key,
            'note' => $request->note,
            'user_id' => $user->id,
        ]);

        return redirect(route('developers.apiKeys.index'))->with([
            'success' => 'Successfully created API Key'
        ]);
    }
}
