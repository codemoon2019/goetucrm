<?php

use App\Models\TicketType as TicketIssueType;
use App\Models\TicketReason;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateTicketConfigurationSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function() {
            $titGroups = TicketIssueType::whereNull('product_id')
                ->get()
                ->groupBy('company_id');

            foreach ($titGroups as $companyId => $ticketIssueTypes) {
                $department = UserType::where('company_id', $companyId)
                    ->where('description', 'Support')
                    ->first();

                $departmentId = optional($department)->id ?? optional(UserType::where('company_id', $companyId)
                    ->first())
                    ->id;

                if ($departmentId == null)
                    continue;
                
                $titDescriptions = $ticketIssueTypes->pluck('description')->all();
                $defaultTit = config('default')['ticket_issue_types'];

                foreach ($defaultTit as $ticketIssueType) {
                    if (in_array($ticketIssueType['description'], $titDescriptions)) {
                        $description = $ticketIssueType['description'];
                        $tit = $ticketIssueTypes->first(function($tit) use ($description) {
                            return $tit->description == $description;
                        });

                        $ticketReasons = $tit->ticketReasons;
                        $trDescriptions = $ticketReasons
                            ->pluck('description')
                            ->all();


                        $defaultTr = $ticketIssueType['ticket_reasons'];
                        foreach ($defaultTr as $ticketReason) {
                            if (! in_array($ticketReason['description'], $trDescriptions)) {

                                TicketReason::create([
                                    'description' => $ticketReason['description'],
                                    'company_id' => $companyId,
                                    'default_workflow' => $ticketReason['default_workflow'] ?? null,
                                    'department_id' => $departmentId,
                                    'ticket_type_id' => $tit->id,
                                    'ticket_priority_code' => $ticketReason['ticket_priority_code'],
                                    'create_by' => 'SYSTEM',
                                    'update_by' => 'SYSTEM',
                                ]);
                            } else {
                                $description = $ticketReason['description'];
                                $tr = $ticketReasons->first(function($tr) use ($description) {
                                    return $tr->description == $description;
                                });

                                $tr->processed = true;
                            }
                        } 

                        $trsToDelete = $ticketReasons->filter(function($tr) {
                            return !isset($tr->processed);
                        });

                        foreach ($trsToDelete as $tr) {
                            $tr->status = 'D';
                            $tr->save();
                        }

                        $tit->processed = true;
                        continue;
                    }

                    $ticketIssueTypeModel = TicketIssueType::create([
                        'description' => $ticketIssueType['description'],
                        'default_workflow' => $ticketIssueType['default_workflow'] ?? null,
                        'company_id' => $companyId,
                        'create_by' => 'SYSTEM',
                        'update_by' => 'SYSTEM',
                    ]);

                    $ticketReasonsData = collect($ticketIssueType['ticket_reasons']);
                    $ticketReasonsData = $ticketReasonsData
                        ->map(function($ticketReason) use ($companyId, $departmentId){
                            $ticketReason['company_id'] = $companyId;
                            $ticketReason['department_id'] = $departmentId;;
                            $ticketReason['create_by'] = 'SYSTEM';
                            $ticketReason['update_by'] = 'SYSTEM';

                            return $ticketReason;
                        })
                        ->toArray();

                    $ticketIssueTypeModel->ticketReasons()->createMany($ticketReasonsData);
                }

                $titsToDelete = $ticketIssueTypes->filter(function($tit) {
                    return !isset($tit->processed);
                });
    
                foreach ($titsToDelete as $tit) {
                    $tit->status = 'D';
                    $tit->save();
                }
            }

            
        });
    }
}
