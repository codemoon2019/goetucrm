<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Requests\Tickets\SaveTicketFaqRequest;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;

class TicketFaqController extends Controller
{
    public function show()
    {
        $ticketFaq = SystemSetting::where('set_code', 'ticket_faq')->first();
        $ticketFaq = optional($ticketFaq)->set_value ?? '';

        return view('tickets.show_faq')->with([
            'ticketFaq' => $ticketFaq
        ]);
    }

    public function edit()
    {
        $ticketFaq = SystemSetting::where('set_code', 'ticket_faq')->first();
        $ticketFaq = optional($ticketFaq)->set_value ?? '';

        return view('tickets.edit_faq')->with([
            'ticketFaq' => $ticketFaq
        ]);
    }

    public function save(SaveTicketFaqRequest $request)
    {
        $systemSetting = SystemSetting::firstOrCreate(
            ['set_code' => 'ticket_faq'], 
            ['name' => 'Ticket FAQ']
        );

        $systemSetting->set_value = $request->ticket_faq;
        $systemSetting->save();

        return response()->json([], 200);
    }
}