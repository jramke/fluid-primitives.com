<?php

namespace FluidPrimitives\Docs\Domain\Model\DTO;

use TYPO3\CMS\Extbase\Annotation\Validate;

class TestFormDTO
{
    #[Validate(['validator' => 'String'])]
    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate([
        'validator' => 'StringLength',
        'options' => ['minimum' => 10, 'maximum' => 50],
    ])]
    private string $something = '';

    private bool $checkboxExample = false;

    #[Validate(['validator' => 'String'])]
    #[Validate(['validator' => 'NotEmpty'])]
    private string $selectExample = '';

    public function getSomething(): string
    {
        return $this->something;
    }

    public function setSomething(string $something): void
    {
        $this->something = $something;
    }

    public function getCheckboxExample(): bool
    {
        return $this->checkboxExample;
    }

    public function setCheckboxExample(bool $checkboxExample): void
    {
        $this->checkboxExample = $checkboxExample;
    }

    public function getSelectExample(): string
    {
        return $this->selectExample;
    }

    public function setSelectExample(string $selectExample): void
    {
        $this->selectExample = $selectExample;
    }

    public static function serialize(self $dto): array
    {
        return [
            'something' => $dto->getSomething(),
            'another' => $dto->getAnother(),
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            $data['something'] ?? '',
            $data['another'] ?? ''
        );
    }
}
