<?php
namespace OpencastApi\Auth\JWT;

use Lcobucci\JWT\Token;
use DateTimeImmutable;
use DateTimeInterface;

class OcJwtClaim
{
    public const OC_CLAIMS = [
        self::EXP,
        self::NBF,
        self::SUB,
        self::NAME,
        self::EMAIL,
        self::ROLES,
        self::OC,
    ];

    public const EXP = 'exp';
    public const NBF = 'nbf';
    public const SUB = 'sub';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const ROLES = 'roles';
    public const OC = 'oc';

    /** @var DateTimeImmutable expiration time
     * A number timestamp in seconds since the unix epoch.
     * JWTs with exp < now() are rejected by Opencast.
     * This claim is required.
    */
    private DateTimeImmutable $exp;

    /** @var int|null not before time
     * A number timestamp in seconds since the unix epoch.
     * JWTs with nbf > now() are rejected by Opencast.
     * This claim is optional.
    */
    private ?int $nbf = null;

    /** @var string Opencast username
     * The subject of the JWT.
     * This claim is required.
    */
    private ?string $sub = null;

    /** @var string Display name of the user
     * The display name of the user.
     * This claim is required.
    */
    private ?string $name = null;

    /** @var string User email
     * The email of the user.
     * This claim is required.
    */
    private ?string $email = null;

    /** @var array Roles
     * The roles assigned to the user.
     * This claim is optional.
    */
    private array $roles = [];

    /** @var array Event ACLs
     * The event-specific ACLs for the user.
     * This claim is optional.
    */
    private array $eventAcls = [];

    /** @var array Series ACLs
     * The series-specific ACLs for the user.
     * This claim is optional.
    */
    private array $seriesAcls = [];

    /** @var array Playlist ACLs
     * The playlist-specific ACLs for the user.
     * This claim is optional.
    */
    private array $playlistAcls = [];

    public function __construct()
    {
    }

