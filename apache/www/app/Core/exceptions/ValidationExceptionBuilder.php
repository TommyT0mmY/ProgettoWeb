<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

/**
 * Builder for ValidationException with multiple errors.
 */
class ValidationExceptionBuilder {
    private array $errors = [];
    private \UnitEnum $mainErrorCode = ValidationErrorCode::INVALID_REQUEST;
    
    /**
     * Adds a validation error code to the builder.
     *
     * @param \UnitEnum $validationErrorCode Error code to add.
     * @return self Same instance for chaining.
     */
    public function addError(\UnitEnum $validationErrorCode): self {
        $this->errors[] = $validationErrorCode;
        return $this;
    }

    /**
     * Checks if any errors have been added.
     *
     * @return bool True if errors exist.
     */
    public function hasErrors(): bool {
        return !empty($this->errors);
    }

    /**
     * Sets the main error code for the exception. Under normal usage,
     * this shouldn't be necessary to change, as the default INVALID_REQUEST
     * is sufficient.
     *
     * @param \UnitEnum $mainErrorCode Main error code to set.
     * @return self Same instance for chaining.
     */
    public function setMainErrorCode(\UnitEnum $mainErrorCode): self {
        $this->mainErrorCode = $mainErrorCode;
        return $this;
    }

    /**
     * Throws ValidationException if any errors were added.
     *
     * @throws ValidationException
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
