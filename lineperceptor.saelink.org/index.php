<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $hostname = gethostname();
    ?>
    <title>Lineperceptor 2025 <?php echo htmlspecialchars($hostname, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="manifest" href="./index.json">
    <style>
        /* Theme variables */
        :root {
            --accent-color: #00BFFF;       /* Accent color */
            --bg-color: #121212;           /* Page background */
            --card-bg: #1e1e1e;            /* Container background */
            --text-color: #FFFFFF;         /* Primary text */
            --muted-color: #AAAAAA;        /* Secondary text */
            --border-radius: 12px;         /* Unified border radius */
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5); /* Card shadow */
            --header-bg: #142231;          /* Table header background */
            --hover-bg: rgba(0, 47, 79, 0.5); /* Row hover background */
        }
        html, body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
            font-size: 18px;
            line-height: 1.6;
        }
        *, *::before, *::after {
            box-sizing: inherit;
        }
        body {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Left alignment */
            padding: 20px;
        }

        /* Page title and timestamp */
        h1 {
            font-size: 24px;
            color: var(--accent-color);
            margin: 0;                  /* Remove default top/bottom margins */
            margin-bottom: 4px;         /* Reduce space below title */
            text-align: left;
            width: 100%;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }
        #lastUpdated {
            font-size: 24px;
            color: var(--accent-color);
            margin: 0;                  /* Remove default margins */
            margin-bottom: 10px;        /* Space between timestamp and table */
            text-align: left;
            width: 100%;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        /* Table styles */
        .table-wrapper {
            width: 100%;
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            overflow-x: auto;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            background-color: transparent;
        }
        th, td {
            padding: 12px;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        th {
            background-color: var(--header-bg);
            color: var(--text-color); /* Header text set to white */
            font-size: 1em;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: none;
        }
        /* Column width classes */
        .num-col    { width: 10%; }
        .domain-col { width: 60%; }
        .port-col   { width: 10%; }
        .status-col { width: 20%; }

        tbody td {
            color: var(--text-color);
            border-bottom: 1px solid rgba(0, 191, 255, 0.3);
            font-size: 1em;
            text-align: left;
        }
        tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.05);
        }
        tbody tr:nth-child(odd) {
            background-color: var(--card-bg);
        }
        tbody tr:hover {
            background-color: var(--hover-bg);
        }

        /* Status colors */
        .up   { color: #00FF00; }
        .down { color: #FF0000; }

        /* Responsive adjustments */
        @media screen and (max-width: 600px) {
            th, td {
                padding: 8px;
                font-size: 0.9em;
            }
        }
    </style>
    <?php include("matomo.php"); ?>
</head>
<body>
    <h1>Lineperceptor 2025 <?php echo htmlspecialchars($hostname, ENT_QUOTES, 'UTF-8'); ?></h1>
    <div id="lastUpdated">TS: Not yet updated</div>

    <div class="table-wrapper">
        <table id="statusTable">
            <thead>
                <tr>
                    <th class="num-col">NUM</th>
                    <th class="domain-col">DOMAIN</th>
                    <th class="port-col">PORT</th>
                    <th class="status-col">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be populated via JavaScript -->
            </tbody>
        </table>
    </div>

    <script>
        function fetchData() {
            fetch('./data.json')
                .then(response => response.json())
                .then(data => {
                    updateStatus(data.domains);
                    updateUpdateTime(data.updateTime);
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        function updateStatus(domains) {
            const tbody = document.getElementById('statusTable').querySelector('tbody');
            tbody.innerHTML = '';
            domains.forEach((domain, index) => {
                const statusClass = domain.status === 'UP' ? 'up' : 'down';
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="num-col">${index + 1}</td>
                    <td class="domain-col">${domain.displayName}</td>
                    <td class="port-col">${domain.port}</td>
                    <td class="status-col ${statusClass}">${domain.status}</td>
                `;
                tbody.appendChild(row);
            });
        }

        function updateUpdateTime(time) {
            document.getElementById('lastUpdated').innerText = `TS: ${time}`;
        }

        setInterval(fetchData, 30000);
        fetchData();
    </script>
</body>
</html>
