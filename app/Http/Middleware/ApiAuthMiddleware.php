<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

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
        $token = $request->header('Authorization'); //get token dari header
        $authenticate =true; //status authentikasi

        //cek token 
        if (!$token)  //jika tidak ada token
        {
            $authenticate =false; //ubah authenticate menjadi false
        } else //jika ada token
        {
            $user = User::where('token', $token)->first();
            if ($user) //jika token nya  ada didatabase
            {
                date_default_timezone_set('Asia/Jakarta');
                $now = strtotime(date('Y-m-d h:m:s'));
                $expired = strtotime(date($user->token_expired));
                
                if ( $now >= $expired  || empty($user->token_expired)){
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
