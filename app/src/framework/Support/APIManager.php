<?php

namespace App\Support;

use JetBrains\PhpStorm\NoReturn;

class APIManager
{
    private array $request = [];
    private bool $checkXSS = true;
    private bool $checkToken = true;
    private array $mandatoryParams = [];

    public function __construct(bool $checkXSS = true, $checkToken = true)
    {
        $this->request = $this->get_input();
        $this->checkXSS = $checkXSS;
        $this->checkToken = $checkToken;
    }

    public function set_input($request): APIManager
    {
        $this->request = $request;
        return $this;
    }

    private function get_input(): array
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function handle(callable $func): void
    {
        if (!empty($this->request)) {
            foreach ($this->mandatoryParams as $param) {
                if (!isset($this->request[$param])) {
                    self::respond(false, 'پارامترهای ارسالی کامل نیستند');
                    return;
                }
            }

            if ($this->isValidToken()) {
                if ( !$this->checkXSS || !$this->hasXSS()) {
                    $func($this->request);
                    return; // Add return to prevent further execution
                }
                self::respond(false, 'اطلاعات وارد شده مجاز نیستند');
                return; // Add return to prevent further execution
            }
            self::respond(false, "توکن منقضی شده است. صفحه را مجدد بارگزاری کنید.");
            return; // Add return to prevent further execution
        }
        $this->forbidden();
    }

    private function isValidToken(): bool
    {
        $token = $this->request['token'] ?? null;
        if (!$this->checkToken)
            return true;
        return $token !== null && session()->has('csrf_token') && session()->get('csrf_token') === $token;
    }

    private function hasXSS(): bool
    {
        $input = $this->request;
        if (!$this->checkXSS)
            return false;

        return hasXSS($input);
    }

    public function validateParameters(...$params): self
    {
        $this->mandatoryParams = $params;
        return $this;
    }

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

    #[NoReturn]
    private function forbidden(): void
    {
        showError(500);
    }
}
