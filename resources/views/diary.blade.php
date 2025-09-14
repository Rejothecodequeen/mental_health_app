@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Welcome, {{ Auth::user()->name }}!</h4>

    <div class="row g-4">

        <!-- Diary / Personal Journal -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">Diary / Personal Journal</div>
                <div class="card-body">

                    <!-- Form to add entry -->
                    <form id="diaryForm" class="mb-4">
                        <div class="mb-2">
                            <input type="text" id="title" class="form-control" placeholder="Title" required>
                        </div>
                        <div class="mb-2">
                            <textarea id="content" class="form-control" placeholder="Write something..." required></textarea>
                        </div>
                        <div class="mb-2">
                            <input type="date" id="entry_date" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm">Save Entry</button>
                    </form>

                    <!-- Display entries -->
                    <h5>My Entries</h5>
                    <ul class="list-group" id="entries"></ul>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    function loadEntries() {
        $.get("/api/diary", function(data) {
            let list = $("#entries");
            list.html("");
            data.forEach(entry => {
                list.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${entry.title}</strong> (${entry.entry_date})<br>
                            ${entry.content}
                        </div>
                        <button class="btn btn-sm btn-danger" onclick="deleteEntry(${entry.id})">Delete</button>
                    </li>
                `);
            });
        });
    }

    $("#diaryForm").submit(function(e) {
        e.preventDefault();
        $.post("/api/diary", {
            _token: csrfToken,
            title: $("#title").val(),
            content: $("#content").val(),
            entry_date: $("#entry_date").val()
        }, function(res) {
            alert(res.message);
            $("#title").val(""); $("#content").val(""); $("#entry_date").val("");
            loadEntries();
        });
    });

    function deleteEntry(id) {
        $.ajax({
            url: `/api/diary/${id}`,
            type: "DELETE",
            data: { _token: csrfToken },
            success: function(res) {
                alert(res.message);
                loadEntries();
            }
        });
    }

    // Initial load
    loadEntries();
</script>
@endsection
