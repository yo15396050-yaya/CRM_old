<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<style>
    #message-new .ql-editor {
        border: 1px solid #a3a3a3;
        border-radius: 6px;
        padding-left: 6px !important;
        height: 100% !important;
    }

    .ql-editor-disabled {
        border-radius: 6px;
        background-color: rgba(124, 0, 0, 0.2);
        transition-duration: 0.5s;
    }

    .ql-toolbar {
        display: none !important;
    }

</style>
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang("modules.messages.startConversation")</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="createConversationForm">
        <div class="row">

            <div class="col-md-12 {{ isset($clientId) ? 'd-none' : '' }}">
                <div class="form-group">
                    <div class="d-flex">

                        @if (!in_array('client', user_roles()))
                            @if (
                            $messageSetting->allow_client_employee == 'yes' && in_array('employee', user_roles())
                            || $messageSetting->allow_client_admin == 'yes' && in_array('admin', user_roles())
                            )
                                <x-forms.radio fieldId="user-type-employee" :fieldLabel="__('app.member')"
                                               fieldValue="employee" fieldName="user_type" checked="true">
                                </x-forms.radio>
                                <x-forms.radio fieldId="user-type-client" :fieldLabel="__('app.client')"
                                               fieldValue="client" fieldName="user_type">
                                </x-forms.radio>
                            @else
                                <input type="hidden" name="user_type" value="employee">
                            @endif
                        @endif

                        @if (in_array('client', user_roles()))
                            @if ($messageSetting->allow_client_employee == 'yes' || $messageSetting->allow_client_admin == 'yes')
                                <input type="hidden" name="user_type" value="employee">
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <input type="hidden" name="mention_user_id" id="mentionUserIds" class="mention_user_ids">

            <div class="col-md-12" id="member-list">
                <div class="form-group">
                    <x-forms.select fieldId="selectEmployee" :fieldLabel="__('modules.messages.chooseMember')"
                                    fieldName="user_id[]" search="true" fieldRequired="true" multiple="true">
                        @foreach ($employees as $item)
                            <x-user-option :user="$item" :pill="true"/>
                        @endforeach
                    </x-forms.select>
                </div>
            </div>

            <div class="col-md-12 d-none" id="client-list">
                <div class="form-group">
                    <x-forms.select fieldId="client_id" :fieldLabel="__('modules.messages.chooseClient')"
                                    fieldName="client_id[]" search="true" fieldRequired="true" multiple="true">
                        @if(isset($clients))
                            @foreach ($clients as $item)
                                <x-user-option :user="$item" :pill="true"
                                               :selected="(isset($clientId) && $clientId == $item->id)"
                                               :additionalText="$item->company_name ?? ''"/>
                            @endforeach
                        @endif
                    </x-forms.select>
                </div>
            </div>

            <input type="hidden" name="task_id" value="{{ $taskId ?? '' }}">

            <div class="col-md-12">
                <div class="form-group">
                    <x-forms.select fieldId="channel" :fieldLabel="__('modules.tasks.channel')"
                                    fieldName="channel" search="false" fieldRequired="true">
                        <option value="internal" {{ !isset($taskId) ? 'selected' : '' }}>@lang('app.chatInternal')</option>
                        <option value="email" {{ isset($taskId) ? 'selected' : '' }}>@lang('app.email')</option>
                        <option value="whatsapp">@lang('app.whatsapp')</option>
                        <option value="sms">@lang('app.sms')</option>
                    </x-forms.select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <x-forms.label :fieldLabel="__('app.message')" fieldRequired="true" fieldId="description">
                    </x-forms.label>
                    <div id="message-new"></div>
                    <input type="hidden" name="types" value="modal"/>
                    <textarea name="message" id="new-message-text" class="d-none"></textarea>
                </div>
            </div>

            <div class="col-md-12 my-5">
                <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2"
                                       :fieldLabel="__('app.menu.addFile')" fieldName="file"
                                       fieldId="message-file-upload-dropzone"/>
                <input type="hidden" name="message_id" id="message_id">
                <input type="hidden" name="type" id="message">

                {{-- These inputs fields are used for file attchment --}}
                <input type="hidden" name="user_list" id="user_list">
                <input type="hidden" name="message_list" id="message_list">
                <input type="hidden" name="receiver_id" id="receiver_id">
            </div>

        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-message" icon="check">@lang('app.send')</x-forms.button-primary>
