@layout('layouts/main')

@section('content')
<div class="content">
@include('plugins.status')
<div class="page-header"><h1>Budgets <small>Delete By Fiscal Year</small></h1></div>
<form action='/admin/buddelete' method='post'>
<input type='input' name='fyyear' class="input-small" placeholder='FY**'></input>
<input type='submit' name='submit' class="btn" value='Submit' /></form>
</div>
@endsection