<div class="col-lg-6 col-xs-6">
    <div class="box">

        <div class="box-header with-border">
            <h3 class="box-title title-header">Task List</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="chart" style="max-height:370px;overflow-y:auto;overflow-x:hidden;">
                        <div class="chart" style="font-size: 10px">
                            <table class="table datatable responsive table-condense table-striped table-bordered" id="taskList" >
                                <thead>
                                <tr>
                                    <th>Merchant </th>
                                    <th>Product</th>
                                    <th>Order#</th>
                                    <th>Task#</th>
                                    <th>Name</th>
                                    <th>Due</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tasks as $m)
                                <tr>
                                    <td>{{$m->company_name}}</td>
                                    <td>{{$m->name}}</td>
                                    <td>{{$m->id}}</td>
                                    <td>{{$m->task_no}}</td>
                                    <td>{{ substr($m->taskname,0,20).'...'}}</td>
                                    <td @if($m->progress == 'Delayed') style="color: red" @endif>{{$m->dueDate}}</td>
                                    <td @if($m->taskStatus == 'Completed') style="color: green" @endif>{{$m->taskStatus}}</td>
                                    <td @if($m->progress == 'Delayed') style="color: red" @endif>{{$m->progress}}</td>
                                    <td><a class="btn btn-info btn-sm" href="/merchants/workflow/{{$m->partner_id}}/{{$m->id}}">View</a></td>
                                </tr>     
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>