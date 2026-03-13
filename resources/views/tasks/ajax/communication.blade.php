@php
    $addTaskNotePermission = user()->permission('add_task_notes');
    $editTaskNotePermission = user()->permission('edit_task_notes');
    $deleteTaskNotePermission = user()->permission('delete_task_notes');
@endphp

<!-- TAB CONTENT START -->
<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-communication-tab">
    @if ($addTaskNotePermission == 'all'
    || ($addTaskNotePermission == 'added' && $task->added_by == user()->id)
    || ($addTaskNotePermission == 'owned' && in_array(user()->id, $taskUsers))
    || ($addTaskNotePermission == 'both' && (in_array(user()->id, $taskUsers) || $task->added_by == user()->id))
    )

        <div class="row p-20">
            <div class="col-md-12">
                <a class="f-15 f-w-500" href="javascript:;" id="add-communication"><i
                        class="icons icon-plus font-weight-bold mr-1"></i>@lang('app.add') @lang('app.clientCommunication')
                    </a>
            </div>
        </div>

        <x-form id="save-communication-data-form" class="d-none">
            <div class="col-md-12 p-20 ">
                <div class="media">
                    <img src="{{ user()->image_url }}" class="align-self-start mr-3 taskEmployeeImg rounded"
                        alt="{{ user()->name }}">
                    <div class="media-body bg-white">
                        <div class="form-group">
                            <div id="task-communication"></div>
                            <textarea name="note" class="form-control invisible d-none" id="task-communication-text"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.checkbox :fieldLabel="__('app.transmitToClient')" fieldName="is_client_visible"
                                fieldId="is_client_visible" fieldValue="1" fieldRequired="true" checked="true" />
                        </div>
                    </div>
                    <div class="col-md-6 w-100 justify-content-end d-flex align-items-center">
                        <x-forms.button-cancel id="cancel-communication" class="border-0 mr-3">@lang('app.cancel')
                        </x-forms.button-cancel>
                        <x-forms.button-primary id="submit-communication" icon="location-arrow">@lang('app.submit')
                            </x-forms.button-primary>
                    </div>
                </div>

            </div>
        </x-form>
    @endif


    <div class="d-flex flex-wrap justify-content-between p-20" id="communication-list">
        @forelse($task->notes()->where('is_client_visible', 1)->orWhere('user_id', user()->id)->orderBy('id', 'desc')->get() as $note)
            <div class="card w-100 rounded-0 border-0 note">
                <div class="card-horizontal">
                    <div class="card-img my-1 ml-0">
                        <img src="{{ $note->user->image_url }}" alt="{{ $note->user->name }}">
                    </div>
                    <div class="card-body border-0 pl-0 py-1">
                        <div class="d-flex flex-grow-1">
                            <h4 class="card-title f-15 f-w-500 text-dark mr-3">{{ $note->user->name }}</h4>
                            <p class="card-date f-11 text-lightest mb-0">
                                {{ $note->created_at->diffForHumans() }}
                            </p>
                            
                            @if($note->is_client_visible)
                                <span class="badge badge-success ml-2"><i class="fa fa-eye"></i> @lang('app.clientCommunication')</span>
                                @if($note->channel)
                                    <span class="f-11 text-lightest ml-2" title="{{ $note->channel }}">
                                        @if(str_contains($note->channel, 'WhatsApp')) <i class="fab fa-whatsapp text-success"></i> @endif
                                        @if(str_contains($note->channel, 'Email')) <i class="fa fa-envelope text-blue"></i> @endif
                                        via {{ $note->channel }} 
                                        @if($note->recipient_name)
                                            à <b>{{ $note->recipient_name }}</b>
                                        @endif
                                    </span>
                                @endif
                            @else
                                <span class="badge badge-secondary ml-2"><i class="fa fa-eye-slash"></i> @lang('app.private')</span>
                            @endif

                            @if ($editTaskNotePermission == 'all' || ($editTaskNotePermission == 'added' && $note->added_by == user()->id) ||
                            $deleteTaskNotePermission == 'all' || ($deleteTaskNotePermission == 'added' && $note->added_by == user()->id))
                            <div class="dropdown ml-auto note-action">
                                <button
                                    class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                     aria-labelledby="dropdownMenuLink" tabindex="0">
                                    @if ($editTaskNotePermission == 'all' || ($editTaskNotePermission == 'added' && $note->added_by == user()->id))
                                        <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 edit-note"
                                           href="javascript:;" data-row-id="{{ $note->id }}">@lang('app.edit')</a>
                                    @endif

                                    @if ($deleteTaskNotePermission == 'all' || ($deleteTaskNotePermission == 'added' && $note->added_by == user()->id))
                                        <a class="cursor-pointer d-block text-dark-grey f-13 pb-3 px-3 delete-note"
                                           data-row-id="{{ $note->id }}"
                                           href="javascript:;">@lang('app.delete')</a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-text f-14 text-dark-grey text-justify ql-editor">{!! $note->note !!}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <x-cards.no-record :message="__('messages.noNoteFound')" icon="clipboard"/>
        @endforelse
    </div>


</div>
<!-- TAB CONTENT END -->

<script>
    $('#add-communication').click(function () {
        $(this).closest('.row').addClass('d-none');
        $('#save-communication-data-form').removeClass('d-none');
    });

    $('#cancel-communication').click(function () {
        $('#save-communication-data-form').addClass('d-none');
        $('#add-communication').closest('.row').removeClass('d-none');
    });

    var atValues = @json($taskuserData);

    $(document).ready(function () {

        quillMention(atValues, '#task-communication');

        $('#submit-communication').click(function () {
            var note = document.getElementById('task-communication').children[0].innerHTML;
            document.getElementById('task-communication-text').value = note;
            var is_client_visible = $('#is_client_visible').is(':checked') ? 1 : 0;
            
            var mention_user_id = $('#task-communication span[data-id]').map(function(){
                return $(this).attr('data-id')
            }).get();
            
            var token = '{{ csrf_token() }}';
            const url = "{{ route('task-note.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-communication-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#submit-communication",
                data: {
                    '_token': token,
                    note: note,
                    is_client_visible: is_client_visible,
                    mention_user_id : mention_user_id,
                    taskId: '{{ $task->id }}'
                },
                success: function (response) {
                    if (response.status == "success") {
                        $('#communication-list').html(response.view);
                        document.getElementById('task-communication').children[0].innerHTML = "";
                        $('#task-communication-text').val('');
                        $('#save-communication-data-form').addClass('d-none');
                        $('#add-communication').closest('.row').removeClass('d-none');
                        
                        // Reload if necessary or update UI
                        if (typeof loadData !== 'undefined') {
                            loadData();
                        }
                    }
                }
            });
        });
    });
</script>
