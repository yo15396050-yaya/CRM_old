<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RadiationRequestController extends Controller
{
    // Soumettre une demande de radiation
    public function requestRadiation(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        RadiationRequest::create([
            'user_id' => $validated['user_id'],
            'status' => 'pending',
            'comments' => null,
        ]);

        return response()->json(['success' => true, 'message' => __('messages.requestSubmitted')]);
    }

    // Valider une demande de radiation
    public function validateRadiationRequest(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $radiationRequest = RadiationRequest::find($id);

        if (!$radiationRequest) {
            return response()->json(['success' => false, 'message' => __('messages.requestNotFound')]);
        }

        if ($request->action === 'approve') {
            $radiationRequest->status = 'approved';
            // Logique pour mettre à jour l'utilisateur
            $clientDetail = ClientDetails::where('user_id', $radiationRequest->user_id)->first();
            if ($clientDetail) {
                $clientDetail->numcc = NULL; // Désactiver la radiation
                $clientDetail->save();
            }
        } elseif ($request->action === 'reject') {
            $radiationRequest->status = 'rejected';
        }

        $radiationRequest->save();

        return response()->json(['success' => true, 'message' => __('messages.requestProcessed')]);
    }

    // Lister les demandes de radiation
    public function listRequests()
    {
        $requests = RadiationRequest::with('user')->get();
        return response()->json($requests);
    }
}
