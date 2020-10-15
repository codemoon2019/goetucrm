<?php

namespace App\Traits;

trait ActorTrait
{
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'create_by', 'username')
                    ->withDefault([
                        'image' => '/images/agent.png',
                        'first_name' => 'GoETU',
                        'last_name' => 'Bot'
                    ]);
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\User', 'update_by', 'username')
            ->withDefault([
                'image' => '/images/agent.png',
                'first_name' => 'GoETU',
                'last_name' => 'Bot'
            ]);
    }
}