@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Admin Dashboard</div>
            <div class="card-body">
                <h5>User List</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            <th>Hobbies</th>
                            <th>Profile Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                @foreach($user->hobbies as $hobby)
                                    {{ $hobby->hobby }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td>
                                {{-- php artisan storage:link --}}
                                @if($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" width="50">
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <a href="{{ route('admin.users.csv') }}" class="btn btn-success">Download CSV</a>
                <a href="{{ route('admin.export.pdf') }}" class="btn btn-success">Download Pdf</a>

                <h5 class="mt-4">Upload CSV</h5>
                <form action="{{ route('admin.users.upload_csv') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <input type="file" name="file" class="form-control-file">
                        @error('file')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
