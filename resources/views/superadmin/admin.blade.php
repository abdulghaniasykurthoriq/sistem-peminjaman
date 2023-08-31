@extends('layouts.theme')
@section('content')
    <div class="bg-primary pt-10 pb-21"></div>
    <div class="container-fluid mt-n22 px-6">
        <div class="col-lg-12 col-md-12 col-12 my-2">
            <!-- Page header -->
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0  text-white">Data Admin</h3>
                    </div>
                    <div>
                        <a href="{{ route('admin.create')}}" class="btn btn-white">Create New Admin</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-12">
            <!-- card  -->
            <div class="card">
                <!-- card header  -->
                {{-- <div class="card-header bg-white  py-4">
                <h4 class="mb-0">Active Projects</h4>
            </div> --}}
                <!-- table  -->
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Jabatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="align-middle">
                                Abdul Ghani Asykur Thoriq
                                </td>
                                <td class="align-middle">agat@gmail.com</td>
                                <td class="align-middle"><span class="badge
            bg-warning">KALAB</span></td>
                                <td class="align-middle">
                                    ACTION
                                </td>
                            </tr>
                            

                        </tbody>
                    </table>
                </div>
                <!-- card footer  -->
                <div class="card-footer bg-white text-center">
                    <a href="#" class="link-primary">View All Projects</a>

                </div>
            </div>

        </div>
    </div>
@endsection
