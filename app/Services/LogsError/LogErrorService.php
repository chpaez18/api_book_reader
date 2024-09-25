<?php

namespace App\Services\LogsError;

use App\Traits\Crud;
use App\Models\LogError;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



class LogErrorService
{
    use ApiResponse, Crud;

    /**
     * @var LogError
     */
    public $logError;

    public function __construct()
    {
        $model = new LogError();
        $this->logError = $model;
        $this->defineModel($model);
    }

    /**
     * Function to get all data in table
     *
     * @return  Collection
     */
    public function getAll(): Collection
    {
        return $this->getAllRecords();
    }

    /**
     * Function to get all data paginated in table
     *
     * @return  LengthAwarePaginator
     */
    public function getAllPaginate(): LengthAwarePaginator
    {
        return $this->getRecordsPaginate();
    }

    /**
     * Function to get especified data by id
     *
     * @param int $id
     *
     * @return  Adapter
     */
    public function getById(int $id): ?Adapter
    {
        return $this->getRecordById($id);
    }

    /**
     * Function to create a new log error
     *
     * @param Throwable  $exception
     * @param Request  $request
     *
     * @return  LogError
     */
    public function createLogError(\Throwable $exception, $request = null): ?LogError
    {
        //Get error data
        //-----------------------------------------------------------------
            $errorData = [
                'env' => app()->environment(),
                'user_id' => Auth::id(),
                'error' => $exception->getMessage(),
                'ip' => $request ? $request->ip() : null,
                'browser' => $request ? $request->header('User-Agent') : 'CLI',
                'trace' => $exception->getTraceAsString(),
            ];
        //-----------------------------------------------------------------

        //Save Record
        //-----------------------------------------------------------------
            $record = $this->createRecord($errorData);
        //-----------------------------------------------------------------

        return $record;
    }


    /**
     * Function to delete records
     *
     * @param array $id
     *
     * @return  bool
     */
    public function deleteLogError(array $id): bool
    {

        //Delete Record
        //-------------------------------------------------------
            $record = $this->deleteRecord($id);
        //-------------------------------------------------------

        return true;
    }
}
