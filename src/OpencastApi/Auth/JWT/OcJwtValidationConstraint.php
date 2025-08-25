<?php
namespace OpencastApi\Auth\JWT;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class OcJwtValidationConstraint implements Constraint
{
    /**
     * @inheritDoc
     */
    public function assert(Token $token): void
    {
        if (!$token instanceof UnencryptedToken) {
            throw new ConstraintViolation('You should pass a plain token');
        }

        try {
            $ocClaim = OcJwtClaim::convertFromTokenWithValidation($token);
        } catch (\Throwable $th) {
            throw new ConstraintViolation('Invalid Opencast token claims!');
        }
        assert($ocClaim instanceof OcJwtClaim, 'Invalid Opencast JWT claim!');
    }
}

