<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book Appointment</title>
</head>
<body>
    <h2>Book an Appointment</h2>
    <label>Select Date:</label>
    <input type="date" id="datePicker">

    <label>Select Time:</label>
    <select id="timePicker"></select>

    <button id="bookBtn">Book Appointment</button>

    <h3>My Appointments</h3>
    <ul id="myAppointments"></ul>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let therapistId = {{ $therapist->id }}; // pass from controller

        $("#datePicker").on("change", function() {
            let date = $(this).val();
            $.get(`/appointments/available/${therapistId}?date=${date}`, function(data) {
                let timePicker = $("#timePicker");
                timePicker.html("");
                data.forEach(time => {
                    timePicker.append(`<option value="${time}">${time}</option>`);
                });
            });
        });

        $("#bookBtn").click(function() {
            $.post("/appointments/book", {
                _token: $("meta[name='csrf-token']").attr("content"),
                therapist_id: therapistId,
                date: $("#datePicker").val(),
                time: $("#timePicker").val()
            }, function(res) {
                alert(res.message);
                loadAppointments();
            }).fail(err => alert(err.responseJSON.error));
        });

        function loadAppointments() {
            $.get("/appointments/my", function(data) {
                let list = $("#myAppointments");
                list.html("");
                data.forEach(appt => {
                    list.append(`<li>${appt.date} at ${appt.time} - ${appt.status}
                        <button onclick="cancelAppt(${appt.id})">Cancel</button></li>`);
                });
            });
        }

        function cancelAppt(id) {
            $.ajax({
                url: `/appointments/${id}/cancel`,
                type: "DELETE",
                data: {_token: $("meta[name='csrf-token']").attr("content")},
                success: function(res) {
                    alert(res.message);
                    loadAppointments();
                }
            });
        }

        loadAppointments();
    </script>
</body>
</html>
