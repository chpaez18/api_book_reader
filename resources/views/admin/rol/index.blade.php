@extends("theme.$theme.layout")
@section('titulo')
Roles
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/pages/scripts/admin/table-manage.js")}}"></script>
@endsection

@section("contenido-header")
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="{{route('inicio')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('crear_rol')}}" class="btn btn-outline-info btn-sm"><i class="fa fa-fw fa-plus-circle"></i> New record</a></li>
    </ol>
    <!-- end breadcrumb -->
    
    <!-- begin page-header -->
    <h1 class="page-header">Roles</h1>
    <!-- end page-header -->
@endsection

@section('contenido')

  <!-- begin row -->
            <div class="row">
                <!-- begin col-10 -->
                <div class="col-xl-12">
                    <!-- begin panel -->
                    <div class="panel panel-inverse">
                        <!-- begin panel-heading -->
                        <div class="panel-heading">
                            <h4 class="panel-title">Roles List</h4>
                            <div class="panel-heading-btn">
                                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            </div>
                        </div>
                        <!-- end panel-heading -->
                        <!-- begin alert -->
                        @include('includes.mensaje')
                        @csrf
                        <!-- end alert -->
                        <!-- begin panel-body -->
                        <div class="panel-body">
                            <table id="data-table-buttons" class="table table-striped table-bordered table-td-valign-middle" >
                                <thead>
                                    <tr>
                                        <th width="1%">Id</th>
                                        <th class="text-nowrap">Name</th>
                                        <th width="10%" >Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($datas as $data)

                                      <tr class="odd gradeX">
                                        <td width="1%" class="f-w-600 text-inverse">{{$data->id}}</td>
                                        <td>{{$data->nombre}}</td>
                                        <td>
                                            <a href="{{route('editar_rol', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Edit this record">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{route('eliminar_rol', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
                                                @csrf @method("delete")
                                                <button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Delete this record">
                                                    <i class="fa fa-times-circle text-danger"></i>
                                                </button>
                                            </form>
                                        </td>
                                       </tr>

                                    @endforeach
                                    
                                </tbody>
                            </table>
                        </div>
                        <!-- end panel-body -->
                    </div>
                    <!-- end panel -->
                </div>
                <!-- end col-10 -->
            </div>
            <!-- end row -->


@endsection


@section('scriptsPlugins')
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
    <script src="{{asset("assets/$theme/plugins/datatables.net/js/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-responsive/js/dataTables.responsive.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-buttons/js/dataTables.buttons.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-buttons/js/buttons.colVis.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-buttons/js/buttons.flash.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-buttons/js/buttons.html5.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/datatables.net-buttons/js/buttons.print.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/pdfmake/build/pdfmake.min.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/pdfmake/build/vfs_fonts.js")}}"></script>
    <script src="{{asset("assets/$theme/plugins/jszip/dist/jszip.min.js")}}"></script>
    <!-- ================== END PAGE LEVEL JS ================== -->
 @endsection
