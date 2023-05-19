@extends("theme.$theme.layout")
@section("titulo")
Rol Permissions
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/permiso-rol/index.js")}}" type="text/javascript"></script>
@endsection


@section("contenido-header")
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="{{route('inicio')}}">Home</a></li>
    </ol>
    <!-- end breadcrumb -->
    
    <!-- begin page-header -->
    <h1 class="page-header">Rol Permissions</h1>
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
                            <h4 class="panel-title">Edit Rol Permissions</h4>
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

                               @csrf
                                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                                    <thead>
                                        <tr>
                                            <th>Permissions</th>
                                            @foreach ($rols as $id => $nombre)
                                            <th class="text-center">{{$nombre}}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permisos as $key => $permiso)
                                            <tr>
                                                <td class="font-weight-bold">{{$permiso["nombre"]}}</td>
                                                @foreach($rols as $id => $nombre)
                                                    <td class="text-center">
                                                        <input
                                                        type="checkbox"
                                                        class="permiso_rol"
                                                        name="permiso_rol[]"
                                                        data-permisoid={{$permiso[ "id"]}}
                                                        value="{{$id}}" {{in_array($id, array_column($permisosRols[$permiso["id"]], "id"))? "checked" : ""}}>
                                                    </td>
                                                @endforeach
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
