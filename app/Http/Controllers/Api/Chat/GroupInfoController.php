<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupInfoController extends Controller
{
    use ApiResponse;
    /**
     * Handle the incoming request to get group info.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, int $id)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $group = Group::query()
            ->whereHas('conversation.participants', function ($query) use ($user) {
                $query->where('participant_id', $user->id);
            })
            ->find($id);


        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }
        return $this->success($group, 'Group info retrieved successfully', 200);
    }
}
