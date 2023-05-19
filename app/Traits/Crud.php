<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;



trait Crud
{
	public $model;

	protected function defineModel($model)
	{
		$this->model = $model;
	}

	protected function getAllRecords()
	{
		$data = $this->model->query();

		//Get request params
        //-------------------------------------------------------------------------------------------------------------------
			$sortBy = (request()->input('sort_by') == null ? 'id':request()->input('sort_by'));
			$sortDirection = (request()->input('sort_direction') == null ? 'asc':request()->input('sort_direction'));
			$search = request()->input('search');
			$perPage = (request()->input('per_page') == null ? 10:request()->input('per_page'));
		//-------------------------------------------------------------------------------------------------------------------

		//Apply defined params in data
		//-------------------------------------------------------------------------------------------------------------------
			$data->when((isset($sortBy))&&(isset($sortDirection)), fn ($query) => $query->reorder($sortBy,$sortDirection) );
			$data->when(isset($search), fn ($query) => $query->filterInTable($search) );
		//-------------------------------------------------------------------------------------------------------------------
			
		return $data->orderBy('id')->get();
	}

	protected function getRecordsPaginate()
	{
		$data = $this->model->query();

        //Get request params
        //-------------------------------------------------------------------------------------------------------------------
            $sortBy = (request()->input('sort_by') == null ? 'id':request()->input('sort_by'));
            $sortDirection = (request()->input('sort_direction') == null ? 'asc':request()->input('sort_direction'));
            $search = request()->input('search');
            $perPage = (request()->input('per_page') == null ? 10:request()->input('per_page'));
        //-------------------------------------------------------------------------------------------------------------------

        //Apply defined params in data
        //-------------------------------------------------------------------------------------------------------------------
            $data->when((isset($sortBy))&&(isset($sortDirection)), fn ($query) => $query->reorder($sortBy,$sortDirection) );
            $data->when(isset($search), fn ($query) => $query->filterInTable($search) );
        //-------------------------------------------------------------------------------------------------------------------

        return $data->paginate($perPage);
	}

	protected function getRecordById($id)
	{
		return $this->model->where('id', $id)->firstOrFail();
	}

	protected function createRecord($data)
	{
		//Insert Record
    	//---------------------------------------------------
			try {

				$record = $this->model->create($data);

			} catch(\Exception $exception) {

				throw $exception;
			}
		//---------------------------------------------------

		return $record;
	}

	protected function updateRecord($data, $id)
	{
		//Update Record
    	//---------------------------------------------------
			try {

				$record = $this->model->where('id', $id)->firstOrFail();
                $record->update($data);

			} catch(\Exception $exception) {

				throw $exception;
			}
		//---------------------------------------------------

		return $record;
	}

	protected function deleteRecord($id)
	{
		//Delete Record
        //-------------------------------------------------------
			if (count($id) > 1) {
				$record = $this->model::whereIn('id', $id)->update(['status' => $this->model::STATUS["Inactive"]]);
			} else {
				$record = $this->model->where('id', $id[0])->first();
				$record->status = $this->model::STATUS["Inactive"];
				if (!$record->update()) {
					return false;
				}
			}
		//-------------------------------------------------------

		return $record;
	}
}