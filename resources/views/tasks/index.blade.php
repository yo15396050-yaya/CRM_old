@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush


@php
$addTaskPermission = user()->permission('add_tasks');
$viewUnassignedTasksPermission = user()->permission('view_unassigned_tasks');
@endphp


@section('filter-section')

    <x-filters.filter-box>
        <!-- DATE START -->
        <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
            <div class="select-status d-flex">
                <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                    id="datatableRange" placeholder="@lang('placeholders.dateRange')">
            </div>
        </div>
        <!-- DATE END -->

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="status" id="status" data-live-search="true" data-size="8">
                    <option value="not finished">@lang('modules.tasks.hideCompletedTask')</option>
                    <option {{ request('status') == 'all' ? 'selected' : '' }} value="all">@lang('app.all')</option>
                    @foreach ($taskBoardStatus as $status)
                        <option value="{{ $status->id }}">{{ $status->slug == 'completed' || $status->slug == 'incomplete' ? __('app.' . $status->slug) : $status->column_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                        placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs {{ request('overdue') != 'yes' ? 'd-none' : '' }}" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

        <!-- MORE FILTERS START -->
        <x-filters.more-filter-box>
            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('app.dateFilterOn')</label>
                <div class="select-filter mb-4">
                    <select class="form-control select-picker" name="date_filter_on" id="date_filter_on">
                        <option value="start_date">@lang('app.startDate')</option>
                        <option value="due_date">@lang('app.dueDate')</option>
                        <option value="completed_on">@lang('app.dateCompleted')</option>
                    </select>
                </div>
            </div>
            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('modules.tickets.type')</label>
                <div class="select-filter mb-4">
                    <select class="form-control select-picker" name="pinned" id="pinned" data-container="body">
                        <option value="all">@lang('app.all')</option>
                        <option value="pinned">@lang('app.pinned')</option>
                        <option value="private">@lang('app.private')</option>
                    </select>
                </div>
            </div>
            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('app.project')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" name="project_id_filter" id="project_id_filter" data-live-search="true"
                            data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('app.client')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="clientID" data-live-search="true"
                            data-container="body" data-size="8">
                            @if (!in_array('client', user_roles()))
                                <option value="all">@lang('app.all')</option>
                            @endif
                            @foreach ($clients as $client)
                                <x-user-option :user="$client"></x-user-option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('modules.tasks.assignTo')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="assignedTo" data-live-search="true"
                            data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($employees as $employee)
                                <x-user-option :user="$employee">
                                </x-user-option>
                            @endforeach
                            @if ($viewUnassignedTasksPermission == 'all')
                                <option value="unassigned">@lang('modules.tasks.unassigned')</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('modules.tasks.assignBy')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="assignedBY" data-live-search="true"
                            data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($employees as $employee)
                                <x-user-option :user="$employee" />
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('app.label')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="label" data-live-search="true" data-container="body"
                            data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($taskLabels as $label)
                                <option
                                    data-content="<span class='badge b-all' style='background:{{ $label->label_color }};'>{{ $label->label_name }}</span> "
                                    value="{{ $label->id }}">{{ $label->label_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 "
                    for="usr">@lang('modules.taskCategory.taskCategory')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="category_id" data-live-search="true"
                            data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($taskCategories as $categ)
                                <option value="{{ $categ->id }}">{{ $categ->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('app.billableTask')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="billable_task" data-live-search="true"
                            data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
                            <option value="1">@lang('app.yes')</option>
                            <option value="0">@lang('app.no')</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 " for="usr">@lang('modules.projects.milestones')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="milestone_id" data-live-search="true"
                            data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($milestones as $milestone)
                                <option value="{{ $milestone->id }}">{{ $milestone->milestone_title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </x-filters.more-filter-box>
        <!-- MORE FILTERS END -->
    </x-filters.filter-box>

@endsection

@section('content')
    <div class="content-wrapper dck-front">

        <x-dck.page-head :titre="__('app.menu.tasks')"
                         :description="$waitingApprovalCount > 0
                            ? $waitingApprovalCount . ' ' . __('app.menu.waiting-approval')
                            : null">
            <x-slot name="actions">
                @if ($addTaskPermission == 'all' || $addTaskPermission == 'added')
                    <x-forms.link-primary :link="route('tasks.create')" class="openRightModal" icon="plus">
                        @lang('app.addTask')
                    </x-forms.link-primary>
                @endif

                @if (!in_array('client', user_roles()))
                    <x-forms.button-secondary id="filter-my-task" icon="user">
                        @lang('modules.tasks.myTask')
                    </x-forms.button-secondary>
                @endif

                {{-- Importation massive. Reservee aux droits complets :
                     l'ecran cree des taches en nombre, et un compte
                     limite a « added » ne doit pas contourner sa
                     restriction par ce biais. --}}
                @if ($addTaskPermission == 'all')
                    <x-forms.link-secondary :link="route('tasks.import')" icon="upload">
                        @lang('Importer')
                    </x-forms.link-secondary>
                @endif
            </x-slot>
        </x-dck.page-head>

        <div class="dck-card">

            <div class="dck-card__bar">
                {{-- Conteneur d'injection du bouton d'export (initComplete) --}}
                <div id="table-actions" class="d-flex align-items-center"></div>

                {{-- Selecteur de vue --}}
                <div class="btn-group dck-seg" role="group">
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary f-14 btn-active task"
                       data-toggle="tooltip" data-original-title="@lang('app.menu.tasks')"><i class="side-icon bi bi-list-ul"></i></a>

                    <a href="{{ route('taskboards.index') }}" class="btn btn-secondary f-14"
                       data-toggle="tooltip" data-original-title="@lang('modules.tasks.taskBoard')"><i class="side-icon bi bi-kanban"></i></a>

                    <a href="{{ route('task-calendar.index') }}" class="btn btn-secondary f-14"
                       data-toggle="tooltip" data-original-title="@lang('app.menu.calendar')"><i class="side-icon bi bi-calendar"></i></a>

                    <a href="javascript:;" class="btn btn-secondary f-14 show-pinned"
                       data-toggle="tooltip" data-original-title="@lang('app.pinned')"><i class="side-icon bi bi-pin-angle"></i></a>

                    @if (in_array('admin', user_roles()) || in_array('employee', user_roles()))
                        <a href="{{ route('tasks.waiting-approval') }}" class="btn btn-secondary f-14 show-waiting-approval-task"
                           data-toggle="tooltip" data-original-title="@lang('app.menu.waiting-approval')">
                            <i class="side-icon bi bi-hourglass-split"></i>
                            @if ($waitingApprovalCount > 0)
                                <span class="badge badge-pill badge-danger position-absolute">{{ $waitingApprovalCount }}</span>
                            @endif
                        </a>
                    @endif
                </div>

                <x-datatable.actions>
                    <div class="select-status mr-3 pl-lg-3">
                        <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                            <option value="">@lang('app.selectAction')</option>
                            <option value="change-status">@lang('modules.tasks.changeStatus')</option>
                            <option value="delete">@lang('app.delete')</option>
                        </select>
                    </div>
                    <div class="select-status mr-3 d-none quick-action-field" id="change-status-action">
                        <select name="status" class="form-control select-picker">
                            @foreach ($taskBoardStatus as $status)
                                {{-- « En attente d'approbation » est reserve a l'administrateur :
                                     l'option est retiree de l'action groupee. Le controleur refuse
                                     de toute facon la transition, l'interface ne fait que l'annoncer. --}}
                                @continue($status->slug == 'waiting_approval' && !in_array('admin', user_roles()))
                                <option value="{{ $status->id }}">{{ $status->slug == 'completed' || $status->slug == 'incomplete' ? __('app.' . $status->slug) : $status->column_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </x-datatable.actions>
            </div>

            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>
        $(document).ready(()=>{
            let assignedVal = "{{ $assignedTo }}";
            if(assignedVal){
                $('.filter-box #assignedTo').val(assignedVal);
            }
        });

        $('#allTasks-table').on('preXhr.dt', function(e, settings, data) {

            var dateRangePicker = $('#datatableRange').data('daterangepicker');
            var startDate = $('#datatableRange').val();

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }

            var projectID = $('#project_id_filter').val();
            if (!projectID) {
                projectID = 0;
            }


            var clientID = $('#clientID').val();
            var assignedBY = $('#assignedBY').val();
            var assignedTo = $('#assignedTo').val();
            var status = $('#status').val();
            var label = $('#label').val();
            var category_id = $('#category_id').val();
            var billable = $('#billable_task').val();
            var pinned = $('#pinned').val();
            var date_filter_on = $('#date_filter_on').val();
            var searchText = $('#search-text-field').val();
            var milestone_id = $('#milestone_id').val();

            data['clientID'] = clientID;
            data['assignedBY'] = assignedBY;
            data['assignedTo'] = assignedTo;
            data['status'] = status;
            data['status'] = status;
            data['label'] = label;
            data['category_id'] = category_id;
            data['billable'] = billable;
            data['projectId'] = projectID;
            data['pinned'] = pinned;
            data['date_filter_on'] = date_filter_on;
            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['searchText'] = searchText;
            data['milestone_id'] = milestone_id;
        });
        const showTable = () => {
            window.LaravelDataTables["allTasks-table"].draw(true);
        }

        $('#milestone_id, #billable_task, #status, #clientID, #category_id, #assignedBY, #assignedTo, #label, #project_id_filter, #pinned, #date_filter_on')
            .on('change keyup',
                function() {
                    if ($('#status').val() != "not finished") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#project_id_filter').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#clientID').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#category_id').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#assignedBY').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#assignedTo').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#label').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#billable_task').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    }else if ($('#milestone_id').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else if ($('#pinned').val() != "all") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    }  else if ($('#date_filter_on').val() != "start_date") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else {
                        $('#reset-filters').addClass('d-none');
                        showTable();
                    }
                });

        $('#search-text-field').on('keyup', function() {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            }
        });

        $('.show-pinned').click(function() {
            $('.task').removeClass('btn-active');

            if ($(this).hasClass('btn-active')) {
                $('#pinned').val('all');
            } else {
                $('#pinned').val('pinned');
            }

            $('#pinned').selectpicker('refresh');
            $(this).toggleClass('btn-active');
            $('#reset-filters').removeClass('d-none');
            showTable();
        });

        $('#reset-filters,#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box #status').val('not finished');
            $('.filter-box #date_filter_on').val('start_date');
            $('.filter-box #assignedTo').val('all');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('#quick-action-type').change(function() {
            const actionValue = $(this).val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        });

        $('#quick-action-apply').click(function() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue == 'delete') {
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('messages.recoverRecord')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('messages.confirmDelete')",
                    cancelButtonText: "@lang('app.cancel')",
                    customClass: {
                        confirmButton: 'btn btn-primary mr-3',
                        cancelButton: 'btn btn-secondary'
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        applyQuickAction();
                    }
                });

            } else {
                applyQuickAction();
            }
        });

        $('body').on('click', '.delete-table-row', function() {
            var id = $(this).data('user-id');
            let activeRunning = $(this).data('active-running');

            if (activeRunning == 1) {

                Swal.fire({
                    title: "@lang('messages.taskTimerRunning')",
                    text: "@lang('messages.stopTheTimer')",
                    icon: 'warning',
                    showConfirmButton: true,
                    confirmButtonText: "@lang('messages.timerOkay')",
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                });
            } else {
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('messages.recoverRecord')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('messages.confirmDelete')",
                    cancelButtonText: "@lang('app.cancel')",
                    customClass: {
                        confirmButton: 'btn btn-primary mr-3',
                        cancelButton: 'btn btn-secondary'
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('tasks.destroy', ':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'DELETE'
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    showTable();
                                }
                            }
                        });
                    }
                });
            }

        });

        const applyQuickAction = () => {
            var rowdIds = $("#allTasks-table input:checkbox:checked").map(function() {
                return $(this).val();
            }).get();

            var url = "{{ route('tasks.apply_quick_action') }}?row_ids=" + rowdIds;

            $.easyAjax({
                url: url,
                container: '#quick-action-form',
                type: "POST",
                disableButton: true,
                buttonSelector: "#quick-action-apply",
                data: $('#quick-action-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        showTable();
                        resetActionButtons();
                        deSelectAll();
                        $('#quick-action-form').hide();
                    }
                }
            })
        };

        $('#allTasks-table').on('change', '.change-status', function() {
            var url = "{{ route('tasks.change_status') }}";
            var token = "{{ csrf_token() }}";
            var id = $(this).data('task-id');
            var status = $(this).val();
            var needApproval = $(this).data('need-approval');
            var projectAdmin = $(this).data('project-admin');
            var loginUser = "{{ user()->id }}";

            var rolesJson = `{!! addslashes(json_encode(user()->roles)) !!}`; // Fetch roles JSON and escape special characters
            var roles = JSON.parse(rolesJson); // Parse JSON string to JavaScript object

            function isAdmin() {
                for (var i = 0; i < roles.length; i++) {
                    if (roles[i].name === 'admin') {
                        return true;
                    }
                }
            }

            if (id != "" && status != "") {
                if(status == 'completed' && !isAdmin() && projectAdmin != loginUser && needApproval == 1){
                    Swal.fire({
                        title: "@lang('messages.sweetAlertTitle')",
                        text: "@lang('messages.approvalmsgsent')",
                        icon: 'warning',
                        showCancelButton: true,
                        focusConfirm: false,
                        confirmButtonText: "@lang('app.yes')",
                        cancelButtonText: "@lang('app.no')",
                        customClass: {
                            confirmButton: 'btn btn-primary mr-3',
                            cancelButton: 'btn btn-secondary'
                        },
                        showClass: {
                            popup: 'swal2-noanimation',
                            backdrop: 'swal2-noanimation'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var url = "{{ route('tasks.send_approval', ':id') }}";
                            url = url.replace(':id', id);

                            var token = "{{ csrf_token() }}";
                            var isApproval = 1;
                            $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {
                                    '_token': token,
                                    taskId: id,
                                    isApproval: isApproval,
                                    '_method': 'POST'
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        showTable();
                                    }
                                }
                            });
                        }
                    });
                }else{
                    $.easyAjax({
                        url: url,
                        type: "POST",
                        container: '.content-wrapper',
                        blockUI: true,
                        data: {
                            '_token': token,
                            taskId: id,
                            status: status,
                            sortBy: 'id'
                        },
                        success: function(response) {
                            $('#timer-clock').html(response.clockHtml);
                            window.LaravelDataTables["allTasks-table"].draw(false);
                        }
                    });
                }
            }
        });

        $('#filter-my-task').click(function () {
            $('.filter-box #assignedTo').val('{{ user()->id }}');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').removeClass('d-none');
            showTable();
        });

        $('#allTasks-table').on('click', '.start-timer', function() {
            var url = "{{ route('timelogs.start_timer') }}";
            var user_id = "{{ user()->id }}";
            var token = "{{ csrf_token() }}";
            var task_id = $(this).data('task-id');
            var memo = "{{ __('app.task') }}#" + $(this).data('task-id');

            $.easyAjax({
                url: url,
                container: '#allTasks-table',
                type: "POST",
                blockUI: true,
                data: {
                    task_id: task_id,
                    memo: memo,
                    '_token': token,
                    user_id: user_id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        if (response.activeTimerCount > 0) {
                            $('#show-active-timer .active-timer-count').html(response.activeTimerCount);
                        } else {
                            $('#show-active-timer .active-timer-count').addClass('d-none');
                        }

                        $('#timer-clock').html(response.clockHtml);
                        if ($('#allTasks-table').length) {
                            window.LaravelDataTables["allTasks-table"].draw(false);
                        }
                    }
                }
            })
        });

        // $('#allTasks-table').on('click', '.stop-timer', function() {
        //     var id = $(this).data('time-id');
        //     var url = "{{ route('timelogs.stop_timer', ':id') }}";
        //     url = url.replace(':id', id);
        //     var token = '{{ csrf_token() }}';
        //     $.easyAjax({
        //         url: url,
        //         blockUI: true,
        //         container: '#allTasks-table',
        //         type: "POST",
        //         data: {
        //             timeId: id,
        //             _token: token
        //         },
        //         success: function(response) {
        //             if (response.activeTimerCount > 0) {
        //                 $('#show-active-timer .active-timer-count').html(response.activeTimerCount);
        //             } else {
        //                 $('#show-active-timer .active-timer-count').addClass('d-none');
        //             }

        //             if (response.activeTimer == null) {
        //                 $('#timer-clock').html('');
        //                 runTimeClock = false;
        //             }

        //             if ($('#allTasks-table').length) {
        //                 window.LaravelDataTables["allTasks-table"].draw(false);
        //             }
        //         }
        //     })
        // });

        $('#allTasks-table').on('click', '.resume-timer', function() {
            var id = $(this).data('time-id');
            var url = "{{ route('timelogs.resume_timer', ':id') }}";
            url = url.replace(':id', id);
            var token = '{{ csrf_token() }}';
            $.easyAjax({
                url: url,
                blockUI: true,
                type: "POST",
                data: {
                    timeId: id,
                    _token: token
                },
                success: function(response) {
                    if (response.status == 'success') {
                        if (response.activeTimerCount > 0) {
                            $('#show-active-timer .active-timer-count').html(response.activeTimerCount);
                        } else {
                            $('#show-active-timer .active-timer-count').addClass('d-none');
                        }

                        $('#timer-clock').html(response.clockHtml);
                        if ($('#allTasks-table').length) {
                            window.LaravelDataTables["allTasks-table"].draw(false);
                        }
                    }
                }
            })
        });

        $('#allTasks-table').on('click', '.pause-timer', function() {
            var id = $(this).data('time-id');
            var url = "{{ route('timelogs.pause_timer', ':id') }}";
            url = url.replace(':id', id);
            var token = '{{ csrf_token() }}';
            $.easyAjax({
                url: url,
                blockUI: true,
                type: "POST",
                disableButton: true,
                buttonSelector: "#pause-timer-btn",
                data: {
                    timeId: id,
                    _token: token
                },
                success: function(response) {
                    if (response.status == 'success') {
                        if (response.activeTimerCount > 0) {
                            $('#show-active-timer .active-timer-count').html(response.activeTimerCount);
                        } else {
                            $('#show-active-timer .active-timer-count').addClass('d-none');
                        }

                        $('#timer-clock').html(response.clockHtml);
                        runTimeClock = false;

                        if ($('#allTasks-table').length) {
                            window.LaravelDataTables["allTasks-table"].draw(false);
                        }
                    }
                }
            })
        });

        $('#allTasks-table').on('click', '.stop-timer', function() {
            var url = "{{ route('timelogs.stopper_alert', ':id') }}?via=timelog";
            var id = $(this).data('time-id');
            url = url.replace(':id', id);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        })

        $('body').on('click', '.send-task-notifications', function() {
            var id = $(this).data('task-id');
            var url = "{{ route('tasks.send_notifications', ':id') }}";
            url = url.replace(':id', id);

            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "{{ __('Voulez-vous envoyer les notifications aux bénéficiaires maintenant ?') }}",
                icon: 'info',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "{{ __('Oui, envoyer') }}",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

    </script>
@endpush
