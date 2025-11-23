<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\Auth;
class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('db_connection')) {
            session(['db_connection' => 'mysql2']); // Default to E-Shop US
        }

        // return view('dashboard');
         $connection = session('db_connection', 'mysql2');

    // Fetch all tasks from the right DB
    $tickets = Task::on($connection)->get();

    // Return to dashboard with data
    \Log::info("Using DB connection: ".$connection);
    \Log::info("Fetched ".count($tickets)." tasks.");
    \Log::info("Tasks: ". $tickets->toJson());
    return view('dashboard', compact('tickets'));
    }


    public function selectEshop(Request $request)
    {
        $shop = $request->input('shop'); // 'us' or 'ca'

        \Log::info("select db :".$shop);
        if ($shop === 'us') {
            session(['db_connection' => 'mysql2']);
        } elseif ($shop === 'ca') {
            session(['db_connection' => 'mysql']);
        }

        return response()->json(['message' => 'E-Shop changed successfully!']);
    }


    public function create()
    {
        return view('create'); // create a new Blade file for the page
    }

//   public function store(Request $request)
// {
//     \Log::info("Storing new task with data: ");
//     \Log::info($request->all());
//     $validated = $request->validate([
//         'ticket_link' => 'nullable|url',
//         'ticket_description' => 'nullable|string',
//         'dealer_code' => 'nullable|string|max:10',
//         'project_name' => 'required|string',
//         'website_link' => 'nullable|url',
//     ]);

//      $userName = Auth::user()->name ?? 'Unknown User';

//     // Add created_by to the validated data
//     $validated['created_by'] = $userName;

//     // Determine DB connection
//     $connection = $request->input('project_name') === 'E-Shop US' ? 'mysql2' : 'mysql';

