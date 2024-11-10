@extends('backend.template.backend')

@section('content')

    <body class="box-layout container background-green">
        <!-- [ Main Content ] start -->
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                

                 <!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Department</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="#!">Hospital</a></li>
                                    <li class="breadcrumb-item"><a href="#!">Department</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->


                <!-- [ Main Content ] start -->
                <div class="row">
               

                   <!-- customar project  start -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-sm-6">

                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <button class="btn btn-success btn-sm btn-round has-ripple" data-bs-toggle="modal" data-bs-target="#modal-report"><i class="feather icon-plus"></i> Add Department</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="simpletable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th width="50%">Description</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                   1
                                                </td>
                                                <td>Anesthetics</td>
                                                <td>There are many variations of passages of Lorem Ipsum ...</td>
                                                <td>
                                                    <a href="#!" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i>Manage Facilities</a>
                                                    <a href="#!" class="btn btn-info btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit </a>
                                                    <a href="#!" class="btn btn-danger btn-sm"><i class="feather icon-trash-2"></i>&nbsp;Delete </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    2
                                                </td>
                                                <td>Cardiology</td>
                                                <td>There are many variations of passages of Lorem Ipsum ...</td>
                                                <td>
                                                    <a href="#!" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i>Manage Facilities</a>
                                                    <a href="#!" class="btn btn-info btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit </a>
                                                    <a href="#!" class="btn btn-danger btn-sm"><i class="feather icon-trash-2"></i>&nbsp;Delete </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    3
                                                </td>
                                                <td>Gastroenterology</td>
                                                <td>There are many variations of passages of Lorem Ipsum ...</td>
                                                <td>
                                                    <a href="#!" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i>Manage Facilities</a>
                                                    <a href="#!" class="btn btn-info btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit </a>
                                                    <a href="#!" class="btn btn-danger btn-sm"><i class="feather icon-trash-2"></i>&nbsp;Delete </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    4
                                                </td>
                                                <td>Anesthetics</td>
                                                <td>There are many variations of passages of Lorem Ipsum ...</td>
                                                <td>
                                                    <a href="#!" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i>Manage Facilities</a>
                                                    <a href="#!" class="btn btn-info btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit </a>
                                                    <a href="#!" class="btn btn-danger btn-sm"><i class="feather icon-trash-2"></i>&nbsp;Delete </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    5
                                                </td>
                                                <td>Gastroenterology</td>
                                                <td>There are many variations of passages of Lorem Ipsum ...</td>
                                                <td>
                                                    <a href="#!" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i>Manage Facilities</a>
                                                    <a href="#!" class="btn btn-info btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit </a>
                                                    <a href="#!" class="btn btn-danger btn-sm"><i class="feather icon-trash-2"></i>&nbsp;Delete </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- customar project  end -->


                </div>
                <!-- [ Main Content ] end -->


            </div>
        </div>
       

        <div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="floating-label" for="Name">Name</label>
                                        <input type="text" class="form-control" id="Name" placeholder="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group fill">
                                        <label class="floating-label" for="Icon">Icon</label>
                                        <input type="file" class="form-control" id="Icon" placeholder="sdf">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="floating-label" for="Description">Description</label>
                                        <textarea class="form-control" id="Description" rows="3"></textarea>
                                    </div>
                                    <button class="btn btn-primary">Submit</button>
                                    <button class="btn btn-danger">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </body>
@endsection
