<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

/**
 * Class ValidationExceptionBuilder
 *
 * A builder class for constructing and throwing ValidationException instances
 * based on accumulated validation errors.
 */
class ValidationExceptionBuilder {
    private array $errors = [];
    private \BackedEnum $mainErrorCode = DomainErrorCode::INVALID_DATA;
    
    /**
     * Adds a validation error code to the builder.
     *
     * @param \BackedEnum $validationErrorCode The validation error code to add.
     * @return self The current instance of the builder.
     */
    public function addError(\BackedEnum $validationErrorCode): self {
        $this->errors[] = $validationErrorCode;
        return $this;
    }

    /**
     * Verifies if there are any errors added to the builder.
     *
     * @return bool True if there are errors, false otherwise.
     */
    public function hasErrors(): bool {
        return !empty($this->errors);
    }

    /**
     * Sets the main error code for the validation exception.
     *
     * @param \BackedEnum $mainErrorCode The main error code to set.
     * @return self The current instance of the builder.
     */
    public function setMainErrorCode(\BackedEnum $mainErrorCode): self {
        $this->mainErrorCode = $mainErrorCode;
        return $this;
    }
    /**
     * Throws a ValidationException if there are any errors added to the builder.
     *
     * @throws ValidationException If there are validation errors.
     */
    public function throwIfAny(): void {
        if ($this->hasErrors()) {
            throw new ValidationException(
                $this->mainErrorCode,
                $this->errors
            );
        }
    }
}
