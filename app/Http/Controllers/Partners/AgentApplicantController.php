<?php

namespace App\Http\Controllers\Partners;

use App\Models\AgentApplicant;
use App\Models\EmailOnQueue;
use App\Models\Partner;
use App\Models\PartnerCompany;
use App\Models\PartnerContact;
use App\Models\PartnerMailingAddress;
use App\Models\PartnerType;
use App\Models\User;
use App\Models\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentApplicantController extends Controller
{
    public function index()
    {
        return view('partners.agent_applicants.index');
    }

    public function getAgentApplicants()
    {
        $queryBuilder = AgentApplicant::with('country', 'state');

            if (isset(request()->filter)) {
            switch (request()->filter) {
                case AgentApplicant::AGENT_APPLICANT_PENDING:
                    $agentApplicants = $queryBuilder->pending()->get();
                    break;

                case AgentApplicant::AGENT_APPLICANT_APPROVED:
                    $agentApplicants = $queryBuilder->approved()->get();
                    break;
                    
                case AgentApplicant::AGENT_APPLICANT_DISAPPROVED:
                    $agentApplicants = $queryBuilder->disapproved()->get();
                    break;
            }

        } else {
            $agentApplicants = $queryBuilder->get();
        }

        return datatables()->collection($agentApplicants)
            ->addColumn('checkbox', function($agentApplicant) {
                return "<input type='checkbox' name='agent_applicant_ids[]' value='{$agentApplicant->id}' />";
            })
            ->addColumn('status', function($agentApplicant) {
                if ($agentApplicant->delete_by != null) {
                    return 'Disapproved';
                } else if ($agentApplicant->approved_by != null) {
                    return 'Approved';
                } else {
                    return 'Pending';
                }
            })
            ->editColumn('mobile_number', function($agentApplicant) {
                $code = $agentApplicant->country->country_calling_code;
                $mobileNumber = $agentApplicant->mobile_number;

                return $code . $mobileNumber;
            })
            ->editColumn('business_address', function($agentApplicant) {
                $completeAddress  = $agentApplicant->business_address;
                $completeAddress .= ", {$agentApplicant->city}";
                $completeAddress .= ", {$agentApplicant->zip}";
                $completeAddress .= ", {$agentApplicant->state->name}";
                $completeAddress .= ", {$agentApplicant->country->name}";

                return $completeAddress;
            })
            ->editColumn('source', function($agentApplicant) {
                return $agentApplicant->source;
            })
            ->rawColumns(['checkbox', 'status'])
            ->make(true);
    }

    public function approve(Request $request)
    {
        $agentApplicantIds = $request->agent_applicant_ids;

        if ($request->ajax()) {

            DB::beginTransaction();
            try {
                foreach ($agentApplicantIds as $id) {
                    $agentApplicant = AgentApplicant::with('country', 'state')
                        ->find($id);
                    $partnerType = PartnerType::select('id')
                        ->where('name', 'AGENT')
                        ->firstOrFail();

                    $maxCount = Partner::where('partner_type_id', 
                        $partnerType->id)->count() + 1;
                    $partnerIdReference = 
                        $partnerType->initial . (100000+$maxCount);

                    $iconPaymentPartnerId = -1;
                    $iconPaymentCompany = PartnerCompany::where('company_name', 'Icon Payments')->first();
                    if (!is_null($iconPaymentCompany))
                        $iconPaymentPartnerId = $iconPaymentCompany->partner_id;
        
                    $partner = Partner::create([
                        'partner_type_id' => $partnerType->id,
                        'original_partner_type_id' => $partnerType->id,
                        'parent_id' => $iconPaymentPartnerId,
                        'original_parent_id' => $iconPaymentPartnerId,
                        'partner_id_reference' => $partnerIdReference,
                        'status' => 'A',
                        'source' => 'External',
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username
                    ]);
        
                    PartnerCompany::create([
                        'partner_id' => $partner->id,
                        'company_name' => $agentApplicant->company,
                        'address1' => $agentApplicant->business_address,
                        'city' => $agentApplicant->city,
                        'state' => $agentApplicant->state->abbr,
                        'zip' => $agentApplicant->zip,
                        'country' => $agentApplicant->country->name,
                        'email' => $agentApplicant->email_address,
                        'update_by' => auth()->user()->username,
                        'mobile_number' => $agentApplicant->mobile_number,
                        'ownership' => 'OTHER',
                        'country_code' => $agentApplicant->country->country_calling_code
                    ]);

                    PartnerContact::create([
                        'partner_id' => $partner->id,
                        'first_name' => $agentApplicant->first_name,
                        'last_name' => $agentApplicant->last_name,
                        'email' => $agentApplicant->email,
                        'mobile_number' => $agentApplicant->mobile_number,
                        'address1' => $agentApplicant->business_address,
                        'city' => $agentApplicant->city,
                        'state' => $agentApplicant->state->abbr,
                        'zip' => $agentApplicant->zip,
                        'country' => $agentApplicant->country->name,
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username,
                        'company_name' => $agentApplicant->company,
                        'is_original_contact' => 1,
                        'country_code' => $agentApplicant->country->country_calling_code,
                        'ownership_percentage' => 0,
                    ]);

                    PartnerMailingAddress::create([
                        'partner_id' => $partner->id,
                        'address' => $agentApplicant->business_address,
                        'city' => $agentApplicant->city,
                        'state' => $agentApplicant->state->abbr,
                        'zip' => $agentApplicant->zip,
                        'country' => $agentApplicant->country->name,
                        'country_code' => $agentApplicant->country->country_calling_code,
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username,
                    ]);

                    $userTypeId = UserType::select('id')
                        ->where('description', 'AGENT')
                        ->firstOrFail()
                        ->id;

                    $password = rand(1111111, 99999999);
                    User::create([
                        'username' => $partnerIdReference,
                        'password' => bcrypt($password),
                        'first_name' => $agentApplicant->first_name,
                        'last_name' => $agentApplicant->last_name,
                        'email_address' => $agentApplicant->email_address,
                        'mobile_number' => $agentApplicant->mobile_number,
                        'user_type_id' => $userTypeId,
                        'reference_id' => -1,
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username,
                        'status' => 'A',
                        'is_agent' => 1,
                        'country' => $agentApplicant->country->name,
                        'country_code' => $agentApplicant->country->country_calling_code,
                    ]);

                    $message  = "<h1>Hi {$agentApplicant->first_name} {$agentApplicant->last_name},</h1>";
                    $message .= "<p>";
                    $message .=   "You have successfully registered to GoETU platform. ";
                    $message .=   "Please see details below for your credentials.";
                    $message .= "</p>";
                    $message .= "<p>Password: <strong>{$password}</strong></p>";
                    $message .= "<p>Thank you, <br /> GoETU Team</p>";

                    $emailOnQueue = EmailOnQueue::create([
                        'subject' => '[GoETU] Account Creation',
                        'body' => $message,
                        'email_address' => $agentApplicant->email_address,
                        'create_by' => auth()->user()->username,
                        'is_sent' => 0,
                        'created_at' => $agentApplicant->created_at,
                        'updated_at' => $agentApplicant->created_at,
                    ]);
                }

                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();

                return response()->json([
                    'success' => false,
                    'message' => 'There was an error processing your Request'
                ], 200);
            }

            AgentApplicant::whereIn('id', $agentApplicantIds)
                ->update(['approved_by' => auth()->user()->id]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully approved Agent Applicants'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bad Request'
        ], 400);
    }

    public function disapprove(Request $request)
    {
        $agentApplicantIds = $request->agent_applicant_ids;

        if ($request->ajax()) {
            $agentApplicants = AgentApplicant::whereIn('id', $agentApplicantIds)
                ->get();

            try {
                foreach ($agentApplicants as $agentApplicant) {
                    $agentApplicant->delete();
                }

                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();

                return response()->json([
                    'success' => false,
                    'message' => 'There was an error processing your Request'
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => 'Successfully disapproved Agent Applicants'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bad Request'
        ], 400);
    }

    public function restore(Request $request)
    {
        $agentApplicantIds = $request->agent_applicant_ids;

        if ($request->ajax()) {
            $agentApplicants = AgentApplicant::onlyTrashed()
                ->whereIn('id', $agentApplicantIds)
                ->get();

            try {
                foreach ($agentApplicants as $agentApplicant) {
                    $agentApplicant->restore();
                }

                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();

                return response()->json([
                    'success' => false,
                    'message' => 'There was an error processing your Request'
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => 'Successfully restored Agent Applicants'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bad Request'
        ], 400);
    }
}
