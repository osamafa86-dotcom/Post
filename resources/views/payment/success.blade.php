@extends('components.layouts.main.maktoob.main')

@section('title')
    {{ config('system.site_name') ? config('system.site_name') : 'مكتوب ميديا' }} | Payment Successful
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fa-solid fa-circle-check text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h1 class="display-4 mb-4">Thank You!</h1>
                    <h2 class="text-success mb-4">Payment Successful</h2>
                    <p class="lead">Your contribution to Maktoob has been received successfully.</p>
                    <p class="mb-4">We greatly appreciate your support which helps us continue our independent journalism.</p>
                    
                    <div class="alert alert-info mt-4">
                        <p class="mb-0">You will receive a confirmation email shortly with the details of your contribution.</p>
                    </div>
                    
                    <div class="mt-5">
                        <a href="{{ route('main.maktoob.index') }}" class="btn btn-primary btn-lg">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
