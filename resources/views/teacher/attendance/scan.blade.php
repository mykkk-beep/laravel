@extends('layouts.app')

@section('pageTitle', 'QR Scanner')
@section('pageSubtitle', 'Scan student QR codes and mark attendance instantly.')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-slate-900">Scan Student QR Code</h2>
            <p class="mt-1 text-sm text-slate-500">Select a class, open the camera, and scan a student QR code to mark attendance.</p>
        </div>

        <div class="mb-6">
            <label class="form-label">Class</label>
            <select id="class_room_id" class="form-input">
                <option value="">Select a class</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div>
                <div class="overflow-hidden rounded-[24px] border border-slate-200 bg-slate-50 p-2">
                    <div id="reader" style="width:100%; min-height:320px;"></div>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                    <div>
                        <label class="form-label">Camera</label>
                        <select id="camera-selection" class="form-input">
                            <option value="">Loading cameras...</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button id="start-camera" class="btn btn-primary">Start Camera</button>
                        <button id="stop-camera" class="btn btn-outline-danger" style="display:none;">Stop Camera</button>
                    </div>
                </div>

                <div class="mt-5 rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                    <label class="form-label"><strong>Or type a student ID / QR value</strong></label>
                    <form id="manual-input-form" class="flex flex-col gap-3 sm:flex-row">
                        <input type="text" id="manual_qr_code" class="form-input" placeholder="Enter Student ID or QR code" autocomplete="off">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>

                <div class="mt-5 rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <h4 class="font-semibold text-slate-900">Quick Attendance</h4>
                        <p class="text-sm text-slate-500">Click a student below to mark attendance instantly.</p>
                    </div>
                    <div class="mb-3 flex flex-wrap gap-2">
                        <button id="mark-all-present" type="button" class="btn btn-success" style="display:none;">Mark all present</button>
                        <button id="finalize-quick-attendance" type="button" class="btn btn-primary" style="display:none;">Mark the rest absent</button>
                    </div>
                    <div id="student-selection-list" class="space-y-2"></div>
                </div>
            </div>

            <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Scan Status</h3>
                    <div id="scan-status" class="mt-3 rounded-2xl border border-indigo-200 bg-indigo-50 p-3 text-sm text-indigo-700">Select a class and start the camera to begin scanning.</div>
                </div>
                <div id="attendance-summary" class="mb-4 grid gap-3 sm:grid-cols-2"></div>
                <div id="latest-result" class="mb-4 rounded-2xl border border-slate-200 bg-white p-3 text-sm text-slate-700 hidden"></div>
                <div>
                    <h4 class="mb-3 font-semibold text-slate-900">Recent Scans</h4>
                    <div id="scans-list" class="max-h-80 space-y-2 overflow-y-auto">
                        <p class="rounded-2xl border border-dashed border-slate-200 bg-white p-3 text-center text-sm text-slate-500">No scans yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let reader = null;
    const startBtn = document.getElementById('start-camera');
    const stopBtn = document.getElementById('stop-camera');
    const cameraSelect = document.getElementById('camera-selection');
    const statusDiv = document.getElementById('scan-status');
    const attendanceSummary = document.getElementById('attendance-summary');
    const latestResult = document.getElementById('latest-result');
    const scansList = document.getElementById('scans-list');
    const classSelect = document.getElementById('class_room_id');
    const manualForm = document.getElementById('manual-input-form');
    const manualInput = document.getElementById('manual_qr_code');
    const studentSelectionList = document.getElementById('student-selection-list');
    const markAllPresentBtn = document.getElementById('mark-all-present');
    const finalizeQuickAttendanceBtn = document.getElementById('finalize-quick-attendance');
    const initializeUrl = '{{ route('teacher.attendance.initialize') }}';
    const recordUrl = '{{ route('teacher.attendance.record') }}';
    const markAllPresentUrl = '{{ route('teacher.attendance.mark-all-present') }}';
    const finalizeQuickUrl = '{{ route('teacher.attendance.finalize-quick') }}';
    const token = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value || '';

    let cameraRunning = false;
    let classInitialized = false;
    let selectedCameraId = null;
    let isProcessing = false;
    let scannerReady = false;
    const classStudents = @json($classStudents ?? []);

    function setStatus(message, type = 'info') {
        statusDiv.className = `alert alert-${type}`;
        statusDiv.innerHTML = message;
    }

    function showLatestResult(message, type = 'secondary') {
        latestResult.className = `alert alert-${type}`;
        latestResult.innerHTML = message;
        latestResult.classList.remove('d-none');
    }

    function updateSummary(summary) {
        const total = summary?.total ?? 0;
        const present = summary?.present ?? 0;
        const absent = summary?.absent ?? 0;

        attendanceSummary.innerHTML = `
            <div class="col-6">
                <div class="border rounded p-2 bg-white text-center">
                    <small class="text-muted d-block">Present</small>
                    <strong>${present}</strong>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-2 bg-white text-center">
                    <small class="text-muted d-block">Absent</small>
                    <strong>${absent}</strong>
                </div>
            </div>
            <div class="col-12">
                <div class="border rounded p-2 bg-white text-center">
                    <small class="text-muted d-block">Total Students</small>
                    <strong>${total}</strong>
                </div>
            </div>`;
    }

    function setQuickAttendanceButtonsVisibility(classId) {
        const hasClass = Boolean(classId);
        markAllPresentBtn.style.display = hasClass ? 'inline-block' : 'none';
        finalizeQuickAttendanceBtn.style.display = hasClass ? 'inline-block' : 'none';
    }

    function renderStudentSelection(classId) {
        if (!classId) {
            studentSelectionList.innerHTML = '<p class="rounded-2xl border border-dashed border-slate-200 bg-white p-3 text-center text-sm text-slate-500">Select a class to see students.</p>';
            setQuickAttendanceButtonsVisibility(classId);
            return;
        }

        const students = (classStudents[classId] || []).filter(Boolean);

        if (!students.length) {
            studentSelectionList.innerHTML = '<p class="rounded-2xl border border-dashed border-slate-200 bg-white p-3 text-center text-sm text-slate-500">No students found for this class yet.</p>';
            setQuickAttendanceButtonsVisibility(classId);
            return;
        }

        studentSelectionList.innerHTML = '';
        const fragment = document.createDocumentFragment();

        students.forEach((student) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-white p-3 text-left transition hover:border-indigo-500 hover:bg-indigo-50';
            button.innerHTML = `
                <span class="font-medium text-slate-900">${student.name}</span>
                <span class="text-xs text-slate-500">${student.student_id || 'No ID'}</span>
            `;
            button.addEventListener('click', () => {
                if (!student.student_id) {
                    setStatus('This student does not have a student ID yet.', 'warning');
                    return;
                }
                processQRCode(student.student_id);
            });
            fragment.appendChild(button);
        });

        studentSelectionList.appendChild(fragment);
        setQuickAttendanceButtonsVisibility(classId);
    }

    function addToScansList(qrCode, label = 'Scan recorded') {
        const now = new Date().toLocaleTimeString();
        const scanItem = document.createElement('div');
        scanItem.className = 'alert alert-success mb-2';
        scanItem.innerHTML = `<small><strong>${now}</strong><br>${label}<br><code>${qrCode.substring(0, 24)}</code></small>`;

        if (scansList.querySelector('p')) {
            scansList.innerHTML = '';
        }

        scansList.insertBefore(scanItem, scansList.firstChild);

        const items = scansList.querySelectorAll('.alert');
        items.forEach((item, index) => {
            if (index >= 10) {
                item.remove();
            }
        });
    }

    function loadScannerLibrary() {
        return new Promise((resolve, reject) => {
            if (typeof window.Html5Qrcode !== 'undefined') {
                resolve();
                return;
            }

            if (!window.isSecureContext) {
                reject(new Error('Camera access requires this page to be served over https:// or localhost.'));
                return;
            }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                reject(new Error('Your browser does not support camera access.'));
                return;
            }

            const scriptUrls = [
                'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/minified/html5-qrcode.min.js',
                'https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js'
            ];

            let index = 0;

            const tryNext = () => {
                if (index >= scriptUrls.length) {
                    reject(new Error('The QR scanner library could not be loaded from the configured CDNs.'));
                    return;
                }

                const script = document.createElement('script');
                script.src = scriptUrls[index++];
                script.async = true;
                script.onload = () => {
                    if (typeof window.Html5Qrcode !== 'undefined') {
                        resolve();
                    } else {
                        tryNext();
                    }
                };
                script.onerror = () => tryNext();
                document.head.appendChild(script);
            };

            tryNext();
        });
    }

    async function ensureScannerReady() {
        if (scannerReady) {
            return;
        }

        try {
            await loadScannerLibrary();
            reader = new window.Html5Qrcode('reader');
            scannerReady = true;
        } catch (error) {
            reader = null;
            scannerReady = false;
            throw error;
        }
    }

    async function populateCameras() {
        try {
            await ensureScannerReady();
        } catch (error) {
            cameraSelect.innerHTML = '<option value="">Scanner unavailable</option>';
            startBtn.style.display = 'none';
            stopBtn.style.display = 'none';
            setStatus(`The QR scanner library could not be loaded. ${error.message} If you are testing locally, use http://localhost and allow camera permission.`, 'warning');
            return;
        }

        try {
            const cameras = await window.Html5Qrcode.getCameras();
            cameraSelect.innerHTML = '';

            if (!cameras || cameras.length === 0) {
                cameraSelect.innerHTML = '<option value="">No cameras found</option>';
                startBtn.style.display = 'none';
                setStatus('No camera found on this device.', 'danger');
                return;
            }

            cameras.forEach((camera, index) => {
                const option = document.createElement('option');
                option.value = camera.id;
                option.textContent = camera.label || `Camera ${index + 1}`;
                cameraSelect.appendChild(option);
            });

            const ua = navigator.userAgent || '';
            const isMobileDevice = /Android|iPhone|iPad|iPod/i.test(ua);

            let preferred = cameras.find(c => /back|rear|environment|wide/i.test(c.label)) || cameras[0];
            selectedCameraId = preferred.id;
            cameraSelect.value = selectedCameraId;

            if (isMobileDevice) {
                try {
                    await startCamera({ facingMode: 'environment' });
                    return;
                } catch (err) {
                    // fallthrough to start with deviceId
                }
            }

            await startCamera(selectedCameraId);
        } catch (error) {
            cameraSelect.innerHTML = '<option value="">Unable to list cameras</option>';
            startBtn.style.display = 'inline-block';
            stopBtn.style.display = 'none';
            setStatus(`Unable to access camera: ${error.message}`, 'danger');
        }
    }

    async function initializeAttendance(classId) {
        if (!classId || classInitialized) {
            return;
        }

        setStatus('Initializing attendance for the selected class...', 'warning');

        const response = await fetch(initializeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ class_room_id: classId }),
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(payload.message || 'Failed to initialize attendance');
        }

        classInitialized = true;
        if (payload.summary) {
            updateSummary(payload.summary);
        }
        setStatus('Attendance initialized. Scan student QR codes now.', 'success');
    }

    async function processQRCode(qrCodeValue) {
        if (isProcessing) {
            return;
        }

        const classId = classSelect.value;

        if (!classId) {
            setStatus('Please select a class first.', 'warning');
            return;
        }

        try {
            isProcessing = true;
            setStatus('Processing scan...', 'warning');

            await initializeAttendance(classId);

            const response = await fetch(recordUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    class_room_id: classId,
                    qr_code: qrCodeValue,
                }),
            });

            const payload = await response.json().catch(() => ({}));

            if (payload.summary) {
                updateSummary(payload.summary);
            }

            if (!response.ok) {
                throw new Error(payload.message || 'The scan could not be processed.');
            }

            const studentName = payload.student?.name || 'Student';
            showLatestResult(`Attendance marked as present for ${studentName}.`, 'success');
            addToScansList(qrCodeValue, `Marked present: ${studentName}`);
            manualInput.value = '';
            setStatus(`Attendance recorded for ${studentName}.`, 'success');
        } catch (error) {
            showLatestResult(error.message, 'danger');
            setStatus(`Scan failed: ${error.message}`, 'danger');
        } finally {
            isProcessing = false;
        }
    }

    async function markAllPresent() {
        const classId = classSelect.value;

        if (!classId) {
            setStatus('Please select a class first.', 'warning');
            return;
        }

        try {
            setStatus('Marking all students present...', 'warning');
            const response = await fetch(markAllPresentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ class_room_id: classId }),
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Unable to mark all students present.');
            }

            if (payload.summary) {
                updateSummary(payload.summary);
            }

            showLatestResult(payload.message || 'All students marked present.', 'success');
            setStatus('All students marked present for the selected class.', 'success');
        } catch (error) {
            showLatestResult(error.message, 'danger');
            setStatus(`Quick attendance failed: ${error.message}`, 'danger');
        }
    }

    async function finalizeQuickAttendance() {
        const classId = classSelect.value;

        if (!classId) {
            setStatus('Please select a class first.', 'warning');
            return;
        }

        try {
            setStatus('Finalizing quick attendance...', 'warning');
            const response = await fetch(finalizeQuickUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ class_room_id: classId }),
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Unable to finalize quick attendance.');
            }

            if (payload.summary) {
                updateSummary(payload.summary);
            }

            showLatestResult(payload.message || 'Quick attendance finalized.', 'success');
            setStatus('Quick attendance finalized. Remaining students are marked absent.', 'success');
        } catch (error) {
            showLatestResult(error.message, 'danger');
            setStatus(`Quick attendance failed: ${error.message}`, 'danger');
        }
    }

    function onScanSuccess(decodedText) {
        processQRCode(decodedText);
    }

    const config = { fps: 10, qrbox: 250 };

    async function startCamera(cameraId) {
        if (!cameraId) {
            setStatus('Please select a camera.', 'warning');
            return;
        }

        try {
            await ensureScannerReady();
        } catch (error) {
            setStatus(`The QR scanner library could not be loaded. ${error.message}`, 'warning');
            return;
        }

        try {
        await reader.stop().catch(() => {});
        await reader.start(cameraId, config, onScanSuccess, () => {});
        cameraRunning = true;
        selectedCameraId = (typeof cameraId === 'string') ? cameraId : selectedCameraId;
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-block';
        setStatus('Camera started. Ready to scan.', 'info');
        } catch (error) {
        cameraRunning = false;
        const message = error?.message || error?.name || String(error) || 'Unknown error';
        console.error('Camera start failed:', error);
        setStatus(`Unable to start camera: ${message}`, 'danger');
        startBtn.style.display = 'inline-block';
        stopBtn.style.display = 'none';
        }
    }

    async function stopCamera() {
        try {
            if (reader) {
                await reader.stop();
            }
            cameraRunning = false;
            startBtn.style.display = 'inline-block';
            stopBtn.style.display = 'none';
            setStatus('Camera stopped.', 'warning');
        } catch (error) {
            console.error('Error stopping camera:', error);
        }
    }

    cameraSelect.addEventListener('change', (event) => {
        selectedCameraId = event.target.value;
        if (cameraRunning) {
            stopCamera().then(() => startCamera(selectedCameraId));
        }
    });

    startBtn.addEventListener('click', () => startCamera(selectedCameraId));
    stopBtn.addEventListener('click', () => stopCamera());
    markAllPresentBtn.addEventListener('click', markAllPresent);
    finalizeQuickAttendanceBtn.addEventListener('click', finalizeQuickAttendance);

    classSelect.addEventListener('change', () => {
        classInitialized = false;
        attendanceSummary.innerHTML = '';
        renderStudentSelection(classSelect.value);
        setStatus('Class changed. Select the class and scan again.', 'info');
    });

    manualForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const qrValue = manualInput.value.trim();

        if (!qrValue) {
            setStatus('Please enter a student ID or QR value.', 'warning');
            return;
        }

        processQRCode(qrValue);
    });

    window.addEventListener('load', () => {
        renderStudentSelection(classSelect.value);
        populateCameras();
    });

    window.addEventListener('beforeunload', () => {
        if (cameraRunning && reader) {
            reader.stop().catch(() => {});
        }
    });
</script>
@endpush
