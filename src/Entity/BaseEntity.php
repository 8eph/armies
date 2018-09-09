<?php


namespace App\Entity;

class BaseEntity
{
    /** @var string */
    protected $name;

    /**
     * Automagically construct entities using an array map with the properties.
     * Normally you'd use setters here but we want entities to be immutable once created.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        foreach ($arguments as $argumentName => $argumentValue) {
            if (!property_exists($this, $argumentName)) {
                throw new \LogicException(sprintf('Attempting to set undefined property %s on %s',
                    $argumentName, get_class($this))
                );
            }
            $this->$argumentName = $argumentValue;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}