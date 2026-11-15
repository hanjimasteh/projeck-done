<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::orderBy('kode')->get();

        return view('admin.program.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.program.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:program,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'kuota' => ['required', 'integer', 'min:1'],
        ]);

        Program::create($validated);

        return redirect()->route('admin.program.index')->with('success', 'Program keahlian berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $program = Program::findOrFail($id);

        return view('admin.program.edit', compact('program'));
    }

    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:program,kode,'.$program->id],
            'nama' => ['required', 'string', 'max:255'],
            'kuota' => ['required', 'integer', 'min:1'],
        ]);

        $program->update($validated);

        return redirect()->route('admin.program.index')->with('success', 'Program keahlian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);

        if ($program->pendaftaranPilihan1()->exists() || $program->pendaftaranPilihan2()->exists()) {
            return back()->with('error', 'Program ini sudah dipakai pendaftar dan tidak dapat dihapus.');
        }

        $program->delete();

        return redirect()->route('admin.program.index')->with('success', 'Program keahlian berhasil dihapus.');
    }
}
