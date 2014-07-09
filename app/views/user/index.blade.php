@extends('layout.master')

@section('header')
{{ HTML::style("css/app.css") }}
@stop

@section('content')
<div class="row">
  <div class="col-xs-12">
    <ol class="breadcrumb">
      <li><a href="{{ route('landing') }}">Home</a></li>
      <li class="active">Users</li>
    </ol>
  </div>
</div>
<h1>Users</h1>
<div class="row">
  <div class="col-xs-12 table-responsive">
    <table id="groupTable" class="table table-striped" cellspacing="0" width="100%">
      <thead>
      <tr>
        <th class="hidden-xs">#</th>
        <th>Name</th>
        <th>Surname</th>
        <th>Email</th>
        <th>Activated?</th>
      </tr>
      </thead>

      <tbody>
      @foreach($users as $user)
      <?php $i++; ?>
      <tr>
        <td class="hidden-xs">{{ $i }}</td>
        <td>{{ $user->first_name }}</td>
        <td>{{ $user->last_name }}</td>
        <td>{{ $user->email }}</td>
        <td>

            <!-- TODO: fix colors -->
          <label for="activateUser">
              @if($user->activated == 1)
              <input type="checkbox" data-userid="{{$user->id}}" class="activateUser checkbox" checked>
              @else
              <input type="checkbox" data-userid="{{$user->id}}" class="activateUser checkbox">
              @endif
          </label>

          </a>
          <span class="loader glyphicon glyphicon-cog"></span>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>

      <a href="#" class="btn btn-lg btn-default btn-educal-primary" data-toggle="modal" data-target="#registerUserModal">Add user <span class="glyphicon glyphicon-link"></span></a>
      @if($errors->has('usererror'))
      <div class="modal fade" id="registerUserModal" tabindex="-1" data-errors="true" role="dialog" aria-labelledby="registerUserModal" aria-hidden="false">
          @else
          <div class="modal fade" id="registerUserModal" tabindex="-1" data-errors="false" role="dialog" aria-labelledby="registerUserModal" aria-hidden="false">
              @endif
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                          <h4 class="modal-title">Add a user</h4>
                      </div>
                      <div class="modal-body">
                          @if($errors->has('usererror'))
                          @foreach ($errors->all() as $message)
                          {{$message}}
                          @endforeach
                          @endif

                          {{ Form::open([
                          'route' => 'user.create',
                          'data-ajax' => 'true',
                          ]), PHP_EOL }}
                           <div class="form-group">
                              <label for="user-email">Name</label>
                              <input type="text" class="form-control" id="user-name" name="name" placeholder="What's your given name?">
                          </div>
                          <div class="form-group">
                              <label for="user-email">Surname</label>
                              <input type="text" class="form-control" id="user-surname" name="surname" placeholder="What's your surname?">
                          </div>
                          <div class="form-group">
                              <label for="user-email">Email address</label>
                              <input type="email" class="form-control" id="user-email" name="email" placeholder="What's your email address?">
                          </div>
                          <div class="form-group">
                              <label for="user-password">Password</label>
                              <input type="password" class="form-control" id="user-password" name="password" placeholder="Choose a password">
                          </div>
                          <div class="form-group">
                              <input type="password" class="form-control" id="user-password-confirmation" name="password_confirmation" placeholder="Repeat that password here">
                          </div>
                          <input type="hidden" name="school" value="{{ Sentry::getUser()->school_id }}">
                          <button type="submit" class="btn btn-default btn-educal-primary">Register</button>
                          {{ Form::close(), PHP_EOL }}
                      </div>
                  </div>
              </div>
          </div>
  </div>
</div>

@stop

@section('footerScript')

{{ HTML::script('packages/datatables/js/jquery.dataTables.min.js') }}

{{ HTML::script('//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.js') }}
{{ HTML::style('//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.css') }}

{{ HTML::script('js/app.js') }}
<script>
    $(document).ready(function() {
        $('#groupTable').dataTable({
            "aoColumnDefs": [
                {"bSortable": false, "aTargets": [4]}
            ]
        });
    } );
</script>
@stop