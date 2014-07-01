@extends('layout.master')

@section('header')
{{ HTML::style("css/app.css") }}
@stop

@section('content')
<h1>Schools</h1>

<ul class="modules-overview">
  <li class="module row">
    <div class="module-detail-container col-xs-12 col-sm-6">
      <span>School 1</span>
      <span class="pull-right">Location</span>
    </div>
  </li>
  <li class="module row">
    <div class="module-detail-container col-xs-12 col-sm-6">
      <span>School 2</span>
      <span class="pull-right">Location</span>
    </div>
  </li>
  <li class="module row">
    <div class="module-detail-container col-xs-12 col-sm-6">
      <span>School 3</span>
      <span class="pull-right">Location</span>
    </div>
  </li>
</ul>
@stop

@section('footerScript')
{{ HTML::script('js/app.js') }}
@stop