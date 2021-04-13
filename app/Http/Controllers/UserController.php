<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\User;

class UserController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index($search = null){
	 	if(!empty($search)){
			$users = User::where('nick', 'LIKE', '%'.$search.'%')
							->orWhere('name', 'LIKE', '%'.$search.'%')
							->orWhere('surname', 'LIKE', '%'.$search.'%')
							->orderBy('id', 'desc')
							->paginate(5);
		}else{
			$users = User::orderBy('id', 'desc')->paginate(5);
		}
		
		return view('user.index',[
			'users' => $users
		]);
    }
    
    
    public function config(){
        return view('user.config');
    }

    public function update(Request $request){
/*         $image_pathh = $request->file('image_path');
        var_dump($image_pathh);
        die(); */
        //conseguir usuario  identificado
        $user = \Auth::user();
        $id = $user->id;
        //validacion del formulario
        $validate = $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'nick' => ['required', 'string', 'max:255', 'unique:users,nick,'.$id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
        ]);
        //recoger datos del formulario
        $name = $request->input('name');
        $surname = $request->input('surname');
        $nick = $request->input('nick');
        $email = $request->input('email');

        //subir la imagen
        $image_path = $request->file('image_path');
        if($image_path){
            //poner nombre unico
            $image_path_name = time().$image_path->getClientOriginalName();
            //guardar en la carpeta storage
            Storage::disk('users')->put($image_path_name, File::get($image_path));
            //setear el nombre de la imagen con el objeto
            $user->image = $image_path_name;
        }


        //asignar nuevos valores al objeto del usuario
        $user->name = $name;
        $user->surname = $surname;
        $user->nick = $nick;
        $user->email = $email;

        //ejecutar consulta y cambios en db
        $user->update();

        return redirect()->route('config')
                         ->with(['message'=>'Usuario Actualizado Correctamente']);
    }

    public function getImage($filename){
        $file = Storage::disk('users')->get($filename);
        return new Response($file,200);
    }

	public function profile($id){
		$user = User::find($id);
		
		return view('user.profile', [
			'user' => $user
		]);
	}
}

