@extends('layout.master')

@section('header')
{{ HTML::style("css/app.css") }}
@stop

@section('content')
<div class="container-fluid" id="content-container">
  <div class="row first-row">
      <div class="col-xs-6">
          <h1>{{ucfirst(trans('educal.groups'))}}</h1>
      </div>
      <div class="col-xs-6">
          <a type="button" class="btn btn-default btn-lg btn-educal-warning pull-right" href="{{route('group.create')}}" id="addEvent">
            <i class="fa fa-plus"></i> {{ucfirst(trans('educal.addgroup'))}}
          </a>
      </div>
  </div>
  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table id="groupTable" class="table content-table" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="hidden-xs">#</th>
            <th>{{ucfirst(trans('educal.name'))}}</th>
            <th>{{ucfirst(trans('educal.urls'))}}</th>
            <th>{{ucfirst(trans('educal.actions'))}}</th>
          </tr>
        </thead>
        <tbody>
        <?php $i=0; ?>
        @foreach($groups as $group)
        <?php $i++ ?>
        <tr>
          <td class="hidden-xs">{{ $i }}</td>
          <td><a href="{{route('group.edit',$group->id)}}">{{ $group->name }}</a></td>
          @if($group->school)
          <td>
              <div class="col-xs-2">
                <a href="#" data-group-id="{{$group->id}}" data-link="{{ URL::to('/') }}/export/pdf/{{$group->school->short}}/{{str_replace($group->school->short."_","",$group->name)}}" title="Switch to PDF link" class="linkTo"><i class="fa fa-file-pdf-o fa-2x"></i></a>
                <a href="#" data-group-id="{{$group->id}}" data-link="{{ URL::to('/') }}/export/{{$group->school->short}}/{{str_replace($group->school->short."_","",$group->name)}}" title="Switch to iCal link" class="linkTo"><i class="fa fa-calendar fa-2x"></i></a>
              </div>
            <div class="col-xs-10">
              <input type="text" class="form-control linkToText linkToText_{{$group->id}}" value="{{ URL::to('/') }}/export/{{$group->school->short}}/{{str_replace($group->school->short."_","",$group->name)}}" />
            </div>
          </td>
          @else
          <td>{{ucfirst(trans('educal.noexport'))}}</td>
          @endif
          <td>
            <a href="export/pdf/{{$group->school->short}}/{{str_replace($group->school->short."_","",$group->name)}}" title="Download Pdf"><i class="fa fa-download fa-2x"></i></a>&nbsp;
            <a href="{{route('group.edit',$group->id)}}"><i class="fa fa-pencil fa-2x"></i></a>&nbsp;
              <a data-toggle="modal" data-target="#confirm-delete" href="#" data-href="{{ route('group.delete', $group->id) }}" title="Remove">
                  <i class="fa fa-times-circle fa-2x"></i>
              </a>
          </td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Confirmation
            </div>
            <div class="modal-body">
                Are you sure you want to delete this group?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-danger danger">Delete</a>
            </div>
        </div>
    </div>
</div>
<div id="content-bg"></div>
@stop

@section('footerScript')

{{ HTML::script('packages/datatables/js/jquery.dataTables.min.js') }}

{{ HTML::script('//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.js') }}
{{ HTML::style('//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.css') }}

{{ HTML::script('js/app.js') }}

@if(Session::get('lang') == 'nl')
<script>
    $(document).ready(function() {
        $('#groupTable').dataTable({
            "language": {
                "url": "http://cdn.datatables.net/plug-ins/be7019ee387/i18n/Dutch.json"
            },
            "aoColumnDefs": [
                {"bSortable": false, "aTargets": [2]}
            ]
        });
    } );
</script>
@elseif(Session::get('lang') == 'en')
<script>
    $(document).ready(function() {
        $('#groupTable').dataTable({
            "language": {
                "url": "http://cdn.datatables.net/plug-ins/be7019ee387/i18n/English.json"
            },
            "aoColumnDefs": [
                {"bSortable": false, "aTargets": [2]}
            ]
        });
    } );
</script>
@elseif(Session::get('lang') == 'fr')
<script>
    $(document).ready(function() {
        $('#groupTable').dataTable({
            "language": {
                "url": "http://cdn.datatables.net/plug-ins/be7019ee387/i18n/French.json"
            },
            "aoColumnDefs": [
                {"bSortable": false, "aTargets": [2]}
            ]
        });
    } );
</script>
@elseif(Session::get('lang') == 'de')
<script>
    $(document).ready(function() {
        $('#groupTable').dataTable({
            "language": {
                "url": "http://cdn.datatables.net/plug-ins/be7019ee387/i18n/German.json"
            },
            "aoColumnDefs": [
                {"bSortable": false, "aTargets": [2]}
            ]
        });
    } );
</script>
@endif

@stop