---
title: IIS
audience: admin, developer
category: examples
order: 80
summary: A web.config for serving Exponential 6 with Microsoft IIS, PHP over FastCGI and the URL Rewrite module, translating .htaccess_root rule for rule, with request filtering as defense in depth.
keywords: IIS, Internet Information Services, Windows, web.config, URL Rewrite, FastCGI, php-cgi.exe, requestFiltering, hiddenSegments, customHeaders, outboundRules, rewrite rules, example
type: example
---
# IIS

Exponential runs on Windows with IIS, PHP for Windows (the non-thread-safe build) over FastCGI and the IIS
**URL Rewrite** module. [`web.config`](web.config) in the installation root carries everything: the PHP handler, the
rewrite rules of `.htaccess_root`, request filtering and the security headers.

> **Note:** This example is derived, not tested on IIS: it was checked for well-formed XML only (`xmllint`). Its
> rewrite rules are `.htaccess_root`'s, whose behavior was tested in Apache and nginx. Test it on your server with the
> requests below before you rely on it, and compare it with `.htaccess_root` after every upgrade. See
> [Operating system support](../../help/os-support.md) for what is known about Windows.

## Install it

1. Install IIS with the **CGI** feature (it brings FastCGI), the
   [URL Rewrite 2.1](https://www.iis.net/downloads/microsoft/url-rewrite) module, and PHP for Windows (NTS, x64).
   Register `php-cgi.exe` under FastCGI Settings and give it `memory_limit`, `upload_max_filesize` and
   `post_max_size` in its `php.ini`.
2. Point the site's physical path at the installation root (where `index.php` is).
3. Copy the file there as `web.config`; adjust `scriptProcessor` to your `php-cgi.exe`.
4. Give the application pool's identity write access to `var/` (and read access to the rest).
5. Bind HTTPS with your certificate in the site's bindings, and add an HTTP-to-HTTPS redirect (URL Rewrite or
   the HTTP Redirect feature) on the port 80 binding.

## The file

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
  Exponential 6: IIS (Internet Information Services) with PHP over FastCGI

  Free documentation: GFDL 1.3 or later, see doc/en/us/README-LICENSE.md; rules derived from .htaccess_root (GPL v2 or later).

  Derived, not shipped by the kernel, and not tested on IIS here: a
  translation of .htaccess_root (this release) into IIS URL Rewrite rules,
  checked for well-formed XML only. Test it on your server before you rely
  on it, and compare it with .htaccess_root after every upgrade.
  Explained in iis.md next to this file.

  Needs: the IIS URL Rewrite module 2.x, the CGI feature (FastCGI) and PHP
  for Windows (NTS build) registered in FastCGI Settings. Put this file as
  web.config in the installation root (the site's physical path, where
  index.php is). Adjust scriptProcessor to your php-cgi.exe.
