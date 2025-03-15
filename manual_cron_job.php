<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Scheduler</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pending { color: orange; }
        .running { color: blue; }
        .success { color: green; }
        .failed { color: red; }
    </style>
    <?php
    define('APP_URL', 'https://mylines.in/projects/ritik/cron_jobs/');
    $firstApiTime = isset($_REQUEST['firstApiTime']) ? $_REQUEST['firstApiTime'] : '07:54:00 PM';
    ?>
</head>
<body>
    <h2>API Scheduler (Asia/Kolkata Time)</h2>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>API URL</th>
                <th>Scheduled Time</th>
                <th>Status</th>
                <th>Response</th>
            </tr>
        </thead>
        <tbody id="apiLog"></tbody>
    </table>

    <script>
        const APP_URL = "<?php echo APP_URL; ?>";
        const apiEndpoints = [
            "start_counter.php", "send_winning_number1.php", "send_winning_sticker1.php",
            "send_winning_number2.php", "send_winning_sticker2.php", "send_winning_number3.php",
            "send_winning_sticker3.php", "send_winning_number4.php", "send_winning_sticker4.php",
            "send_winning_number5.php", "send_winning_sticker5.php", "send_winning_number6.php",
            "send_winning_sticker6.php", "send_winning_number7.php", "send_winning_sticker7.php",
            "send_winning_number8.php", "send_winning_sticker8.php", "send_winning_number9.php",
            "send_winning_sticker9.php", "send_winning_number10.php", "send_winning_sticker10.php",
            "send_winning_number11.php", "send_winning_sticker11.php", "close_counter.php"
        ];
        
        const firstApiTime = "<?php echo $firstApiTime; ?>";
        const timeGaps = [60, 70, 5, 55, 5, 55, 5, 55, 5, 55, 5, 55, 5, 55, 5, 55, 5, 55, 5, 55, 5, 55, 5];

        function generateApiTimes(firstTime, gaps) {
            let apiTimes = [];
            let currentTime = new Date();
            let [hours, minutes, seconds, period] = firstTime.split(/:| /);
            hours = parseInt(hours);
            minutes = parseInt(minutes);
            seconds = parseInt(seconds);
            
            if (period === "PM" && hours !== 12) hours += 12;
            if (period === "AM" && hours === 12) hours = 0;

            currentTime.setHours(hours, minutes, seconds, 0);
            apiTimes.push(currentTime.toLocaleTimeString("en-US", { timeZone: "Asia/Kolkata" }));

            for (let gap of gaps) {
                currentTime.setSeconds(currentTime.getSeconds() + gap);
                apiTimes.push(currentTime.toLocaleTimeString("en-US", { timeZone: "Asia/Kolkata" }));
            }

            return apiTimes;
        }

        const apiTimes = generateApiTimes(firstApiTime, timeGaps);

        function initializeTable() {
            apiEndpoints.forEach((endpoint, index) => {
                let apiUrl = `${APP_URL}${endpoint}`;
                $("#apiLog").append(`
                    <tr id="row${index}">
                        <td>${index + 1}</td>
                        <td>${apiUrl}</td>
                        <td>${apiTimes[index]}</td>
                        <td class="status pending">Pending</td>
                        <td class="response">-</td>
                    </tr>
                `);
            });
        }

        function runApi(index) {
            if (index < apiEndpoints.length) {
                let row = $(`#row${index}`);
                let apiUrl = `${APP_URL}${apiEndpoints[index]}`;
                row.find(".status").removeClass("pending").addClass("running").text("Running...");

                $.ajax({
                    url: apiUrl,
                    method: "GET",
                    success: function(response) {
                        row.find(".status").removeClass("running").addClass("success").text("Success");
                        row.find(".response").text(JSON.stringify(response));
                    },
                    error: function() {
                        row.find(".status").removeClass("running").addClass("failed").text("Failed");
                        row.find(".response").text("Error in API Call");
                    }
                });
            }
        }

        function checkTime() {
            let now = new Date().toLocaleTimeString("en-US", { timeZone: "Asia/Kolkata" });
            let index = apiTimes.indexOf(now);
            if (index !== -1) {
                runApi(index);
            }
        }

        $(document).ready(function() {
            initializeTable();
            setInterval(checkTime, 1000);
        });
    </script>
    <button onclick="document.location='set_manual_cron.php';" style="font-size:15px;">Set Another TIme</button>
</body>
</html>
