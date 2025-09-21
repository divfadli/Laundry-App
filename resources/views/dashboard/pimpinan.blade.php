@extends('layouts.app')
@section('title', 'Dashboard Pimpinan')

@section('content')
    <h4>Selamat datang, Pimpinan!</h4>
    @include('dashboard.partials.pimpinan_stats', ['data' => $data])

    {{-- Ringkasan laporan 7 hari --}}
    <div class="card mt-4">
        <div class="card-header">Ringkasan 7 Hari Terakhir</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Total Orders</th>
                        <th>Selesai</th>
                        <th>Pending</th>
                        <th>Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['daily_summary'] ?? [] as $summary)
                        <tr>
                            <td>{{ $summary['date'] }}</td>
                            <td>{{ number_format($summary['total_orders'], 0, ',', '.') }}</td>
                            <td>{{ number_format($summary['completed_orders'], 0, ',', '.') }}</td>
                            <td>{{ number_format($summary['pending_orders'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/animateCounter.js') }}"></script>
@endpush
