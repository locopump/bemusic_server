<?php namespace App\Http\Controllers\Auth;

use App\Services\BootstrapData;
use App\Services\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @var BootstrapData
     */
    private $bootstrapData;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * Create a new controller instance.
     *
     * @param BootstrapData $bootstrapData
     * @param Settings $settings
     */
    public function __construct(BootstrapData $bootstrapData, Settings $settings)
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->bootstrapData = $bootstrapData;
        $this->settings = $settings;
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string|email_confirmed',
            'password' => 'required|string',
        ]);
    }

    /**
     * The user has been authenticated.
     *
     * @return mixed
     */
    protected function authenticated()
    {

        $data = $this->bootstrapData->get();
        return $this->success(['data' => $data]);
    }

    /**
     * Get the failed login response instance.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse()
    {
        return $this->error(['general' => trans('auth.failed')]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return $this->success();
    }
}