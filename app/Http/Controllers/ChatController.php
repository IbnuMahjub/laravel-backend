<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\tr_chat;
use App\Models\tr_pesan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // public function get_dataChat()
    // {
    //     try {
    //         $userId = Auth::id();

    //         // Ambil semua chat yang berkaitan sama user yang login
    //         $chats = tr_chat::where('sender_id', $userId)
    //             ->orWhere('receiver_id', $userId)
    //             ->orderBy('created_at', 'asc')
    //             ->get();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Data retrieved successfully.',
    //             'data' => $chats
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Something went wrong.',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }

    public function getLastChat($id1, $id2)
    {
        try {
            $pesan = tr_pesan::where(function ($q) use ($id1) {
                $q->where('incoming_msg_id', $id1)
                    ->orWhere('outgoing_msg_id', $id1);
            })
                ->where(function ($q) use ($id2) {
                    $q->where('incoming_msg_id', $id2)
                        ->orWhere('outgoing_msg_id', $id2);
                })
                ->orderByDesc('msg_id') // pastikan kolom ini ada di tabel kamu
                ->first();

            if ($pesan) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Last message retrieved successfully.',
                    'data' => $pesan
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No message available.',
                    'data' => null
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // Ambil chat antara user login dan targetId
    public function get_dataChat($targetId)
    {
        try {
            $userId = Auth::id();

            $chats = tr_chat::where(function ($q) use ($userId, $targetId) {
                $q->where('sender_id', $userId)
                    ->where('receiver_id', $targetId);
            })
                ->orWhere(function ($q) use ($userId, $targetId) {
                    $q->where('sender_id', $targetId)
                        ->where('receiver_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            $data = $chats->map(function ($chat) {
                return [
                    'id_chat'      => $chat->id,
                    'sender_id'    => $chat->sender_id,
                    'receiver_id'  => $chat->receiver_id,
                    'dari'         => $chat->sender->name ?? "",
                    'untuk'        => $chat->receiver->name ?? "",
                    'message'      => $chat->message,
                    'message_type' => $chat->message_type,
                    'is_read'      => $chat->is_read,
                    'created_at'   => $chat->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Chat retrieved successfully.',
                'data'    => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }


    public function get_dataChat_byUser($userId)
    {
        try {
            $chats = tr_chat::with(['sender', 'receiver'])
                ->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)
                        ->orWhere('receiver_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            // Mapping biar id jadi id_chat dan hapus object sender
            $data = $chats->map(function ($chat) {
                return [
                    'id_chat'       => $chat->id,
                    'sender_id'     => $chat->sender_id,
                    'nama_pengirim' => $chat->sender->name ?? null,   // jaga-jaga kalau null
                    'receiver_id'   => $chat->receiver_id,
                    'nama_penerima' => $chat->receiver->name ?? null,
                    'message'       => $chat->message,
                    'message_type'  => $chat->message_type,
                    'is_read'       => $chat->is_read,
                    // 'sender' => $chat->sender // ini hapus aja
                ];
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Data retrieved successfully.',
                'data'    => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function get_dataChat_byAuthUser()
    {
        try {
            $userId = auth()->user()->id;

            // Ambil semua chat yg berhubungan dgn user ini, urut descending by waktu terakhir chat
            $chats = tr_chat::with(['sender', 'receiver'])
                ->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)
                        ->orWhere('receiver_id', $userId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Map ke user lawan chat dengan data terakhir
            $uniqueUsers = $chats->map(function ($chat) use ($userId) {
                if ($chat->sender_id === $userId) {
                    $opponent = $chat->receiver;
                    $opponent_id = $chat->receiver_id;
                } else {
                    $opponent = $chat->sender;
                    $opponent_id = $chat->sender_id;
                }
                return [
                    'id_chat'       => $chat->id,
                    'sender_id'     => $chat->sender_id,
                    'nama_pengirim' => $chat->sender->name ?? null,
                    'receiver_id'   => $chat->receiver_id,
                    'nama_penerima' => $chat->receiver->name ?? null,
                    'message'       => $chat->message,
                    'message_type'  => $chat->message_type,
                    'is_read'       => $chat->is_read,
                    // key tambahan:
                    'last_time'     => $chat->created_at->format('Y-m-d H:i:s'),
                    'opponent_id'   => $opponent_id,
                    'opponent_name' => $opponent->name ?? null,
                ];
            })
                ->unique('user_id') // pastikan user lawan unik
                ->values();

            return response()->json([
                'status'  => 'success',
                'message' => 'Unique chat users retrieved successfully.',
                'data'    => $uniqueUsers
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }






    // public function get_dataChat($userId, $targetId)
    // {
    //     try {
    //         $chats = tr_chat::with('sender')
    //             ->where(function ($q) use ($userId, $targetId) {
    //                 $q->where('sender_id', $userId)
    //                     ->where('receiver_id', $targetId);
    //             })
    //             ->orWhere(function ($q) use ($userId, $targetId) {
    //                 $q->where('sender_id', $targetId)
    //                     ->where('receiver_id', $userId);
    //             })
    //             ->orderBy('created_at', 'asc')
    //             ->get();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Data retrieved successfully.',
    //             'data' => $chats
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Something went wrong.',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }


    public function kirim_chat(Request $request)
    {
        try {
            $userId = Auth::id();

            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message'     => 'required|string',
                'message_type' => 'nullable|string',
            ]);

            $chat = tr_chat::create([
                'sender_id'    => $userId,
                'receiver_id'  => $request->receiver_id,
                'message'      => $request->message,
                'message_type' => $request->message_type ?? 'text',
                'is_read'      => false,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim.',
                'data' => $chat
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim pesan.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function get_dataChatByReceiver()
    {
        try {
            $chats = tr_chat::with('sender')
                ->where('receiver_id', auth()->user()->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($chat) {
                    return [
                        'id_chat' => $chat->id,
                        'sender_id' => $chat->sender_id,
                        'nama_pengirim' => $chat->sender ? $chat->sender->name : null,
                        'receiver_id' => $chat->receiver_id,
                        'message' => $chat->message,
                        'message_type' => $chat->message_type,
                        'is_read' => $chat->is_read,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully.',
                'data' => $chats
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function send_chat(Request $request)
    {
        try {
            $validated = $request->validate([
                'message'       => 'required|string',
                'message_type'  => 'in:text,image,file',
                'room_id'       => 'nullable|exists:tr_chat_rooms,id',
                'receiver_id'   => 'nullable|exists:users,id',
                'room_name'     => 'nullable|string'
            ]);

            // Kalau room_id ga ada, bikin room dulu
            if (empty($validated['room_id'])) {
                if (empty($validated['receiver_id'])) {
                    return response()->json(['message' => 'receiver_id wajib diisi kalau room_id kosong'], 400);
                }

                // Buat room baru
                $room = \App\Models\tr_chat_room::create([
                    'name' => $validated['room_name'] ?? 'Chat Room'
                ]);

                // Tambahkan anggota room
                \App\Models\tr_chat_room_members::insert([
                    ['room_id' => $room->id, 'user_id' => Auth::id()],
                    ['room_id' => $room->id, 'user_id' => $validated['receiver_id']]
                ]);

                $room_id = $room->id;
            } else {
                $room_id = $validated['room_id'];
            }

            // Kirim chat
            $chat = \App\Models\tr_chat::create([
                'room_id'       => $room_id,
                'sender_id'     => Auth::id(),
                'receiver_id'   => $validated['receiver_id'] ?? null,
                'message'       => $validated['message'],
                'message_type'  => $validated['message_type'],
                'is_read'       => false,
            ]);

            return response()->json([
                'status'  => 'success',
                'data'    => $chat,
                'message' => 'Pesan berhasil dikirim'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function n_kirim_chat(Request $request)
    {
        try {
            $validated = $request->validate([
                'outgoing_msg_id' => 'required|string', // sebenarnya ini penerima
                'msg'             => 'required|string',
            ]);

            // Betulkan posisi sender & receiver
            $chat = tr_pesan::create([
                'incoming_msg_id' => $request->outgoing_msg_id,      // penerima (dari request)
                'outgoing_msg_id' => auth()->user()->id,             // pengirim (yang login)
                'msg'             => $validated['msg'],
            ]);

            return response()->json([
                'status'  => 'success',
                'data'    => $chat,
                'message' => 'Pesan berhasil dikirim'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function listChatWithLastMessages()
    {
        try {
            $authId = auth()->id();

            // Ambil semua user selain yang login
            $users = User::where('id', '!=', $authId)->get();

            $result = $users->map(function ($user) use ($authId) {
                // Ambil pesan terakhir antara user login dan user ini
                $lastChat = DB::table('tr_pesans')
                    ->where(function ($query) use ($user) {
                        $query->where('incoming_msg_id', $user->id)
                            ->orWhere('outgoing_msg_id', $user->id);
                    })
                    ->where(function ($query) use ($authId) {
                        $query->where('incoming_msg_id', $authId)
                            ->orWhere('outgoing_msg_id', $authId);
                    })
                    ->orderByDesc('id')
                    ->first();

                // Gabungkan data user + last chat
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_chat' => $lastChat,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'List chat with last messages',
                'data' => $result
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function listchat()
    {
        try {
            // Ambil ID user yang lagi login
            $currentUserId = auth()->id();

            // Ambil semua user selain yang login
            $users = User::where('id', '!=', $currentUserId)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully.',
                'data' => $users
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function room($incomingId)
    {
        $authId = Auth::id();

        $roomChat = DB::table('tr_pesans as tp')
            ->leftJoin('users as u1', 'u1.id', '=', 'tp.outgoing_msg_id')
            ->leftJoin('users as u2', 'u2.id', '=', 'tp.incoming_msg_id')
            ->where(function ($query) use ($authId, $incomingId) {
                $query->where('tp.outgoing_msg_id', $authId)
                    ->where('tp.incoming_msg_id', $incomingId);
            })
            ->orWhere(function ($query) use ($authId, $incomingId) {
                $query->where('tp.outgoing_msg_id', $incomingId)
                    ->where('tp.incoming_msg_id', $authId);
            })
            ->orderBy('tp.id')
            ->select(
                'tp.*',
                'u1.name as outgoing_name',
                'u2.name as incoming_name'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $roomChat
        ]);
    }



    public function lastChat(Request $request)
    {
        try {
            $authId = auth()->user()->id;
            $targetIds = $request->target_ids;

            $lastChats = [];

            foreach ($targetIds as $targetId) {
                $lastChat = DB::table('tr_pesans')
                    ->where(function ($query) use ($targetId) {
                        $query->where('incoming_msg_id', $targetId)
                            ->orWhere('outgoing_msg_id', $targetId);
                    })
                    ->where(function ($query) use ($authId) {
                        $query->where('outgoing_msg_id', $authId)
                            ->orWhere('incoming_msg_id', $authId);
                    })
                    ->orderByDesc('id')
                    ->first();

                $lastChats[] = [
                    'target_id' => $targetId,
                    'last_chat' => $lastChat
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $lastChats
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
