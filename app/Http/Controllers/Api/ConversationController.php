<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Shop;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Liste toutes les conversations de l'utilisateur connecté
     * (côté client : ses conversations avec des boutiques)
     * (côté vendeur : les conversations reçues sur sa boutique)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $shop = $user->shop;

        if ($shop) {
            // Vendeur : conversations reçues sur sa boutique
            $conversations = Conversation::with([
                    'customer:id,name,avatar',
                    'lastMessage',
                ])
                ->withCount(['messages as unread' => function ($q) use ($user) {
                    $q->where('sender_id', '!=', $user->id)->where('is_read', false);
                }])
                ->where('shop_id', $shop->id)
                ->orderByDesc('last_message_at')
                ->get()
                ->map(function ($c) {
                    return [
                        'id'              => $c->id,
                        'with'            => $c->customer,
                        'lastMessage'     => $c->lastMessage,
                        'unread'          => (int) $c->unread,
                        'shop_id'         => $c->shop_id,
                        'customer_id'     => $c->customer_id,
                        'last_message_at' => $c->last_message_at,
                    ];
                });
        } else {
            // Client : ses conversations avec des boutiques
            $conversations = Conversation::with([
                    'shop:id,name,slug,logo',
                    'lastMessage',
                ])
                ->withCount(['messages as unread' => function ($q) use ($user) {
                    $q->where('sender_id', '!=', $user->id)->where('is_read', false);
                }])
                ->where('customer_id', $user->id)
                ->orderByDesc('last_message_at')
                ->get()
                ->map(function ($c) {
                    return [
                        'id'              => $c->id,
                        'with'            => $c->shop,
                        'lastMessage'     => $c->lastMessage,
                        'unread'          => (int) $c->unread,
                        'shop_id'         => $c->shop_id,
                        'customer_id'     => $c->customer_id,
                        'last_message_at' => $c->last_message_at,
                    ];
                });
        }

        $totalUnread = $conversations->sum('unread');

        return response()->json([
            'conversations' => $conversations,
            'total_unread'  => $totalUnread,
        ]);
    }

    /**
     * Récupère les messages d'une conversation
     * ET marque comme lus les messages de l'interlocuteur
     */
    public function show(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est bien membre de cette conversation
        if ($conversation->customer_id !== $user->id && optional($user->shop)->id !== $conversation->shop_id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Marquer les messages reçus comme lus
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $conversation->messages()
            ->with('sender:id,name,avatar')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'conversation' => $conversation->load(['shop:id,name,slug,logo', 'customer:id,name,avatar']),
            'messages'     => $messages,
        ]);
    }

    /**
     * Envoyer un message (crée la conversation si elle n'existe pas encore)
     */
    public function sendMessage(Request $request, int $shopId)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $shop = Shop::findOrFail($shopId);

        // Un vendeur ne peut pas s'envoyer un message à lui-même
        if ($shop->user_id === $user->id) {
            return response()->json(['message' => 'Vous ne pouvez pas vous écrire à vous-même'], 422);
        }

        // Créer ou récupérer la conversation
        $conversation = Conversation::firstOrCreate(
            ['shop_id' => $shop->id, 'customer_id' => $user->id],
            ['last_message_at' => now()]
        );

        // Créer le message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'content'         => $request->input('content'),
            'is_read'         => false,
        ]);

        // Mettre à jour last_message_at
        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'message'      => $message->load('sender:id,name,avatar'),
            'conversation' => $conversation,
        ], 201);
    }

    /**
     * Répondre à une conversation existante (vendeur ou client)
     */
    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $user = $request->user();

        // Vérifier que l'utilisateur est bien membre
        if ($conversation->customer_id !== $user->id && optional($user->shop)->id !== $conversation->shop_id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'content'         => $request->input('content'),
            'is_read'         => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json(
            $message->load('sender:id,name,avatar'),
            201
        );
    }

    /**
     * Nombre de messages non lus (pour le badge navbar)
     */
    public function unreadCount(Request $request)
    {
        $user    = $request->user();
        $shop    = $user->shop;

        $query = $shop
            ? Conversation::where('shop_id', $shop->id)
            : Conversation::where('customer_id', $user->id);

        $count = $query->withCount(['messages as unread_count' => function ($q) use ($user) {
            $q->where('sender_id', '!=', $user->id)->where('is_read', false);
        }])->get()->sum('unread_count');

        return response()->json(['unread' => $count]);
    }
}
