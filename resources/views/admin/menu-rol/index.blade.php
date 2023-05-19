@extends("theme.$theme.layout")
@section("titulo")
 Role Access Control 
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/menu-rol/index.js")}}" type="text/javascript"></script>
@endsection

@section("contenido-header")
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="{{route('inicio')}}">Home</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Role Access Control</h1>
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
                    <h4 class="panel-title">Edit Role Access Control</h4>
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
                                <th>Menu</th>
                                @foreach ($rols as $id => $nombre)
                                <th class="text-center">{{$nombre}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menus as $key => $menu)
                            @if ($menu["menu_id"] != 0)
                                @break
                            @endif
                                <tr>
                                    <td class="font-weight-bold"><i class="fa fa-arrows-alt"></i> {{$menu["nombre"]}}</td>
                                    @foreach($rols as $id => $nombre)
                                        <td class="text-center">
                                            <input
                                            type="checkbox"
                                            class="menu_rol"
                                            name="menu_rol[]"
                                            data-menuid={{$menu[ "id"]}}
                                            value="{{$id}}" {{in_array($id, array_column($menusRols[$menu["id"]], "id"))? "checked" : ""}}>
                                        </td>
                                    @endforeach
                                </tr>
                                @foreach($menu["submenu"] as $key => $hijo)
                                    <tr>
                                        <td class="pl-40"><i class="fa fa-arrow-right"></i> {{ $hijo["nombre"] }}</td>
                                        @foreach($rols as $id => $nombre)
                                            <td class="text-center">
                                                <input
                                                type="checkbox"
                                                class="menu_rol"
                                                name="menu_rol[]"
                                                data-menuid={{$hijo[ "id"]}}
                                                value="{{$id}}" {{in_array($id, array_column($menusRols[$hijo["id"]], "id"))? "checked" : ""}}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    @foreach ($hijo["submenu"] as $key => $hijo2)
                                        <tr>
                                            <td class="pl-30"><i class="fa fa-arrow-right"></i> {{$hijo2["nombre"]}}</td>
                                            @foreach($rols as $id => $nombre)
                                                <td class="text-center">
                                                    <input
                                                    type="checkbox"
                                                    class="menu_rol"
                                                    name="menu_rol[]"
                                                    data-menuid={{$hijo2[ "id"]}}
                                                    value="{{$id}}" {{in_array($id, array_column($menusRols[$hijo2["id"]], "id"))? "checked" : ""}}>
                                                </td>
                                            @endforeach
                                        </tr>
                                        @foreach ($hijo2["submenu"] as $key => $hijo3)
                                            <tr>
                                                <td class="pl-40"><i class="fa fa-arrow-right"></i> {{$hijo3["nombre"]}}</td>
                                                @foreach($rols as $id => $nombre)
                                                <td class="text-center">
                                                    <input
                                                    type="checkbox"
                                                    class="menu_rol"
                                                    name="menu_rol[]"
                                                    data-menuid={{$hijo3[ "id"]}}
                                                    value="{{$id}}" {{in_array($id, array_column($menusRols[$hijo3["id"]], "id"))? "checked" : ""}}>
                                                </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
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