    /**
     * Sets the user information claims for the JWT.
     *
     * @param string $sub   The subject (username) of the JWT.
     * @param string|null $name  The display name of the user (optional).
     * @param string|null $email The email address of the user (optional).
     *
     * @return void
     */
    public function setUserInfoClaims(string $sub, ?string $name = null, ?string $email = null): void
    {
        $this->sub = $sub;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * Gets the subject (username) claim of the JWT.
     *
     * @return string|null The subject of the JWT, or null if not set.
     */
    public function getSub(): ?string
    {
        return $this->sub;
    }

    /**
     * Gets the name (name of user) claim of the JWT.
     *
     * @return string|null The name of the user, or null if not set.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Gets the email claim of the JWT.
     *
     * @return string|null The email of the user, or null if not set.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets Not Before (nbf) claim of the JWT.
     *
     * @param int $nbf The Not Before time as a Unix timestamp.
     * @return void
     */
    public function setNbf(int $nbf): void
    {
        $this->nbf = $nbf;
    }

    /**
     * Gets the Not Before (nbf) claim of the JWT.
     *
     * @return int|null The Not Before time as a Unix timestamp, or null if not set.
     */
    public function getNbf(): ?int
    {
        return $this->nbf;
    }

    /**
     * Sets the expiration time (exp) claim for the JWT.
     *
     * @param DateTimeImmutable $exp The expiration time as a DateTimeImmutable object.
     * @return void
     */
    public function setExp(DateTimeImmutable $exp): void
    {
        $this->exp = $exp;
    }

    /**
     * Gets the expiration time (exp) claim of the JWT.
     *
     * @return DateTimeImmutable The expiration time as a DateTimeImmutable object.
     */
    public function getExp(): DateTimeImmutable
    {
        return $this->exp;
    }

    /**
     * Checks if the object has an expiration time (exp) claim.
     *
     * @return bool True if the expiration time is set, false otherwise.
     */
    public function hasExp(): bool
    {
        return isset($this->exp);
    }

    /**
     * Sets the roles claim for the JWT.
     *
     * @param array $roles The roles to assign to the user.
     * @return void
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Gets the roles claim of the JWT.
     *
     * @return array The roles assigned to the user.
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Sets the event access control lists (ACLs) for the JWT.
     *
     * @param array $acls The event ACLs to assign to the user.
     * @return void
     */
    public function setEventAcls(array $acls): void
    {
        foreach ($acls as $identifier => $actions) {
            $this->eventAcls[] = ["e:{$identifier}" => $actions];
        }
    }

    /**
     * Sets the series access control lists (ACLs) for the JWT.
     *
     * @param array $acls The series ACLs to assign to the user.
     * @return void
     */
    public function setSeriesAcls(array $acls): void
    {
        foreach ($acls as $identifier => $actions) {
            $this->seriesAcls[] = ["s:{$identifier}" => $actions];
        }
    }

    /**
     * Sets the playlist access control lists (ACLs) for the JWT.
     *
     * @param array $acls The playlist ACLs to assign to the user.
     * @return void
     */
    public function setPlaylistAcls(array $acls): void
    {
        foreach ($acls as $identifier => $actions) {
            $this->playlistAcls[] = ["p:{$identifier}" => $actions];
        }
    }

    /**
     * Gets the access control lists (ACLs) as for "oc" claims of the JWT.
     * it is a combination of event, series and playlist ACLs in "oc" claims format.
     *
     * @return array The ACLs assigned to the user.
     */
    public function getAclsClaims(): array
    {
        return [
            'event' => $this->eventAcls,
            'serie' => $this->seriesAcls,
            'playlist' => $this->playlistAcls,
        ];
    }

    /**
     * Converts the Opencast JWT claim object to an array.
     *
     * @return array The Opencast JWT claims as an array.
     * @throws \InvalidArgumentException if opencast claims requirements are not met!
     */
    public function toArray(): array
    {
        if (empty($this->exp)) {
            throw new \InvalidArgumentException("JWT: Expiration time is required");
        }
        $data = [
            // 'exp' => $this->exp->getTimestamp(),
            'exp' => $this->exp,
        ];
        if (!empty($this->sub)) {
            $data['sub'] = $this->sub;
        }
        if (!empty($this->name)) {
            $data['name'] = $this->name;
        }
        if (!empty($this->email)) {
            $data['email'] = $this->email;
        }
        if (!empty($this->roles) && is_array($this->roles)) {
            $data['roles'] = $this->roles;
        }
        if ($this->hasOcAcls()) {
            $data['oc'] = array_merge(
                $this->eventAcls,
                $this->seriesAcls,
                $this->playlistAcls
            );
        }

        if (empty($data['oc']) && empty($data['roles'])) {
            throw new \InvalidArgumentException("JWT: At least one of 'oc' or 'roles' claims must be set");
        }

        if ($this->nbf !== null) {
            $data['nbf'] = $this->nbf;
        }
        return $data;
    }

    /**
     * Converts the Opencast JWT claim object to JSON.
     *
     * @return string The Opencast JWT claims as a JSON string.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Checks if the object has any Opencast ACLs set.
     *
     * @return bool True if any Opencast ACLs are set, false otherwise.
     */
    private function hasOcAcls(): bool
    {
        return !empty($this->eventAcls) || !empty($this->seriesAcls) || !empty($this->playlistAcls);
    }

    /**
     * Converts a JWT token to an Opencast JWT claim object.
     *
     * @param Token $token The JWT token to convert.
     * @return self The Opencast JWT claim object.
     * @throws \InvalidArgumentException if the token is invalid.
     */
    public static function convertFromTokenWithValidation(Token $token): self
    {
        $instance = new self();

        if (!$token->claims()->has('exp')) {
            throw new \InvalidArgumentException("JWT: Expiration time is required");
        }

        $instance->setExp($token->claims()->get('exp'));

        if (!$token->claims()->has('oc') && !$token->claims()->has('roles')
            || (empty($token->claims()->get('oc')) && empty($token->claims()->get('roles')))) {
            throw new \InvalidArgumentException("JWT: At least one of 'oc' or 'roles' claims must be set");
        }

        $aclClaims = $token->claims()->get('oc') ?? [];
        if (!empty($aclClaims)) {
            $eventAcls = [];
            $seriesAcls = [];
            $playlistAcls = [];
            foreach ($aclClaims as $aclKey => $actions) {
                $identifier = substr($aclKey, 2);
                if (str_starts_with($aclKey, 'e:')) {
                    $eventAcls[$identifier] = $actions;
                } elseif (str_starts_with($aclKey, 's:')) {
                    $seriesAcls[$identifier] = $actions;
                } elseif (str_starts_with($aclKey, 'p:')) {
                    $playlistAcls[$identifier] = $actions;
                }
            }
            $instance->setEventAcls($eventAcls);
            $instance->setSeriesAcls($seriesAcls);
            $instance->setPlaylistAcls($playlistAcls);
        }

        $instance->setRoles($token->claims()->get('roles') ?? []);

        if (!empty($token->claims()->get('sub'))) {
            $instance->setUserInfoClaims(
                $token->claims()->get('sub'),
                $token->claims()->get('name') ?? null,
                $token->claims()->get('email') ?? null
            );
        }

        $instance->setNbf($token->claims()->get('nbf') ?? null);

        return $instance;
    }

    /**
     * Converts an array to an Opencast JWT claim object.
     *
     * @param array $data The array to convert.
     * @return self The Opencast JWT claim object.
     * @throws \InvalidArgumentException if the array is invalid.
     */
    public static function createFromArray(array $data): self
    {
        $instance = new self();

        if (isset($data['exp'])) {
            $instance->setExp($data['exp']);
        }

        if (isset($data['sub'])) {
            $instance->setUserInfoClaims(
                $data['sub'],
                $data['name'] ?? null,
                $data['email'] ?? null
            );
        }

        if (empty($data['oc']) && empty($data['roles'])) {
            throw new \InvalidArgumentException("JWT: At least one of 'oc' or 'roles' claims must be set");
        }

        if (isset($data['roles'])) {
            $instance->setRoles($data['roles']);
        }

        if (isset($data['oc'])) {
            $aclClaims = $data['oc'];
            $eventAcls = [];
            $seriesAcls = [];
            $playlistAcls = [];
            foreach ($aclClaims as $aclKey => $actions) {
                $identifier = substr($aclKey, 2);
                if (str_starts_with($aclKey, 'e:')) {
                    $eventAcls[$identifier] = $actions;
                } elseif (str_starts_with($aclKey, 's:')) {
                    $seriesAcls[$identifier] = $actions;
                } elseif (str_starts_with($aclKey, 'p:')) {
                    $playlistAcls[$identifier] = $actions;
                }
            }
            $instance->setEventAcls($eventAcls);
            $instance->setSeriesAcls($seriesAcls);
            $instance->setPlaylistAcls($playlistAcls);
        }

        return $instance;
    }

    /**
     * Generates a formatted DateTimeImmutable object from a Unix timestamp.
     * Using RFC 3339 standards formatting required by Opencast.
     *
     * @param int $timestamp The Unix timestamp to convert.
     * @return \DateTimeImmutable The formatted DateTimeImmutable object.
     */
    public static function generateFormattedDateTimeObject(int $timestamp): \DateTimeImmutable
    {
        $dt = new DateTimeImmutable();
        $dt->setTimestamp($timestamp);

        $dtFormatted = DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339, $dt->format(DateTimeInterface::RFC3339));

        return $dtFormatted;
    }
}
