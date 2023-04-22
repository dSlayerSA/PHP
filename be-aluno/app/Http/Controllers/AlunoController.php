<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aluno;

class AlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    $alunos = Aluno::all();

    return response()->json($alunos);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $aluno = new Aluno;
    $aluno->RA = $request->input('RA');
    $aluno->nome = $request->input('nome');
    $aluno->cpf = $request->input('cpf');
    $aluno->email = $request->input('email');
    $aluno->save();
    return response()->json([
        'message' => 'Aluno criado com sucesso!',
        'data' => $aluno
    ], 201);
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
{
    $searchText = $request->input('searchText');
    $query = Aluno::query();

    if (!empty($searchText)) {
        $searchText = preg_replace('/[^A-Za-z0-9\-]/', '', $searchText); // remove caracteres especiais
        if (strlen($searchText) > 10) { // CPF
            $query->where('cpf', $searchText);
        } else if (strlen($searchText) <= 4) { // RA com 4 dígitos
            $query->where('RA', $searchText);
        } else { // nome
            $query->where('nome', 'like', "%$searchText%");
        }
    }

    if (!empty($request->input('RA'))) {
        $query->where('RA', $request->input('RA'));
    }

    $alunos = $query->get();

    return response()->json($alunos);
}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
