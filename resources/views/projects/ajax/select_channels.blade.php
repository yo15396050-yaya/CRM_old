<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">Choisir le canal d’envoi (Diligence)</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <p class="f-14 text-dark-grey">Sélectionnez les canaux de notification pour informer le client et les collaborateurs de cette diligence.</p>
            
            <div id="attachments-preview-container" class="mb-3 d-none">
                <hr>
                <h6 class="f-14 font-weight-bold text-dark-grey mb-2">Pièces jointes qui seront envoyées :</h6>
                <ul id="attachments-list" class="list-unstyled f-13 text-dark-grey ml-2">
                    <!-- Rempli par JS -->
                </ul>
            </div>

            <div class="form-group mb-3 text-dark-grey">
                <x-forms.checkbox fieldId="notify_email" :fieldLabel="__('app.email')" fieldName="notify_channels[]" fieldValue="email" checked="true"/>
            </div>
            <div class="form-group mb-3 text-dark-grey">
                <x-forms.checkbox fieldId="notify_whatsapp" fieldLabel="WhatsApp" fieldName="notify_channels[]" fieldValue="whatsapp" checked="true"/>
            </div>
            <div class="form-group mb-3 text-dark-grey">
                <x-forms.checkbox fieldId="notify_sms" fieldLabel="SMS" fieldName="notify_channels[]" fieldValue="sms" checked="true"/>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Lister les fichiers depuis Dropzone si présent dans la page parente
        if (typeof myDropzone !== 'undefined' && myDropzone.files.length > 0) {
            $('#attachments-preview-container').removeClass('d-none');
            myDropzone.files.forEach(function(file) {
                $('#attachments-list').append('<li><i class="fa fa-paperclip mr-2 text-primary"></i>' + file.name + '</li>');
            });
        }
    });

    function submitWithChannels(channels) {
        var mainForm = $('#save-project-data-form');
        mainForm.find('input[name="chosen_channels[]"]').remove();
        
        channels.forEach(function(channel) {
            mainForm.append('<input type="hidden" name="chosen_channels[]" value="' + channel + '">');
        });

        $(MODAL_LG).modal('hide');
        $('#save-project-form').trigger('click', [true]);
    }

    $('#confirm-project-notifications').click(function () {
        var channels = [];
        $('input[name="notify_channels[]"]:checked').each(function() {
            channels.push($(this).val());
        });

        if (channels.length === 0) {
            Swal.fire({
                icon: 'error',
                text: 'Veuillez sélectionner au moins un canal.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
            return false;
        }

        submitWithChannels(channels);
    });

    $('#save-without-notify').click(function () {
        submitWithChannels(['none']);
    });
</script>
