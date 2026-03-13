<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\MessageSetting;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\User;
use App\Models\UserChat;
use App\Notifications\NewChat;
use App\Notifications\WhatsAppNotification;
use App\Scopes\ActiveScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DirectChatMail;
use Illuminate\Support\Facades\Notification;

class MessageController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.messages';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('messages', $this->user->modules));

            return $next($request);
        });
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $this->messageSetting = message_setting();

        if (request()->taskId) {
            $this->taskId = request()->taskId;
        }

        $this->userLists = UserChat::userList();

        // Get the first conversation to show their messages
        $firstChat = $this->userLists->first();

        if ($firstChat) {
            // Determine the other user in the conversation
            $otherUserId = ($firstChat->from == user()->id) ? $firstChat->to : $firstChat->from;
            $this->viewUser = User::find($otherUserId);
        }
        else {
            $this->viewUser = null;
        }

        if ($this->viewUser) {
            $this->chatDetails = UserChat::chatDetail($this->viewUser->id, user()->id);
            UserChat::where('from', $this->viewUser->id)->where('to', user()->id)->update(['message_seen' => 'yes']);
        }
        else {
            $this->chatDetails = [];
        }

        // Fetch users for mention system
        $mentionUsers = User::allEmployees(null, true, 'all');
        $userData = [];
        foreach ($mentionUsers as $mentionUser) {
            $url = route('employees.show', [$mentionUser->id]);
            $userData[] = ['id' => $mentionUser->id, 'value' => $mentionUser->name, 'image' => $mentionUser->image_url, 'link' => $url];
        }
        $this->userData = $userData;

        if (request()->ajax()) {
            $userListHtml = view('messages.user_list', $this->data)->render();
            $messageListHtml = view('messages.message_list', $this->data)->render();

            return response()->json([
                'status' => 'success',
                'userList' => $userListHtml,
                'user_list' => $userListHtml,
                'message_list' => $messageListHtml,
                'html' => $messageListHtml,
                'userName' => $this->viewUser ? $this->viewUser->name : '',
                'receiver_id' => $this->viewUser ? $this->viewUser->id : '',
            ]);
        }

        return view('messages.index', $this->data);
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $this->messageSetting = message_setting();
        $this->project_id = Project::where('client_id', user()->id)->pluck('id');
        $this->employee_project_id = ProjectMember::where('user_id', user()->id)->pluck('project_id');
        $this->employee_user_id = ProjectMember::whereIn('project_id', $this->employee_project_id)->pluck('user_id');
        $this->employee_client_id = Project::whereIn('id', $this->employee_project_id)->pluck('client_id');

        $this->user_id = ProjectMember::whereIn('project_id', $this->project_id)->pluck('user_id');

        if (request()->taskId) {
            $this->taskId = request()->taskId;
            $this->task = Task::with('users', 'project', 'boardColumn')->findOrFail($this->taskId);
        }

        if (!in_array('client', user_roles())) {
            $this->employees = User::allEmployees($this->user->id, true, 'all');
            $this->clients = User::allClients(null, false, 'all');
        }

        // This will return true if message button from projects overview button is clicked
        if (request()->clientId && is_numeric(request()->clientId)) {
            $this->clientId = request()->clientId;
            $this->client = User::find($this->clientId);
        }

        if (in_array('client', user_roles())) {
            if ($this->messageSetting->allow_client_employee == 'yes' && $this->messageSetting->restrict_client == 'no') {
                $this->employees = User::allEmployees(null, true);
            }
            else if ($this->messageSetting->allow_client_employee == 'yes' && $this->messageSetting->restrict_client == 'yes') {
                $this->employees = User::whereIn('id', $this->user_id)->get();
            }
            else {
                $this->employees = User::allEmployees(null, true);
            }
        }

        $mentionUsers = User::allEmployees(null, true, 'all');
        $userData = [];
        foreach ($mentionUsers as $mentionUser) {
            $url = route('employees.show', [$mentionUser->id]);
            $userData[] = ['id' => $mentionUser->id, 'value' => $mentionUser->name, 'image' => $mentionUser->image_url, 'link' => $url];
        }
        $this->userData = $userData;

        return view('messages.create', $this->data);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $recipients = [];
        $userType = $request->input('user_type', 'employee');

        // Priorité au client si le type est 'client', sinon prendre user_id
        if ($userType === 'client' && $request->client_id) {
            $recipients = is_array($request->client_id) ? $request->client_id : [$request->client_id];
        }
        elseif ($request->user_id) {
            $recipients = is_array($request->user_id) ? $request->user_id : [$request->user_id];
        }
        elseif ($request->client_id) {
            // Fallback : client_id présent mais user_type non défini
            $recipients = is_array($request->client_id) ? $request->client_id : [$request->client_id];
        }

        if (empty($recipients)) {
            return response()->json(['status' => 'error', 'message' => __('messages.selectRecipient')], 422);
        }

        $lastMessage = null;
        $users = User::whereIn('id', $recipients)->get();

        foreach ($users as $recipientUser) {
            $message = new UserChat();
            $message->from = user()->id;
            $message->to = $recipientUser->id;
            $message->user_one = user()->id;
            $message->user_id = $recipientUser->id;
            $message->message = $request->message;
            $message->company_id = company()->id;
            $message->task_id = $request->taskId ?? $request->task_id;

            // Suppress the default NewChat email if we are sending via a specific professional channel (email, whatsapp, sms)
            if ($request->channel && $request->channel != 'none') {
                $message->skip_new_chat_email = true;
            }

            $message->save();
            $lastMessage = $message;

            // Send Notifications based on channel
            if ($request->channel == 'email' && $recipientUser->email) {
                // Envoi direct par email, sans condition email_notifications
                \Illuminate\Support\Facades\Mail::to($recipientUser->email)
                    ->send(new \App\Mail\DirectChatMail($message, $recipientUser));
            }
            elseif ($request->channel == 'whatsapp' || $request->channel == 'sms') {
                if ($recipientUser->mobile) {
                    Notification::send($recipientUser, new WhatsAppNotification($message));
                }
            }
        }

        if (request()->ajax()) {
            $this->viewUser = $users->last();
            $this->chatDetails = UserChat::chatDetail($this->viewUser->id, user()->id);
            $this->userLists = UserChat::userList();

            $userListHtml = view('messages.user_list', $this->data)->render();
            $messageListHtml = view('messages.message_list', $this->data)->render();

            return response()->json([
                'status' => 'success',
                'user_list' => $userListHtml,
                'message_list' => $messageListHtml,
                'html' => $messageListHtml,
                'userName' => $this->viewUser->name,
                'receiver_id' => $this->viewUser->id,
                'message_id' => $lastMessage->id
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $this->viewUser = User::find($id);

        if (!$this->viewUser) {
            abort(404);
        }
        $this->chatDetails = UserChat::chatDetail($id, user()->id);

        UserChat::where('from', $id)->where('to', user()->id)->update(['message_seen' => 'yes']);

        if (request()->ajax()) {
            $view = view('messages.message_list', $this->data)->render();
            return response()->json([
                'status' => 'success',
                'html' => $view,
                'message_list' => $view,
                'id' => $id,
                'unreadMessages' => 0
            ]);
        }

        return view('messages.index', $this->data);
    }

    public function fetchUserListView()
    {
        $this->userLists = UserChat::userList();

        if (request()->ajax()) {
            $view = view('messages.user_list', $this->data)->render();

            return response()->json([
                'status' => 'success',
                'userList' => $view,
                'user_list' => $view
            ]);
        }
    }

    public function fetchUserMessages($id)
    {
        $this->chatDetails = UserChat::chatDetail($id, user()->id);

        if (request()->ajax()) {
            $view = view('messages.message_list', $this->data)->render();

            return response()->json([
                'status' => 'success',
                'message_list' => $view,
                'html' => $view
            ]);
        }
    }

    public function checkNewMessages()
    {
        $unreadMessages = UserChat::where('to', user()->id)
            ->where('message_seen', 'no')
            ->count();

        return response()->json([
            'status' => 'success',
            'unreadMessages' => $unreadMessages
        ]);
    }

    public function destroy($id)
    {
        UserChat::destroy($id);

        return response()->json([
            'status' => 'success',
            'chat_details' => []
        ]);
    }
}
