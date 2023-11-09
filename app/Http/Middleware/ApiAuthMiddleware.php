<?php

namespace App\Http\Middleware;

use App\Models\remember_token;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        date_default_timezone_set('Asia/Jakarta');
        $token = $request->header('Authorization'); //get token dari header
        $authenticate =true; //status authentikasi

        //cek token 
        if (!$token)  //jika tidak ada token
        {
            $authenticate =false; //ubah authenticate menjadi false
        } else //jika ada token
        {
            $gettoken = remember_token::where("token", $token)->first();
            if ($gettoken) //jika token nya  ada didatabase
            {
                $time = Carbon::now('Asia/Jakarta');
                $now =  strtotime($time->format('d-m-Y H:i:s'));
                $expired = strtotime(date($gettoken->token_expired));
                if ( $now >= $expired ){
                    $authenticate = false;
                } 
            }else{
                $authenticate = false;
            }
        }
        if ($authenticate) //jika user memang sudah login/terauthorized
        {
            return $next($request);
        } else { //jika tidak terauthorized
            return response()->json([
                "errors" =>[
                    "general" => [
                        "You Are Unauthorized or Your Token Has Been Expired"
                    ]
                ]
        ])->setStatusCode(401);
        }

        
    }
}
