<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\MessageSent;

class MessageController extends Controller
{
    /**
     * Display chat view with users.
     */
    public function chat($receiverId = null)
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $receiver = $receiverId ? User::findOrFail($receiverId) : null;
        return view('chat', compact('users', 'receiver'));
    }

    /**
     * Send a message and handle bot replies via Hugging Face.
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'message'     => 'required|string',
        ]);

        try {
            // Save user's message
            $message = Message::create([
                'sender_id'   => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message'     => $request->message,
            ]);

            Log::info("User ({$message->sender_id}) sent: {$message->message}");
            broadcast(new MessageSent($message))->toOthers();

            $botMessage = null;

            // Chatbot logic (id=0)
            if ($request->receiver_id == 0) {
                $systemPrompt = "You are a supportive, empathetic, and professional mental health therapist. 
                                 You listen carefully, validate feelings, and encourage self-care. 
                                 You do not give medical diagnoses but provide comfort and helpful coping strategies.";

                // Hugging Face Chat Completions API
                $hfResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_TOKEN'),
                    'Content-Type'  => 'application/json',
                ])->post('https://router.huggingface.co/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-120b:cerebras',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $request->message],
                    ],
                    'stream' => false,
                ]);

                if ($hfResponse->successful()) {
                    $botReply = $hfResponse->json()['choices'][0]['message']['content'] ?? 
                                "Sorry, I didn’t understand that.";
                } else {
                    $errorMessage = "Hugging Face API error. Status: {$hfResponse->status()}, Body: {$hfResponse->body()}";
                    Log::error($errorMessage);
                    $botReply = "Sorry, I'm having trouble connecting right now.";
                }

                // Save bot reply
                $botMessage = Message::create([
                    'sender_id'   => 0,
                    'receiver_id' => Auth::id(),
                    'message'     => $botReply,
                ]);

                Log::info("Bot replied: {$botMessage->message}");
                broadcast(new MessageSent($botMessage))->toOthers();
            }

            return response()->json([
                'user_message' => $message,
                'bot_message'  => $botMessage,
            ]);

        } catch (\Exception $e) {
            Log::error('MessageController::send error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal server error. Check logs for details.'
            ], 500);
        }
    }

    /**
     * Fetch conversation history between two users.
     */
    public function fetch($receiverId)
    {
        return Message::where(function ($q) use ($receiverId) {
            $q->where('sender_id', Auth::id())
              ->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($receiverId) {
            $q->where('sender_id', $receiverId)
              ->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();
    }
}
