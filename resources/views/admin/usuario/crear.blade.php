@extends("theme.$theme.layout")
@section('titulo')
    User
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/usuario/crear.js")}}" type="text/javascript"></script>
@endsection

@section("contenido-header")
     <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="{{route('usuario')}}" class="btn btn-outline-info btn-sm"><i class="fa fa-fw fa-reply-all"></i> Back to list</a></li>
    </ol>
    <!-- end breadcrumb -->
    
    <!-- begin page-header -->
    <h1 class="page-header">User</h1>
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
                            <h4 class="panel-title">Create user</h4>
                            <div class="panel-heading-btn">
                                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            </div>
                        </div>
                        <!-- end panel-heading -->
                        <!-- begin alert -->
                        @include('includes.form-error')
                        @include('includes.mensaje')
                        <!-- end alert -->
                        <!-- begin panel-body -->
                        <div class="panel-body">
                            
                                <form action="{{route('guardar_usuario')}}" id="form-general" class="form-horizontal form--label-right" method="POST" autocomplete="off">
                                @csrf
                                <div class="card-body">
                                    @include('admin.usuario.form')
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-5"></div>
                                        <div class="col-lg-2">
                                            @include('includes.boton-form-crear')
                                        </div>
                                        <div class="col-lg-5"></div>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <!-- end panel-body -->
                    </div>
                    <!-- end panel -->
                </div>
                <!-- end col-10 -->
            </div>
            <!-- end row -->





@endsection
