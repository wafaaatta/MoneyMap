<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $validated = Validator::make($request->all(),[
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'transaction_date' => 'nullable|date',

        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $transaction = Transaction::create([
            'user_id' => Auth::user()->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
        ]);


        // Créer la transaction pour l'utilisateur connecté

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
        $user = Auth::user();
        
        $transaction = Transaction::find($id);

        if($transaction->user_id != $user->id) {
            return response()->json("Forbidden", 403);
        }


        Transaction::destroy($id);

        return response()->json("Transaction deleted successfully", 200);
    }
}
