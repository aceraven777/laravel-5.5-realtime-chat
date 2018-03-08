<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
	/**
	 * List users
	 * 
	 * @param  Request $request [description]
	 * @return View
	 */
    public function index(Request $request)
    {
    	$current_user = Auth::user();

    	$users = User::where('id', '!=', $current_user->id)
    		->orderBy('name')
    		->paginate(50);

        return view('users.index', compact('users'));
    }
}
