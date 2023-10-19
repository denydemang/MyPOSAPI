<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Models\CompanyProfile;
use App\Models\remember_token;
use App\Models\User;
use App\Models\UserView;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request) : UserResource
    {
        $datavalidated = $request->validated();

        if(User::where("username", $datavalidated["username"])->count() >=1 ){
            throw new HttpResponseException(response([
                "errors" => [
                    "username" => [
                        "username already exists" 
                    ],
                ]
                ],400));
        }
        try {
            $user =  new User($datavalidated);
            $user->password = Hash::make($datavalidated["password"]);
            $user->save();
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }


        return new UserResource($user, "Successfully Created User");

    }
    public function login(UserLoginRequest $request) : UserResource
    {
        $dataValidated = $request->validated();
        date_default_timezone_set('Asia/Jakarta');
        remember_token::where("token_expired" , "<=" ,date('Y-m-d h:m:s'))->delete();
        try {
            $user = User::where("username", $dataValidated["username"])->where('active', 1)->first();
            if($user && Hash::check($dataValidated['password'], $user->password)){
                $remember_token = new remember_token();
                $remember_token->token = Str::uuid()->toString();
                $remember_token->token_expired = date("Y-m-d H:i:s" ,strtotime('+8 hours'));
                $remember_token->id_user = $user->id;
                $remember_token->save();

            }
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        };
        if (!$user || !Hash::check($dataValidated['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "username or password is invalid" 
                    ]
                ]
                ],401));
        }
        return new UserResource($user, "User Login Successfully", $remember_token->token);
    }
    public function logout(Request $request) :JsonResponse
    {
        date_default_timezone_set('Asia/Jakarta');

        try {
            remember_token::where("token_expired" , "<=" ,date('Y-m-d h:m:s'))->delete();
            $token = $request->input("token");
            $gettoken = remember_token::where("token", $token)->first();
            if($gettoken){

                $data = [
                    "success" => "Successfully Log Out"
                    ];
                $gettoken->delete();
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

        if (!$gettoken){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Token Not Exists" 
                    ]
                ]
                ],401));
        } 
        return response()->json($data)->setStatusCode(200);
    }
    public function getall(Request $request) :JsonResponse
    {
        $branchcode = $request->input('branchcode');

        if(!$branchcode){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Must Input Parameter BranchCode" 
                    ]
                ]
                ],401));
        } else {

            try {
                $data = UserView::where('branchcode', $branchcode)->where("active" , 1)->get()->makeHidden(["token","password","created_at","updated_at"]);
                $data = [
                    "data" => $data->collect(),
                    "success" => "Successfully Get Data"
                ];
                return response()->json($data)->setStatusCode(200);
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

    }
    public function get($key) :UserResource
    {
        try {
            $data = User::where("id" , $key)->orWhere("username", $key)->first();
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        if(!$data){
            
            throw new HttpResponseException(response([
                "data" => [],
                "errors" => [
                    "general" => [
                        "User Not Found" 
                    ]
                ]
                ],404));
        } else {

            return new UserResource($data, "Successfully Get Specific User");
        }



    }
    public function delete($key) :JsonResponse
    {
        
        try {
            $user = User::where("id", $key)->orWhere("username",$key)->first();
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if ($user){
            User::where("id", $key)->orWhere("username",$key)->delete();
            return response()->json([
                "data" => [
                    "id" => $user->id,
                    "username" => $user->username,
                    "name" => $user->name
                ],
                "success" => "User Successfully Deleted"
            ]);
        } else{
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "User Not Found"
                    ]
                ]
                ],404));
        }
    
    }
    public function update($key,UserUpdateRequest $request) :JsonResponse
    {

        $dataValidated = $request->validated();
        $user = User::where("id", $key)->orWhere("username", $key)->first();
        if (!$user){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "User Is Not Found" 
                    ]
                ]
                ],404));
        }
        if($user->name != $dataValidated["name"]) {
            $user->name = $dataValidated["name"];
        }
        if($user->id_role != $dataValidated["id_role"]) {
            $user->id_role = $dataValidated["id_role"];
        }
        // $user->username = $dataValidated["username"];
        if(!empty($dataValidated["password"])) {
            $pwcheck =true;
            if(!hash::check($dataValidated["password"],$user->password ))
            {
                
                $user->password = hash::make($dataValidated["password"]);
            }
        }
        // $user->id_role = $dataValidated["id_role"]
        $user->update();
        return response()->json([
            "data" => [
                "id" => $user->id,
                "username" => $user->username,
            ],
            "success" => "User Successfully Updated"
        ]);

    }
    public function checkcompany($key) : JsonResponse
    {
        try {
            $user = User::where("id", $key)->orWhere("username", $key)->first();
            if($user) {
                $isexistcompany = CompanyProfile::where("branchcode", $user->branchcode)->first();

                if($isexistcompany){
                    return response()->json([
                        "data" => [
                            "iscompanyexists" => true,
                        ]
                        ]);
                } else{
                    return response()->json([
                        "data" => [
                            "iscompanyexists" =>false
                        ]
                        ]);
                }
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
        if(!$user){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "User Not Found"
                    ]
                ]
                ],404));
        }

    }


}
