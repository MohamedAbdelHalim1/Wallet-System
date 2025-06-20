<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>


<body class="bg-light py-4">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/admin/transactions') }}">Wallet Admin</a>

            <div class="d-flex ms-auto">
                <!-- Notification icon -->
                <a href="{{ url('/admin/notifications') }}" class="btn btn-outline-light position-relative me-2">
                    <i class="fas fa-bell"></i>
                    @if ($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>



                <!-- Logout button -->
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Notifications</h2>

        @if ($notifications->isEmpty())
            <div class="alert alert-info">No notifications yet.</div>
        @else
            <ul class="list-group">
                @foreach ($notifications as $note)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ ucfirst($note->type) }} - Transaction #{{ $note->transaction_id }}
                        <span class="badge bg-{{ $note->read ? 'secondary' : 'primary' }}">
                            {{ $note->created_at->diffForHumans() }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="mt-4">
            <a href="{{ url('/admin/transactions') }}" class="btn btn-dark">Back to Transactions</a>
        </div>
    </div>

</body>

</html>
