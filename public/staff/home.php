<?php
require_once __DIR__ . '/../../config/session.php';
requireRole("staff") 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/anim.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <div class="min-h-screen flex items-center justify-center">
        <div class="glass w-full max-w-5xl space-y-6">

            <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold">Staff Dashboard</h1>
                    <h2 class="text-base md:text-lg">
                        View and manage patient check-ins and waitlists.
                    </h2>
                </div>
                <div>
                     <a href="../../backend/auth/logout.php" class="btn btn-outline">Logout</a>
                </div>
               
            </header>
            <section class="space-y-3">
                <h2 class="text-xl font-semibold">Severity Overview</h2>
                <p class="text-sm text-slate-400">Distribution of severity across all patient check-ins.</p>

                <div class="bg-black/30 border border-purple-500/30 rounded-xl p-4">
                    <canvas id="severityGraph" height="80"></canvas>
                </div>
            </section>


            <main class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <section class="space-y-3">
                    <h2 class="text-xl font-semibold">Pending Waitlist</h2>
                    <p class="text-sm text-slate-400">
                        Patients currently waiting to be seen.
                    </p>
                    <div id="pending-container" class="space-y-4">
                    </div>
                </section>

                <section class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-semibold">Approved Check-ins</h2>
                            <p class="text-sm text-slate-400">
                            Filter using staff dashboard views.
                            </p>
                        </div>

                        <div>
                            <label for="approved-filter" class="block text-xs mb-1">Filter</label>
                            <select class=" px-5 py-1" id="approved-filter">
                                <option value="all">All Approved</option>
                                <option value="above_avg">Above-Avg Severity</option>
                                <option value="unassigned">Unassigned</option>
                                <option value="assigned_to_me">Assigned To Me</option>
                            </select>
                        </div>
                    </div>

                    <div id="approved-container" class="space-y-4"></div>
                </section>
            </main>
        </div>
    </div>

    <div id="fireflies">
        <div class="firefly"></div><div class="firefly"></div><div class="firefly"></div>
        <div class="firefly"></div><div class="firefly"></div><div class="firefly"></div>
        <div class="firefly"></div><div class="firefly"></div><div class="firefly"></div>
        <div class="firefly"></div><div class="firefly"></div><div class="firefly"></div>
        <div class="firefly"></div><div class="firefly"></div><div class="firefly"></div>
        <div class="firefly"></div><div class="firefly"></div><div class="firefly"></div>
        <div class="firefly"></div><div class="firefly"></div>
    </div>

    <script>
        // ========== TOGGLE LOGIC (NEW) ==========
        window.toggleView = function(id, view) {
            const noteDiv = document.getElementById(`notes-${id}`);
            const aiDiv   = document.getElementById(`ai-${id}`);
            const noteBtn = document.getElementById(`btn-notes-${id}`);
            const aiBtn   = document.getElementById(`btn-ai-${id}`);

            if (view === 'notes') {
                noteDiv.classList.remove('hidden');
                aiDiv.classList.add('hidden');
                
                // Active Style for Notes
                noteBtn.className = "text-purple-400 font-bold border-b-2 border-purple-400 pb-1";
                // Inactive Style for AI
                aiBtn.className   = "text-slate-500 hover:text-purple-400 pb-1 transition-colors";
            } else {
                noteDiv.classList.add('hidden');
                aiDiv.classList.remove('hidden');
                
                // Inactive Style for Notes
                noteBtn.className = "text-slate-500 hover:text-purple-400 pb-1 transition-colors";
                // Active Style for AI
                aiBtn.className   = "text-purple-400 font-bold border-b-2 border-purple-400 pb-1";
            }
        }

        // ========== CARD + RENDER HELPERS ==========
        function createCard(item, mode = 'pending') {
            const div = document.createElement('div');
            div.className = 'form-card space-y-2';

            const severityValue = item.severity ?? '';
            const waitValue     = item.wait ?? '';
            const notesValue    = item.notes ?? '';
            // NEW: Get AI Notes safely
            const aiValue       = item.ai_notes ?? 'Analysis pending...'; 

            // Unique IDs for toggling
            const notesId = `notes-${item.id}`;
            const aiId    = `ai-${item.id}`;
            const btnNotesId = `btn-notes-${item.id}`;
            const btnAiId    = `btn-ai-${item.id}`;

            // NEW: Base HTML with Toggle Tabs
            let baseHtml = `
                <div class="flex items-center justify-between gap-2">
                    <span class="font-none"><strong>Patient Name:</strong> ${item.name}</span>
                    <span class="text-xs px-2 py-1 rounded-full" 
                        style="background: rgba(0,0,0,0.4); border: 1px solid rgba(126, 92, 255, 0.35);">
                        CID: ${item.id}
                    </span>
                </div>
                <div class="text-sm">
                    <strong>Patient ID:</strong> ${item.p_ID ?? 'N/A'}
                </div>
                
                <div class="text-sm mb-2">
                    <strong>Staff Assigned (ID):</strong> ${item.assigned_staff_ID ?? 'None'}
                </div>

                <div class="mt-3">
                    <div class="flex gap-4 text-sm border-b border-white/10 mb-2">
                        <button type="button" id="${btnNotesId}" onclick="toggleView(${item.id}, 'notes')" 
                                class="text-purple-400 font-bold border-b-2 border-purple-400 pb-1">
                            Patient Notes
                        </button>
                        <button type="button" id="${btnAiId}" onclick="toggleView(${item.id}, 'ai')" 
                                class="text-slate-500 hover:text-purple-400 pb-1 transition-colors">
                            ✨ AI Analysis
                        </button>
                    </div>

                    <div class="bg-black/20 p-2 rounded min-h-[3rem]">
                        <div id="${notesId}" class="block text-sm">
                            ${notesValue !== '' ? notesValue : '<span class="italic text-slate-400">None</span>'}
                        </div>
                        <div id="${aiId}" class="hidden text-sm text-purple-200/90 italic">
                            <strong class="text-purple-400 not-italic">Gemini:</strong> ${aiValue}
                        </div>
                    </div>
                </div>
                `;

            let formHtml = '';

            if (mode === 'pending') {
                // Full edit mode: severity, wait, status, notes
                formHtml = `
                    <form class="mt-3 space-y-2 update-form">
                        <div class="text-sm space-y-1">
                            <label class="block"><strong>Edit Severity (1–5):</strong></label>
                            <select name="severity" class="w-full bg-black/30 border border-purple-500/40 rounded px-2 py-1">
                                <option value="">Select ESI Level</option>
                                <option value="1" ${severityValue == 1 ? 'selected' : ''}>1 - High</option>
                                <option value="2" ${severityValue == 2 ? 'selected' : ''}>2</option>
                                <option value="3" ${severityValue == 3 ? 'selected' : ''}>3 - Medium</option>
                                <option value="4" ${severityValue == 4 ? 'selected' : ''}>4</option>
                                <option value="5" ${severityValue == 5 ? 'selected' : ''}>5 - Low</option>
                            </select>
                        </div>

                        <div class="text-sm space-y-1">
                            <label class="block"><strong>Edit Wait Time (mins):</strong></label>
                            <input type="number" name="wait" min="0"
                                class="w-full px-2 py-1 rounded bg-black/30 border border-purple-500/40"
                                value="${waitValue !== '' ? waitValue : ''}">
                        </div>

                        <div class="text-sm space-y-1">
                            <label class="block text-slate-400 text-xs">Edit Notes (Optional):</label>
                            <textarea name="notes" rows="1"
                                class="w-full px-2 py-1 rounded bg-black/30 border border-purple-500/40 text-xs"
                            >${notesValue ?? ''}</textarea>
                        </div>

                        <div class="text-sm space-y-1">
                            <label class="block"><strong>Update Status:</strong></label>
                            <select name="status" class="w-full bg-black/30 border border-purple-500/40 rounded px-2 py-1">
                                <option value="Waiting">Waiting</option>
                                <option value="Approved">Approved</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <button type="button"
                                class="primary save-status-btn mt-2 w-full py-2 bg-purple-600 hover:bg-purple-500 rounded text-white font-bold transition-all"
                                data-id="${item.id}">
                            Save
                        </button>
                    </form>
                `;
            } else if (mode === 'approved') {
                // Approved: only status control (Approved <-> Completed), notes read-only
                let approvedInfoHtml = `
                    <div class="mt-2 text-sm">
                        <strong>Current Severity:</strong> ${item.severity ?? 'N/A'}
                    </div>
                    <div class="text-sm">
                        <strong>Current Wait Time:</strong> ${item.wait ?? 'N/A'} mins
                    </div>
                    <div class="text-sm">
                        <strong>Status:</strong> ${item.status}
                    </div>
                `;
                formHtml = `
                    ${approvedInfoHtml}
                    <form class="mt-3 space-y-2 update-form">
                        <div class="text-sm space-y-1">
                            <label class="block"><strong>Update Status:</strong></label>
                            <select name="status" class="w-full bg-black/30 border border-purple-500/40 rounded px-2 py-1">
                                <option value="Approved"  ${item.status === 'Approved'  ? 'selected' : ''}>Approved</option>
                                <option value="Completed" ${item.status === 'Completed' ? 'selected' : ''}>Completed</option>
                            </select>
                        </div>

                        <button type="button"
                                class="primary save-status-btn mt-2 w-full py-2 bg-purple-600 hover:bg-purple-500 rounded text-white font-bold transition-all"
                                data-id="${item.id}">
                            Save
                        </button>
                    </form>
                `;
            }

            div.innerHTML = baseHtml + formHtml;

            const form     = div.querySelector('form.update-form');
            const statusEl = form.querySelector('select[name="status"]');
            const button   = form.querySelector('.save-status-btn');

            let sevEl   = null;
            let waitEl  = null;
            let notesEl = null;

            if (mode === 'pending') {
                sevEl   = form.querySelector('select[name="severity"]');
                waitEl  = form.querySelector('input[name="wait"]');
                notesEl = form.querySelector('textarea[name="notes"]');

                if (statusEl && item.status) {
                    statusEl.value = item.status;
                }
            }

            if (button) {
                button.addEventListener('click', async () => {
                    const newStatus = statusEl.value;

                    if (mode === 'pending') {
                        const newSev   = sevEl.value;
                        const newWait  = waitEl.value;
                        const newNotes = notesEl.value;

                        // Enforce severity + wait when approving from Waiting
                        if (item.status === 'Waiting' && newStatus === 'Approved') {
                            if (!newSev || !newWait) {
                                alert('Please set severity and wait time before approving.');
                                return;
                            }
                        }

                        await updateStatus(item.id, newStatus, newSev, newWait, newNotes);
                    } else if (mode === 'approved') {
                        // Approved cards: only status changes, no notes edit
                        await updateStatus(item.id, newStatus);
                    }
                });
            }

            return div;
        }


        // ========== UPDATE STATUS ==========

        async function updateStatus(id, status, severity = '', wait = '', notes = undefined) {
            try {
                const body = new URLSearchParams();
                body.append('id', id);
                body.append('status', status);

                if (severity !== '' && severity !== null && severity !== undefined) {
                    body.append('severity', severity);
                }
                if (wait !== '' && wait !== null && wait !== undefined) {
                    body.append('wait', wait);
                }
                // Only append notes if provided (pending cards)
                if (notes !== undefined) {
                    body.append('notes', notes);
                }

                const res = await fetch('../../backend/staff/update_waitlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: body.toString()
                });

                const data = await res.json();

                if (!data.ok) {
                    console.error('Update failed:', data.error);
                    alert('Failed to update status.');
                    return;
                }

                await loadPending();
                const filter = document.getElementById('approved-filter').value;
                await loadApproved(filter);

            } catch (err) {
                console.error('Error updating status:', err);
                alert('Error updating status. Check console.');
            }
        }

        // ========== RENDER LIST ==========

        function renderList(containerId, data, mode = 'pending') {
            const container = document.getElementById(containerId);
            container.innerHTML = '';

            if (!data || data.length === 0) {
                const p = document.createElement('p');
                p.className = 'text-sm';
                p.style.color = 'var(--text-muted)';
                p.textContent = 'No records.';
                container.appendChild(p);
                return;
            }

            data.forEach(item => {
                container.appendChild(createCard(item, mode));
            });
        }

        // ========== APPROVED LIST ==========

        async function loadApproved(filter = 'all') {
            try {
                const res = await fetch(`../../backend/staff/get_approved.php?filter=${encodeURIComponent(filter)}`);
                
                const data = await res.json();
                console.log("error",data);
                renderList('approved-container', data, 'approved');
            } catch (err) {
                console.error('Error loading approved list:', err);
            }
        }

        document.getElementById('approved-filter').addEventListener('change', (e) => {
            loadApproved(e.target.value);
        });

        // ========== PENDING LIST ==========

        async function loadPending() {
            try {
                const res = await fetch('../../backend/staff/get_pending.php');
                const data = await res.json();
                renderList('pending-container', data.pending, 'pending');
            } catch (err) {
                console.error('Error loading pending list:', err);
            }
        }

        // ==========  BAR GRAPH LOAD ==========
        async function loadSeverityGraph(){
            try{
                const res = await fetch('../../backend/staff/get_severity_graph.php');
                const data = await res.json();

                const labels=data.map(r => `S${r.severity}`);
                const values = data.map(r => Number(r.total)); 

                const ctx = document.getElementById('severityGraph');
                
                new Chart(ctx,{
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label:'Total Severities',
                            data: values,
                            backgroundColor: 'rgba(126, 92, 255, 0.5)',
                            borderColor: 'rgba(126, 92, 255, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            hoverBackgroundColor: 'rgba(126, 92, 255, 0.8)'


                        }]
                    },
                    options:{
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                })
            } catch (err){
                console.error('Error loading chart',err);
            }
        }

        // Initial loads
        loadSeverityGraph();
        loadPending();
        loadApproved();
    </script>

</body>
</html>