<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['form', 'url'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');

        // Enable CSRF protection
        helper('security');
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated(): bool
    {
        return (bool) $this->session->get('user_id');
    }

    /**
     * Get current user ID
     */
    protected function getCurrentUserId(): ?int
    {
        return $this->session->get('user_id');
    }

    /**
     * Get current user data
     */
    protected function getCurrentUser(): ?array
    {
        return $this->session->get('user_data');
    }

    /**
     * Require authentication for this controller method
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }
    }

    /**
     * Check user role permissions
     */
    protected function hasRole(string $role): bool
    {
        $userData = $this->getCurrentUser();
        return $userData && $userData['role'] === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    protected function hasAnyRole(array $roles): bool
    {
        $userData = $this->getCurrentUser();
        return $userData && in_array($userData['role'], $roles);
    }

    /**
     * Log user activity with context
     */
    protected function logActivity(string $action, array $data = []): void
    {
        $userId = $this->getCurrentUserId();
        $userAgent = $this->request->getUserAgent();
        $ipAddress = $this->request->getIPAddress();

        $logData = array_merge($data, [
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent->__toString(),
            'timestamp' => date('Y-m-d H:i:s'),
            'url' => current_url()
        ]);

        log_message('info', 'User Activity: ' . json_encode($logData));
    }

    /**
     * Handle errors with proper logging and user feedback
     */
    protected function handleError(\Exception $e, string $userMessage = 'Ein Fehler ist aufgetreten', bool $redirect = true)
    {
        // Log detailed error for debugging
        log_message('error', 'Controller Error: ' . $e->getMessage() . "\nFile: " . $e->getFile() . "\nLine: " . $e->getLine() . "\nTrace: " . $e->getTraceAsString());

        // Log user context
        $this->logActivity('error_encountered', [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine()
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $userMessage
            ]);
        }

        if ($redirect) {
            return redirect()->back()->with('error', $userMessage);
        }

        throw $e;
    }

    /**
     * Standard 404 Error Response
     */
    protected function render404(string $resource = 'Ressource'): ResponseInterface
    {
        $message = $resource . ' nicht gefunden';

        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => $message,
                'timestamp' => date('c')
            ]);
        }

        return $this->response->setStatusCode(404)->setBody(view('errors/custom', [
            'title' => 'Fehler 404',
            'message' => $message,
            'statusCode' => 404
        ]));
    }

    /**
     * Standard 403 Error Response
     */
    protected function render403(string $action = 'Aktion'): ResponseInterface
    {
        $message = 'Zugriff verweigert: ' . $action . ' nicht erlaubt';

        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => $message,
                'timestamp' => date('c')
            ]);
        }

        return $this->response->setStatusCode(403)->setBody(view('errors/custom', [
            'title' => 'Fehler 403',
            'message' => $message,
            'statusCode' => 403
        ]));
    }

    /**
     * Validation Error Response
     */
    protected function renderValidationError(array $errors): ResponseInterface
    {
        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Validierungsfehler',
                'validation_errors' => $errors,
                'timestamp' => date('c')
            ]);
        }

        return redirect()->back()->withInput()->with('validation_errors', $errors);
    }
}
