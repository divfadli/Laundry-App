@extends('layouts.app')
@section('title', 'Dashboard Operator')

@section('content')
    <h4>Selamat datang, Operator!</h4>
    @include('dashboard.partials.operator_stats', ['data' => $data])
    @include('dashboard.partials.recent_orders', ['data' => $data])
@endsection

@push('scripts')
    <script src="{{ asset('js/animateCounter.js') }}"></script>
@endpush
