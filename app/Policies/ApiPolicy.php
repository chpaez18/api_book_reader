<?php

namespace App\Policies;


use App\Traits\PolicyAuthorization;
use HandlesAuthorization;

abstract class ApiPolicy
{
    use PolicyAuthorization;
}

?>