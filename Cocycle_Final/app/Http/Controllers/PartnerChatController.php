<?php

namespace App\Http\Controllers;

use App\Models\{Message, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerChatController extends Controller
{
    public function index()
    {
        $store = Auth::user()->store;
        return view('partner.chat.index', compact('store'));
    }

    public function getContacts(Request $request)
    {
        $id = Auth::id();
        $messages = Message::where('sender_id', $id)->orWhere('receiver_id', $id)->latest('id')->get();
        $latest = $messages->unique(fn ($m) => $m->sender_id == $id ? $m->receiver_id : $m->sender_id);
        $ids = $latest->map(fn ($m) => $m->sender_id == $id ? $m->receiver_id : $m->sender_id);
        $users = User::whereIn('id', $ids)->get()->keyBy('id');
        return response()->json($latest->map(function ($message) use ($id, $users) {
            $user = $users->get($message->sender_id == $id ? $message->receiver_id : $message->sender_id);
            if (!$user) return null;
            return ['id' => $user->id, 'username' => $user->username, 'fullname' => $user->fullname ?: $user->username,
                'profile_pic' => asset($user->profile_pic), 'last_message' => $message->message,
                'time_display' => $message->created_at->format('d/m H:i')];
        })->filter()->values());
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'nullable|string|max:100']);
        $q = trim($request->query('q', ''));
        if ($q === '') return response()->json([]);
        $role = Auth::user()->role === 'partner' ? 'customer' : 'partner';
        return response()->json(User::where('role', $role)->whereKeyNot(Auth::id())
            ->where(fn ($query) => $query->where('username', 'like', "%$q%")->orWhere('fullname', 'like', "%$q%"))
            ->limit(10)->get()->map(fn ($u) => ['id' => $u->id, 'username' => $u->username,
                'fullname' => $u->fullname ?: $u->username, 'profile_pic' => asset($u->profile_pic),
                'last_message' => '', 'time_display' => '']));
    }

    public function getMessages(Request $request)
    {
        $request->validate(['contact_id' => 'required|integer|exists:users,id']);
        $id = Auth::id();
        $contact = $request->integer('contact_id');
        $messages = Message::where(fn ($q) => $q->where('sender_id', $id)->where('receiver_id', $contact))
            ->orWhere(fn ($q) => $q->where('sender_id', $contact)->where('receiver_id', $id))
            ->orderBy('id')->get();
        return response()->json($messages->map(fn ($m) => ['id' => $m->id, 'message' => $m->message,
            'time' => $m->created_at->format('H:i'), 'is_me' => $m->sender_id == $id]));
    }

    public function sendMessage(Request $request)
    {
        $request->validate(['receiver_id' => 'required|integer|exists:users,id', 'message' => 'required|string|max:5000']);
        $recipient = User::findOrFail($request->receiver_id);
        abort_if($recipient->id === Auth::id() || $recipient->role === Auth::user()->role, 422, 'Pilih kontak customer atau partner yang sesuai.');
        $message = Message::create(['sender_id' => Auth::id(), 'receiver_id' => $recipient->id, 'message' => $request->message]);
        return response()->json(['status' => 'success', 'data' => ['id' => $message->id, 'message' => $message->message,
            'time' => $message->created_at->format('H:i'), 'is_me' => true]]);
    }
}
