<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\GynecoObstetricHistory;
use Illuminate\Http\Request;

class GynecoObstetricHistoryController extends Controller
{
    public function create($evaluation_id)
    {
        $evaluation = Evaluation::findOrFail($evaluation_id);
        return view('evaluation.gyneco_obstetric.create', compact('evaluation'));
    }

    public function store(Request $request, $evaluation_id)
    {
        $request->validate([
            'gestations' => 'nullable|integer|min:0',
            'deliveries' => 'nullable|integer|min:0',
            'abortions' => 'nullable|integer|min:0',
            'cesarean_sections' => 'nullable|integer|min:0',
            'menarche_age' => 'nullable|integer|min:8|max:60',
        ]);

        GynecoObstetricHistory::create([
            'evaluation_id' => $evaluation_id,
            'gestations' => $request->gestations,
            'deliveries' => $request->deliveries,
            'abortions' => $request->abortions,
            'cesarean_sections' => $request->cesarean_sections,
            'last_menstruation_date' => $request->last_menstruation_date,
            'last_delivery_date' => $request->last_delivery_date,
            'last_cesarean_date' => $request->last_cesarean_date,
            'last_abortion_date' => $request->last_abortion_date,
            'menarche_age' => $request->menarche_age,
            'catamenial_pattern' => $request->catamenial_pattern,
            'menstrual_cycle' => $request->menstrual_cycle,
        ]);

        return redirect()->route('evaluations.show', $evaluation_id)
            ->with('success', 'Gyneco-obstetric history added successfully.');
    }

    public function edit(GynecoObstetricHistory $gynecoObstetricHistory)
    {
        return view('evaluation.gyneco_obstetric.edit', compact('gynecoObstetricHistory'));
    }

    public function update(Request $request, GynecoObstetricHistory $gynecoObstetricHistory)
    {
        $gynecoObstetricHistory->update($request->all());
        return back()->with('success', 'Gyneco-obstetric history updated successfully.');
    }
}
