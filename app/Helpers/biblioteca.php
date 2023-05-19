<?php

use App\Models\Admin\Permiso;
use App\Models\Machine;
use App\Models\Shape;

if (!function_exists('getMenuActivo')) 
{
    function getMenuActivo($ruta)
    {
        if (request()->is($ruta) || request()->is($ruta . '/*')) {
            return 'active';
        } else {
            return '';
        }
    }
}

if (!function_exists('canUser')) 
{
    function can($permiso, $redirect = true)
    {
        if (session()->get('rol_nombre') == 'Admixn') 
        {
            return true;
        } 
        else 
        {
            $rolId = session()->get('rol_id');
            
            /*$permisos = cache()->tags('Permiso')->rememberForever("Permiso.rolid.$rolId", 
                function () 
                {
                    return Permiso::whereHas('roles', function ($query) 
                    {
                        $query->where('rol_id', session()->get('rol_id'));

                    })->get()->pluck('slug')->toArray();
                });*/


            $permisos=Permiso::whereHas('roles', function ($query){ $query->where('rol_id', session()->get('rol_id')); })->get()->pluck('slug')->toArray();
            
          


            if (!in_array($permiso, $permisos)) 
            {
                if($redirect) 
                {
                    if (!request()->ajax())
                        return redirect()->route('inicio')->with('mensaje', 'No tienes permisos para entrar en este modulo')->send();

                    abort(403, 'No tiene permiso');
                } 
                else 
                {
                    return false;
                }
            }

            return true;
        
        }
    }
}

if(!function_exists('getMachine')) 
{
    function getMachine($id)
    {
        $obj = Machine::find($id);
        return $obj;
    }
}

if(!function_exists('getShape')) 
{
    function getShape($id)
    {
        $obj = Shape::find($id);
        return $obj;
    }
}