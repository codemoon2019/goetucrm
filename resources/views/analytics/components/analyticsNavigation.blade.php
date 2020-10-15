<ul class="nav nav-pills justify-content-end mx-4 mb-4">
  <li class="nav-item">
    <a class="nav-link {{ $filter == 0 ? 'active' : '' }}" 
      href="{{ $url }}">
      All
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link {{ $filter == 1 ? 'active' : '' }}" 
      href="{{ $url }}?filter=1">
      Partners
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link {{ $filter == 2 ? 'active' : '' }}" 
      href="{{ $url }}?filter=2">
      Agents
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link {{ $filter == 4 ? 'active' : '' }}" 
      href="{{ $url }}?filter=4">
      Merchant
    </a>
  </li>
  
  <li class="nav-item">
    <a class="nav-link {{ $filter == 3 ? 'active' : '' }}" 
      href="{{ $url }}?filter=3">
      Employees
    </a>
  </li>
</ul>