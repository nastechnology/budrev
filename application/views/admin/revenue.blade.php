@layout('layouts/main')

@section('content')
<div class="content">
@include('plugins.status')
<div class="page-header"><h1>Revenues <small>building revenues...</small></h1></div>
<form name='buildingbudget' method='post' action='/admin/budget'>
<select name="building">
	@foreach($buildings as $building)
		<option value="{{ $building->id }}">{{ $building->building }}</option>
	@endforeach
</select>
<br/><input type='text' name='amount' id='amount' placeholder='53000.00'></input>
<br/><input type='text' name='fyyear' id='fyyear' placeholder='FY14'></input>
<br/><input type='submit' name='submit' class="btn" value='Submit' /></form>

</div>
@endsection
