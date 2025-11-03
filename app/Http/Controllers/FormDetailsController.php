<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormDetails;
use App\Http\Requests\FormsRequest;
use Illuminate\Support\Facades\Log;

class FormDetailsController extends Controller
{
    public function addBusinessPermit(array $request) {

        return FormDetails::create($request);
    }
}
