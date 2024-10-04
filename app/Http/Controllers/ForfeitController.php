<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForfeitAddRequest;
use App\Http\Requests\ForfeitDeleteRequest;
use Illuminate\Http\Request;
use App\Services\ForfeitService;

class ForfeitController extends Controller
{
    protected ForfeitService $forfeitService;
    public function __construct(ForfeitService $forfeitService)
    {
        $this->forfeitService = $forfeitService;
    }
    
    public function add(ForfeitAddRequest $request)
    {
        return $this->forfeitService->add($request->validated());
    }
    public function delete(ForfeitDeleteRequest $request)
    {
        return $this->forfeitService->delete($request->validated());
    }

    public function get_forfeit($place_id)
    {
        return $this->forfeitService->get($place_id);
    }

    public function get_forfeit_id($place_id, $id)
    {
        return $this->forfeitService->get_id($place_id, $id);
    }

}
