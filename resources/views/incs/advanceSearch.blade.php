<div class="adv-search-overlay">
    <div class="adv-search">
        <div class="adv-header">
            <h4>
                Advance Search <br/>
                <small>{{isset($advanceSearchLabel) ? $advanceSearchLabel : "" }}</small>
            </h4>
            <a href="#" class="adv-close"><i class="fa fa-times-circle-o fa-2x"></i></a>
        </div>
        @if(isset($partnerSearch))
        <div class="adv-content">
            <ul>
                <!-- <li class="title">Partner Type</li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-company" value="" checked/>
                    <label for="adv-company">Company</label>
                </li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-iso" value="" checked/>
                    <label for="adv-iso">ISO</label>
                </li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-subIso" value="" checked/>
                    <label for="adv-subIso">Sub ISO</label>
                </li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-agent" value="" checked/>
                    <label for="adv-agent">Agent</label>
                </li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-subAgent" value="" checked/>
                    <label for="adv-subAgent">Sub Agent</label>
                </li> -->

                <!-- <li class="title">Products</li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-go3gar" value="" checked/>
                    <label for="adv-go3gar">Go3 Gift and Rewards</label>
                </li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-pos" value="" checked/>
                    <label for="adv-pos">POS</label>
                </li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-rewards" value="" checked/>
                    <label for="adv-rewards">Rewards App</label>
                </li> -->

                <!-- <li class="title">State</li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-california" value="" checked/>
                    <label for="adv-california">California</label>
                </li>
                <li>
                    <input type="checkbox" name="partnerType" id="adv-nn" value="" checked/>
                    <label for="adv-nn">New York</label>
                </li> -->
                <ul>
                    <li class="title"> Country: 
                        <select id="country">
                            <option value="US">US</option>
                            <option value="PH">PH</option>
                            <option value="CN">CN</option>
                        </select>
                    </li>
                </ul>
                <ul id="state_us" style="max-height:500px;overflow-y:auto;">
                    @foreach($states as $s)
                        <li>
                            <input type="checkbox" name="states[]" id="states" value="{{$s->abbr}}"/>
                            <label for="adv-states">{{$s->name}}</label>
                        </li>
                    @endforeach
                </ul>
                <ul id="state_ph" style="max-height:500px;overflow-y:auto;">
                    @foreach($statesPH as $s)
                        <li>
                            <input type="checkbox" name="statesPH[]" id="states" value="{{$s->abbr}}"/>
                            <label for="adv-states">{{$s->name}}</label>
                        </li>
                    @endforeach
                </ul>
                <ul id="state_cn" style="max-height:500px;overflow-y:auto;">
                    @foreach($statesCN as $s)
                        <li>
                            <input type="checkbox" name="statesCN[]" id="states" value="{{$s->abbr}}"/>
                            <label for="adv-states">{{$s->name}}</label>
                        </li>
                    @endforeach
                </ul>
                <li class="btn-search">
                    <a href="#" class="btn btn-flat btn-primary" onclick="advanceSearchPartners();">Search Partners</a>
                </li>
            </ul>
        </div>
        @endif
        @if(isset($userSearch))
        <div class="adv-content">
            <input type="hidden" name="advance_department_id" id="advance_department_id" />
            <input type="hidden" name="advance_company_id" id="advance_company_id" />
            <ul>
                <li class="title">Company</li>                  
                    <select name="company-op" id="company-op" class="form-control">
                        @if($is_partner==0)
                        <option value="-1" data-code="-1">--NO COMPANY--</option>
                        @endif
                        @if(count($companies)>0)
                            @foreach($companies as $company)
                                <option value="{{ $company->parent_id }}" data-code="{{ $company->parent_id }}" {{ $company->parent_id == auth()->user()->company_id ? 'selected' : '' }}>{{ $company->dba }}</option>
                            @endforeach
                        @endif
                    </select>
                </li>
                <li class="title">Departments</li>                  
                @foreach($departments as $department)
                    <li class="department-li department-li-{{$department->company_id}}">
                        <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="adv-department-cb"/>
                        <label for="adv-department">{{$department->description}}</label>
                    </li>
                @endforeach
                <li class="btn-search">
                    <a href="#" class="btn btn-flat btn-primary" onclick="advanceSearchUsers();">Search Users</a>
                </li>
            </ul>
        </div>
        @endif
        @if(isset($systemUserSearch))
        <div class="adv-content">
            <input type="hidden" name="advance_department_id" id="advance_department_id" />
            <input type="hidden" name="advance_company_id" id="advance_company_id" />
            <ul>
                <li class="title">System Defined Groups</li>                  
                @foreach($departments as $department)
                    <li class="department-li department-li-{{$department->company_id}}">
                        <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="adv-department-cb"/>
                        <label for="adv-department">{{$department->description}}</label>
                    </li>
                @endforeach
                <li class="btn-search">
                    <a href="#" class="btn btn-flat btn-primary" onclick="advanceSearchSystemUsers();">Search Users</a>
                </li>
            </ul>
        </div>
        @endif

        @if (isset($departmentSearch))
            <div class="adv-content">
                <input type="hidden" name="advance_department_id" id="advance_department_id" />
                <input type="hidden" name="advance_company_id" id="advance_company_id" />
                <ul>
                    <li class="title">Company</li>                  
                        <select name="company-op" id="company-op" class="form-control">
                            @if($is_partner==0)
                            <option value="-1" data-code="-1">--NO COMPANY--</option>
                            @endif
                            @if(count($companies)>0)
                                @foreach($companies as $company)
                                    <option value="{{ $company->parent_id }}" data-code="{{ $company->parent_id }}" {{ $company->parent_id == auth()->user()->company_id ? 'selected' : '' }}>{{ $company->dba }}</option>
                                @endforeach
                            @endif
                        </select>
                    </li>
                    <li class="title">Departments</li>                  
                    @foreach($departments as $department)
                        <li class="department-li department-li-{{$department->company_id}}">
                            <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="adv-department-cb"/>
                            <label for="adv-department">{{$department->description}}</label>
                        </li>
                    @endforeach
                    <li class="btn-search">
                        <a href="#" class="btn btn-flat btn-primary" onclick="advanceSearchDepartments();">Search Department</a>
                    </li>
                </ul>
            </div>
        @endif

        @if(isset($leadsSearch) || isset($prospectsSearch))
        <div class="adv-content">
            <ul>
                <li class="title">Interested Products</li>
                @foreach($products as $p)
                    <li>
                        <input type="checkbox" name="interested_products[]" id="interested_product" value="{{$p->id}}"/>
                        <label for="adv-interested">{{$p->name}}</label>
                    </li>
                @endforeach
                <li class="btn-search">
                    <a href="#" class="btn btn-flat btn-primary" onclick="advanceSearchLeadsProspects();">Search {{$advanceSearchLabel}}</a>
                </li>
            </ul>
        </div>
        @endif
        @if(isset($merchantSearch))
        <div class="adv-content">
            <ul>
                <li class="title"> Country: 
                    <select id="country">
                        <option value="US">US</option>
                        <option value="PH">PH</option>
                        <option value="CN">CN</option>
                    </select>
                </li>
            </ul>
            <ul id="state_us" style="max-height:500px;overflow-y:auto;">
                @foreach($states as $s)
                    <li>
                        <input type="checkbox" name="states[]" id="states" value="{{$s->abbr}}"/>
                        <label for="adv-states">{{$s->name}}</label>
                    </li>
                @endforeach
            </ul>
            <ul id="state_ph" style="max-height:500px;overflow-y:auto;">
                @foreach($statesPH as $s)
                    <li>
                        <input type="checkbox" name="statesPH[]" id="states" value="{{$s->abbr}}"/>
                        <label for="adv-states">{{$s->name}}</label>
                    </li>
                @endforeach
            </ul>
            <ul id="state_cn" style="max-height:500px;overflow-y:auto;">
                @foreach($statesCN as $s)
                    <li>
                        <input type="checkbox" name="statesCN[]" id="states" value="{{$s->abbr}}"/>
                        <label for="adv-states">{{$s->name}}</label>
                    </li>
                @endforeach
            </ul>
            <ul>
                <li class="btn-search">
                    <a href="#" class="btn btn-flat btn-primary" onclick="advanceSearchMerchants();">Search Merchants</a>
                </li>
            </ul>
        </div>
        @endif

        @if(isset($branchSearch))
        <div class="adv-content">
            <ul>
                <li class="title"> Country: 
                    <select id="country">
                        <option value="US">US</option>
                        <option value="PH">PH</option>
                        <option value="CN">CN</option>
                    </select>
                </li>
            </ul>
            <ul id="state_us" style="max-height:500px;overflow-y:auto;">
                @foreach($states as $s)
                    <li>
                        <input type="checkbox" name="states[]" id="states" value="{{$s->abbr}}"/>
                        <label for="adv-states">{{$s->name}}</label>
                    </li>
                @endforeach
            </ul>
            <ul id="state_ph" style="max-height:500px;overflow-y:auto;">
                @foreach($statesPH as $s)
                    <li>
                        <input type="checkbox" name="statesPH[]" id="states" value="{{$s->abbr}}"/>
                        <label for="adv-states">{{$s->name}}</label>
                    </li>
                @endforeach
            </ul>
            <ul id="state_cn" style="max-height:500px;overflow-y:auto;">
                @foreach($statesCN as $s)
                    <li>
                        <input type="checkbox" name="statesCN[]" id="states" value="{{$s->abbr}}"/>
                        <label for="adv-states">{{$s->name}}</label>
                    </li>
                @endforeach
            </ul>
            <ul>
                <li class="btn-search">
                    <a href="#" class="btn btn-flat btn-primary" onclick="advanceSearchBranches();">Search Branch</a>
                </li>
            </ul>
        </div>
        @endif

    </div>
</div>
{{--rgba(195, 224, 239, 0.9)--}}