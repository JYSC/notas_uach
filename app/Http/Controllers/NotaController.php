<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Auth;
use DB;

class NotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //$notas = Nota::get();
        //$notas = Nota::where('users_id',Auth::id())->get();

        $notas = DB::table('notas')
        ->join('categories', 'notas.categories_id','=','categories.id')
        ->where('users_id',Auth::id())
        ->select('notas.*','category_name')
        ->get();
        return Inertia::render('Notas/Index', [
            'notas' => $notas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = Category::where('category_status',true);

        return Inertia::render('Notas/Create',[
            'categories'=> $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
      $request->validate([
          'titulo' => 'required',
          'contenido' => 'required',
          'categories_id'=>'required',
      ]);
    
      // Nota::create($request->all()); //tambien almacenar el id del usuairo autetnticado
        //ORM = MAPEANDO LA BASES DE DATOS (TABLA nota con la clase Nota)
      $nota = new Nota;
      $nota->titulo = $request->titulo;
      $nota->contenido = $request->contenido;
      $nota->categories_id = $request->categories_id;
      $nota->users_id = Auth::id(); //id del usuario que este conectado
      $nota->save();

       return redirect()->route('noticias.index')->with('status','se ha creado una noiticia');;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //where('users_id',Auth::id())->get();
        //$nota = Nota::findOrFail($id);
        //select * from notas where id = $id and users_id =Auth::id()

        //$nota = Nota::where('id',$id)->where('users_id',Auth::id())->first();

        /*if($nota){ //no se encontro
            Auth::logout();
        }*/

        $notas = DB::table('notas')
        ->join('categories', 'notas.categories_id','=','categories.id')
        ->where('nota.id',$id)
        ->where('users_id',Auth::id())
        ->select('notas.*','category_name')
        ->first();

        return Inertia::render('Notas/Show', [
            'nota' => $nota
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        

        $nota = Nota::where('id',$id)->where('users_id',Auth::id())->first();
        $categories = Category::where('category_status',true);

        return Inertia::render('Notas/Edit', [
            'nota' => $nota,
            'categories'=>$categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required',
            'contenido' => 'required',
            'categories_id'=>'required',
        ]);

        $nota = Nota::where('id',$id)->where('users_id',Auth::id())->first();
        
        $nota->update($request->all());
        
        return redirect()->route('noticias.index')->with('status','La noticia se ha actualizado');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nota = Nota::where('id',$id)->where('users_id',Auth::id())->first();

        $nota->delete();
        return redirect()->route('noticias.index')->with('status','La noticia se ha eliminado');;

    }
}
