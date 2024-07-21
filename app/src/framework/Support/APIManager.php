<?php

namespace App\Support;

use Exception;
use JetBrains\PhpStorm\NoReturn;

class APIManager
{
    private array $request = [];
    private bool $checkXSS = true;
    private bool $checkToken = true;
    private array $mandatoryParams = [];

    /**
     * Initializes the APIManager instance.
     *
     * @param bool $checkXSS Whether to check for XSS attacks.
     * @param bool $checkToken Whether to check for a valid token.
     */
    public function __construct(bool $checkXSS = true, bool $checkToken = true)
    {
        $this->request = $this->get_input();
        $this->checkXSS = $checkXSS;
        $this->checkToken = $checkToken;
    }

    /**
     * Sets the request input data.
     *
     * @param array $request The request data.
     * @return APIManager Returns the instance of APIManager.
     */
    public function set_input(array $request): APIManager
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Retrieves the input data from the request.
     *
     * @return array The input data as an associative array.
     */
    private function get_input(): array
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Handles the incoming request and executes the provided callback function.
     *
     * @param callable $func The callback function to handle the request.
     * @throws Exception If any validation fails or if the token is invalid.
     */
    public function handle(callable $func): void
    {
        if (!empty($this->request)) {
            foreach ($this->mandatoryParams as $param) {
                if (!isset($this->request[$param])) {
                    self::respond(false, 'پارامترهای ارسالی کامل نیستند');
                }
            }

            if ($this->isValidToken()) {
                if (!$this->checkXSS || !$this->hasXSS()) {
                    $func($this->request);
                    return;
                }
                self::respond(false, 'اطلاعات وارد شده مجاز نیستند');
            }
            self::respond(false, "توکن منقضی شده است. صفحه را مجدد بارگزاری کنید.");
        }
        $this->forbidden();
    }

    /**
     * Validates if the token in the request is valid.
     *
     * @return bool True if the token is valid, otherwise false.
     */
    private function isValidToken(): bool
    {
        $token = $this->request['token'] ?? null;
        if (!$this->checkToken)
            return true;
        return $token !== null && session()->has('csrf_token') && session()->get('csrf_token') === $token;
    }

    /**
     * Checks for XSS attacks in the request data.
     *
     * @return bool True if XSS is detected, otherwise false.
     */
    private function hasXSS(): bool
    {
        $input = $this->request;
        if (!$this->checkXSS)
            return false;

        return hasXSS($input);
    }

    /**
     * Sets the mandatory parameters for the request.
     *
     * @param mixed ...$params The mandatory parameters.
     * @return self Returns the instance of APIManager.
     */
    public function validateParameters(...$params): self
    {
        $this->mandatoryParams = $params;
        return $this;
    }

    /**
     * Sends a JSON response and terminates the script execution.
     *
     * @param bool $status The status of the response.
     * @param string $message The message of the response.
     * @param array $data Additional data to include in the response.
     */
    #[NoReturn]
    public static function respond(bool $status, string $message, array $data = []): void
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }

        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Sends a forbidden response and terminates the script execution.
     *
     * @throws Exception If an error occurs while sending the response.
     */
    #[NoReturn]
    private function forbidden(): void
    {
        showError(500);
    }
}
