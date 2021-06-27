<?php

namespace App\Http\Controllers;

use App\Http\Resources\addResource;
use App\Http\Resources\HomeResource;
use App\Http\Resources\LiteAddResource;
use App\Models\City;
use App\Models\Client;
use App\Models\Governorate;
use App\Models\Lost;
use Helper\NotificationHelper;
use Illuminate\Http\Request;
use App\Models\Category;

class MainController extends Controller
{
    public function responseJson($status, $message, $data = null)
    {
        switch ($message){
            case 'تم التحميل':
                $message = __('api.response.data_loaded');
                break;
            case 'تم الإضافة بنجاح':
                $message = __('api.response.data_added_successful');
                break;
            case 'تعذر الحصول علي البيانات':
                $message = __('api.response.data_not_found');
                break;
            case 'تم الحذف بنجاح':
                $message = __('api.response.data_deleted_successful');
                break;
            case 'تم القراءه بنجاح':
                $message = __('api.response.data_read_successful');
                break;
        }

        $response = [
            'status' => $status,
            'massage' => $message,
            'data' => $data,
        ];

        return response()->json($response);
    }

    public function list_categories()
    {

        $categories = Category::get();

        return $this->responseJson(1, 'تم التحميل', $categories);
    }

    public function home()
    {

        $categories = Category::get();

        return $this->responseJson(1, 'تم التحميل', HomeResource::collection($categories));
    }

    public function list_governorates()
    {

        $goveronrates = Governorate::all();

        return $this->responseJson(1, 'تم التحميل', $goveronrates);
    }

    public function list_cities(Request $request)
    {

        $cities = City::where(function ($q) use ($request) {
            if ($request->gov_id)
                $q->where('governorate_id', '=', $request->gov_id);

        })->get();

        return $this->responseJson(1, 'تم التحميل', $cities);

    }

    ////////////////////////////////////////
    /// lost things

    public function listLosts(Request $request)
    {

        $records = Lost::where(function ($q) use ($request) {

            if ($request->category_id) {
                $q->where('category_id', $request->category_id);
            }

            if ($request->city_id) {
                $q->where('city_id', $request->city_id);
            }

            if ($request->type && in_array($request->type, ['lost', 'found'])) {
                $q->where('type', $request->type);
            }

            if ($request->search_key) {
                $q->where('name', 'Like', '%' . $request->search_key . '%');
                $q->orWhere('description', 'Like', '%' . $request->search_key . '%');
            }

        })->latest()->get();

        return $this->responseJson(1, 'تم التحميل', LiteAddResource::collection($records));
    }

    public function showLosts(Request $request)
    {

        $records = Lost::where(function ($q) use ($request) {

            if ($request->id) {
                $q->where('id', $request->id);
            }

        })->first();


        if ($records)
            return $this->responseJson(1, 'تم التحميل', new addResource($records));
        else
            return $this->responseJson(1, 'تم التحميل', []);
    }

    public function myAds(Request $request)
    {

        $user = $request->user();
        $records = $user->losts()->where(function ($q) use ($request) {

            if ($request->category_id) {
                $q->where('category_id', $request->category_id);
            }

        })->latest()->get();

        return $this->responseJson(1, 'تم التحميل', LiteAddResource::collection($records));
    }

    public function adsAddNew(Request $request)
    {

        $user = $request->user('client');

        $rules =
            [
                'city_id' => 'required|exists:cities,id',
                'category_id' => 'required|exists:categories,id',
                'type' => 'required|in:lost,found',
                'name' => 'required',
                'photos' => 'required|array',
                'photos.*' => 'required|image|mimes:jpeg,jpg,png,gif',
            ];

        $validator = validator()->make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->responseJson(0, $validator->errors()->first(), $validator->errors());
        }

        $record = $user->losts()->create($request->all());

        if ($request->photos) {
            foreach ($request->photos as $photo) {

                \Helper\Attachment::addAttachment(
                    $photo,
                    $record,
                    'losts');
            }
        }
        $clients = Client::where('city_id' , $request->city_id)->pluck('id')->toArray();

        $title = $clients->name .
            __('api.notification.ad.title');
        $body = $clients->name .
            __('api.notification.ad.'.$request->type).
            $record->name;
        NotificationHelper::sendNotification($record,$clients,'clients',$title,$body,'ad',new LiteAddResource($record));

        return $this->responseJson(1, 'تم الإضافة بنجاح');
    }


    ///////// Notifications //////////////////////////


    public function notifications(Request $request)
    {
        $user = $request->user('client');
        $records = $user->notifications()->where(function ($q) use ($request) {

            if ($request->notification_id) {

                $q->where('notification_id', $request->notification_id);
            }

        })->latest()->get();

        foreach ($records as $record) {

            $notification = $user->notifications()->find($record->id);

            $notification->clients()->updateExistingPivot($user->id, ['is_read' => 1]);
        }

        return $this->responseJson(1, 'تم التحميل', $records);
    }

    public function deleteNotification(Request $request)
    {
        $user = $request->user('client');

        $rules =
            [
                'notification_id' => 'required|exists:notifications,id'
            ];

        $data = validator()->make($request->all(), $rules);

        if ($data->fails()) {
            return $this->responseJson(0, $data->errors()->first());
        }

        $notification = $user->notifications()->find($request->notification_id);

        if (!$notification)
            return $this->responseJson(0, 'تعذر الحصول علي البيانات');

        $notification->clients()->detach($user->id);

        if (!$notification->clients()->count() || !$notification->deliveries()->count())
            $notification->delete();

        return $this->responseJson(1, 'تم الحذف بنجاح');
    }

    public function readNotification(Request $request)
    {
        $user = $request->user('client');

        $rules =
            [
                'notification_id' => 'required|exists:notifications,id'
            ];

        $data = validator()->make($request->all(), $rules);

        if ($data->fails()) {
            return $this->responseJson(0, $data->errors()->first());
        }

        $notification = $user->notifications()->find($request->notification_id);

        if (!$notification)
            return $this->responseJson(0, 'تعذر الحصول علي البيانات');

        $notification->clients()->updateExistingPivot($user->id, ['is_read' => 1]);

        return $this->responseJson(1, 'تم القراءه بنجاح');
    }

    public function unReadNotificationCount(Request $request)
    {
        $user = $request->user('client');

        $notificationCount = $user->notifications()->where('is_read', 0)->count();

        return $this->responseJson(1, 'تم التحميل', ['count' => $notificationCount]);
    }

    ///////// ///////////// /////////////////////////

}
