@extends('layouts.app')

@section('title', 'Daftar User')

@section('content')
    <div class="container">
        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">+ Tambah User</a>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-info">{{ $user->level->level_name ?? '-' }}</span></td>
                        <td>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('users.destroy', $user->id) }}"
                                class="btn btn-danger btn-sm delete-user-btn @if ($user->id === Auth::id()) disabled @endif"
                                data-id="{{ $user->id }}" data-confirm-delete="true">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada user</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
