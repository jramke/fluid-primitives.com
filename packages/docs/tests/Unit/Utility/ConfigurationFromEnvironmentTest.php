<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Utility;

use FluidPrimitives\Docs\Utility\ConfigurationFromEnvironment;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ConfigurationFromEnvironmentTest extends UnitTestCase
{
    #[Test]
    public function setsNestedValuesAndTypesThemByWhatTheyLookLike(): void
    {
        $configuration = (new ConfigurationFromEnvironment())->apply([
            'DB' => ['Connections' => ['Default' => ['host' => 'REPLACED_BY_ENV', 'charset' => 'utf8mb4']]],
            'SYS' => ['sitename' => 'Fluid Primitives'],
        ], [
            'TYPO3__DB__Connections__Default__host' => 'db',
            'TYPO3__DB__Connections__Default__port' => '3306',
            'TYPO3__SYS__displayErrors' => '0',
            'TYPO3__SYS__ratio' => '1.5',
            'TYPO3__BE__debug' => 'true',
            'TYPO3__FE__debug' => 'false',
            'TYPO3__MAIL__transport_sendmail_command' => 'null',
            'TYPO3__SYS__zipCode' => '007',
            'TYPO3__SYS__sitename' => 'Docs',
            'APP_URL' => 'https://example.com',
            'TYPO3_CONTEXT' => 'Production',
        ]);

        $this->assertSame(
            [
                'DB' => ['Connections' => ['Default' => ['host' => 'db', 'charset' => 'utf8mb4', 'port' => 3306]]],
                'SYS' => ['sitename' => 'Docs', 'displayErrors' => 0, 'ratio' => 1.5, 'zipCode' => '007'],
                'BE' => ['debug' => true],
                'FE' => ['debug' => false],
                'MAIL' => ['transport_sendmail_command' => null],
            ],
            $configuration,
        );
    }

    #[Test]
    public function keepsASecretAStringWhatEverItLooksLike(): void
    {
        $configuration = (new ConfigurationFromEnvironment())->apply([], [
            // @mago-expect lint:no-literal-password
            'TYPO3__DB__Connections__Default__password' => '12345678',
            'TYPO3__SYS__encryptionKey' => '1e10',
            // @mago-expect lint:no-literal-password
            'TYPO3__BE__installToolPassword' => 'true',
        ]);

        $this->assertSame(
            [
                // @mago-expect lint:no-literal-password
                'DB' => ['Connections' => ['Default' => ['password' => '12345678']]],
                'SYS' => ['encryptionKey' => '1e10'],
                // @mago-expect lint:no-literal-password
                'BE' => ['installToolPassword' => 'true'],
            ],
            $configuration,
        );
    }

    #[Test]
    public function readsTheSystemMaintainersAsAListOfUserIds(): void
    {
        $configuration = (new ConfigurationFromEnvironment())->apply([], [
            'TYPO3__SYS__systemMaintainers' => ' 1, 2 ,,3 ',
        ]);

        $this->assertSame(['SYS' => ['systemMaintainers' => [1, 2, 3]]], $configuration);
    }

    #[Test]
    public function ignoresAVariableWithoutAPathBelowThePrefix(): void
    {
        $configuration = (new ConfigurationFromEnvironment())->apply(['SYS' => ['sitename' => 'Docs']], [
            'TYPO3__' => 'x',
            'TYPO3____SYS' => 'x',
            'TYPO3__SYS____sitename' => 'x',
            'TYPO3__SYS__sitename__' => 'x',
        ]);

        $this->assertSame(['SYS' => ['sitename' => 'Docs']], $configuration);
    }

    #[Test]
    public function namesTheVariablesOfWhatIsEmptyOrStillAPlaceholder(): void
    {
        $configuration = [
            'SYS' => ['encryptionKey' => 'REPLACED_BY_ENV'],
            'DB' => ['Connections' => ['Default' => ['host' => 'db', 'password' => '']]],
        ];

        $this->assertSame(
            ['TYPO3__SYS__encryptionKey', 'TYPO3__DB__Connections__Default__password'],
            (new ConfigurationFromEnvironment())->missing($configuration, [
                'SYS/encryptionKey',
                'DB/Connections/Default/host',
                'DB/Connections/Default/password',
            ]),
        );
    }
}
