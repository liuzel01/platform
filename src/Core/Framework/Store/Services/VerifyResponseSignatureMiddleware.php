<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Services;

use Psr\Http\Message\ResponseInterface;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Exception\StoreSignatureValidationException;

/**
 * @internal
 */
#[Package('merchant-services')]
class VerifyResponseSignatureMiddleware implements MiddlewareInterface
{
    private const SHUWEI_SIGNATURE_HEADER = 'X-Shuwei-Signature';

    public function __construct(private readonly OpenSSLVerifier $openSslVerifier)
    {
    }

    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        $signatureHeaderName = self::SHUWEI_SIGNATURE_HEADER;
        $header = $response->getHeader($signatureHeaderName);
        if (!isset($header[0])) {
            throw new StoreSignatureValidationException(sprintf('Signature not found in header "%s"', $signatureHeaderName));
        }

        $signature = $header[0];

        if (empty($signature)) {
            throw new StoreSignatureValidationException(sprintf('Signature not found in header "%s"', $signatureHeaderName));
        }

        if (!$this->openSslVerifier->isSystemSupported()) {
            return $response;
        }

        if ($this->openSslVerifier->isValid($response->getBody()->getContents(), $signature)) {
            $response->getBody()->rewind();

            return $response;
        }

        throw new StoreSignatureValidationException('Signature not valid');
    }
}