</div>

<script>

    $('#selectEmployee, #client_id').selectpicker();

    var atValues = @json($userData);
    quillMention(atValues, '#message-new');

    $("input[name=user_type]").click(function () {
        if ($(this).val() == 'employee') {
            $('#member-list').removeClass('d-none');
            $('#client-list').addClass('d-none');
            // Activer employee, désactiver client (évite la soumission des valeurs cachées)
            $('#selectEmployee').prop('disabled', false);
            $('#client_id').prop('disabled', true);
            // Remettre le canal par défaut sur internal pour les employés
            $('#channel').selectpicker('val', 'internal');
        } else {
            $('#member-list').addClass('d-none');
            $('#client-list').removeClass('d-none');
            // Désactiver employee, activer client
            $('#selectEmployee').prop('disabled', true);
            $('#client_id').prop('disabled', false);
            // Forcer le canal email quand on sélectionne un client
            $('#channel').selectpicker('val', 'email');
        }
    });

    /* Upload images */
    Dropzone.autoDiscover = false;

    //Dropzone class
    taskDropzone1 = new Dropzone("#message-file-upload-dropzone", {
        dictDefaultMessage: "{{ __('app.dragDrop') }}",
        url: "{{ route('message-file.store') }}",
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        paramName: "file",
        maxFilesize: DROPZONE_MAX_FILESIZE,
        maxFiles: DROPZONE_MAX_FILES,
        autoProcessQueue: false,
        uploadMultiple: true,
        addRemoveLinks: true,
        parallelUploads: DROPZONE_MAX_FILES,
        acceptedFiles: DROPZONE_FILE_ALLOW,
        init: function () {
            taskDropzone1 = this;
            this.on("success", function (file, response) {
                $('#message_list').val(response.message_list);
                if (typeof showContent === "function") {
                    showContent(response.message_list);
                    $(MODAL_LG).modal('hide');
                } else {
                    setContent(response);
                }
                $.easyUnblockUI();
                taskDropzone1.removeAllFiles(true);
            })
        }
    });
    taskDropzone1.on('sending', function (file, xhr, formData) {
        var ids = $('#message_id').val();
        formData.append('message_id', ids);
        formData.append('type', 'message');
        formData.append('receiver_id', $('#receiver_id').val());
        $.easyBlockUI();
    });
    taskDropzone1.on('uploadprogress', function () {
        $.easyBlockUI();
    });
    taskDropzone1.on('removedfile', function () {
        var grp = $('div#file-upload-dropzone').closest(".form-group");
        var label = $('div#file-upload-box').siblings("label");
        $(grp).removeClass("has-error");
        $(label).removeClass("is-invalid");
    });
    taskDropzone1.on('error', function (file, message) {
        taskDropzone1.removeFile(file);
        var grp = $('div#file-upload-dropzone').closest(".form-group");
        var label = $('div#file-upload-box').siblings("label");
        $(grp).find(".help-block").remove();
        var helpBlockContainer = $(grp);

        if (helpBlockContainer.length == 0) {
            helpBlockContainer = $(grp);
        }

        helpBlockContainer.append('<div class="help-block invalid-feedback">' + message + '</div>');
        $(grp).addClass("has-error");
        $(label).addClass("is-invalid");

    });

    $('#save-message').click(function () {
        var note = document.getElementById('message-new').children[0].innerHTML;
        document.getElementById('new-message-text').value = note;
        var mention_user_id = $('#message-new span[data-id]').map(function () {
            return $(this).attr('data-id')
        }).get();
        $('#mentionUserIds').val(mention_user_id.join(','));

        // Channel Validation
        var channel = $('#channel').val();
        var error = false;
        var selectedRecipients = [];
        var userType = $("input[name='user_type']").is(':radio') ? $("input[name='user_type']:checked").val() : $("input[name='user_type']").val();

        if (userType == 'employee') {
            selectedRecipients = $('#selectEmployee option:selected').map(function() {
                return { name: $(this).text().trim(), email: $(this).data('email'), mobile: $(this).data('mobile') };
            }).get();
        } else {
            selectedRecipients = $('#client_id option:selected').map(function() {
                return { name: $(this).text().trim(), email: $(this).data('email'), mobile: $(this).data('mobile') };
            }).get();
        }

        if (selectedRecipients.length === 0) {
            Swal.fire({
                icon: 'error',
                text: "{{ __('messages.selectRecipient') }}",
            });
            return false;
        }

        selectedRecipients.forEach(function(recipient) {
            if (channel === 'email' && (!recipient.email || recipient.email === '')) {
                error = "L'email est manquant pour : " + recipient.name;
            } else if ((channel === 'sms' || channel === 'whatsapp') && (!recipient.mobile || recipient.mobile === '')) {
                error = "Le numéro de téléphone est manquant pour : " + recipient.name;
            }
        });

        if (error) {
            Swal.fire({
                icon: 'error',
                text: error,
            });
            return false;
        }

        var url = "{{ route('messages.store') }}";
        $.easyAjax({
            url: url,
            container: '#createConversationForm',
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-message",
            type: "POST",
            data: $('#createConversationForm').serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    if (taskDropzone1.getQueuedFiles().length > 0) {
                        message_id = response.message_id;
                        $('#message_id').val(response.message_id);
                        taskDropzone1.processQueue();
                    } else {
                        if (typeof showContent === "function") {
                             showContent(response.message_list);
                             $(MODAL_LG).modal('hide');
                        } else {
                             setContent(response);
                        }
                    }
                }
            }
        })
    });

    function setContent(response) {
        if (!response) return;

        @if (isset($client) && !isset($taskId))
        let clientId = $('#client_id').val();
        var redirectUrl = "{{ route('messages.index') }}?clientId=" + clientId;
        window.location.href = redirectUrl;
        @endif

        if (response.user_list) {
            document.getElementById('msgLeft').innerHTML = response.user_list;
        }
        if (response.message_list) {
            document.getElementById('chatBox').innerHTML = response.message_list;
            $('#sendMessageForm').removeClass('d-none');
        }

        if (response.receiver_id) {
            $('#current_user_id').val(response.receiver_id);
            $('#receiver_id').val(response.receiver_id);
            $('.message-user').html(response.userName);
            $('.show-user-messages').removeClass('active');
            $('#user-no-' + response.receiver_id + ' a').addClass('active');
        }

        $(MODAL_LG).modal('hide');
        scrollChat();
    }

    // If request comes from project overview tab where client id is set, then it will select that client name default
    @if (isset($client) || isset($taskId))
        $("#user-type-client").prop("checked", true);
        $('#member-list').addClass('d-none');
        $('#client-list').removeClass('d-none');
        // Désactiver employee pour ne pas soumettre ses valeurs
        $('#selectEmployee').prop('disabled', true);
        $('#client_id').prop('disabled', false);
        // Pré-sélectionner email comme canal pour les clients
        setTimeout(() => { $('#channel').selectpicker('val', 'email'); }, 100);
        @if(isset($taskId))
            $('#channel').selectpicker('val', 'email');
            
            setTimeout(() => {
                let taskHeading = "{!! $task->heading !!}";
                let taskId = "{{ $task->id }}";
                let project = "{{ $task->project ? $task->project->project_name : '--' }}";
                let status = "{{ $task->boardColumn ? $task->boardColumn->column_name : '--' }}";
                let dueDate = "{{ $task->due_date ? $task->due_date->format($global->date_format) : '--' }}";
                let users = "{!! $task->users->pluck('name')->implode(', ') !!}";

                let content = "<h2><strong>Communication : [" + taskHeading + " #" + taskId + "]</strong></h2>";
                content += "<p><strong>Diligence :</strong> " + project + "</p>";
                content += "<p><strong>Statut :</strong> " + status + "</p>";
                content += "<p><strong>Date d'échéance :</strong> " + dueDate + "</p>";
                content += "<p><strong>Responsable(s) :</strong> " + users + "</p>";
                content += "<hr>";
                content += "<p>Bonjour,</p>";
                content += "<p>Nous revenons vers vous concernant l'avancement de la tâche : <strong>" + taskHeading + "</strong>.</p>";
                quill.root.innerHTML = content;
            }, 500);
        @endif
    @endif

    init('#createConversationForm');
</script>
