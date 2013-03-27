@layout('layouts/main')
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
