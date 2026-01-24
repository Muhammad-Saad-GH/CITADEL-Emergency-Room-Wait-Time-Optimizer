<?php
require_once(__DIR__ . "/../../config/session.php");
require_once(__DIR__ . "/../../config/config.php");
requireRole("patient");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard | Citadel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/anim.css">

    <style>
        .admin-grid {
            display: grid;
            gap: 2rem;
            padding: 2rem;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            max-width: 1300px;
            margin: 0 auto;
        }

        .admin-card {
            background: rgba(11, 7, 18, 0.72);
            border: 1px solid rgba(126, 92, 255, 0.18);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 18px rgba(126, 92, 255, 0.12);
            backdrop-filter: blur(8px);
            transition: 0.25s ease;
        }

        .admin-card:hover {
            border-color: rgba(126, 92, 255, 0.45);
            box-shadow: 0 0 24px rgba(126, 92, 255, 0.28);
            transform: translateY(-4px);
        }

        .admin-card h2 {
            margin-bottom: 0.75rem;
            font-size: 1.4rem;
            color: #ede9fe;
            text-shadow: 0 0 12px rgba(126, 92, 255, 0.5);
        }

        .admin-card p {
            color: #b8addd;
            font-size: 0.95rem;
            margin-bottom: 1.25rem;
        }

        .admin-card a {
            font-weight: 600;
        }

        /* Small card style for hospitals */
        .hospital-card {
            position: relative;
            margin-top: 0.75rem;
            padding: 0.75rem 1rem 2.6rem;
            border-radius: 0.75rem;
            background: rgba(11, 7, 18, 0.9);
            border: 1px solid rgba(126, 92, 255, 0.15);
            box-shadow: 0 0 12px rgba(126, 92, 255, 0.08);
            font-size: 0.9rem;
            overflow: hidden;
            transition: 0.25s ease;
        }

        .hospital-card h3 {
            margin: 0 0 0.25rem;
            font-size: 1rem;
        }

        .hospital-card p {
            margin: 0.15rem 0;
        }

        .hospital-card:hover {
            border-color: rgba(126, 92, 255, 0.45);
            box-shadow:
                0 0 20px rgba(126, 92, 255, 0.35),
                0 0 35px rgba(126, 92, 255, 0.15) inset;
            transform: translateY(-4px);
        }


        /* Directions button – top-right, hover-only */
        .directions-btn {
            position: absolute;
            right: 0.9rem;
            top: 0.75rem;
            display: none;

            padding: 0.55rem 1.2rem;
            background: #22c55e;
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 0.5rem;
            text-decoration: none;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.4);
            transition: 0.2s ease;
        }
        .hospital-card:hover .directions-btn {
            display: inline-block;
        }
        .directions-btn:hover {
            background: #16a34a;
            box-shadow: 0 0 16px rgba(34, 197, 94, 0.55);
            transform: translateY(-2px);
        }

        /* Estimated wait-time pill – bottom-right, hover-only */
        .waittime-tooltip {
            position: absolute;
            right: 0.9rem;
            bottom: 0.75rem;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: rgba(126, 92, 255, 0.12);
            border: 1px solid rgba(126, 92, 255, 0.5);
            font-size: 0.75rem;
            color: #ede9fe;
            box-shadow: 0 0 12px rgba(126, 92, 255, 0.35);
            display: none;
        }

        .hospital-card:hover .waittime-tooltip {
            display: inline-flex;
        }

        /* Map preview styling */
        .map-preview iframe {
            width: 100%;
            height: 170px;
            border: 1px solid rgba(126, 92, 255, 0.12);
            border-radius: 0.75rem;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 14px rgba(126, 92, 255, 0.25);
            transition: 0.25s ease;
        }

        .hospital-card:hover .map-preview iframe {
            box-shadow: 0 0 24px rgba(126, 92, 255, 0.35);
            transform: translateY(-2px);
        }

    </style>
</head>

