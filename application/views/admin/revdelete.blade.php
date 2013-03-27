@layout('layouts/main')

@section('content')
<div class="content">
@include('plugins.status')
<div class="page-header"><h1>Revenues <small>Delete By Fiscal Year</small></h1></div>
<form action='/admin/revdelete' method='post'>
<input type='input' name='fyyear' class="input-small" placeholder='FY**'></input>
<input type='submit' name='submit' value='Submit'  class="btn" /></form>
</div>
@endsection