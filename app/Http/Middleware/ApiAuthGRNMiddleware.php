<?php

namespace App\Http\Middleware;

use App\Models\AccessView;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ApiAuthGRNMiddleware
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
        $token = $request->header('Authorization');
        $authenticate = true;
        if ($request->isMethod("GET")){

            try {
                $access = AccessView::where("token", $token)->where('module_sub_name', 'grn')->first();
                if (!$access){
                    $authenticate =false;
                }
            } catch (\Throwable $th) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "general" => [
                            $th->getMessage()
                        ]
                    ]
                    ],500));
            }
        }

        if ($request->isMethod("POST")){
            try {
                $access = AccessView::where("token", $token)->where('module_sub_name', 'grn')->where("xCreate", 1)->first();
                if (!$access){
                    $authenticate =false;
                }
            } catch (\Throwable $th) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "general" => [
                            $th->getMessage()
                        ]
                    ]
                    ],500));
            }
        }
        if ($request->isMethod("PATCH")){
            try {
                $access = AccessView::where("token", $token)->where('module_sub_name', 'grn')->where("xApprove", 1)->first();
                if (!$access){
                    $authenticate =false;
                }
            } catch (\Throwable $th) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "general" => [
                            $th->getMessage()
                        ]
                    ]
                    ],500));
            }
        }
        if ($request->isMethod("PUT")){
            try {
                $access = AccessView::where("token", $token)->where('module_sub_name', 'grn')->where("xUpdate", 1)->first();
                if (!$access){
                    $authenticate =false;
                }
            } catch (\Throwable $th) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "general" => [
                            $th->getMessage()
                        ]
                    ]
                    ],500));
            }
        }
        if ($request->isMethod("DELETE")){
            try {
                $access = AccessView::where("token", $token)->where('module_sub_name', 'grn')->where("xDelete", 1)->first();
                if (!$access){
                    $authenticate =false;
                }
            } catch (\Throwable $th) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "general" => [
                            $th->getMessage()
                        ]
                    ]
                    ],500));
            }
        }

        if ($authenticate) {
            return $next($request);
        } else {
            return response()->json([
                "errors" =>[
                    "general" => [
                        "You Don't Have Permission"
                    ]
                ]
        ])->setStatusCode(403);
        }
    }
}
