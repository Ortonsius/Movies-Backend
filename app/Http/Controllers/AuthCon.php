<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\Onlines;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthCon extends Controller
{
    public function login(Request $req){
        $validator = Validator::make($req->all(),[
            "usr" => "required",
            "pwd" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $data = Accounts::select("uid","role")->where("name",$req->usr)->where("pwd",md5($req->pwd))->first();
        if($data != NULL){
            $arrayed_data = $data->toArray();
            while(true){
                $token = Str::random(16);
                $token_exist = Onlines::where("token",$token)->first();
                if($token_exist == NULL){
                    $online = new Onlines;
                    $online->uid = $arrayed_data["uid"];
                    $online->role = $arrayed_data["role"];
                    $online->token = $token;
                    $online->expired_at = now()->timestamp + 86400;
                    $online->save();

                    return response()->json(["token" => $token,"role" => $arrayed_data["role"]],200);                    
                }
            }
        }

        return response()->json(["msg" => "Invalid username/password"],401);
    }

    public function logout(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized token"],401);
        Onlines::where("token",$req->token)->delete();
        return response()->json(["msg" => "Logout success"],200);
    }

    public function register(Request $req){
        $validator = Validator::make($req->all(),[
            "usr" => "required",
            "pwd" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_exist = Accounts::where("name",$req->usr)->first();
        if($user_exist != NULL) return response()->json(["msg" => "Username exist"],200);
        $acc = new Accounts;
        $acc->name = $req->usr;
        $acc->pwd = md5($req->pwd);
        $acc->role = "u";
        $acc->save();
        return response()->json(["msg" => "Account successfully created"],200);
    }
}
