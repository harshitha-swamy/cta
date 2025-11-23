<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class PreviewController extends Controller
{
    // public function approver_review()
    // {
    //     return view('approver_review');
    // }

    public function approver_review(Request $request)
    {
        if (!session()->has('db_connection')) {
            session(['db_connection' => 'mysql2']); // Default to E-Shop US
        }

        

        // return view('dashboard');
         $connection = session('db_connection', 'mysql2');

         $id = $request->id;
        $action = $request->action;

        // Fetch all tasks from the right DB
        $ticket = Task::on($connection)->find($id);

        \Log::info("Approver reviewing ticket: ". $ticket->toJson());
        return view('approver_review', compact('ticket'));

    }

     public function developer_edit(Request $request)
    {
        if (!session()->has('db_connection')) {
            session(['db_connection' => 'mysql2']); // Default to E-Shop US
        }
   

        // return view('dashboard');
         $connection = session('db_connection', 'mysql2');

         $id = $request->id;
        $action = $request->action;

        // Fetch all tasks from the right DB
        $ticket = Task::on($connection)->find($id);
\Log::info("Action Type: ". $action);
        \Log::info("Approver reviewing ticket: ". $ticket->toJson());
        return view('create', compact('ticket','action'));

    }

    public function approveTask(Request $request)
{
    try {
         if (!session()->has('db_connection')) {
            session(['db_connection' => 'mysql2']); // Default to E-Shop US
        }

        // return view('dashboard');
         $connection = session('db_connection', 'mysql2');

    // Fetch all tasks from the right DB
       


        $taskId = $request->task_id;
        \Log::info("Incoming Approve Task ID:", [$taskId]); // DEBUG
        $task = Task::on($connection)->find($taskId);
        // $task = Task::find($taskId);

        if (!$task) {
            \Log::error("Task not found for ID: " . $taskId);
            return response()->json(['success' => false, 'message' => 'Task not found']);
        }

        \Log::info("Task found:", $task->toArray()); // DEBUG

        $updated = DB::connection($connection)
                ->table('dealers_cta_config')
                ->where('dealer_code', $task->dealer_code)
                ->update([
                    'button_image_url'      => $task->current_image_link,
                    'button_image_url_vdp'  => $task->current_image_link,
                    'button_image_url_cpov' => $task->current_image_link,
                ]);

        \Log::info("Update result:", ['updated_rows' => $updated]);

        if ($updated) {
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No row updated. Dealer code might be wrong.'
        ]);

    } catch (\Exception $e) {
        \Log::error("Approval Error: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// public function sendForChanges(Request $request, $id)
// {
//     try {
//         $task = Task::findOrFail($id);
//         $task->comment = $request->comment;
//         $task->status = 'changes_requested'; // optional
//         $task->save();

//         return response()->json(['success' => true, 'message' => 'Comment saved']);
//     } catch (\Exception $e) {
//         return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
//     }
// }

public function sendForChanges(Request $request, $id)
{
    try {
          if (!session()->has('db_connection')) {
            session(['db_connection' => 'mysql2']); // Default to E-Shop US
        }

        // return view('dashboard');
         $connection = session('db_connection', 'mysql2');

        $task = Task::on($connection)->findOrFail($id);

        // $task = Task::findOrFail($id);
        $task->comments = $request->comment;
        $task->status = "Reopen";  // required
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Comment and status updated successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}


}
