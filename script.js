/* ============================================================
   LOAD STUDENTS FROM DATABASE
============================================================ */
function loadStudents() {

    $.ajax({
        url: "list_students_api.php",
        method: "GET",
        dataType: "json",

        success: function (students) {

            let tbody = $("#studentsBody");
            tbody.html("");

            students.forEach(stu => {

                let row = `
                <tr>
                    <td>${stu.id}</td>
                    <td>${stu.last_name}</td>
                    <td>${stu.first_name}</td>
                    <td>AWP</td>
                `;

                // 6 sessions
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

}

/* ============================================================
   UPDATE SUMMARY
============================================================ */
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
        totalPresent += (6 - abs);
    });

    $("#sum_students").text(totalStudents);
    $("#sum_present").text(totalPresent);
    $("#sum_absent").text(totalAbsent);
    $("#sum_part").text(totalParts);
}

/* ============================================================
   CALCULATE ONE ROW
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
        msg.textContent = "Warning – attendance low – You need more participation";
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
    const sessionPart    = [0,0,0,0,0,0];

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
                { label: "Present",      data: sessionPresent, backgroundColor: "#7a5c2f" },
                { label: "Participation", data: sessionPart,    backgroundColor: "#c2a878" }
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

    loadStudents(); // 🔥 FIRST LOAD

    /* ADD STUDENT */
    $("#addForm").submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: "add_student.php",
            method: "POST",
            data: $("#addForm").serialize(),

            success: function() {
                $("#addMsg").text("Student added!");
                $("#sid,#lname,#fname,#email").val("");
                loadStudents();
            }
        });
    });

    /* UPDATE ROW ON CHANGE */
    $(document).on("change", "input.present, input.part", function () {
        calculateRow(this.closest("tr"));
        updateSummary();
    });

    /* SHOW REPORT */
    $("#showReportBtn").click(showReport);

    /* RESET COLORS */
    $("#resetBtn").click(function () {
        $("#attendanceTable tbody tr").removeClass("good warning bad");
        updateSummary();
    });

    /* SEARCH */
    $("#searchInput").on("keyup", function () {
        const value = $(this).val().toLowerCase();
        $("#attendanceTable tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().includes(value));
        });
    });

    /* SORT BY ABSENCES */
    $("#sortAbs").click(function () {
        calculateAll();
        const rows = $("#studentsBody tr").get();
        rows.sort((a,b)=> parseInt($(a).find(".abs").text()) - parseInt($(b).find(".abs").text()));
        $("#studentsBody").append(rows);
    });

    /* SORT BY PARTICIPATIONS */
    $("#sortPar").click(function () {
        calculateAll();
        const rows = $("#studentsBody tr").get();
        rows.sort((a,b)=> parseInt($(b).find(".par").text()) - parseInt($(a).find(".par").text()));
        $("#studentsBody").append(rows);
    });

});
