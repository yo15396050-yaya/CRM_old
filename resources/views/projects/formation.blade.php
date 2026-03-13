@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@php
$addProjectPermission = user()->permission('add_projects');
$manageProjectTemplatePermission = user()->permission('manage_project_template');
$viewProjectTemplatePermission = user()->permission('view_project_template');
$deleteProjectPermission = user()->permission('delete_projects');
@endphp


@section('content')
<div class="content-wrapper">
    <h6>C'est en cours de développement.</h6>
</div>
@endsection


@push('scripts')
@endpush