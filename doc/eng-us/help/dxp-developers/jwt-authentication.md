---
title: JWT authentication
audience: dxp-developer
category: dxp-development
order: 26
summary: Token authentication for the DXP's REST and GraphQL APIs with lexik/jwt-authentication-bundle - generating and rotating the keypair, the environment variables, the firewalls the recipe writes commented out, getting a token, and keeping the keys safe.
keywords: JWT, JSON Web Token, lexik/jwt-authentication-bundle, keypair, lexik:jwt:generate-keypair, JWT_PASSPHRASE, firewall, security.yaml, token, REST, GraphQL, rotation
type: help
---
# JWT authentication

This page is for developers who let apps and services sign in to the DXP's APIs with tokens. The DXP requires
`lexik/jwt-authentication-bundle`; the skeleton's guide (section 10) says the keypair is "required for the REST API
to function" and is generated on every installation.

## Contents

1. [The keypair](#the-keypair)
2. [Turning on the JWT firewalls](#turning-on-the-jwt-firewalls)
3. [Getting and using a token](#getting-and-using-a-token)
4. [Rotating the keys](#rotating-the-keys)
5. [Keeping it safe](#keeping-it-safe)

## The keypair

```bash
php bin/console lexik:jwt:generate-keypair
# writes config/jwt/private.pem and config/jwt/public.pem
```

The paths and the passphrase come from `.env.local`:

```bash
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<64 random hex characters>
```

The key files are ignored by Git. Back them up securely: when they are lost, every token is invalid.

## Turning on the JWT firewalls

In an installed Platform v5 project, the recipe's `config/packages/security.yaml` contains three JWT firewalls,
commented out, under the line "Uncomment the rules below to enable JSON Web Token (JWT) authentication for REST
and/or GraphQL":

| Firewall | Purpose |
|---|---|
| `ibexa_jwt_rest` | Issues tokens: a JSON login on the route `ibexa.rest.create_token` |
| `ibexa_jwt_rest.api` | Accepts tokens on REST requests that carry an `Authorization` header |
| `ibexa_jwt_graphql` | Accepts tokens on GraphQL requests from outside the administration |

Uncomment the ones you need, keep their order before the general firewalls, and clear the cache. Check your own
project's file: if the recipe wrote it differently, follow your file.

## Getting and using a token

The token route `ibexa.rest.create_token` is `/user/token/jwt` under the REST prefix
([The REST API](the-rest-api.md#where-it-lives)). The login fields are `JWTInput.username` and `JWTInput.password`:

```bash
BASE="https://127.0.0.1:8000/api/ibexa/v2"       # your prefix
curl -s -X POST "$BASE/user/token/jwt" \
  -H "Content-Type: application/vnd.ibexa.api.JWTInput+json" \
  -H "Accept: application/vnd.ibexa.api.JWT+json" \
  -d '{"JWTInput": {"username": "api-reader", "password": "..."}}'

curl -s "$BASE/content/locations/1/2" \
  -H "Accept: application/vnd.ibexa.api.Location+json" \
  -H "Authorization: Bearer <token>"
```

Create a dedicated user for each client, with a role that allows only what the client needs. Never use the
administrator account for an API client.

## Rotating the keys

```bash
php bin/console lexik:jwt:generate-keypair --overwrite
php bin/console cache:clear
```

Existing tokens stop working; clients sign in again. Plan rotation like a deploy.

## Keeping it safe

- HTTPS only; a token is a password for its lifetime.
- `JWT_PASSPHRASE` and the key files never go into Git, tickets or chat.
- Short token lifetimes for clients that run on users' devices.
- `JWT authentication errors` after a deploy usually mean missing or unreadable key files
  ([Troubleshooting](troubleshooting-dxp.md)).

## Related pages

- [The REST API](the-rest-api.md)
- [GraphQL and GraphiQL](graphql-and-graphiql.md)
- [Environment variables](environment-variables.md#jwt)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