-->
<configuration>
  <system.webServer>

    <defaultDocument enabled="true">
      <files>
        <clear />
        <add value="index.php" />
      </files>
    </defaultDocument>

    <!-- PHP over FastCGI. Only the front controllers are ever reached:
         the rewrite rules below send nothing else to a .php file. -->
    <handlers>
      <remove name="PHP_via_FastCGI" />
      <add name="PHP_via_FastCGI" path="*.php" verb="GET,HEAD,POST,PUT,PATCH,DELETE,OPTIONS"
           modules="FastCgiModule" scriptProcessor="C:\PHP\php-cgi.exe"
           resourceType="File" requireAccess="Script" />
    </handlers>

    <!-- Defense in depth. Request filtering runs before URL Rewrite and answers
         404 for these names and suffixes whatever the rules below say. The
         rules alone already keep settings/, var/, vendor/, kernel/ and the
         rest from being sent (every path not on the static list is rewritten
         to index.php). hiddenSegments matches a segment ANYWHERE in the path,
         so it lists only names no URL alias of a site can have: adding
         "settings", "update" or "doc" would also hide pages such as
         /about/settings. Uploads: maxAllowedContentLength in bytes (64 MB). -->
    <security>
      <requestFiltering allowDoubleEscaping="false">
        <requestLimits maxAllowedContentLength="67108864" />
        <hiddenSegments applyToWebDAV="true">
          <add segment=".git" />
          <add segment=".env" />
          <add segment=".htaccess" />
          <add segment="composer.json" />
          <add segment="composer.lock" />
        </hiddenSegments>
        <fileExtensions allowUnlisted="true">
          <add fileExtension=".ini" allowed="false" />
          <add fileExtension=".db" allowed="false" />
          <add fileExtension=".sqlite" allowed="false" />
          <add fileExtension=".log" allowed="false" />
          <add fileExtension=".phtml" allowed="false" />
          <add fileExtension=".phar" allowed="false" />
        </fileExtensions>
      </requestFiltering>
    </security>

    <rewrite>
      <rules>
        <!-- 1. No dot file or dot directory, anywhere; .well-known stays reachable -->
        <rule name="exp-dot-paths" stopProcessing="true">
          <match url="(^|/)\.(?!well-known(/|$))" />
          <action type="CustomResponse" statusCode="404" statusReason="Not Found" statusDescription="Not Found" />
        </rule>
        <!-- 2. No script below the root runs because its path was asked for -->
        <rule name="exp-scripts-below-root" stopProcessing="true">
          <match url="^.+/[^/]+\.(php[0-9]?|phtml|phar)$" />
          <conditions>
            <add input="{REQUEST_FILENAME}" matchType="IsFile" />
          </conditions>
          <action type="CustomResponse" statusCode="404" statusReason="Not Found" statusDescription="Not Found" />
        </rule>
        <!-- 3. Front controllers -->
        <rule name="exp-rest-api" stopProcessing="true">
          <match url="^api/" />
          <action type="Rewrite" url="index_rest.php" />
        </rule>
        <rule name="exp-rest-direct" stopProcessing="true">
          <match url="^index_rest\.php" />
          <action type="None" />
        </rule>
        <rule name="exp-treemenu" stopProcessing="true">
          <match url="^([^/]+/)?content/treemenu.*" />
          <action type="Rewrite" url="index_treemenu.php" />
        </rule>
        <!-- 4. Files served as they are: the list of .htaccess_root -->
        <rule name="exp-static" stopProcessing="true">
          <match url="^(var/([^/]+/)?storage/images(-versioned)?/.*|var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$|var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$|var/([^/]+/)?cache/(texttoimage|public)/.*|design/[^/]+/(stylesheets|images|javascript|fonts)/.*|share/icons/.*|extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/.*|packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.*|packages/styles/.+/thumbnail/.*|var/storage/packages/.+\.(png|jpe?g|gif|webp)$|favicon\.ico|design/standard/images/favicon\.ico|robots\.txt|index\.js$|sw\.js$|w3c/p3p\.xml)" />
          <action type="None" />
        </rule>
        <!-- With site.ini [FileSettings] CacheVarDir=var_cache (multi-site hosting),
             add var_cache/([^/]+/)?cache/(texttoimage|public)/.* to the list above. -->
        <!-- 5. Everything else goes to index.php -->
        <rule name="exp-front-controller" stopProcessing="true">
          <match url=".*" />
          <action type="Rewrite" url="index.php" />
        </rule>
      </rules>
      <outboundRules>
        <!-- Uploaded SVG images run nothing and load nothing when opened directly -->
        <rule name="exp-svg-sandbox" preCondition="exp-is-svg">
          <match serverVariable="RESPONSE_Content_Security_Policy" pattern=".*" />
          <action type="Rewrite" value="sandbox; default-src 'none'; style-src 'unsafe-inline'" />
        </rule>
        <preConditions>
          <preCondition name="exp-is-svg">
            <add input="{RESPONSE_CONTENT_TYPE}" pattern="^image/svg\+xml" />
          </preCondition>
        </preConditions>
      </outboundRules>
    </rewrite>

    <!-- The values of the kernel's site.ini [HTTPHeaderSettings] SecurityHeaders[]
         for the files IIS serves itself. IIS adds them to PHP's answers too:
         remove the same header from SecurityHeaders[] in site.ini, or keep both
         equal, so a page does not carry two different values. -->
    <httpProtocol>
      <customHeaders>
        <remove name="X-Powered-By" />
        <add name="X-Content-Type-Options" value="nosniff" />
        <add name="X-Frame-Options" value="SAMEORIGIN" />
        <add name="Referrer-Policy" value="strict-origin-when-cross-origin" />
        <add name="Permissions-Policy" value="camera=(), microphone=(), payment=(), usb=()" />
      </customHeaders>
    </httpProtocol>

    <staticContent>
      <remove fileExtension=".webp" />
      <mimeMap fileExtension=".webp" mimeType="image/webp" />
      <remove fileExtension=".woff2" />
      <mimeMap fileExtension=".woff2" mimeType="font/woff2" />
    </staticContent>

  </system.webServer>
</configuration>
```

## How it maps

| `.htaccess_root` | `web.config` |
|---|---|
| dot rule, `[R=404,L]` | rule `exp-dot-paths`, `CustomResponse` 404 (IIS URL Rewrite has look-ahead, the pattern is the same) |
| script rule with `%{REQUEST_FILENAME} -f` | rule `exp-scripts-below-root` with `{REQUEST_FILENAME}` `IsFile` |
| `api/`, `index_rest.php`, tree menu | rules `exp-rest-api`, `exp-rest-direct`, `exp-treemenu` |
| the `- [L]` asset rules | one rule `exp-static` with `action type="None"`: the file is served as it is |
| `RewriteRule .* index.php` | rule `exp-front-controller` |
| the SVG `FilesMatch` block | outbound rule `exp-svg-sandbox` on `image/svg+xml` responses |

IIS URL Rewrite matches the path **without** its leading slash, as Apache does in `.htaccess`, so the patterns are
`.htaccess_root`'s unchanged.

**Request filtering** answers 404 before any rewrite rule runs. It is defense in depth: the rewrite rules alone
already send `settings/`, `var/`, `vendor/`, `kernel/` and `composer.json` to `index.php`. `hiddenSegments` matches a
segment *anywhere* in a path, so it lists only names no page of a site can have (`.git`, `.env`, `.htaccess`,
`composer.json`, `composer.lock`): listing `settings` or `doc` would also hide a page such as `/about/settings`. The
`fileExtensions` list refuses `.ini`, `.db`, `.sqlite`, `.log`, `.phtml` and `.phar` everywhere.

**Headers.** `customHeaders` adds the kernel's default security headers to every response, PHP's included. Keep the
values equal to `site.ini [HTTPHeaderSettings] SecurityHeaders[]`, or remove the header from one of the two, so a page
never carries two different values.

## Check it

```powershell
foreach ($p in '/.git/config','/.env','/kernel/classes/ezini.php','/extension/ezjscore/ezinfo.php') {
  try { (Invoke-WebRequest "https://example.com$p" -UseBasicParsing).StatusCode } catch { $_.Exception.Response.StatusCode.value__ }
}   # 404 each
(Invoke-WebRequest https://example.com/settings/site.ini -UseBasicParsing).Content -match '\[DatabaseSettings\]'   # False
```

## Related pages

- [Apache .htaccess](apache-htaccess.md): the rules explained one by one.
- [Chapter 13, section 13.2: What the web server must never hand out](../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out).
- [Operating system support](../../help/os-support.md), [Web server examples](README.md) and [all examples](../README.md).
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
