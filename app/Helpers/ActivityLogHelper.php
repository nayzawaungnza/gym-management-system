<?php

/*
 * Global helpers file with misc functions.
 */

 
if (!function_exists('saveActivityLog')) {
    function saveActivityLog($data)
    {
        $caused_user = auth()->check() ? auth()->user() : null;
        $subject = isset($data['subject']) ? $data['subject'] : null;
        $event = isset($data['event']) ? $data['event'] : null;
        $description = isset($data['description']) ? $data['description'] : null;
        $activity = activity();
        if ($caused_user) {
            $activity->causedBy($caused_user);
        }
        if ($subject) {
            $activity->performedOn($subject);
        }
        $activity
            ->event($event)
            ->log($description);
    }
}


if (!function_exists('getModelTypeForActivity')) {
    function getModelTypeForActivity($model)
    {
        $splitted = explode('\\', $model);
        $model_type = end($splitted);
        if ($model_type === config('constants.LABEL_NAME.USER')) {
            return 'User';
        }
        return $model_type;
    }
}