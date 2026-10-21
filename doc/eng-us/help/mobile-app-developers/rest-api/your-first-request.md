---
title: Your first request
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 110
summary: Make the first authenticated call to the REST content API of Exponential - check that the API answers, send a credential, read node 2 at v1 and v2, and reproduce the call from Kotlin, Swift and JavaScript.
keywords: first request, curl, REST, v1, v2, Authorization, Bearer, API key, OAuth, 401, node 2, Kotlin, Swift, JavaScript, fetch, mobile app, tutorial
type: help
---
# Your first request

This page is for app developers who want one working call before anything else. In about ten minutes you check that
the REST API answers, send a credential and read the root node, first with `curl`, then from app code.

## Contents

1. [Step 1: does the API answer?](#step-1-does-the-api-answer)
2. [Step 2: get a development credential](#step-2-get-a-development-credential)
3. [Step 3: read node 2](#step-3-read-node-2)
4. [Step 4: the same call from app code](#step-4-the-same-call-from-app-code)
5. [If it does not work](#if-it-does-not-work)

## Step 1: does the API answer?

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/api/ezp/v1/content/node/2
```

| Answer | Meaning |
|---|---|
| `401` | The API is there and wants a credential (the default). Continue. |
| `404` | `ezprestapi` is not active on that installation, or the path is wrong. Ask its administrator. |
| `200` | Authentication is switched off there; calls run as the default user. |

The `401` has no body and the header `WWW-Authenticate: OAuth realm='Exponential REST'`.

## Step 2: get a development credential

On a test installation where you have an account with the policy `apikey/create`, open **My account > API access**
(`/apikey/list`), make a key with the `read` scope and copy it; it is shown once
([Personal API keys for tools](../auth/personal-api-keys-for-tools.md)). Put it in an environment variable, not in a
file you commit:

```bash
export EXP_API_KEY='expk_...'
```

A personal API key is right for development. A published app uses OAuth2 for its users
([Choosing a credential](../auth/choosing-a-credential.md)).

## Step 3: read node 2

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/2
```

Both answer `200` with the same body: `metadata` (`objectName`, `classIdentifier`, `nodeId` 2 ...) and `links`
([Reading a node](reading-a-node.md)). Node 2 is the content root in a standard installation; on yours it may be
named differently.

With an OAuth2 access token instead of a key, send `Authorization: OAuth <token>`; the REST layer also accepts
`Bearer <token>` for OAuth tokens.

## Step 4: the same call from app code

The request is plain HTTPS with one header. Three sketches, to adapt to your project's HTTP client and error
handling:

```kotlin
// Kotlin, OkHttp
val request = Request.Builder()
    .url("https://example.com/api/ezp/v2/content/node/2")
    .header("Authorization", "Bearer $credential")
    .header("Accept", "application/json")
    .build()
client.newCall(request).execute().use { response ->
    if (response.code == 401) { /* sign in again */ }
    val json = JSONObject(response.body!!.string())
    val name = json.getJSONObject("metadata").getString("objectName")
}
```

```swift
// Swift, URLSession
var request = URLRequest(url: URL(string: "https://example.com/api/ezp/v2/content/node/2")!)
request.setValue("Bearer \(credential)", forHTTPHeaderField: "Authorization")
request.setValue("application/json", forHTTPHeaderField: "Accept")
let (data, response) = try await URLSession.shared.data(for: request)
guard (response as? HTTPURLResponse)?.statusCode == 200 else { /* handle the status */ return }
let json = try JSONSerialization.jsonObject(with: data) as? [String: Any]
```

```javascript
// JavaScript (React Native, a web app on the same origin)
const answer = await fetch('https://example.com/api/ezp/v2/content/node/2', {
  headers: { Authorization: 'Bearer ' + credential, Accept: 'application/json' },
});
if (answer.status !== 200) { /* handle the status */ }
const node = await answer.json();
console.log(node.metadata.objectName);
```

These sketches were not compiled for this page; they show the request, nothing more. Never hard-code the
credential: [Storing secrets on devices](../auth/storing-secrets-on-devices.md).

## If it does not work

| Symptom | Look at |
|---|---|
| `401` with a key you just made | Header name and scheme; a proxy that drops `Authorization` ([The Authorization header and proxies](../auth/authorization-header-and-proxies.md)) |
| `401` `{"error":"invalid_token"}` | A wrong, revoked or expired key, or the key sent in the URL instead of the header |
| `403` `insufficient_scope` | The key lacks the `read` scope |
| `403` `access_denied` | The key's owner may not read that node |
| `404` | `ezprestapi` not active, or no such node |

All statuses are in [Errors and status codes](errors-and-status-codes.md).

## Related pages

- [Reading a node](reading-a-node.md)
- [Listing children and pagination](listing-children-and-pagination.md)
- [REST API v1 and v2: examples with curl](../../../guides/rest-api-v1-and-v2.md#4-examples-with-curl)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
