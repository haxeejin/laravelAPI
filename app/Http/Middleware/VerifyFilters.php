<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyFilters
{   
    private $allowedFilters = [
        'category',
        'is_published'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $filters = $request->query('filter');
        if (!is_null($filters)) {
            
            $notAllowedFilters = '';
            foreach ($filters as $filterKey => $FilterValue) {

                if(!in_array($filterKey,$this->allowedFilters)){
                    $notAllowedFilters .= ','.$filterKey;
                }
            }

            if ($notAllowedFilters != '') {
                //Returning Bad Request
                $error = [
                    'message' => 'Bad Request',
                    'debug' => 'Not allowed filters: '.substr($notAllowedFilters,1)
                ];

                return response()->error(400, $error);
            }
        }
        return $next($request);
    }
}
