<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

/**
 * @method authorize(string $string)
 */
class ProgramController extends Controller
{
    protected function validateProgram(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string',
            'day_of_week' => 'string|max:50',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after_or_equal:start_time',
            'published' => 'nullable|boolean',
        ]);
    }

    // Public index
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        // Build a driver-aware ordering because SQLite doesn't support FIELD()
        $programsQuery = Program::where('published', true);

        if (DB::getDriverName() === 'sqlite') {
            // SQLite: use CASE expression to define weekday order
            $programs = $programsQuery
                ->orderByRaw("CASE day_of_week WHEN 'Sunday' THEN 1 WHEN 'Monday' THEN 2 WHEN 'Tuesday' THEN 3 WHEN 'Wednesday' THEN 4 WHEN 'Thursday' THEN 5 WHEN 'Friday' THEN 6 WHEN 'Saturday' THEN 7 ELSE 8 END")
                ->orderBy('start_time')
                ->get();
        } else {
            // Others: use FIELD()
            $programs = $programsQuery
                ->orderByRaw("FIELD(day_of_week, 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
                ->orderBy('start_time')
                ->get();
        }

        return view('program', compact('programs'));
    }

    // Admin manage view
    public function manage(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        // Use same ordering logic for admin/manage listing
        if (DB::getDriverName() === 'sqlite') {
            $programs = Program::orderByRaw("CASE day_of_week WHEN 'Sunday' THEN 1 WHEN 'Monday' THEN 2 WHEN 'Tuesday' THEN 3 WHEN 'Wednesday' THEN 4 WHEN 'Thursday' THEN 5 WHEN 'Friday' THEN 6 WHEN 'Saturday' THEN 7 ELSE 8 END")
                ->orderBy('start_time')
                ->get();
        } else {
            $programs = Program::orderByRaw("FIELD(day_of_week, 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
                ->orderBy('start_time')
                ->get();
        }

        return view('admin.program.manage', compact('programs'));
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $program = new Program();
        return view('admin.program.create', compact('program'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validateProgram($request);
        // Use boolean helper so unchecked checkbox results in false
        $data['published'] = $request->boolean('published');
        Program::create($data);
        return Redirect::route('program.manage')->with('success', 'Program item created.');
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $program = Program::findOrFail($id);
        return view('admin.program.edit', compact('program'));
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $program = Program::findOrFail($id);
        $data = $this->validateProgram($request);
        $data['published'] = $request->boolean('published');
        $program->update($data);
        return Redirect::route('program.manage')->with('success', 'Program item updated.');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $program = Program::findOrFail($id);
        $program->delete();
        return Redirect::route('program.manage')->with('success', 'Program item deleted.');
    }
}
