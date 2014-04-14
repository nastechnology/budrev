@layout('layouts.main')
@section('content')
<div class="content">
@include('plugins.status')
	<div class="page-header"><h1>Users <small>View Users</small></h1></div>
	<a class="btn btn-success" href="/admin/user/add"><i class="icon-plus icon-white"></i> Add user</a>
	<table class="table table-striped">
		<thead>
			<th>Name</th>
			<th>Username</th>
			<th>Email</th>
			<th>Bulding</th>
			<th>Delete</th>
		</thead>
		<tbody>
			<tr>
				<td colspan="5">None</td>
			</tr>
		</tbody>
	</table>
</div>

@endsection