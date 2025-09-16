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

                    <!-- Alerts -->
                    <div id="alertBox"></div>

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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Diary Entry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit_id">
            <div class="mb-2">
                <input type="text" id="edit_title" class="form-control" placeholder="Title" required>
            </div>
            <div class="mb-2">
                <textarea id="edit_content" class="form-control" placeholder="Write something..." required></textarea>
            </div>
            <div class="mb-2">
                <input type="date" id="edit_entry_date" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Function to show Bootstrap alert
    function showAlert(message, type = 'success') {
        let alertId = "alert-" + Date.now();
        $("#alertBox").html(`
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        setTimeout(() => { $("#" + alertId).alert('close'); }, 3000);
    }

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
                        <div>
                            <button class="btn btn-sm btn-primary me-1" onclick="openEditModal(${entry.id}, '${entry.title}', '${entry.content}', '${entry.entry_date}')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteEntry(${entry.id})">Delete</button>
                        </div>
                    </li>
                `);
            });
        });
    }

    // Save new entry
    $("#diaryForm").submit(function(e) {
        e.preventDefault();
        $.post("/api/diary", {
            _token: csrfToken,
            title: $("#title").val(),
            content: $("#content").val(),
            entry_date: $("#entry_date").val()
        }, function(res) {
            showAlert(res.message, "success");
            $("#title").val(""); $("#content").val(""); $("#entry_date").val("");
            loadEntries();
        }).fail(() => showAlert("Failed to save entry.", "danger"));
    });

    // Open modal with entry details
    function openEditModal(id, title, content, entry_date) {
        $("#edit_id").val(id);
        $("#edit_title").val(title);
        $("#edit_content").val(content);
        $("#edit_entry_date").val(entry_date);
        $("#editModal").modal("show");
    }

    // Update entry
    $("#editForm").submit(function(e) {
        e.preventDefault();
        let id = $("#edit_id").val();
        $.ajax({
            url: `/api/diary/${id}`,
            type: "PUT",
            data: {
                _token: csrfToken,
                title: $("#edit_title").val(),
                content: $("#edit_content").val(),
                entry_date: $("#edit_entry_date").val()
            },
            success: function(res) {
                showAlert(res.message, "success");
                $("#editModal").modal("hide");
                loadEntries();
            },
            error: function() {
                showAlert("Failed to update entry.", "danger");
            }
        });
    });

    // Delete entry
    function deleteEntry(id) {
        $.ajax({
            url: `/api/diary/${id}`,
            type: "DELETE",
            data: { _token: csrfToken },
            success: function(res) {
                showAlert(res.message, "success");
                loadEntries();
            },
            error: function() {
                showAlert("Failed to delete entry.", "danger");
            }
        });
    }

    // Initial load
    loadEntries();
</script>
@endsection
