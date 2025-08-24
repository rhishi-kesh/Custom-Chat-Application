<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageAttachmentResource;
use App\Models\Group;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GroupMediaController extends Controller
{
    use ApiResponse;
    /**
     * Handle the incoming request to get group media.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, int $id)
    {
        $group = Group::find($id);

        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }

        $mediaMessages = $group->conversation
            ->messages()
            ->whereHas('attachments')
            ->with('attachments')
            ->latest()
            ->paginate(20);

        return $this->success(MessageAttachmentResource::collection($mediaMessages), 'Group media retrieved successfully', 200);
    }
}
