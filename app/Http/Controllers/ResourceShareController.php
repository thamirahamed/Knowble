<?php

namespace App\Http\Controllers;

use App\Models\ResourceShare;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceShareController extends Controller
{
    public function index()
    {
        return ResourceShare::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => ['required'],
            'fileName' => ['required'],
        ]);

        // Store the file in S3 and get its path
        $path = $request->file('file')->store('uploads', 's3');

        // Get the URL of the file
        $fileUrl = Storage::disk('s3')->url($path);

        // Get the tutor_id
        $tutor_id = Tutor::where('user_id', auth()->id())->first()->id;

        // Save to database
        ResourceShare::create([
            'tutor_id' => $tutor_id,
            'fileName' => $request->fileName,
            'fileLocation' => $fileUrl,
        ]);


        return redirect()->back();
    }

    public function show(ResourceShare $resourceShare)
    {
        return $resourceShare;
    }

    public function update(Request $request, ResourceShare $resourceShare)
    {
        $data = $request->validate([
            'fileLocation' => ['required'],
            'tutor_id' => ['required', 'exists:tutors'],
            'fileName' => ['required'],
        ]);

        $resourceShare->update($data);

        return $resourceShare;
    }

    public function destroy($id)
    {
        $resource = ResourceShare::findOrFail($id);

        // Delete file from S3
        $parsedUrl = parse_url($resource->fileLocation);
        $path = ltrim($parsedUrl['path'], '/');
        Storage::disk('s3')->delete($path);

        // Delete resource record
        $resource->delete();

        return back()->with('success', 'File deleted successfully!');
    }
    public function download($id): StreamedResponse
    {
    // Find the resource by ID
    $resource = ResourceShare::findOrFail($id);

    // Parse the file path from the S3 URL
    $parsedUrl = parse_url($resource->fileLocation);
    $path = ltrim($parsedUrl['path'], '/'); // Remove the leading '/'

    // Check if the file exists in the S3 bucket
    if (!Storage::disk('s3')->exists($path)) {
        abort(404, 'File not found!');
    }

    // Retrieve file's MIME type and original extension
    $mimeType = Storage::disk('s3')->mimeType($path);
    $extension = pathinfo($resource->fileLocation, PATHINFO_EXTENSION);

    // Force download with the correct file name and extension
    return response()->streamDownload(function () use ($path) {
        echo Storage::disk('s3')->get($path);
    }, "{$resource->fileName}.{$extension}", [
        'Content-Type' => $mimeType, // Set the MIME type
        'Content-Disposition' => 'attachment', // Force download
    ]);
}
}
