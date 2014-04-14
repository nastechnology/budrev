@layout('layouts/main')
@section('content')
<div class="content">
@if (!is_null(Session::get('login_error')))
<div class="alert alert-error">
    <a class="close" data-dismiss="alert" href="#">×</a>
    <h4 class="alert-heading">Oh Snap!</h4>
    @if (is_array(Session::get('login_error')))
        <ul>
        @foreach (Session::get('login_error') as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    @else
        {{ Session::get('login_error') }}
    @endif
</div>
@endif
		<form id="login" name="login" class="form-horizontal" method="post" action="/admin/login">
            <div class="control-group">
                <label class="control-label" for="uid">Username</label>
                <div class="controls">
                    <input type="text" name="uid" id="uid" value="" placeholder="Username">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="pwd">Password</label>
                <div class="controls">
                    <input type="password" name="pwd" id="pwd" value="" placeholder="Password">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input type="submit" name="submit" id="submit" value="Submit" class="btn">
                </div>
            </div>
        </form>
@endsection