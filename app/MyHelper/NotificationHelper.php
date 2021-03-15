<?php
namespace Helper;

use App\Customers;
use App\Models\Token;
use Helper\Helper;

class NotificationHelper
{



    static function notifyByFirebase($title, $body, $tokens, $data = [] , $imageUrl = null, $sound = 'on')        // paramete 5 =>>>> $type
    {

        $registrationIDs = $tokens;

        $fcmMsg = array(
            'body' => $body,
            'title' => $title,
            'color' => "#203E78"
        );

        $sound == 'on' ? $fcmMsg += ['sound' => "default"] : null;
        $imageUrl ? $fcmMsg += ['image' => $imageUrl] : null;

        $fcmFields = array(
            'registration_ids' => $registrationIDs,
            'priority' => 'high',
            'notification' => $fcmMsg,
            'data' => $data
        );
        $headers = array(
            'Authorization: key=AAAAvcrUe3s:APA91bG58npTC5VYTqorRNd9PeA-Q_arMdtzAG3R2uctMVGzrB77rHT8S_NRXQqnxzb1pQc7fQXUh2kfxWEXEMep7bH-4aMdv9FZ314ZKPwS2EOwymnBBnqfcMZEII_T2NxnPsQWwe_b',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    static function sendNotification($model, $notifierIds, $relation, $title, $body, $data_type = 'admin', $data = [], $image = null): void
    {
        $notifierIds = (array)$notifierIds;

        $notifierIds = Customers::whereIn('id' , $notifierIds)->where('push_notification' , 1)->pluck('id')->toArray();

        if (count($notifierIds)) {
            $notification = $model->notifications()->create([
                'title' => $title,
                'body' => $body
            ]);

            if ($image) {
                Attachment::addAttachment($image, $notification, 'notifications', ['size' => 600, 'quality' => 50]);
            }

            $notification->customers()->attach($notifierIds);

            $mute_query = Token::CheckType($relation)->whereIn('tokenable_id', $notifierIds)->whereHas('customer' , function ($q) {

                $q->where('notification_sound' , 0);
            });

            $active_query = Token::CheckType($relation)->whereIn('tokenable_id', $notifierIds)->whereHas('customer' , function ($q) {

                $q->where('notification_sound' , 1);
            });

            if ($mute_query->count()) {

                $mute_query->chunk(999, function ($records) use ($notification, $data, $data_type) {

                    $tokens = $records->pluck('token')->toArray();

                    $data =
                        [
                            $data_type => $data
                        ];

                    //send notification for client tokens
                    $send = self::notifyByFirebase($notification->title, $notification->body, $tokens, $data, $notification->photo,'off');
//                    info($send);
                });
            }

            if ($active_query->count()) {

                $active_query->chunk(999, function ($records) use ($notification, $data, $data_type) {

                    $tokens = $records->pluck('token')->toArray();

                    $data =
                        [
                            $data_type => $data
                        ];

                    //send notification for client tokens
                    $send = self::notifyByFirebase($notification->title, $notification->body, $tokens, $data, $notification->photo);
//                    info($send);
                });

            }
        }
    }

}