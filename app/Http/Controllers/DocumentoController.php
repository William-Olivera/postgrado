<?php

namespace App\Http\Controllers;

class DocumentoController extends Controller
{
    public function index()
    {
        return view('documentos.index');
    }
}