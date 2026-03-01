<?php

namespace Webkul\Marketplace\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base controller for all Marketplace vendor controllers.
 *
 * Follows the Bagisto package convention of having a single base controller
 * that provides common traits, so all vendor controllers extend this class.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
