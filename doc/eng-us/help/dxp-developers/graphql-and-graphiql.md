---
title: GraphQL and GraphiQL
audience: dxp-developer
category: dxp-development
order: 27
summary: The GraphQL API of the DXP (ibexa/graphql) - the endpoint, the schema generated from your content types into config/graphql/types/ibexa, when to regenerate it, the GraphiQL explorer in development, authentication and good query habits.
keywords: GraphQL, GraphiQL, ibexa/graphql, overblog/graphiql-bundle, /graphql, /graphiql, ibexa:graphql:generate-schema, schema, content types, JWT, headless
type: help
---
# GraphQL and GraphiQL

This page is for developers who query content with GraphQL. The DXP ships `ibexa/graphql` and the GraphiQL explorer
(`overblog/graphiql-bundle`). The schema is "auto-generated from the content type model" (skeleton guide, section 11).

## Contents

1. [The endpoint](#the-endpoint)
2. [The schema](#the-schema)
3. [The explorer](#the-explorer)
4. [Authentication](#authentication)
5. [A first query](#a-first-query)
6. [Good habits](#good-habits)

## The endpoint

`/graphql`, defined in `config/routes/graphql.yaml` (route `overblog_graphql_endpoint`). Clients send `POST` requests
with a JSON body that holds the query.

## The schema

Generate it after the install and after every change to content types or field types:

```bash
php bin/console ibexa:graphql:generate-schema
php bin/console cache:clear
```

The command writes YAML type files into `config/graphql/types/ibexa/` of your project (the directory the package's
extension class sets). Commit those files: they are part of your API. A content type becomes a GraphQL type and its
fields become typed fields, so renaming a field identifier changes your API.

The command still carries its upstream name; there is no `exponential:` alias for it
([Console commands](console-commands-and-renames.md)).

## The explorer

In `APP_ENV=dev`, the route file `config/routes/dev/graphiql.yaml` loads the GraphiQL bundle's routes; in an installed
v5 project the explorer answers at **`/graphiql`**. The skeleton's guide names `/graphql/explorer`; check your
project with `php bin/console debug:router | grep graphiql`. The explorer is not routed in production.

## Authentication

Without credentials, queries run as the Anonymous user and see what that role may read. Apps use JWT: the recipe's
`security.yaml` has a commented `ibexa_jwt_graphql` firewall for this ([JWT authentication](jwt-authentication.md)).

## A first query

Open the explorer, use its documentation panel to see the types your content model produced, and start from there.
The type and field names depend on your content types, so copy them from the panel instead of from an example.

## Good habits

- Ask only for the fields you need; GraphQL makes that easy, and it keeps responses small.
- Regenerate and commit the schema in the same change as a content type change, so code and API stay in step.
- Test queries in CI against a project installed from your seed data.
- For write-heavy integrations, compare with the REST API ([The REST API](the-rest-api.md)).

## Related pages

- [Headless with the DXP](headless-with-the-dxp.md)
- [Content, content types and locations](content-types-and-locations.md)
- [JWT authentication](jwt-authentication.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
