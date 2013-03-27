@layout('layouts.main');

@section('content')
<div class="content">
@include('plugins.status')
	
	<div class="page-header"><h1>Users <small>Add User</small></h1></div>
	<form class="form-horizontal" method="post" action="/admin/user/add">
		<div class="control-group">
                <label class="control-label" for="uid">Username</label>
                <div class="controls">
                    <input type="text" name="uid" id="uid" value="" placeholder="100XXXXX">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="name">Name</label>
                <div class="controls">
                    <input type="text" name="name" id="name" value="" placeholder="John Smith">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Email</label>
                <div class="controls">
                    <input type="text" name="email" id="email" value="" placeholder="user@napoleonareaschools.org">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="building">Building</label>
                <div class="controls">
                	<select name="building">
                    	@foreach($buildings as $building)
                    		<option value="{{ $building->id }}">{{ $building->building }}</option>
                    	@endforeach
                    </select>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input type="submit" name="submit" id="submit" value="Submit" class="btn">
                </div>
            </div>
	</form>

</div>
@endsection