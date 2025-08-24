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
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $name = $request->input('name') ?? null;

        $group = Group::query()
            ->with([
                'conversation.participants' => function ($query) use ($name) {
                    $query->whereHas('participant', function ($q) use ($name) {
                        $q->when($name, function ($q) use ($name) {
                            $q->where('name', 'like', '%' . $name . '%');
                        });
                    })
                        ->with([
                            'participant' => function ($q) use ($name) {
                                $q->select('id', 'name', 'avatar')
                                    ->when($name, function ($q) use ($name) {
                                        $q->where('name', 'like', '%' . $name . '%');
                                    });
                            }
                        ]);
                }
            ])
            ->find($id);


        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }
        return $this->success($group, 'Group info retrieved successfully', 200);
    }
}
