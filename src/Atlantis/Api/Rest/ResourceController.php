<?php namespace Atlantis\Api\Rest;

use Dingo\Api\Routing\Controller;
use Atlantis\Api\ApiProblem;

abstract class ResourceController extends Controller{

    /**
     * Create a record in the resource
     *
     * @param  array|object $data
     * @return array|object
     */
    public function create($data){

        return new ApiProblem(405, 'The POST method has not been defined');
    }

    /**
     * Update (replace) an existing record
     *
     * @param  string|int $id
     * @param  array|object $data
     * @return array|object
     */
    public function update($id, $data){

        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }


    /**
     * Update (replace) an existing collection of records
     *
     * @param  array $data
     * @return array|object
     */
    public function replaceList($data){

        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }
    public function store($data){
        $this->replaceList($data);
    }


    /**
     * Partial update of an existing record
     *
     * @param  string|int $id
     * @param  array|object $data
     * @return array|object
     */
    public function patch($id, $data){

        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }


    /**
     * Delete an existing record
     *
     * @param  string|int $id
     * @return bool
     */
    public function delete($id){

        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }
    public function destroy($id){
        $this->delete($id);
    }

    /**
     * Delete an existing collection of records
     *
     * @param  null|array $data
     * @return bool
     */
    public function deleteList($data = null){

        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }


    /**
     * Fetch an existing record
     *
     * @param  string|int $id
     * @return false|array|object
     */
    public function fetch($id){

        return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }
    public function show($id){
        $this->fetch($id);
    }


    /**
     * Fetch a collection of records
     *
     */
    public function fetchAll(){

        return new ApiProblem(405, 'The GET method has not been defined for collections');
    }
    public function index(){
        $this->fetchAll();
    }
}