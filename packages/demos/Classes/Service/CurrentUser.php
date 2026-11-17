<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Service;

use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * The logged-in frontend user of a request, as the site header and the account settings show it.
 */
final class CurrentUser
{
    /**
     * @return array{uid: int, name: string, email: string, initials: string, memberSince: int}|null
     */
    public static function fromRequest(ServerRequestInterface $request): ?array
    {
        // @mago-expect analysis:mixed-assignment
        $frontendUser = $request->getAttribute('frontend.user');
        $user = $frontendUser instanceof FrontendUserAuthentication ? Typed::arrayOrNull($frontendUser->user) : null;
        $uid = Typed::int($user['uid'] ?? null);
        if ($user === null || $uid === 0) {
            return null;
        }

        $username = Typed::string($user['username'] ?? null);
        $name = trim(Typed::string($user['name'] ?? null)) ?: $username;

        return [
            'uid' => $uid,
            'name' => $name,
            'email' => Typed::string($user['email'] ?? null) ?: $username,
            'initials' => self::initials($name),
            'memberSince' => Typed::int($user['crdate'] ?? null),
        ];
    }

    /**
     * The first letters of the first two words: "Ada Lovelace" is "AL", "ada@example.com" is "A".
     */
    public static function initials(string $name): string
    {
        $words = preg_split('/\s+/u', trim($name), limit: -1, flags: PREG_SPLIT_NO_EMPTY) ?: [];

        return mb_strtoupper(implode('', array_map(
            static fn(string $word): string => mb_substr($word, start: 0, length: 1),
            array_slice($words, offset: 0, length: 2),
        )));
    }
}
