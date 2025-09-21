@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
    <h4>Selamat datang, Admin!</h4>
    {{-- Tampilkan statistik, recent orders, dll (sama seperti sebelumnya) --}}
    @include('dashboard.partials.stats', ['data' => $data])
    @include('dashboard.partials.recent_orders', ['data' => $data])
@endsection

@push('scripts')
    <script src="{{ asset('js/animateCounter.js') }}"></script>
@endpush
