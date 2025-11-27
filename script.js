/* ============================================================
   LOAD STUDENTS FROM DATABASE
============================================================ */
// Load students from MySQL and show them in main table
function loadStudents() {

    $.ajax({
        url: "list_students_api.php",
        method: "GET",
        dataType: "json",

        success: function (students) {

            let tbody = $("#studentsBody");
            tbody.html(""); // Clear table

            students.forEach(stu => {

                let row = `
                <tr>
                    <td>${stu.id}</td>
                    <td>${stu.last_name}</td>
                    <td>${stu.first_name}</td>
                    <td>AWP</td>
                `;

                for (let i = 0; i < 6; i++) {
                    row += `
                        <td><input type="checkbox" class="present"></td>
                        <td><input type="checkbox" class="part"></td>
                    `;
                }

                row += `
                    <td class="abs">0</td>
                    <td class="par">0</td>
                    <td class="msg"></td>
                    <td><button class="btn small del">X</button></td>
                </tr>
                `;

                tbody.append(row);
            });
        }
    });
loadAttendanceValues();


}

function updateSummary() {
    let totalStudents = $("#studentsBody tr").length;
    let totalPresent = 0;
    let totalAbsent = 0;
    let totalParts = 0;

    $("#studentsBody tr").each(function () {

        let abs = parseInt($(this).find(".abs").text()) || 0;
        let par = parseInt($(this).find(".par").text()) || 0;

        totalAbsent += abs;
        totalParts  += par;

        // THERE ARE 6 SESSIONS
        totalPresent += (6 - abs);
    });

    $("#sum_students").text(totalStudents);
    $("#sum_present").text(totalPresent);
    $("#sum_absent").text(totalAbsent);
    $("#sum_part").text(totalParts);
}

$("#sortAbs").click(function () {
    // your sorting...
    updateSummary();
});

$("#resetBtn").click(function(){
    updateSummary();
});

/* ============================================================
   CALCULATE ROW
============================================================ */
function calculateRow(tr) {
    const presentBoxes = tr.querySelectorAll("input.present");
    const partBoxes = tr.querySelectorAll("input.part");

    let presents = 0, parts = 0;

    presentBoxes.forEach(cb => cb.checked && presents++);
    partBoxes.forEach(cb => cb.checked && parts++);

    const abs = presentBoxes.length - presents;

    tr.querySelector(".abs").textContent = abs;
    tr.querySelector(".par").textContent = parts;

    tr.classList.remove("good", "warning", "bad");

    const msg = tr.querySelector(".msg");

    if (abs < 3) {
        tr.classList.add("good");
        msg.textContent = "Good attendance – Excellent participation";
    } else if (abs <= 4) {
        tr.classList.add("warning");
        msg.textContent = "Warning – attendance low – You need to participate more";
    } else {
        tr.classList.add("bad");
        msg.textContent = "Excluded – too many absences";
    }
}

/* ============================================================
   CALCULATE ALL ROWS
============================================================ */
function calculateAll() {
    $("#attendanceTable tbody tr").each(function () {
        calculateRow(this);
    });
}

/* ============================================================
   REPORT
============================================================ */
function showReport() {
    calculateAll();

    const rows = document.querySelectorAll("#attendanceTable tbody tr");

    const sessionPresent = [0,0,0,0,0,0];
    const sessionPart = [0,0,0,0,0,0];

    rows.forEach(tr => {
        const presentBoxes = tr.querySelectorAll("input.present");
        const partBoxes = tr.querySelectorAll("input.part");

        for (let i = 0; i < 6; i++) {
            if (presentBoxes[i].checked) sessionPresent[i]++;
            if (partBoxes[i].checked) sessionPart[i]++;
        }
    });

    if (window.reportChartInstance) {
        window.reportChartInstance.destroy();
    }

    const ctx = document.getElementById("reportChart");

    window.reportChartInstance = new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["S1","S2","S3","S4","S5","S6"],
            datasets: [
                { label: "Present", data: sessionPresent, backgroundColor: "#7a5c2f" },
                { label: "Participated", data: sessionPart, backgroundColor: "#c2a878" }
            ]
        },
        options: { indexAxis: "y", responsive: true }
    });

    $("#report").show();
}

