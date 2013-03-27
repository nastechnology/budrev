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
			@foreach($users as $user)
				<tr>
					<td>{{ $user->name }}</td>
					<td>{{ $user->username }}</td>
					<td>{{ $user->email }}</td>
					<td>{{ $buildings[$user->building_id] }}</td>
					<td><a href="/admin/user/delete?id={{ $user->id }}"><i class="icon-remove"></i> Delete</a></td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>

@endsection