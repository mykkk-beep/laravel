@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4 py-md-6">
    <div class="row">
        <div class="col-12 col-sm-10 col-md-6 col-lg-5 col-xl-4 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h3 h2-md mb-4 text-center">Student Login</h2>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.login.submit') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="student_id" class="form-label fw-semibold">Student ID</label>
                            <input 
                                id="student_id" 
                                name="student_id" 
                                class="form-control form-control-lg" 
                                placeholder="Enter your student ID"
                                required 
                                autofocus />
                            @error('student_id')
                                <div class="small text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>

                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="text-muted small mb-0">Having trouble? Contact your teacher or administration for assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }
    
    @media (max-width: 576px) {
        .h2-md {
            font-size: 1.5rem !important;
        }
        .card-body {
            padding: 1.5rem !important;
        }
    }
    
    @media (min-width: 577px) {
        .h2-md {
            font-size: 2rem !important;
        }
    }
</style>
@endsection
