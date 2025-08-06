<?php

namespace App\Http\Controllers\API\Auth;

use ALifAhmmed\HelperPackage\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\UserFollower;
use App\Models\UserLink;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileUpdateController extends Controller
{
    use ApiResponse;

    public function changeEmail(Request $request)
    {
        $validator = $request->validate([
            'password' => 'required|string',
            'email' => 'required|string|confirmed',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $user = auth()->user();

            if (!Hash::check($request->password, $user->password)) {
                return $this->error('Incorrect Password', 402);
            }

            $user->email = $request->email;
            $user->save();
            return $this->ok('Email changed successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $user = auth()->user();

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->error('Incorrect Current Password', 402);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            return $this->ok('Password changed successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileUrl(Request $request)
    {
        $validator = $request->validate([
            'url' => 'nullable|string|max:255',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $user = auth()->user();

            $url = $request->url;

            if ($user->url == $url) {
                return $this->error('Profile url cannot be same as your current profile url');
            }

            $exist_url = User::where('url', $url)->exists();

            if ($exist_url) {
                return $this->error('Profile url already exists');
            }

            $user->url = $url ?? $user->url;
            $user->save();

            return $this->ok('Profile info updated successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileAvatarUpload(Request $request)
    {
        $validator = $request->validate([
            'avatar' => 'image|max:2048',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $user = auth()->user();

            $image = $request->avatar;

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Helper::fileDelete($user->avatar);
                }
                $imagePath = Helper::fileUpload($request->file('avatar'), 'avatar', time() . '_' . $request->file('avatar')->getClientOriginalName());
                $user->avatar = $imagePath;
            }

            $user->save();

            return $this->ok('Profile image upload successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileAvatarRemove()
    {
        try {

            $user = auth()->user();

            if ($user->avatar) {
                Helper::fileDelete($user->avatar);
                $user->avatar = null;
                $user->save();

                return $this->ok('Profile image removed successfully');
            }

            return $this->error('Profile image not found', 404);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileAbout(Request $request)
    {
        $validator = $request->validate([
            'about' => 'nullable|string',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $user = auth()->user();

            $about = $request->about;

            $user->about = $about;
            $user->save();

            return $this->ok('Profile about updated successfully', $user, 200);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileInfo(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'status' => 200,
            'data' => $user,
        ]);
    }

    public function updateDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "bio"           => 'nullable|string',
            "youtube"       => 'nullable|string',
            "facebook"      => 'nullable|string',
            "tiktok"        => 'nullable|string',
            "instagram"     => 'nullable|string',
            "blog"          => 'nullable|string',
            "location"      => 'required|string',
            "website"       => 'nullable|array',
            "website*"      => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        if ($request->website) {

            UserLink::where('user_id', $user->id)->delete();

            foreach ($request->website as $website) {
                UserLink::create([
                    'user_id' => $user->id,
                    'url'     => $website,
                ]);
            }
        }

        $update = $user->update([
            "bio"           => $request->bio,
            "youtube"       => $request->youtube,
            "facebook"      => $request->facebook,
            "tiktok"        => $request->tiktok,
            "instagram"     => $request->instagram,
            "blog"          => $request->blog,
            "location"      => $request->location,
        ]);

        if ($update) {
            return response()->json([
                'success' => 'Profile information successfully updated.',
            ]);
        } else {
            return response()->json([
                'error' => 'Failed to update profile.',
            ], 500);
        }
    }

    public function profileFollow(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'follower_id'   => 'required',
        ]);

        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors(), 'status' => 422]);
        }

        $user = Auth::user();

        $existFollow = UserFollower::where('follower_user_id', $request->follower_id)->where('user_id', $user->id)->first();

        if ($existFollow) {
            return $this->success('User already follow', $existFollow, 201);
        }

        $data = UserFollower::create([
            'user_id'       => $user->id,
            'follower_user_id'   => $request->follower_id,
        ]);

        if ($data) {
            return $this->success('Followed successfully', $data, 200);
        } else {
            return $this->error('Something went wrong.', 500);
        }
    }

    public function profileUnfollow(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'follower_id'   => 'required',
        ]);

        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors(), 'status' => 422]);
        }

        $user = Auth::user();

        $data = UserFollower::where('follower_user_id', $request->follower_id)->where('user_id', $user->id)->first();

        if ($data) {

            $data->delete();

            return $this->success('Unfollowed successfully', [], 200);
        }

        return $this->error('User follow not found', 404);

    }

}