<body class="dark-bg">
    <div id="fireflies">
        <?php for ($i = 1; $i <= 20; $i++): ?>
            <div class="firefly"></div>
        <?php endfor; ?>
    </div>

    <header>
        <div>
            <h1>Patient Dashboard</h1>
            <div class="user-info">
                Logged in as:
                <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Patient'); ?></strong>
            </div>
        </div>

        <div>
            <a href="checkin.php" class="btn btn-primary">Create New Check-in</a>
            <a href="../../backend/auth/logout.php" class="btn btn-outline">Logout</a>
        </div>
    </header>

    <div class="dashboard-flex">
        <!-- Hospitals Section -->
        <section id="hospitalsSection" class="dashboard-card">
            <h2>Hospitals</h2>
            <p class="user-info" id="hospitalsSubText">Loading hospitals...</p>
            <div id="hospitalList"></div>
        </section>

        <!-- Check-in Section -->
        <section class="dashboard-card">
            <h2>Check-in Details</h2>

            <section class="summary-box" id="summaryBox" style="display:none;">
                <!-- Filled by JS -->
            </section>

            <table id="checkinsTable" style="display:none;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hospital</th>
                        <th>Severity</th>
                        <th>Approval Status</th>
                        <th>Wait Time (mins)</th>
                        <th>Notes</th>
                        <th>Completion Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <p id="emptyMessage">You have no check-ins.</p>
        </section>
    </div>

    <!--get key from config.php-->
    <script>
    const GOOGLE_API_KEY = "<?php echo $API_KEY; ?>";
    </script>


    <script>
        async function loadCheckins() {
            try {
                const response = await fetch('../../backend/patient/get_checkins.php', {
                    credentials: 'include'
                });
                const data = await response.json();

                const tbody = document.querySelector('#checkinsTable tbody');
                const table = document.getElementById('checkinsTable');
                const emptyMsg = document.getElementById('emptyMessage');
                const summaryBox = document.getElementById('summaryBox');

                tbody.innerHTML = '';
                summaryBox.style.display = 'none';

                if (!response.ok || data.error) {
                    emptyMsg.textContent = data.error || 'Failed to load check-ins.';
                    emptyMsg.style.display = 'block';
                    table.style.display = 'none';
                    return;
                }

                if (!Array.isArray(data) || data.length === 0) {
                    emptyMsg.textContent =
                        'You have no active check-ins. Use "Create New Check-in" to submit one.';
                    emptyMsg.style.display = 'block';
                    table.style.display = 'none';
                    summaryBox.textContent = 'You have no check-ins at the moment.';
                    summaryBox.style.display = 'block';
                    return;
                }

                emptyMsg.style.display = 'none';
                table.style.display = 'table';

                data.forEach(row => {
                    const tr = document.createElement('tr');

                    const statusClass = 'status-' + (row.Status || '').replace(/\s+/g, '-');
                    const isApproved  = (row.Approved === 1 || row.Approved === '1');
                    const approvedLabel = isApproved ? 'Approved' : 'Pending';
                    const approvedColorClass = isApproved ? 'status-Completed' : 'status-Waiting';

                    tr.innerHTML = `
                        <td>${row.Checkin_ID}</td>
                        <td>${row.HospitalName} (${row.HospitalLocation})</td>
                        <td>${row.Severity !== null ? row.Severity : '-'}</td>
                        <td>
                            <span class="status-tag ${statusClass}">
                                ${row.Status}
                            </span>
                        </td>
                        <td>${row.Wait_Time !== null ? row.Wait_Time : '-'}</td>
                        <td>${row.Notes ? row.Notes : ''}</td>
                        <td>
                            <span class="status-tag ${approvedColorClass}">
                                ${approvedLabel}
                            </span>
                        </td>
                    `;

                    tbody.appendChild(tr);
                });

                const row = data[0];
                const isApproved = (row.Approved === 1 || row.Approved === '1');

                if (!row.Checkin_ID) {
                    summaryBox.textContent = 'You have no check-ins at the moment.';
                } else if (row.Status === 'Waiting' && !isApproved) {
                    summaryBox.textContent =
                        'You have 1 check-in that is waiting and pending staff approval.';
                } else if (row.Status === 'Waiting' && isApproved) {
                    summaryBox.textContent =
                        'Your check-in is waiting to be seen and has been approved by staff.';
                } else if (row.Status === 'In-Treatment') {
                    summaryBox.textContent =
                        'Your check-in is currently in treatment.';
                } else if (row.Status === 'Completed') {
                    summaryBox.textContent =
                        'Your last check-in has been marked as completed.';
                } else if (row.Status === 'Cancelled') {
                    summaryBox.textContent =
                        'Your last check-in has been cancelled.';
                } else {
                    summaryBox.textContent =
                        'Your current check-in status is: ' + row.Status + '.';
                }

                summaryBox.style.display = 'block';

            } catch (err) {
                console.error(err);
                const emptyMsg = document.getElementById('emptyMessage');
                emptyMsg.textContent = 'An error occurred while loading check-ins.';
                emptyMsg.style.display = 'block';
                const table = document.getElementById('checkinsTable');
                table.style.display = 'none';
            }
        }

        async function loadHospitals() {
            try {
                const response = await fetch('../../backend/patient/get_hospitals.php', {
                    credentials: 'include'
                });
                const data = await response.json();

                const subText = document.getElementById('hospitalsSubText');
                const list    = document.getElementById('hospitalList');

                list.innerHTML = '';

                if (!response.ok || data.error) {
                    subText.textContent = data.error || 'Failed to load hospitals.';
                    return;
                }

                if (!Array.isArray(data) || data.length === 0) {
                    subText.textContent = 'No hospitals found.';
                    return;
                }

                subText.textContent = `Showing ${data.length} hospital(s).`;

                data.forEach(h => {
                    const div = document.createElement('div');
                    div.className = 'hospital-card';

                    const wait =
                        h.Avg_Wait_Time !== null
                            ? `${h.Avg_Wait_Time} min${h.Avg_Wait_Time == 1 ? '' : 's'}`
                            : 'No data';

                    div.innerHTML = `
                        <h3>${h.Name}</h3>
                        <p>${h.Location}</p>
                        <p>Phone: ${h.Phone_Num}</p>
                        <p>Rating: ${h.Rating ?? 'N/A'}/5 ⭐</p>

                        <a 
                            href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(h.Name + ' ' + h.Location)}" 
                            target="_blank" 
                            class="directions-btn">
                            Directions
                        </a>

                        <div class="waittime-tooltip">
                            Est. wait: ${wait}
                        </div>

                        <div class="map-preview">
                            <iframe
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps/embed/v1/place?key=${GOOGLE_API_KEY}&q=${encodeURIComponent(h.Name + ' ' + h.Location)}">
                            </iframe>
                        </div>
                    `;

                    list.appendChild(div);
                });

            } catch (err) {
                console.error(err);
                const subText = document.getElementById('hospitalsSubText');
                subText.textContent = 'An error occurred while loading hospitals.';
            }
        }

        loadCheckins();
        loadHospitals();
    </script>
</body>
</html>