/* ============================================================
   PAGE READY
============================================================ */
$(document).ready(function () {

    /* Load database students */
    loadStudents();
$("#addForm").submit(function(e) {
    e.preventDefault();

    $.ajax({
        url: "add_student.php",
        method: "POST",
        data: $("#addForm").serialize(),

        success: function() {
            $("#addMsg").text("Student added!");
            $("#sid, #lname, #fname, #email").val("");

            loadStudents(); // Reload table immediately
        }
    });
});




    /* Attendance change = update row */
    $(document).on("change", "input.present, input.part", function () {
        calculateRow(this.closest("tr"));
    });

    /* Buttons */
    $("#showReportBtn").click(showReport);

    $("#resetBtn").click(function () {
        $("#attendanceTable tbody tr").removeClass("good warning bad");
    });

    $("#excellentBtn").click(function () {
        calculateAll();
        $("#attendanceTable tbody tr").each(function () {
            if (parseInt($(this).find(".abs").text()) < 3)
                $(this).fadeOut(100).fadeIn(100);
        });
    });

    $("#searchInput").on("keyup", function () {
        const value = $(this).val().toLowerCase();
        $("#attendanceTable tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().includes(value));
        });
    });

    $("#sortAbs").click(function () {
        calculateAll();
        const rows = $("#attendanceTable tbody tr").get();
        rows.sort((a, b) =>
            parseInt($(a).find(".abs").text()) -
            parseInt($(b).find(".abs").text())
        );
        $("#attendanceTable tbody").append(rows);
    });

    $("#sortPar").click(function () {
        calculateAll();
        const rows = $("#attendanceTable tbody tr").get();
        rows.sort((a, b) =>
            parseInt($(b).find(".par").text()) -
            parseInt($(a).find(".par").text())
        );
        $("#attendanceTable tbody").append(rows);
    });

function saveAttendance(sessionId) {
    let records = [];

    $("#attendanceTable tbody tr").each(function(){
        let id = $(this).find("td:first").text();

        let presents = $(this).find("input.present:checked").length;
        let parts = $(this).find("input.part:checked").length;

        records.push({
            student_id: id,
            present: presents > 0 ? 1 : 0,
            participated: parts > 0 ? 1 : 0
        });
    });

    $.post("save_attendance.php", {
        session_id: sessionId,
        records: JSON.stringify(records)
    }, function(res){
        alert("Attendance saved!");
    });
}
})
$(document).ready(function() {
    loadStudents();
});

$(document).on("change", ".present, .part", function () {

    let tr = $(this).closest("tr");
    let studentId = tr.find("td:first").text();

    // Detect which session number (1 to 6)
    let cellIndex = $(this).closest("td").index();
    let sessionNumber = Math.ceil((cellIndex - 3) / 2);

    let present = tr.find(`td:nth-child(${4 + (sessionNumber - 1)*2}) input.present`).prop("checked") ? 1 : 0;
    let participated = tr.find(`td:nth-child(${5 + (sessionNumber - 1)*2}) input.part`).prop("checked") ? 1 : 0;

    $.ajax({
        url: "save_attendance.php",
        type: "POST",
        data: {
            student_id: studentId,
            session_id: CURRENT_SESSION_ID,
            session_number: sessionNumber,
            present: present,
            participated: participated
        }
    });

});

function loadAttendanceValues() {
    $.ajax({
        url: "load_attendance.php",
        type: "GET",
        data: { session_id: CURRENT_SESSION_ID },
        success: function (response) {
            let data = JSON.parse(response);

            data.forEach(a => {

                let row = $(`#studentsBody tr`).filter(function(){
                    return $(this).find("td:first").text() == a.student_id;
                });

                let presentCell = row.find(`td:nth-child(${4 + (a.session_number - 1)*2}) input.present`);
                let partCell = row.find(`td:nth-child(${5 + (a.session_number - 1)*2}) input.part`);

                presentCell.prop("checked", a.present == 1);
                partCell.prop("checked", a.participated == 1);
            });
        }
    });
}
