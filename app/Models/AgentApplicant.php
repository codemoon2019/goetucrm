<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentApplicant extends Model
{  
    use SoftDeletes;

    protected $tables = 'agent_applicants';
    protected $dates = ['deleted_at'];

    /** Constants */
    const AGENT_APPLICANT_PENDING = 0;
    const AGENT_APPLICANT_APPROVED = 1;
    const AGENT_APPLICANT_DISAPPROVED = 2;

    /** Scopes */
    public function scopePending($query)
    {
        return $query->where('approved_by', null);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved_by', '!=', null);
    }

    public function scopeDisapproved($query)
    {
        return $query->onlyTrashed();
    }

    /** Relationships */
    public function country()
    {
        return $this->belongsTo('App\\Models\\Country', 'country_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo('App\\Models\\State', 'state_id', 'id');
    }
}
