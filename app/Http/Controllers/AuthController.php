<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
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

    /**
     * @return int
     */
    public function getPinCode()
    {
        return 1111;
//        return rand(1000 , 9999);
    }

    /**
     * @return string
     */
    public function getPinCodeExpiredDate()
    {
        return Carbon::now()->addMinutes(5);
    }

    /**
     * @return string
     */
    public function checkPinCodeExpiredDate($expired_date)
    {
        $check = Carbon::now() > $expired_date ? false : true;
        return $check;
    }


    /**
     * @param Request $request
     * @param $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function createToken(Request $request , $client)
    {
        $client->api_token = $client->api_token ? $client->api_token : Str::random(100);
        $client->save();

        if ($client->tokens()->where('serial_number', $request->serial_number)->first()) {
            $phone_token = $client->tokens()->where('serial_number', $request->serial_number)->first();
            $phone_token->update([

                'token' => $request->token,
                'os' => $request->os,
                'serial_number' => $request->serial_number
            ]);

        } else {

            $client->tokens()->create(['token' => $request->token, 'os' => $request->os, 'serial_number' => $request->serial_number]);

        }

        return $this->responseJson(1, 'تم التسجيل بنجاح',
            [
                'token' => $client->api_token,
                'user' => $client,
            ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request){
        $rules =
            [
                'phone' => 'required|unique:clients,phone|regex:/(01)[0-9]{9}/',
                'password' => 'required|confirmed|min:6',
            ];

        $validator = validator()->make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->responseJson(0, $validator->errors()->first(), $validator->errors());
        }

        $client = Client::create($request->all());
        $client->password = Hash::make($request->password);
        $client->save();

        return $this->sendPinCode($request , $client);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $rules =
            [
                'phone' => 'required|exists:clients,phone',
                'password' => 'required|min:6',
                'token' => 'required',
                'serial_number' => 'required',
                'os' => 'required|in:android,ios',
            ];

        $data = validator()->make($request->all(), $rules);

        if ($data->fails())
            return $this->responseJson(0, $data->errors()->first(), $data->errors());

        $client = Client::where(['phone' => $request->phone])->first();

        if ($client) {

            if (Hash::check($request->password, $client->password)) {

                // check user confirmation and activation
                ///
                if ($client->is_active == 0)
                    return $this->responseJson(0, 'الرجاء تأكيد الحساب أولا');

                //create token
                return $this->createToken($request , $client);
            }
        }

        return $this->responseJson(0, 'رقم الهاتف غير صحيح');
    }


    public function sendPinCode(Request $request , $model = null)
    {
        if (!$model) {

            $rules =
                [
                    'phone' => 'required|exists:clients,phone',
                ];


            $data = validator()->make($request->all(), $rules);

            if ($data->fails()) {

                return $this->responseJson(0, $data->errors()->first(), $data->errors());
            }

            $model = Client::where(['phone' => $request->phone])->first();

            if (!$model)
                return $this->responseJson(0, 'رقم الهاتف غير صحيح');

        }

        $pin_code = $this->getPinCode();
        $model->pin_code = $pin_code;

//        Mail::to($model->email)->send(new SendPinCode($model));

        $model->pin_code_date_expired = $this->getPinCodeExpiredDate();
        $model->save();

        return $this->responseJson(1, 'تم إرسال الكود بنجاح');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeAccount(Request $request)
    {
        $rules =
            [
                'phone' => 'required|exists:clients,phone',
                'pin_code' => 'required|numeric',
                'token' => 'required',
                'serial_number' => 'required',
                'os' => 'required|in:android,ios',
            ];


        $data = validator()->make($request->all(), $rules);

        if ($data->fails()) {

            return $this->responseJson(0, $data->errors()->first(), $data->errors());
        }

        $record = Client::where(['phone' => $request->phone])->first();

        if ($record) {

            if ($record->is_active == 1)
                return $this->responseJson(0, 'تم تأكيد الحساب بالفعل');


            if ($record->pin_code == $request->pin_code) {

                //check pin code date time expired
                if ($this->checkPinCodeExpiredDate($record->pin_code_date_expired)) {

                    $record->pin_code = $this->getPinCode();
                    $record->is_active = 1;
                    $record->save();

                    return $this->createToken($request, $record);
                }

                return $this->responseJson(0, 'انتهت صلاحيه كود التفعيل');
            }

            return $this->responseJson(0, 'كود التفعيل خطأ');
        }
        return $this->responseJson(0, 'رقم الهاتف غير صحيح');
    }

    public function resetPasswordOutAuth(Request $request)
    {
        $rules =
            [
                'phone' => 'required|exists:clients,phone',
                'pin_code' => 'required|numeric',
                'password' => 'required|confirmed|min:6',
            ];


        $data = validator()->make($request->all(), $rules);

        if ($data->fails()) {

            return $this->responseJson(0, $data->errors()->first(), $data->errors());
        }

        $record = Client::where(['is_active' => 1, 'phone' => $request->phone])->first();

        if ($record) {
            if ($record->pin_code == $request->pin_code) {

                //check pin code date time expired
                if ($this->checkPinCodeExpiredDate($record->pin_code_date_expired)) {

                    $record->pin_code = $this->getPinCode();
                    $record->password = Hash::make($request->password);
                    $record->save();

                    return $this->responseJson(1, 'تم التحديث بنجاح');
                }

                return $this->responseJson(0, 'انتهت صلاحيه كود التفعيل');
            }

            return $this->responseJson(0, 'كود التفعيل خطأ');
        }
        return $this->responseJson(0, 'رقم الهاتف غير صحيح');

    }

    public function resetPasswordInAuth(Request $request)
    {
        $rules =
            [
                'old_password' => 'required|min:6',
                'password' => 'required|confirmed|min:6',
            ];

        $data = validator()->make($request->all(), $rules);

        if ($data->fails()) {

            return $this->responseJson(0, $data->errors()->first(), $data->errors());
        }

        $record = $request->user('client');

        if (Hash::check($request->old_password, $record->password)) {

            $record->password = Hash::make($request->password);
            $record->save();

            return $this->responseJson(1, 'تم التحديث بنجاح');

        } else {

            return $this->responseJson(0, "كلمة المرور القديمه خاطئه ");
        }

    }

    public function resetPassword(Request $request)
    {
        return auth('client')->check() ? $this->resetPasswordInAuth($request) : $this->resetPasswordOutAuth($request);
    }

    public function showProfile(Request $request)
    {
        $user = $request->user();

        return $this->responseJson(1, 'تم التحميل', ['token' => $user->api_token, 'user' => $user]);

    }

    public function updateProfile(Request $request)
    {
        $record = $request->user('client');

        $rules =
            [
                'name' => 'nullable|max:70',
                'email' => 'nullable|unique:clients,email,' . $record->id . '|email',
                'city_id' => 'nullable|exists:cities,id',
                'photo' => 'nullable|image|mimes:jpeg,jpg,png,gif',
            ];

        $validator = validator()->make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->responseJson(0, $validator->errors()->first(), $validator->errors());
        }

        $record->update([
            'name' => $request->name ? $request->name : $record->name,
            'email' => $request->email ? $request->email : $record->email,
            'city_id' => $request->city_id ? $request->city_id : $record->city_id,
        ]);

        if ($request->hasFile('photo')) {
            \Helper\Attachment::updateAttachment(
                $request->file('photo'),
                $record->photo,
                $record,
                'clients');
        }

        return $this->responseJson(1, 'تمت العمليه',[
            'token' => $record->api_token,
            'user' => $record,
        ]);
    }

    public function logout(Request $request)
    {
        $rules =
            [
                'serial_number' => 'required|exists:tokens,serial_number',
            ];


        $data = validator()->make($request->all(), $rules);

        if ($data->fails()) {

            return $this->responseJson(0, $data->errors()->first(), $data->errors());
        }

        $request->user('client')->tokens()->where('serial_number', $request->serial_number)->delete();

        return $this->responseJson(1, Translation::trans('you are logout successful'));
    }
}
