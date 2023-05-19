@extends("theme.$theme.layout")
@section("titulo")
Menu
@endsection

@section("styles")
<link href="{{asset("assets/js/jquery-nestable/jquery.nestable.css")}}" rel="stylesheet" type="text/css" />
@endsection

@section("scriptsPlugins")
<script src="{{asset("assets/js/jquery-nestable/jquery.nestable.js")}}" type="text/javascript"></script>
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/menu/index.js")}}" type="text/javascript"></script>
@endsection


@section("contenido-header")
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="{{route('inicio')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('crear_menu')}}" class="btn btn-outline-info btn-sm"><i class="fa fa-fw fa-plus-circle"></i> New record</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Menu</h1>
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
                            <h4 class="panel-title">Edit menu layout</h4>
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

                            <div class="dd" id="nestable">
                                <ol class="dd-list">
                                    @foreach ($menus as $key => $item)
                                        @if ($item["menu_id"] != 0)
                                            @break
                                        @endif
                                        @include("admin.menu.menu-item",["item" => $item])
                                    @endforeach
                                </ol>
                            </div>
                            
                        </div>
                        <!-- end panel-body -->
                    </div>
                    <!-- end panel -->
                </div>
                <!-- end col-10 -->
            </div>
            <!-- end row -->

@endsection
