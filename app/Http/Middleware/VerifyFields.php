<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Flyer;
use Illuminate\Http\Request;


class VerifyFields
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //Validating the fields parameter 
        $fields = $request->query('fields');
        if (!is_null($fields)) {
            
            $fieldsArray = explode(',', $fields);
            $flyer = new Flyer();
            $flyerFillable = $flyer->getFillable();
            $notAllowedFields = '';

            foreach ($fieldsArray as $field) {
                if (!in_array($field,$flyerFillable)) {
                    $notAllowedFields .= ','.$field;
                }
            }

            if ($notAllowedFields != '') {
                //Returning Bad Request
                $error = [
                    'message' => 'Bad Request',
                    'debug' => 'Not allowed fields: '.substr($notAllowedFields,1)
                ];

                return response()->error(400, $error);
            }
        }

        return $next($request);
    }
}
