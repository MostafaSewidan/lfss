<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Governorate;
use App\Models\Lost;
use Illuminate\Http\Request;
use App\Models\Category;

class MainController extends Controller
{
    public function responseJson($status, $message, $data = null)
    {
        $response = [
                'status' => $status,
                'massage' => $message,
                'data' => $data,
            ];

        return response()->json($response);
    }

    public function list_categories(){

        $categories = Category::paginate(10);

        return $this->responseJson(1,'تم التحميل', $categories);
    }

    public function list_governorates(){

        $goveronrates = Governorate::all();

        return $this->responseJson(1,'تم التحميل' , $goveronrates);
    }

    public function list_cities(Request $request){

        if($request->gov_id)
        {
            $cities = City::where('governorate_id','=',$request->gov_id)->get();

            return $this->responseJson(1,'تم التحميل' , $cities);

        }else{
            return $this->responseJson(0,'gov_id required');
        }
    }

    ////////////////////////////////////////
    /// lost things

    public function listLosts(Request $request){

        $records = Lost::where(function ($q) use ($request){

            if($request->category_id) {
                $q->where('category_id' , $request->category_id);
            }

        })->latest()->paginate(10);

        return $this->responseJson(1,'تم التحميل' , $records);
    }

    public function myAds(Request $request){

        $user = $request->user();
        $records = $user->losts()->where(function ($q) use ($request){

            if($request->category_id) {
                $q->where('category_id' , $request->category_id);
            }

        })->latest()->paginate(10);

        return $this->responseJson(1,'تم التحميل' , $records);
    }

    public function adsAddNew(Request $request){

        $user = $request->user('client');

        $rules =
            [
                'city_id' => 'required|exists:cities,id',
                'category_id' => 'required|exists:categories,id',
                'type' => 'required|in:lost,found',
                'name' => 'required',
                'photo' => 'required|image|mimes:jpeg,jpg,png,gif',
            ];

        $validator = validator()->make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->responseJson(0, $validator->errors()->first(), $validator->errors());
        }

        $record = $user->losts()->create($request->all());

        if ($request->hasFile('photo')) {
            \Helper\Attachment::updateAttachment(
                $request->file('photo'),
                $record->photo,
                $record,
                'losts');
        }
        return $this->responseJson(1,'تم الإضافة بنجاح');
    }




    ///////// Notifications //////////////////////////


    public function notifications(Request $request)
    {
        $records = $request->user('client')->notifications()->where(function ($q) use ($request) {

            if ($request->notification_id) {

                $q->where('notification_id', $request->notification_id);
            }

        })->latest()->paginate(20);

        return $this->responseJson(1,'تم التحميل' , $records);
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

        $notificationCount = $user->notifications()->where('is_read' , 0)->count();

        return $this->responseJson(1, 'تم التحميل', ['count' => $notificationCount]);
    }

    ///////// ///////////// /////////////////////////

}
