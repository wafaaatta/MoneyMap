<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    //
    public function index()
    {
        // Vérifier si l'utilisateur est admin
        if (Auth::user()->role === 'admin') {
            // Si admin, récupérer toutes les transactions
            $transactions = Transaction::all();
        } else {
            // Sinon, récupérer seulement les transactions de l'utilisateur connecté
            $transactions = Auth::user()->transactions;
        }

        return response()->json($transactions);
    }
    

    public function show($id)
    {
        // Vérifier si l'utilisateur est admin
        if (Auth::user()->role === 'admin') {
            // Si admin, récupérer la transaction par son id
            $transaction = Transaction::findOrFail($id);
        } else {
            // Sinon, récupérer seulement la transaction de l'utilisateur connecté
            $transaction = Auth::user()->transactions()->findOrFail($id);
        }

        return response()->json($transaction);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            
        ]);
        // Créer la transaction pour l'utilisateur connecté
        $transaction = Auth::user()->transactions()->create($validated);

        return response()->json($transaction, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        // Vérifier si l'utilisateur est admin
        if (Auth::user()->role === 'admin') {
            // Si admin, récupérer la transaction par son id
            $transaction = Transaction::findOrFail($id);
        } else {
            // Sinon, récupérer seulement la transaction de l'utilisateur connecté
            $transaction = Auth::user()->transactions()->findOrFail($id);
        }

        // Mettre à jour la transaction
        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        // Vérifier si l'utilisateur est admin
        if (Auth::user()->role === 'admin') {
            // Si admin, récupérer la transaction par son id
            $transaction = Transaction::findOrFail($id);
        } else {
            // Sinon, récupérer seulement la transaction de l'utilisateur connecté
            $transaction = Auth::user()->transactions()->findOrFail($id);
        }

        // Supprimer la transaction
        $transaction->delete();

        return response()->json(null, 204);
    }
}
