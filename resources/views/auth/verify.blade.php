@extends('layouts.app')
@section('content')
<div class="container" style="max-width:760px;">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="fw-bold mb-2">Verify Your Email Address</h3>
            <p class="text-muted mb-4">Before proceeding, please check your email for a verification link.</p>
            @if (session('resent'))
                <div class="alert alert-success border-0">A fresh verification link has been sent to your email address.</div>
            @endif
            <p class="mb-3">If you did not receive the email, request another link below.</p>
            <form method="POST" action="{{ route('verification.resend') }}">@csrf<button type="submit" class="btn btn-primary">Resend Verification Email</button></form>
        </div>
    </div>
</div>
@endsection
