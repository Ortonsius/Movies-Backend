<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\Onlines;
use App\Models\Movies;
use App\Models\Fav_movies;
use App\Models\Movie_rates;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieCon extends Controller
{
    public function OnShow(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "u") return response()->json(["msg" => "Unauthorized"],401);

        $current_time = now()->timestamp;
        $movies = Movies::select("mid","title","description","rating","show_time","end_time")->where("show_time","<=",$current_time)->where("end_time",">",$current_time)->get()->toArray();
        return response()->json($movies,200);
    }

    public function ComingSoon(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "u") return response()->json(["msg" => "Unauthorized"],401);

        $current_time = now()->timestamp;
        $movies = Movies::select("mid","title","description","rating","show_time","end_time")->where("show_time",">",$current_time)->get()->toArray();
        return response()->json($movies,200);
    }

    public function getFavoriteMovie(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "u") return response()->json(["msg" => "Unauthorized"],401);

        $favMovies = Fav_movies::select("mid")->where("uid",$user_info["uid"])->get()->toArray();
        $res = [];
        foreach($favMovies as $i){
            $tmp = Movies::select("title","description","rating","show_time","end_time")->where("mid",$i)->first();
            if($tmp != NULL) $res[$i["mid"]] = $tmp->toArray();
        }
        return response()->json($res,200);
    }

    public function setFavoriteMovie(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required",
            "mid" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "u") return response()->json(["msg" => "Unauthorized"],401);

        $movie_exist = Movies::where("mid",$req->mid)->first();
        if($movie_exist == NULL) return response()->json(["msg" => "Unknown movie"],200);

        $fav = new Fav_movies;
        $fav->uid = $user_info["uid"];
        $fav->mid = $req->mid;
        $fav->save();

        $favMovies = Fav_movies::select("mid")->where("uid",$user_info["uid"])->get()->toArray();
        $res = array();
        foreach($favMovies as $i){
            $tmp = Movies::select("title","description","rating","show_time","end_time")->where("mid",$i)->first();
            if($tmp != NULL) $res[$i["mid"]] = $tmp->toArray();
        }
        return response()->json($res,200);
    }

    public function delFavoriteMovie(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required",
            "mid" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "u") return response()->json(["msg" => "Unauthorized"],401);

        $movie_exist = Movies::where("mid",$req->mid)->first();
        if($movie_exist == NULL) return response()->json(["msg" => "Unknown movie"],200);

        $fav = Fav_movies::where("uid",$user_info["uid"])->where("mid",$req->mid);
        $fav->delete();

        $favMovies = Fav_movies::select("mid")->where("uid",$user_info["uid"])->get()->toArray();
        $res = [];
        foreach($favMovies as $i){
            $tmp = Movies::select("title","description","rating","show_time","end_time")->where("mid",$i)->first();
            if($tmp != NULL) $res[$i["mid"]] = $tmp->toArray();
        }
        return response()->json($res,200);
    }

    public function rateMovie(Request $req,int $mid,int $rate){
        $validator = Validator::make($req->all(),[
            "token" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "u") return response()->json(["msg" => "Unauthorized"],401);

        $movie_exist = Movies::where("mid",$mid)->first();
        if($movie_exist == NULL) return response()->json(["msg" => "Unknown movie"],200);
        
        $movie_rated = Movie_rates::where("mid",$mid)->where("uid",$user_info["uid"])->first();
        if($movie_rated != NULL) return response()->json(["msg" => "You already rate this movie"],200);

        if($rate >= 0 && $rate < 6){
            $mrate = new Movie_rates;
            $mrate->uid = $user_info["uid"];
            $mrate->mid = $mid;
            $mrate->rate = $rate;
            $mrate->save();
    
            $calculate_rate = Movie_rates::where("mid",$mid)->get()->toArray();
            $total_rate = 0;
            $rater = count($calculate_rate);
            foreach($calculate_rate as $i){
                $total_rate += intval($i["rate"]);
            }

            if($total_rate > 0 || $rater > 0){
                $rating = doubleval($total_rate / $rater);
                $movie = Movies::where("mid",$mid);
                $movie->update([
                    "rating" => doubleval($rating)
                ]);
            }else{
                $movie = Movies::where("mid",$mid);
                $movie->update([
                    "rating" => 0
                ]);
            }

            return response()->json(["msg" => "Movie rated"],200);
        }

        return response()->json(["msg" => "Invalid rate value"],200);
    }

    public function getAllMovies(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "a") return response()->json(["msg" => "Unauthorized"],401);

        $movies = Movies::select("mid","title","description","rating","show_time","end_time")->get()->toArray();
        return response()->json($movies,200);
    }

    public function addMovie(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required",
            "title" => "required",
            "description" => "required",
            "show_time" => "required",
            "end_time" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "a") return response()->json(["msg" => "Unauthorized"],401);

        $movie = new Movies;
        $movie->title = $req->title;
        $movie->description = $req->description;
        $movie->rating = 0;
        $movie->show_time = $req->show_time;
        $movie->end_time = $req->end_time;
        $movie->save();

        $movies = Movies::select("mid","title","description","rating","show_time","end_time")->get()->toArray();
        return response()->json($movies,200);
    }

    public function editMovie(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required",
            "mid" => "required",
            "title" => "required",
            "description" => "required",
            "show_time" => "required",
            "end_time" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "a") return response()->json(["msg" => "Unauthorized"],401);

        $movie = Movies::where("mid",$req->mid);
        $movie->update([
            "title" => $req->title,
            "description" => $req->description,
            "rating" => 0,
            "show_time" => $req->show_time,
            "end_time" => $req->end_time
        ]);

        $movies = Movies::select("mid","title","description","rating","show_time","end_time")->get()->toArray();
        return response()->json($movies,200);
    }

    public function delMovie(Request $req){
        $validator = Validator::make($req->all(),[
            "token" => "required",
            "mid" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["msg" => "Invalid input"],422);
        }

        $user_token = Onlines::where("token",$req->token)->first();
        if($user_token == NULL) return response()->json(["msg" => "Unauthorized"],401);

        $user_info = $user_token->toArray();
        if($user_info["role"] != "a") return response()->json(["msg" => "Unauthorized"],401);

        Movies::where("mid",$req->mid)->delete();

        $movies = Movies::select("mid","title","description","rating","show_time","end_time")->get()->toArray();
        return response()->json($movies,200);
    }
}
