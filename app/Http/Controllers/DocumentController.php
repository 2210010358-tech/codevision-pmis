<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Authorization
        if ($user->hasRole('Client') && $project->client_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,docx,xlsx,jpg,jpeg,png|max:10240', // Max 10MB
            'task_id' => 'nullable|exists:tasks,id',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        
        $fileType = 'Other';
        if ($extension === 'pdf') {
            $fileType = 'PDF';
        } elseif (in_array($extension, ['doc', 'docx'])) {
            $fileType = 'DOCX';
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            $fileType = 'XLSX';
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $fileType = 'Image';
        }

        $path = $file->store('documents', 'public');

        Document::create([
            'project_id' => $project->id,
            'task_id' => $validated['task_id'] ?? null,
            'name' => $validated['name'],
            'file_path' => $path,
            'file_type' => $fileType,
            'uploaded_by' => $user->id,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(Document $document)
    {
        $user = Auth::user();
        $project = $document->project;

        // Check project access
        if ($user->hasRole('Client') && $project->client_id !== $user->id) {
            abort(403);
        }

        if ($user->hasRole('Developer')) {
            $hasTask = \App\Models\Task::whereHas('milestone', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })->where('assigned_to', $user->id)->exists();

            $hasBug = \App\Models\Bug::where('project_id', $project->id)->where('assigned_to', $user->id)->exists();

            if (!$hasTask && !$hasBug) {
                abort(403);
            }
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found on storage.');
        }

        return Storage::disk('public')->download($document->file_path, $document->name . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
    }

    public function destroy(Document $document)
    {
        $user = Auth::user();

        // Only Admin or the uploader can delete
        if (!$user->hasRole('Administrator') && $document->uploaded_by !== $user->id) {
            abort(403, 'Unauthorized to delete this document.');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
