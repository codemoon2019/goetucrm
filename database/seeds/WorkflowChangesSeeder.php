<?php

use App\Models\TicketHeader;
use App\Models\ProductOrder;
use App\Models\SubTaskTemplateDetail as SubtaskTemplate;
use App\Models\SubTaskDetail as Subtask;
use App\Models\SubTaskHeader as Task;
use App\Services\Workflow\TicketGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkflowChangesSeeder extends Seeder
{
    public function run()
    {
        TicketHeader::where('assignee', '[]')->update([
            'assignee' => null,
        ]);

        TicketHeader::where('priority', null)->update([
            'priority' => 'H',
        ]);

        $ticketHeaders = TicketHeader::all();
        foreach ($ticketHeaders as $ticketHeader) {
            $ticketHeader->assignee = 
                json_decode($ticketHeader->assignee)[0] ?? 
                $ticketHeader->assignee;

            $ticketHeader->save();
        }

        SubtaskTemplate::where('assignee', '[]')->update([
            'assignee' => null,
        ]);

        SubtaskTemplate::where('ticket_priority_code', null)->update([
            'ticket_priority_code' => 'H',
        ]);

        SubtaskTemplate::where('prerequisite', '')->update([
            'prerequisite' => null,
        ]);

        Subtask::where('assignee', '[]')->update([
            'assignee' => null,
        ]);

        Subtask::where('prerequisite', '')->update([
            'prerequisite' => null,
        ]);

        Subtask::where('ticket_priority_code', null)->update([
            'ticket_priority_code' => 'H',
        ]);

        Subtask::where('status', null)->update([
            'ticket_priority_code' => 'H',
        ]);

        ProductOrder::whereHas('partner', function($query) {
            $query->whereDoesntHave('connectedUser');
        })->delete();

        Task::whereDoesntHave('productOrder')->delete();
        
        $subtasks = Subtask::all();
        foreach ($subtasks as $subtask) {
            $subtask->assignee = 
                json_decode($subtask->assignee)[0] ?? 
                $subtask->assignee;

            $subtask->save();
        }

        $tasks = Task::with('productOrder')->get();
        foreach ($tasks as $task) {
            $ticketGenerator = new TicketGenerator($task);
            $count = null;
            do {
                $count = $ticketGenerator->generateTickets();

                echo "Task #$task->id - Generated {$count} tickets\n";
            } while ($count != 0);
        }
    }
}
