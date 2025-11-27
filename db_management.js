$(document).ready(function() {

    // Load sessions
    function loadSessions() {
        $.get("list_sessions.php", function(data) {
            $("#sessionTable").html(data);
        });
    }

    loadSessions();

    // Create a session
    $("#sessionForm").submit(function(e) {
        e.preventDefault();

        $.post("create_session.php", $(this).serialize(), function(response) {
            let res = JSON.parse(response);

            if (res.success) {
                $("#sessionMsg").text("Session created! ID = " + res.session_id);
                loadSessions();
            } else {
                $("#sessionMsg").text("Error: " + res.error);
            }
        });
    });

    // Close session
    $(document).on("click", ".closeSessionBtn", function() {
        let id = $(this).data("id");

        $.post("close_session.php", { session_id: id }, function(msg) {
            alert(msg);
            loadSessions();
        });
    });

});
