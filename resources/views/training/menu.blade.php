
    <div class="col-md-2">
        <div class="list-group">
            @foreach($trainings as $training)
                <a href="/training/training_module/{{$training->id}}" class="list-group-item @if(isset($module->id) && $module->id == $training->id) active @endif"  aria-expanded="true"> {{$training->name}} </a>
            @endforeach
        </div>
    </div>
