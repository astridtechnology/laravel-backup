<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="https://astridtechnology.com/wp-content/uploads/2024/02/a3-150x150.png" sizes="32x32" />
    <link rel="icon" href="https://astridtechnology.com/wp-content/uploads/2024/02/a3-300x300.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://astridtechnology.com/wp-content/uploads/2024/02/a3-300x300.png" />
    <title>Astrid Backup Plugin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <style>
        body {
            background-color: #feba00;
        }

        /* Loader styles */
        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            width: 50px;
            height: 50px;
            -webkit-animation: spin 1s linear infinite;
            /* Safari */
            animation: spin 1s linear infinite;
            display: none;
            /* Hidden by default */
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -25px;
            /* Half of width */
            margin-left: -25px;
            /* Half of height */
            z-index: 9999;
        }

        /* Add any other custom styles here */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="antialiased">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-lg-10">
                <h1 class="text-center mb-4">
                    <a href="https://astridtechnology.com/" target="_blank">
                        <img src="https://astridtechnology.com/wp-content/uploads/2024/02/logo-1-1536x438.png.webp" width="180">
                    </a>
                </h1>
                <div id="backupStatus"></div>
                <div class="mt-5 col-12 p-5" style="background-color: #f3f3f3;">
                    <div id="progressStatus" class="mt-3"></div>
                    <div class="col-md-12 my-2">
                        <button id="createBackupBtn" class="btn btn-success">Create New Backup</button>
                        <div id="createBackupLoader" class="loader"></div>
                    </div>
                    <div class="responsive-table-container">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Disk</th>
                                    <th>Reachable</th>
                                    <th>Healthy</th>
                                    <th>Newest Backup</th>
                                    <th>Used Storage</th>
                                    <th>Download</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($backups) > 0)
                                @foreach($backups as $backup)
                                <tr>
                                    <td><label class="d-lg-none d-inline"><strong>Name : </strong></label> {{ $backup['name'] }}</td>
                                    <td><label class="d-lg-none d-inline"><strong>Disk : </strong></label> {{ $backup['disk'] }}</td>
                                    <td><label class="d-lg-none d-inline"><strong>Reachable : </strong></label> {{ $backup['reachable'] }}</td>
                                    <td><label class="d-lg-none d-inline"><strong>Healthy : </strong></label> {{ $backup['healthy'] }}</td>
                                    <td><label class="d-lg-none d-inline"><strong>Newest Backup : </strong></label> {{ $backup['newest'] }}</td>
                                    <td><label class="d-lg-none d-inline"><strong>Used Storage : </strong></label> {{ $backup['fileSize'] }}</td>
                                    <td>
                                        <a href="{{ route('download.file', ['filename' => $backup['name']]) }}" class="btn btn-success">Download</a>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger deleteBtn" data-filename="{{ $backup['name'] }}">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="8"><span class="text-danger">No backup found!</span></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Add this CSS for mobile responsiveness */
        @media only screen and (max-width: 991px) {
            .responsive-table-container table,
            .responsive-table-container thead,
            .responsive-table-container tbody,
            .responsive-table-container th,
            .responsive-table-container td,
            .responsive-table-container tr,
            .responsive-table-container tfoot {
                display: block;
            }

            .responsive-table-container tfoot {
                border: 1px solid #ebedf2 !important;
            }

            .responsive-table-container thead {
                display: none;
            }

            .responsive-table-container .table tbody tr td {
                white-space: normal;
            }

            .responsive-table-container tr {
                margin: 0 0 1rem 0;
                display: flex;
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleLoader(loaderId, show) {
            const loader = document.getElementById(loaderId);
            if (show) {
                loader.style.display = 'block';
            } else {
                loader.style.display = 'none';
            }
        }

        function updateProgressStatus(progress) {
            const progressStatus = document.getElementById('progressStatus');
            progressStatus.innerHTML = `<div class="progress">
                                            <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: ${progress}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100">${progress}% Complete</div>
                                        </div>`;
        }

        // Function to handle backup creation
        document.getElementById('createBackupBtn').addEventListener('click', function() {
            this.disabled = true;
            toggleLoader('createBackupLoader', false);

            // Send AJAX request to trigger backup creation
            fetch('{{ route('create - backup ') }}', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    // Refresh the page after backup creation
                    location.reload();
                })
                .catch(error => {
                    console.error('There was an error!', error);
                })
                .finally(() => {
                    // Re-enable the button and hide loader
                    this.disabled = false;
                    toggleLoader('createBackupLoader', false);
                });

            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                if (progress > 100) {
                    clearInterval(interval);
                }
                updateProgressStatus(progress);
            }, 1000);
        });

        // Function to handle directory deletion
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function() {
                this.disabled = true;
                toggleLoader('createBackupLoader', true);
                const filename = this.getAttribute('data-filename');
                if (confirm(`Are you sure you want to delete ${filename}?`)) {
                    // Send AJAX request to delete the directory
                    fetch('{{ route('delete.file ') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    filename: filename
                                })
                            })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('File deleted successfully');
                                // Refresh the page after deletion
                                location.reload();
                            } else {
                                alert('Failed to delete the file');
                            }
                        })
                        .catch(error => {
                            console.error('There was an error!', error);
                        }).finally(() => {
                            // Re-enable the button and hide loader
                            this.disabled = false;
                            toggleLoader('createBackupLoader', false);
                        });
                }
            });
        });
    </script>
</body>
</html>