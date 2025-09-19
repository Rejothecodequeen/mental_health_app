@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Resources</h2>

    {{-- Show upload form only if user is therapist --}}
    @can('isTherapist')
    <div class="card mb-4">
        <div class="card-header">Upload New Resource</div>
        <div class="card-body">
            <form action="{{ url('/resources') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Type *</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="pdf">PDF</option>
                        <option value="doc">DOC/DOCX</option>
                        <option value="video">Video</option>
                        <option value="image">Image</option>
                        <option value="link">External Link</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="file" class="form-label">Upload File</label>
                    <input type="file" name="file" id="file" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">Or provide URL</label>
                    <input type="url" name="url" id="url" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
    @endcan

    {{-- List all resources --}}
    <div class="card">
        <div class="card-header">Available Resources</div>
        <div class="card-body">
            @if($resources->isEmpty())
                <p>No resources uploaded yet.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resources as $resource)
                        <tr>
                            <td>{{ $resource->title }}</td>
                            <td>{{ $resource->description }}</td>
                            <td>{{ strtoupper($resource->type) }}</td>
                            <td>
                                @if($resource->file_path)
                                    <a href="{{ asset('storage/' . $resource->file_path) }}" class="btn btn-sm btn-success" download>Download</a>
                                @elseif($resource->url)
                                    <a href="{{ $resource->url }}" target="_blank" class="btn btn-sm btn-info">Open Link</a>
                                @endif

                                {{-- Therapist-only controls --}}
                                @can('isTherapist')
                                    {{-- Delete --}}
                                    <form action="{{ url('/resources/' . $resource->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                    {{-- Edit button (if you implement edit/update routes) --}}
                                    {{-- <a href="{{ url('/resources/'.$resource->id.'/edit') }}" class="btn btn-sm btn-warning">Edit</a> --}}
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
