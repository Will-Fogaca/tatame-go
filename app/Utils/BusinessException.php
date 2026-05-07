<?php

namespace App\Utils;

class BusinessException extends \Exception {

    /**
     * Código HTTP associado ao erro
     * @var int
     */
    private int $httpCode;

    /**
     * Construtor da exceção de negócio
     * @param string $message Mensagem de erro
     * @param int $httpCode Código HTTP (padrão 400)
     * @param \Throwable|null $previous Exceção anterior
     */
    public function __construct(string $message, int $httpCode = 400, ?\Throwable $previous = null){
        $this->httpCode = $httpCode;
        parent::__construct($message, $httpCode, $previous);
    }

    /**
     * Retorna o código HTTP associado ao erro
     * @return int
     */
    public function getHttpCode(): int {
        return $this->httpCode;
    }
}