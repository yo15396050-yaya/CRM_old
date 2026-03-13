<?php

namespace App\Mail;

use App\Models\UserChat;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DirectChatMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userChat;
    public $recipient;
    public $company;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(UserChat $userChat, User $recipient)
    {
        $this->userChat = $userChat->load(['files', 'fromUser']);
        $this->recipient = $recipient;
        $this->company = $userChat->company;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->company->company_name . ' | Nouveau message de ' . $this->userChat->fromUser->name;

        $attachments = [];
        foreach ($this->userChat->files as $file) {
            $attachments[] = [
                'name' => $file->filename,
                'url' => route('front.chat_file_download', md5($file->id))
            ];
        }

        return $this->view('emails.direct_chat_mail', [
            'url' => route('messages.index'),
            'attachments' => $attachments
        ])
            ->subject($subject);
    }
}
