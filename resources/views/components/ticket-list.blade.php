<h3>
    <i class="fa fa-ticket"></i> &nbsp; {{ $description }} Priority Ticket List
</h3>

<form id="form-ticket-{{ $id }}" class="form-ticket" method="post">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <select id="select-ticket-{{ $id }}" class="form-control select-ticket">
                    <option value="0">All Tickets</option>
                    <option value="1">All Unsolved Tickets</option>
                    <option value="2">Unassigned Tickets</option>
                    <option value="3">Pending Tickets</option>
                    <option value="4">Solved Tickets</option>
                    <option value="5">New Tickets in my Group</option>
                    <option value="6">Unsolved Tickets in my Group</option>
                    <option value="7">My Unsolved Tickets</option>
                    <option value="8">Deleted Tickets</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-info">Action</button>
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    @if ($canAssign)
                        <li><a id="btn-assign-tickets-{{ $id }}" class="btn-assign-tickets" href="#">Assign</a></li>
                    @endif

                    @if ($resources->contains('resource', 'tickets/assign'))
                        <li><a id="btn-assign-tickets-{{ $id }}" class="btn-assign-tickets" href="#">Transfer</a></li>
                    @endif
                    
                    @if ($resources->contains('resource', 'tickets/delete'))
                        <li><a id="btn-delete-tickets-{{ $id }}" class="btn-delete-tickets" href="#">Delete</a></li>
                    @endif
                    
                    @if ($resources->contains('resource', 'tickets/merge'))
                        <li><a id="btn-merge-tickets-{{ $id }}" class="btn-merge-tickets" href="#">Merge</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-12">
            <table id="ticket-list-{{ $id }}" class="table responsive datatables table-condense p-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Created At</th>
                        <th>Due Date</th>
                        <th>Requester</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Relation</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</form>