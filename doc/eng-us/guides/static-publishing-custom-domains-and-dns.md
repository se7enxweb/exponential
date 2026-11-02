# Static publishing: custom domains and DNS

This guide is for administrators who serve a static copy at their own domain instead of `<owner>.github.io`. It
covers the target's settings, the DNS records, the DNS check on the console and in the wizard, and HTTPS.

[Guides](README.md) · Related: [Use your own domain](../help/static-publishing/use-your-own-domain.md),
[Links and base path](../help/static-publishing/links-and-base-path.md),
[Static publishing](../features/6.0/static-publishing.md#github)

## What you'll have at the end

A target that writes `CNAME`, links at the root of your domain, DNS records that point at GitHub Pages, and HTTPS
enforced.

## 1. The target

```ini
[Target_github-public]
CustomDomain=www.example.com
BasePath=
PublicURL=https://www.example.com/
Links=relative
```

- `CustomDomain` is written to the file `CNAME` at the root of the build (the extra is added when the setting is
  set). GitHub Pages reads it on every push; without it the domain is dropped.
- With a custom domain the site is served at the root, so `BasePath` stays empty. A base path left over from a
  project site makes style sheets and images go missing.
- `PublicURL` is used by the sitemap, `robots.txt`, canonical links and social cards.

Build and publish after changing these: the `CNAME` file and the absolute links are part of the build.

## 2. The records

| Domain | Record | Value |
|---|---|---|
| A subdomain, `www.example.com` | `CNAME` | `<owner>.github.io` (the account or organization, never the repository) |
| The apex, `example.com` | `A`, all four | `185.199.108.153`, `185.199.109.153`, `185.199.110.153`, `185.199.111.153` |
| The apex, optional | `AAAA` | `2606:50c0:8000::153`, `2606:50c0:8001::153`, `2606:50c0:8002::153`, `2606:50c0:8003::153` |

Prefer the subdomain: it needs one record and follows GitHub if its addresses change. Set both names, and GitHub
redirects one to the other. Remove old `A` and `AAAA` records of the same name.

## 3. Check them

```bash
./console exp:static:dns www.example.com
./console exp:static:dns example.com --owner=octo-org
./console exp:static:dns www.example.com --format=json
```

The check looks up A, AAAA and CNAME, compares them with GitHub Pages' addresses and the expected
`<owner>.github.io`, and lists what to set when they differ. The owner comes from the target whose `CustomDomain` it
is, or from `--owner`. In the administration the same check is **Check the DNS** in step 6 of the wizard. DNS
changes can take as long as the records' TTL to be seen everywhere.

## 4. GitHub and HTTPS

With a token or a GitHub App credential:

```bash
./console exp:static:github setup github-public --plan    # what it would do
./console exp:static:github setup github-public           # sets the domain and enforces HTTPS
./console exp:static:github status github-public
```

GitHub issues the certificate once it sees the records, which can take up to a day; until then the setup reports
HTTPS as planned. Run it again later. Without the API: repository **Settings > Pages > Custom domain**, then
**Enforce HTTPS**.

Verify the domain for your account or organization on GitHub (**Settings > Pages > Verified domains**) so nobody
else can claim it if the repository is ever removed.

## Other hosts

`CNAME` is a GitHub Pages file. Other hosts (GitLab Pages, a web server serving the branch) set the domain in their own
settings; the target still needs an empty `BasePath` and the right `PublicURL`. You can leave `CustomDomain` empty
there.

## Troubleshooting

| Symptom | Fix |
|---|---|
| The domain answers 404 | `CNAME` is missing from the branch: set `CustomDomain`, build and publish |
| CSS and images missing at the domain | Empty `BasePath`, build and publish |
| A certificate warning | GitHub has not issued it yet; wait and run the setup again |
| The DNS check names foreign A records | Remove the old host's records |

## Next step

[Static publishing: limits and GitHub terms](static-publishing-limits-and-github-terms.md).
