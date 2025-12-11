<?php
namespace FW\Routing\Http;

class Response
{
    public int $status = 200;
    public array $headers = [];
    public string $body = '';

    public function __construct(string $body = '', int $status = 200)
    {
        $this->body = $body;
        $this->status = $status;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $k => $v) {
            header("$k: $v");
        }

        echo $this->body;
    }
}