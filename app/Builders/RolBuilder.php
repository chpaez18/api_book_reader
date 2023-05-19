<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class RolBuilder extends Builder
{
    /**
     * class to build specific queries for a given model
     */

    public function filterInTable($searchTerm): self
    {
        //Define the fields and their type for filtering
        //-----------------------------------------------------------------------
            $fields = [
                'name' => 'string_ilike'
            ];
        //-----------------------------------------------------------------------

        //Build dynamic query based ond fields an search term received
        //-----------------------------------------------------------------------
            foreach ($fields as $key=>$value) {
                if ($value == "string") {
                    $this->orWhere($key, "=", $searchTerm);
                }
                if ($value == "string_ilike") {
                    $this->orWhere($key, "ilike", "%".$searchTerm."%");
                }
                if (($value == "float") || ($value == "integer")) {
                    if (is_numeric($searchTerm)) {
                        $this->orWhere($key, "=", $searchTerm);
                    }
                }
            }
        //-----------------------------------------------------------------------
        return $this;
    }

}
