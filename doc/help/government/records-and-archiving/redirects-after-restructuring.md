---
title: Redirects after restructuring
audience: government, admin, editor, project-manager
category: public-sector
order: 200
summary: For public bodies reorganizing their site (a merger of departments, a new navigation, a relaunch) - a redirect plan with Exponential's aliases, global aliases and wildcards, testing with curl, and watching 404.log afterwards so cited addresses keep working.
keywords: redirects, restructuring, relaunch, merger, reorganization, redirect plan, wildcards, URL aliases, URL translator, 301, 404.log, testing, link rot, public sector
type: help
---
# Redirects after restructuring

This page is for administrators, editors and project managers. Public bodies reorganize: departments merge, services
move, sites relaunch. Addresses cited in letters and laws must keep leading somewhere. Moves and renames inside one
Exponential site redirect by themselves; restructurings that remove pages or change sites need a redirect plan
([Redirects and moved content: Relaunching a site](../../creators/website-creators/redirects-and-moved-content.md#relaunching-a-site-a-redirect-plan)).

## Contents

1. [The plan](#the-plan)
2. [Choosing the tool](#choosing-the-tool)
3. [Testing](#testing)
4. [After the change](#after-the-change)
5. [Related pages](#related-pages)

## The plan

1. **Collect the old addresses**: the sitemap, the search engines' list of indexed pages, the web server's logs, and
   addresses cited in your own documents.
2. **Decide a destination for each**: the same page, a better page, or a "gone" page explaining where the service went.
3. **Prefer moving to recreating**: a node moved keeps its id and its old address redirects automatically.
4. **Write patterns as wildcards** and single pages as aliases of their node.

| Old address | New destination | Kind |
|---|---|---|
| `/department-a/*` | `/merged-department/{1}` | wildcard |
| `/forms/permit.html` | node "Apply for a permit" | alias on the node |
| `/contact` | module view or page | global alias |

## Choosing the tool

| Tool | Where | Notes |
|---|---|---|
| Alias on the node | the node's **URL aliases** tab | for one old address |
| Global alias | **Setup > URL translator** | "Alias should redirect" for a 301 |
| Wildcard | **Setup > URL wildcards** | consulted only when no alias matches, in creation order; **Try an address** tests without changing anything; "Redirecting URL" for a redirect |
| Web server | the server or Velocity | host and scheme changes (`http` to `https`, old domain to new) |

## Testing

```bash
while read old; do
  curl -s -o /dev/null -w "$old %{http_code} %{redirect_url}\n" "https://www.example.gov$old"
done < old-addresses.txt
```

Every line should show `301` (or `200` where the address itself still exists) and the intended destination.

## After the change

- Watch **Setup > Log files** > "Not found requests (404)" for misses; the `reason=` field shows why the kernel found
  nothing ([Request logs](../../../features/6.0/request-logs.md)).
- Keep the redirects for years: cited documents live long.
- Record the plan with the change in your records.

## Related pages

- [Preserving URLs](preserving-urls.md)
- [Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)
- [Links, URL aliases, wildcards](../../../guides/urls-and-aliases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
