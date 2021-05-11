<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\User;
use Illuminate\Support\Str;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB, Hash, Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;


class AccountController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['MyInfo', 'GetUserData' , 'Settings' , 'Privacy' , 'delete']]);
    }

        /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */ 
    public function GetUserData(Request $request){
        $unique_id = $request['u_id'];
        $check = DB::table('users')->where('unique_id',$request['u_id'])->first();
        
        // $sales = DB::table('order_lines')
        // ->join('orders', 'orders.id', '=', 'order_lines.order_id')
        // ->select(DB::raw('sum(order_lines.quantity*order_lines.per_qty) AS total_sales'))
        // ->where('order_lines.product_id', $product->id)
        // ->where('orders.order_status_id', 4)
        // ->first();

        return response()->json([
            'success'=> true,
            'message'=> $check
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function MyInfo(Request $request){

        $data = $request['value'];
        $unique_id = $request['val'];

        $validator = Validator::make($data, [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
               'new_password_confirmation' => 'required|string|min:6',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

//        $check = DB::table('users')->where('unique_id',$unique_id['u_id'])->first();

        $data_q=array('password' => $data['new_password'] , 'unique_id'=>$unique_id['u_id']);
        $result =  DB::update('update users set password = ? where unique_id = ?',[bcrypt($data['new_password']),$unique_id['u_id']]);
        return response()->json([
            'success'=> true,
            'message'=> $result
        ]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */ 
    public function Settings(Request $request){
        $data = $request['value'];
        $unique_id = $request['val'];
        $result =  DB::update('update users set show_direct_link = ? where unique_id = ?',[$data['check_direct'] ,$unique_id['u_id']]);
        $result =  DB::update('update users set show_html_code = ? where unique_id = ?',[$data['check_html'] ,$unique_id['u_id']]);
        $result =  DB::update('update users set show_forum_code = ? where unique_id = ?',[$data['check_bulletin'] ,$unique_id['u_id']]);
        $result =  DB::update('update users set show_social_share = ? where unique_id = ?',[$data['check_button'] ,$unique_id['u_id']]);
        return response()->json([
            'success'=> true,
            'message'=> $result
        ]);
    }
    public function Privacy(Request $request){
        $data = $request['value'];
        $unique_id = $request['val'];
        $result =  DB::update('update users set is_account_public = ? where unique_id = ?',[$data['seleted'] ,$unique_id['u_id']]);
        return response()->json([
            'success'=> true,
            'message'=> $result
        ]);
    }
    public function delete(Request $request){
        $result = DB::delete('delete from users where unique_id = ?',[$request['u_id']]);
        return response()->json([
            'success'=> true,
            'message'=> $result
        ]);
    }
}
