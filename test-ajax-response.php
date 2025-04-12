<?php
// This is a diagnostic script to test the AJAX response

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>AJAX Response Tester</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #333;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
            overflow-x: auto;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 20px;
        }
        button:hover {
            background-color: #45a049;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 20px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>AJAX Response Tester</h1>
    <p>This tool will help diagnose issues with JSON responses from the AJAX endpoints.</p>
    
    <h2>Test ajax-save-timeslots.php</h2>
    <form id="testForm">
        <div>
            <label for="selected_day">Selected Day:</label>
            <select id="selected_day" name="selected_day">
                <option value="monday">Monday</option>
                <option value="tuesday">Tuesday</option>
                <option value="wednesday">Wednesday</option>
                <option value="thursday">Thursday</option>
                <option value="friday">Friday</option>
                <option value="saturday">Saturday</option>
                <option value="sunday">Sunday</option>
            </select>
        </div>
        <div style="margin-top: 10px;">
            <label for="time_slots_ajax">Time Slots Data (JSON):</label>
            <textarea id="time_slots_ajax" name="time_slots_ajax">[{"start":"09:00","end":"09:30","type":"Video Consultation","checked":true}]</textarea>
        </div>
        <button type="button" id="testButton">Test AJAX Response</button>
    </form>
    
    <div id="results" style="display: none;">
        <h2>Results</h2>
        <h3>Raw Response:</h3>
        <pre id="rawResponse"></pre>
        
        <h3>Parsed JSON:</h3>
        <pre id="parsedJson"></pre>
    </div>
    
    <script>
    document.getElementById('testButton').addEventListener('click', function() {
        const resultsDiv = document.getElementById('results');
        const rawResponsePre = document.getElementById('rawResponse');
        const parsedJsonPre = document.getElementById('parsedJson');
        
        // Show results section
        resultsDiv.style.display = 'block';
        
        // Get form data
        const selectedDay = document.getElementById('selected_day').value;
        const timeSlotsData = document.getElementById('time_slots_ajax').value;
        
        // Create form data
        const formData = new FormData();
        formData.append('selected_day', selectedDay);
        formData.append('time_slots_ajax', timeSlotsData);
        
        // Send AJAX request
        fetch('ajax-save-timeslots.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Get the raw text from the response
            return response.text().then(text => {
                // Display the raw response
                rawResponsePre.textContent = text;
                
                // Try to parse as JSON
                try {
                    const json = JSON.parse(text);
                    parsedJsonPre.innerHTML = '<span class="success">Valid JSON:</span>\n' + 
                                             JSON.stringify(json, null, 2);
                } catch (e) {
                    parsedJsonPre.innerHTML = '<span class="error">Invalid JSON: ' + e.message + '</span>';
                    
                    // If there's HTML in the response, show a hint
                    if (text.includes('<')) {
                        parsedJsonPre.innerHTML += '\n\nPossible issues:\n' +
                            '- PHP errors or warnings are being output before the JSON\n' +
                            '- HTML content is being included in the response\n' +
                            '- Headers are being sent after output has started';
                    }
                }
            });
        })
        .catch(error => {
            rawResponsePre.textContent = 'Network error: ' + error.message;
            parsedJsonPre.innerHTML = '<span class="error">Request failed</span>';
        });
    });
    </script>
</body>
</html> 