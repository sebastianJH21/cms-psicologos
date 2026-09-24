<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('orden')->orderBy('id')->get();

        return view('dashboard.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('dashboard.faqs.create');
    }

    public function store(FaqRequest $request)
    {
        $datos = $request->validated();
        $datos['orden'] = (int) (Faq::max('orden') ?? 0) + 1;

        Faq::create($datos);

        return redirect()
            ->route('dashboard.faqs.index')
            ->with('success', 'Pregunta creada correctamente.');
    }

    public function edit(Faq $faq)
    {
        return view('dashboard.faqs.edit', compact('faq'));
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        $faq->update($request->validated());

        return redirect()
            ->route('dashboard.faqs.index')
            ->with('success', 'Pregunta actualizada correctamente.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('dashboard.faqs.index')
            ->with('success', 'Pregunta eliminada correctamente.');
    }

    public function reordenar(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) {
            return response()->json(['ok' => false], 422);
        }

        foreach ($ids as $orden => $id) {
            Faq::where('id', (int) $id)->update(['orden' => $orden]);
        }

        return response()->json(['ok' => true]);
    }
}
