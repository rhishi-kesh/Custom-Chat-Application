<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeleteMessageController extends Controller
{
    use ApiResponse;

    /**
     * Handle the incoming request to delete a message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, int $id)
    {
        $validator = Validator::make($request->query(), [
            'key' => ['required', 'in:me,everyone', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $message = Message::withTrashed()->find($id);
        if (!$message) {
            return $this->error([], 'Message not found', 404);
        }

        $key = $request->query('key');

        if ($key === 'me') {
            $message->delete(); // Soft delete
            return $this->success([], 'Message deleted for you');
        }

        // key === 'everyone'
        if ($message->sender_id !== $user->id) {
            return $this->error([], 'You are not authorized to delete this message for everyone', 403);
        }

        $message->forceDelete(); // Permanent delete
        return $this->success([], 'Message deleted for everyone');
    }
}
