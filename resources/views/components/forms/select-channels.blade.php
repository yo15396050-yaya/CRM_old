<div class="form-group my-3">
    <x-forms.label fieldId="chosen_channels" :fieldLabel="__('Canaux de notification')">
    </x-forms.label>
    <div class="d-flex flex-wrap mt-2">
        <div class="custom-control custom-checkbox custom-control-inline mr-4">
            <input type="checkbox" class="custom-control-input" id="channel_email" name="chosen_channels[]" value="email" checked>
            <label class="custom-control-label pt-1" for="channel_email">Email</label>
        </div>
        <div class="custom-control custom-checkbox custom-control-inline mr-4">
            <input type="checkbox" class="custom-control-input" id="channel_whatsapp" name="chosen_channels[]" value="whatsapp" checked>
            <label class="custom-control-label pt-1" for="channel_whatsapp">WhatsApp</label>
        </div>
        <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input" id="channel_sms" name="chosen_channels[]" value="sms">
            <label class="custom-control-label pt-1" for="channel_sms">SMS</label>
        </div>
    </div>
    <small class="form-text text-muted">Sélectionnez les moyens par lesquels vous souhaitez notifier le client et les collaborateurs.</small>
</div>
