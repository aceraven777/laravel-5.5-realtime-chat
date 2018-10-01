<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
	/**
	 * List of users
	 * 
	 * @param  Request $request
	 * @return \Illuminate\View\View
	 */
    public function index(Request $request)
    {
    	$currentUser = Auth::user();

    	$users = User::where('id', '!=', $currentUser->id)
    		->orderBy('name')
    		->paginate(50);

        return view('users.index', compact('users'));
    }
}
