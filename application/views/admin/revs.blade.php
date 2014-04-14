@layout('layouts.main')
@section('content')
<div class="content">
	@include('plugins.status')
	<h1 class="page-header">Revenues <small>View Current Activity Revenues</small></h1>

<a href="/admin/revs/add" class="btn btn-success"><i class="icon-plus icon-white"></i> Add Revenue</a>
<table class="table table-striped">
	<thead>
		<tr>
			<th>TI</th>
			<th>FUND</th>
			<th>RECEIPT</th>
			<th>SCC/th>
			<th>SUBJECT</th>
			<th>OPU</th>
			<th>Description</th>
			<th><i class="icon-remove"></i> Delete | <i class="icon-pencil"></i> Edit</th>
		</tr>
	</thead>
	<tbody>
		@foreach($revenues as $rev)
			<tr>
				<td>{{ $rev->ti }}</td>
				<td>{{ $rev->fund }}</td>
				<td>{{ $rev->receipt }}</td>
				<td>{{ $rev->scc }}</td>
				<td>{{ $rev->subject }}</td>
				<td>{{ $rev->opu }}</td>
				<td>{{ $rev->description }}</td>
				<td><a href="/admin/revs/delete?id={{ $rev->id }}"><i class="icon-remove"></i> Delete</a> | <a href="/admin/revs/edit?id={{ $rev->id }}"><i class="icon-pencil"></i> Edit</a></td>
			</tr>
		@endforeach
	</tbody>
</table>
</div>
@endsection