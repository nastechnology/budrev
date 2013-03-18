@layout('layouts/main')
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
		</ul>
	</li>
	<li class="dropdown" id="menu3"><a class="dropdown-toggle" data-toggle="dropdown" href="#menu3">Export<b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a href="/admin/budexport">Budgets</a></li>
			<li><a href="/admin/revexport">Revenues</a></li>
		</ul>
	</li>
	<li><a href="/admin/logout">Logout</a></li>
</ul></div>
@endsection
@section('content')
<div class="content">
@include('plugins.status')

<div class="page-header"><h1>Revenues <small>All Revenues</small></h1></div>
<form name='budreport' method='post' action='/admin/rev'>
<table class='table-striped table'>
	<thead>
		<tr>
			<th>Group</th>
			<th>TI</th>
			<th>FUND</th>
			<th>RECEIPT</th>
			<th>SCC</th>
			<th>SUBJECT</th>
			<th>OPU</th>
			<th>DESCRIPTION</th>
		</tr>
	</thead>
	<tbody>
		@foreach($revenues as $revenue)
			<tr>
				<td><input type='checkbox' name='rev[]' id='rev' value='{{ $revenue->id}}'></td>
				<td>{{ $revenue->ti }}</td>
				<td>{{ $revenue->fund }}</td>
				<td>{{ $revenue->receipt }}</td>
				<td>{{ $revenue->scc }}</td>
				<td>{{ $revenue->subject }}</td>
				<td>{{ $revenue->opu }}</td>
				<td>{{ $revenue->description }}</td>
			</tr>
		@endforeach
	</tbody>
</table>
<br/><input type='text' name='email' id='email' placeholder='john.smith@example.com'></input>
<br/><input type='submit' name='submit' class="btn" value='Submit' /></form>

</div>
@endsection
