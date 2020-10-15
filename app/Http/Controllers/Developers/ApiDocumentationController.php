<?php

namespace App\Http\Controllers\Developers;

use App\Models\Access;
use App\Models\ApiKey;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Developers\ApiKeyStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiDocumentationController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:developers,view api documentation')->only('index');
    }

    public function index()
    {
        auth()->user()->session_id = session()->getId();
        auth()->user()->save();
        
        return redirect('http://lv2.goetu.com?session_id=' . auth()->user()->session_id);
    }
}
