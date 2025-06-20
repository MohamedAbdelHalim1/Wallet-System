<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pending Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-light py-4">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/admin/transactions') }}">Wallet Admin</a>

            <div class="d-flex ms-auto">
                <a href="#" id="generateCodeBtn" class="btn btn-outline-light me-2">
                    <i class="fas fa-plus"></i> Generate Code
                </a>

                <a href="{{ url('/admin/notifications') }}" class="btn btn-outline-light position-relative me-2">
                    <i class="fas fa-bell"></i>
                    @if ($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>

                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">

        <!-- Show session messages -->
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div id="referralSection" class="mb-4">
            <h5>Your Generated Referral Codes</h5>
            <ul id="referralList" class="list-group">

            </ul>
        </div>

        <!-- Withdraw Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Request Withdraw</h5>
                <form action="{{ url('/admin/transactions/withdraw/request') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <input type="number" name="amount" step="0.01" min="1" class="form-control"
                            placeholder="Enter amount" required>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Request Withdraw</button>
                    </div>
                </form>
            </div>
        </div>

        <h2 class="mb-4">Pending Top-up & Withdraw Requests</h2>

        @if ($pendingTopups->isEmpty())
            <div class="alert alert-info">No pending top-ups or withdraw requests at the moment.</div>
        @else
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Requested By</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendingTopups as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>
                                @if ($transaction->type === 'topup')
                                    {{ $transaction->user->name ?? 'N/A' }}
                                @else
                                    {{ $transaction->admin->name ?? 'Admin' }}
                                @endif
                            </td>
                            <td>{{ $transaction->amount }}</td>
                            <td>{{ ucfirst($transaction->type) }}</td>
                            <td>{{ $transaction->created_at->diffForHumans() }}</td>
                            <td>
                                <!-- Approve -->
                                <form
                                    action="{{ $transaction->type === 'topup'
                                        ? url('/admin/transactions/' . $transaction->id . '/approve')
                                        : url('/admin/transactions/withdraw/' . $transaction->id . '/approve') }}"
                                    method="POST" style="display:inline-block;">
                                    @csrf
                                    <button class="btn btn-success btn-sm">✔ Approve</button>
                                </form>

                                <!-- Reject -->
                                <form
                                    action="{{ $transaction->type === 'topup'
                                        ? url('/admin/transactions/' . $transaction->id . '/reject')
                                        : url('/admin/transactions/withdraw/' . $transaction->id . '/reject') }}"
                                    method="POST" style="display:inline-block;">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">✖ Reject</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const generateBtn = document.getElementById("generateCodeBtn");
            const referralList = document.getElementById("referralList");

            generateBtn.addEventListener("click", function(e) {
                e.preventDefault();
                fetch("/admin/referrals/generate", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .getAttribute("content")
                        },
                        body: JSON.stringify({})
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.code) {
                            const li = document.createElement("li");
                            li.classList.add("list-group-item");
                            li.innerHTML =
                                `<strong>${data.code.code}</strong> - ${data.code.used_by ? "Used by user ID: " + data.code.used_by : "Not used"}`;
                            referralList.prepend(li);
                        } else if (data.error) {
                            alert(data.error);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Something went wrong");
                    });
            });

            // Fetch existing codes on page load
            fetch("/admin/referrals/list")
                .then(res => res.json())
                .then(data => {
                    if (data.codes) {
                        data.codes.forEach(code => {
                            const li = document.createElement("li");
                            li.classList.add("list-group-item");
                            li.innerHTML =
                                `<strong>${code.code}</strong> - ${code.used_by ? "Used by " + code.used_by : "Not used"}`;
                            referralList.appendChild(li);
                        });

                    }
                });
        });
    </script>
</body>

</html>
