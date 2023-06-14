<?php declare(strict_types=1);

namespace Shuwei\Core;

class PlatformRequest
{
    /**
     * Context headers
     */
    public const HEADER_VERSION_ID = 'sw-version-id';
    public const HEADER_CONTEXT_TOKEN = 'sw-context-token';
    public const HEADER_LANGUAGE_ID = 'sw-language-id';

    /**
     * Context attributes
     */
    public const ATTRIBUTE_ROUTE_SCOPE = '_routeScope';
    public const ATTRIBUTE_ACL = '_acl';
    public const ATTRIBUTE_CONTEXT_OBJECT = 'sw-context';
    public const ATTRIBUTE_LOGIN_REQUIRED = '_loginRequired';
    public const ATTRIBUTE_LOGIN_REQUIRED_ALLOW_GUEST = '_loginRequiredAllowGuest';

    /**
     * CSP
     */
    public const ATTRIBUTE_CSP_NONCE = '_cspNonce';
    /**
     * OAuth attributes
     */
    public const ATTRIBUTE_OAUTH_ACCESS_TOKEN_ID = 'oauth_access_token_id';
    public const ATTRIBUTE_OAUTH_CLIENT_ID = 'oauth_client_id';
    public const ATTRIBUTE_OAUTH_USER_ID = 'oauth_user_id';
    public const ATTRIBUTE_OAUTH_SCOPES = 'oauth_scopes';
}
