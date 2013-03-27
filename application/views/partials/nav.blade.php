@section('nav')
<div class="nav-collapse"><ul class="nav"><li class='active'>
	<a href="/admin">Home</a></li>
	<li class="dropdown" id="menu1"><a  class="dropdown-toggle" data-toggle="dropdown" href="#menu1">Budget 
			@if( $bBadge != '0')
				<span class='badge badge-important'>{{$bBadge}}</span><b class="caret">
			@endif
		</b></a>
		<ul class="dropdown-menu">
			<li><a href="/admin/bud">All Budgets</a></li>
			<li><a href="/admin/budedit">Edit Proposed Budgets</a></li>
			<li><a href="/admin/buddelete">Delete Fiscal Year</a></li>
			<li><a href="/admin/budunproposed">Set unproposed</a></li>
			<li><a href="/admin/buds">View</a></li>
			<li><a href="/admin/buds/add">Add</a></li>
		</ul>
	</li>
	<li class="dropdown" id="menu2"><a class="dropdown-toggle" data-toggle="dropdown" href="#menu2">Revenues 
			@if( $rBadge != '0')
				<span class='badge badge-important'>{{$rBadge}}</span><b class="caret"></b></a>
			@endif
		<ul class="dropdown-menu">
			<li><a href="/admin/rev">All Revenues</a></li>
			<li><a href="/admin/revedit">Edit Proposed Revenues</a></li>
			<li><a href="/admin/revdelete">Delete Fiscal Year</a></li>
			<li><a href="/admin/revunproposed">Set unproposed</a></li>
			<li><a href="/admin/revs">View</a></li>
			<li><a href="/admin/revs/add">Add</a></li>
		</ul>
	</li>
	<li class="dropdown" id="menu3"><a class="dropdown-toggle" data-toggle="dropdown" href="#menu3">Building Users<b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a href="/admin/user">View List</a></li>
			<li><a href="/admin/user/add">Add</a></li>
		</ul>
	</li>
	<li class="dropdown" id="menu4"><a class="dropdown-toggle" data-toggle="dropdown" href="#menu4">Building Bud/Rev<b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a href="/admin/budget">Budgets</a></li>
			<li><a href="/admin/revenue">Revenues</a></li>
		</ul>
	</li>
	<li class="dropdown" id="menu5"><a class="dropdown-toggle" data-toggle="dropdown" href="#menu5">Export<b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a href="/admin/budexport">Budgets</a></li>
			<li><a href="/admin/revexport">Revenues</a></li>
		</ul>
	</li>
	<li><a href="/admin/logout">Logout</a></li>
</ul></div>
@endsection