//     // Use dynamic connection to insert data
//     \DB::connection($connection)->table('tasks')->insert([
//         'ticket_link' => $validated['ticket_link'] ?? null,
//         'ticket_description' => $validated['ticket_description'] ?? null,
//         'dealer_code' => $validated['dealer_code'] ?? null,
//         'project_name' => $validated['project_name'],
//         'website_link' => $validated['website_link'] ?? null,
//         'status' => 'In Progress',
//         'created_by' => $validated['created_by'],
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     return response()->json([
//         'message' => 'Task created successfully',
//         'success' => true
//     ]);
// }


// public function store(Request $request)
// {
//     $validated = $request->validate([
//         'ticket_link' => 'nullable|url',
//         'ticket_description' => 'nullable|string',
//         'dealer_code' => 'nullable|string|max:10',
//         'project_name' => 'required|string',
//         'website_link' => 'nullable|url',
//         'task_id' => 'nullable|integer'
//     ]);

//     $userName = Auth::user()->name ?? 'Unknown User';
//     $connection = $request->project_name === 'E-Shop US' ? 'mysql2' : 'mysql';

//     // IF task_id exists → UPDATE
//     if (!empty($validated['task_id'])) {

//         \DB::connection($connection)
//             ->table('tasks')
//             ->where('id', $validated['task_id'])
//             ->update([
//                 'ticket_link' => $validated['ticket_link'],
//                 'ticket_description' => $validated['ticket_description'],
//                 'dealer_code' => $validated['dealer_code'],
//                 'project_name' => $validated['project_name'],
//                 'website_link' => $validated['website_link'],
//                 'updated_at' => now(),
//             ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Task updated successfully!'
//         ]);
//     }

//     // ELSE → INSERT NEW
//     $id = \DB::connection($connection)->table('tasks')->insertGetId([
//         'ticket_link' => $validated['ticket_link'] ?? null,
//         'ticket_description' => $validated['ticket_description'] ?? null,
//         'dealer_code' => $validated['dealer_code'] ?? null,
//         'project_name' => $validated['project_name'],
//         'website_link' => $validated['website_link'] ?? null,
//         'status' => 'In Progress',
//         'created_by' => $userName,
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Task created successfully!',
//         'task_id' => $id
//     ]);
// }

// public function store(Request $request)
// {
//     $validated = $request->validate([
//         'ticket_link' => 'nullable|url',
//         'ticket_description' => 'nullable|string',
//         'dealer_code' => 'nullable|string|max:10',
//         'project_name' => 'required|string',
//         'website_link' => 'nullable|url',
//         'task_id' => 'nullable|integer'
//     ]);

//     $userName = Auth::user()->name ?? 'Unknown User';
//     $connection = $request->project_name === 'E-Shop US' ? 'mysql2' : 'mysql';

//     // -------------------------------------------------------
//     // DUPLICATE LINK CHECK (EXCEPT CURRENT RECORD IN UPDATE)
//     // -------------------------------------------------------
//     if (!empty($validated['ticket_link'])) {
//         $exists = \DB::connection($connection)
//             ->table('tasks')
//             ->where('ticket_link', $validated['ticket_link'])
//             ->when(!empty($validated['task_id']), function ($q) use ($validated) {
//                 $q->where('id', '!=', $validated['task_id']); // exclude current row
//             })
//             ->exists();

//         if ($exists) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'This ticket link already exists. Please use a different link.'
//             ], 409);
//         }
//     }

//     // -------------------------------------------------------
//     // UPDATE EXISTING TASK
//     // -------------------------------------------------------
//     if (!empty($validated['task_id'])) {

//         \DB::connection($connection)
//             ->table('tasks')
//             ->where('id', $validated['task_id'])
//             ->update([
//                 'ticket_link' => $validated['ticket_link'],
//                 'ticket_description' => $validated['ticket_description'],
//                 'dealer_code' => $validated['dealer_code'],
//                 'project_name' => $validated['project_name'],
//                 'website_link' => $validated['website_link'],
//                 'updated_at' => now(),
//             ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Task updated successfully!'
//         ]);
//     }

//     // -------------------------------------------------------
//     // CREATE NEW TASK
//     // -------------------------------------------------------
//     $id = \DB::connection($connection)->table('tasks')->insertGetId([
//         'ticket_link' => $validated['ticket_link'] ?? null,
//         'ticket_description' => $validated['ticket_description'] ?? null,
//         'dealer_code' => $validated['dealer_code'] ?? null,
//         'project_name' => $validated['project_name'],
//         'website_link' => $validated['website_link'] ?? null,
//         'status' => 'In Progress',
//         'created_by' => $userName,
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Task created successfully!',
//         'task_id' => $id
//     ]);
// }

public function store(Request $request)
{
    $validated = $request->validate([
        'ticket_link' => 'nullable|url',
        'ticket_description' => 'nullable|string',
        'dealer_code' => 'nullable|string|max:10',
        'project_name' => 'required|string',
        'website_link' => 'nullable|url',
        'task_id' => 'nullable|integer'
    ]);

    $userName = Auth::user()->name ?? 'Unknown User';
    $connection = $request->project_name === 'E-Shop US' ? 'mysql2' : 'mysql';

    // -------------------------------
    // DUPLICATE LINK CHECK
    // -------------------------------
    if (!empty($validated['ticket_link'])) {
        $exists = \DB::connection($connection)
            ->table('tasks')
            ->where('ticket_link', $validated['ticket_link'])
            ->when(!empty($validated['task_id']), function ($q) use ($validated) {
                // exclude current task during edit
                $q->where('id', '!=', $validated['task_id']);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket link already exists. Please use a different link.'
            ], 409);
        }
    }

    // -------------------------------
    // UPDATE EXISTING TASK
    // -------------------------------
    if (!empty($validated['task_id'])) {
        \DB::connection($connection)
            ->table('tasks')
            ->where('id', $validated['task_id'])
            ->update([
                'ticket_link' => $validated['ticket_link'],
                'ticket_description' => $validated['ticket_description'],
                'dealer_code' => $validated['dealer_code'],
                'project_name' => $validated['project_name'],
                'website_link' => $validated['website_link'],
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully!'
        ]);
    }

    // -------------------------------
    // CREATE NEW TASK
    // -------------------------------
    $id = \DB::connection($connection)->table('tasks')->insertGetId([
        'ticket_link' => $validated['ticket_link'] ?? null,
        'ticket_description' => $validated['ticket_description'] ?? null,
        'dealer_code' => $validated['dealer_code'] ?? null,
        'project_name' => $validated['project_name'],
        'website_link' => $validated['website_link'] ?? null,
        'status' => 'In Progress',
        'created_by' => $userName,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Task created successfully!',
        'task_id' => $id
    ]);
}

}