<?php

namespace App\Traits;

use App\Models\LogError;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Fractalistic\ArraySerializer;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
	protected function successResponse($data, $code, $collection = false)
	{

		if ($collection) { 
			return response()->json([
				'responseCode' => $code, 
				'total_records' => $collection->total(), 
				'last_page' => $collection->lastPage(), 
				'data' => $data
			], $code); 
		}
		
		return response()->json([
			'responseCode' => $code, 
			'data' => $data
		], $code);
	}
	
	protected function errorResponse($message, $code, $exception = false)
	{

		return response()->json([
			'responseCode' => $code,
            'data' => $message, 
        ], $code);
	}

	protected function showAll(Collection $collection, $code = 200)
	{
		if ($collection->isEmpty())
			return $this->successResponse(['responseCode' => $code, 'data' => $collection], 200);

		//dd($collection);

		$transformer = $collection->first()->transformer;

        $collection = $this->transformData($collection, $transformer);

		return $this->successResponse($collection, $code);
	}

	protected function showPaginate(LengthAwarePaginator $collection, $code = 200)
	{
		
		if ($collection->isEmpty())
			return $this->successResponse($collection->items(), 200);

		$transformer = $collection->first()->transformer;
        $collectionItems = $this->transformData($collection->items(), $transformer);
		
		return $this->successResponse($collectionItems, $code, $collection);
	}

	protected function showOne(Model $instance, $code = 200)
	{
        
		$transformer = $instance->transformer;
		$instance = $this->transformData($instance, $transformer);
		
		return $this->successResponse($instance, $code);
	}

	protected function showMessage($message, $code)
	{
		return response()->json([
			'responseCode' => $code, 
			'data' => $message
		], $code);
	}

	protected function transformData($data, $transformer)
    {
		

    	if (isset($_GET["first"]))
        	$data = $data->first();
        
        $transformation = fractal($data, new $transformer)->serializeWith(new ArraySerializer());
		
        if (isset($_GET['include']))
            $transformation->parseIncludes($_GET['include']);

        return $transformation->toArray();
    }
}