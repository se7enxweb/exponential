# Content Security Policy

A Content Security Policy tells the browser which scripts, stylesheets, images and connections a page may use. A
strict policy (`script-src 'self'`, no `'unsafe-inline'`) means that a script injected into a page through a stored
or reflected value does not run, even where a template forgets to escape it.

Exponential sends a policy from `site.ini [ContentSecurityPolicy]`, beside the security headers of
`[HTTPHeaderSettings] SecurityHeaders[]` (see [Security hardening](../../install/13-security-hardening.md), 13.9).
By default it is **report-only on the administration** and off on the sites.

## Settings

```ini
[ContentSecurityPolicy]
# disabled | report-only | enforce | auto
Mode=auto
Directives[]
Directives[default-src]='self'
Directives[script-src]='self'
Directives[style-src]='self'
Directives[img-src]='self' data: blob:
Directives[font-src]='self' data:
Directives[media-src]='self' blob:
Directives[connect-src]='self'
Directives[frame-src]='self'
Directives[object-src]='none'
Directives[base-uri]='self'
Directives[form-action]='self'
Directives[frame-ancestors]='self'
ReportURI=
```

- **Mode**
  - `disabled`: no policy.
  - `report-only`: the header `Content-Security-Policy-Report-Only`. The browser reports what the policy would block
    (in its developer console, and to `ReportURI` when set) and blocks nothing.
  - `enforce`: the header `Content-Security-Policy`. The browser blocks what the policy does not allow. It is sent as a
    second policy after `SecurityHeaders[Content-Security-Policy]` (`frame-ancestors 'self'` by default); the browser
    applies both.
  - `auto` (the default): `report-only` on administration siteaccesses, those whose `[DesignSettings] SiteDesign`
    begins with `admin` (admin, admin3, admin4, admin4l, adminui), `disabled` on all others.
- **Directives[]**: the policy, one directive per entry. Set an entry empty in an override to leave the directive out;
  an override that starts with `Directives[]` alone replaces the whole list. An entry whose value holds `;` or `,`, or
  whose name is not a directive name, is ignored, so a setting cannot split the header or add a directive of its own.
- **ReportURI**: where the browser posts its reports (`report-uri`), for example a collector of your own. Empty: the
  developer console only.

Per path, `[HTTPHeaderSettings] HeaderList` still overrides any header, the policy included. Pages answered from the
kernel's HTTP cache carry the same headers.

## Turning it on

1. Leave the administration in `report-only` and work with it for a while: open the developer console (or collect the
   reports with `ReportURI`) and note what the policy would block. Extensions and site designs of your own are the
   usual source.
2. For a site, set `Mode=report-only` in its siteaccess and do the same.
3. When nothing is reported for the pages you use, set `Mode=enforce`.

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
Mode=enforce
```

## The directives shipped

- `script-src 'self'` allows script files of this site only: no inline `<script>`, no `on...=` attribute, no
  `javascript:` link, no `eval()`.
- `style-src 'self'` allows stylesheet files only: no `<style>` element and no `style=""` attribute. A script that sets
  `element.style` is not affected.
- `img-src` and `media-src` add `data:` and `blob:` (previews of files picked for an upload, icons embedded in
  stylesheets); `font-src` adds `data:`.
- `object-src 'none'`, `base-uri 'self'`, `form-action 'self'`, `frame-ancestors 'self'` close the plugin, base URL,
  form target and framing paths.

A page that needs more (a map service, a font host, an analytics script) adds the host to the directive in its
siteaccess, for example `Directives[script-src]='self' https://maps.example.com`.
