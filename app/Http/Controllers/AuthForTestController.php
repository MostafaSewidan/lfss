<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class AuthForTestController extends Controller
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

    public function register(Request $request) {
        $data = validator()->make($request->all(), $rules);

        if ($data->fails()) {

            return $this->responseJson(0, $data->errors()->first(), $data->errors());
        }

        if($request->phone) {
            $old_client = Client::where('phone' , '=', $request->phone)->first();

            if($old_client) {
                return $this->responseJson(0,'رقم الهاتف مسجل مسبقا');
            } else {
                $client = Client::create([
                    'phone' => $request->phone,
                    'password' => $request->password
                ]);

                return $this->responseJson(1,'تمت العمليه بنجاح');
            }
        }else {
            return $this->responseJson(0,'رقم الهاتف مطلوب داروري');
        }

    }
}
