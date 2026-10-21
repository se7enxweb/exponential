---
title: IIS
audience: admin, developer
category: examples
order: 80
summary: Eine web.config, mit der Microsoft IIS Exponential 6 über PHP per FastCGI und das Modul URL Rewrite ausliefert. Sie überträgt .htaccess_root Regel für Regel und nutzt die Anforderungsfilterung als zusätzliche Schutzschicht.
keywords: IIS, Windows, web.config, Umschreiberegeln, Anforderungsfilterung, Sicherheits-Header, Beispiel, Internet Information Services, URL Rewrite, FastCGI, php-cgi.exe, requestFiltering, hiddenSegments, customHeaders, outboundRules, rewrite rules, example
type: example
---
# IIS

Exponential läuft unter Windows mit IIS, PHP für Windows (dem nicht threadsicheren Build) über FastCGI und dem
IIS-Modul **URL Rewrite**. [`web.config`](../../../eng-us/examples/web-servers/web.config) im Wurzelverzeichnis der
Installation enthält alles: den PHP-Handler, die Umschreiberegeln von `.htaccess_root`, die Anforderungsfilterung und
die Sicherheits-Header.

> **Hinweis:** Dieses Beispiel ist abgeleitet, nicht unter IIS getestet: Geprüft wurde nur, ob das XML wohlgeformt ist
> (`xmllint`). Seine Umschreiberegeln sind die von `.htaccess_root`, deren Verhalten unter Apache und nginx getestet
> wurde. Testen Sie es auf Ihrem Server mit den Anfragen unten, bevor Sie sich darauf verlassen, und vergleichen Sie es
> nach jedem Upgrade mit `.htaccess_root`. Was über Windows bekannt ist, steht unter
> [Betriebssystemunterstützung](../../hilfe/betriebssystemunterstuetzung.md).

## Installieren

1. Installieren Sie IIS mit dem Feature **CGI** (es bringt FastCGI mit), das Modul
   [URL Rewrite 2.1](https://www.iis.net/downloads/microsoft/url-rewrite) und PHP für Windows (NTS, x64).
   Registrieren Sie `php-cgi.exe` unter den FastCGI-Einstellungen und setzen Sie in dessen `php.ini` die Werte
   `memory_limit`, `upload_max_filesize` und `post_max_size`.
2. Lassen Sie den physischen Pfad der Website auf das Wurzelverzeichnis der Installation zeigen (dort, wo `index.php`
   liegt).
3. Kopieren Sie die Datei dorthin als `web.config`; passen Sie `scriptProcessor` an Ihre `php-cgi.exe` an.
4. Geben Sie der Identität des Anwendungspools Schreibrechte auf `var/` (und Leserechte auf den Rest).
5. Binden Sie HTTPS mit Ihrem Zertifikat in den Bindungen der Website und richten Sie auf der Bindung für Port 80 eine
   Weiterleitung von HTTP auf HTTPS ein (mit URL Rewrite oder dem Feature HTTP-Umleitung).

## Die Datei

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
  Exponential 6: IIS (Internet Information Services) with PHP over FastCGI

  Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md; rules derived from .htaccess_root (GPL v2 or later).

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
          <match url="^(var/([^/]+/)?storage/images(-versioned)?/.*|var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$|var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$|var/([^/]+/)?cache/(texttoimage|public)/.*|design/[^/]+/(stylesheets|images|javascript|fonts)/.*|share/icons/.*|extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$|extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/.*|packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.*|packages/styles/.+/thumbnail/.*|var/storage/packages/.+\.(png|jpe?g|gif|webp)$|favicon\.ico|design/standard/images/favicon\.ico|robots\.txt|index\.js$|sw\.js$|w3c/p3p\.xml)" />
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

## Wie die Regeln einander entsprechen

| `.htaccess_root` | `web.config` |
|---|---|
| Punkt-Regel, `[R=404,L]` | Regel `exp-dot-paths`, `CustomResponse` 404 (IIS URL Rewrite kennt Lookahead, das Muster ist dasselbe) |
| Skript-Regel mit `%{REQUEST_FILENAME} -f` | Regel `exp-scripts-below-root` mit `{REQUEST_FILENAME}` `IsFile` |
| `api/`, `index_rest.php`, Baummenü | Regeln `exp-rest-api`, `exp-rest-direct`, `exp-treemenu` |
| die Regeln `- [L]` für statische Dateien | eine Regel `exp-static` mit `action type="None"`: Die Datei wird so ausgeliefert, wie sie ist |
| `RewriteRule .* index.php` | Regel `exp-front-controller` |
| der Block `FilesMatch` für SVG | ausgehende Regel `exp-svg-sandbox` auf Antworten vom Typ `image/svg+xml` |

IIS URL Rewrite vergleicht den Pfad **ohne** seinen führenden Schrägstrich, wie Apache in einer `.htaccess`. Die Muster
sind deshalb unverändert die von `.htaccess_root`.

Die **Anforderungsfilterung** antwortet mit 404, bevor irgendeine Umschreiberegel läuft. Sie ist eine zusätzliche
Schutzschicht: Schon die Umschreiberegeln allein schicken `settings/`, `var/`, `vendor/`, `kernel/` und `composer.json`
an `index.php`. `hiddenSegments` trifft ein Segment *irgendwo* im Pfad, deshalb stehen dort nur Namen, die keine Seite
einer Website haben kann (`.git`, `.env`, `.htaccess`, `composer.json`, `composer.lock`): Mit `settings` oder `doc` in
der Liste wäre auch eine Seite wie `/about/settings` verborgen. Die Liste `fileExtensions` verweigert `.ini`, `.db`,
`.sqlite`, `.log`, `.phtml` und `.phar` überall.

**Header.** `customHeaders` fügt die Standard-Sicherheits-Header des Kernels jeder Antwort hinzu, auch denen von PHP.
Halten Sie die Werte gleich mit `site.ini [HTTPHeaderSettings] SecurityHeaders[]` oder entfernen Sie den Header an
einer der beiden Stellen, damit eine Seite nie zwei verschiedene Werte trägt.

## Prüfen

```powershell
foreach ($p in '/.git/config','/.env','/kernel/classes/ezini.php','/extension/ezjscore/ezinfo.php') {
  try { (Invoke-WebRequest "https://example.com$p" -UseBasicParsing).StatusCode } catch { $_.Exception.Response.StatusCode.value__ }
}   # 404 each
(Invoke-WebRequest https://example.com/settings/site.ini -UseBasicParsing).Content -match '\[DatabaseSettings\]'   # False
```

## Verwandte Seiten

- [Apache .htaccess](apache-htaccess.md): die Regeln, eine nach der anderen erklärt.
- [Kapitel 13, Abschnitt 13.2: Was der Webserver niemals ausliefern darf](../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf).
- [Betriebssystemunterstützung](../../hilfe/betriebssystemunterstuetzung.md), [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite dürfen Sie frei kopieren, übersetzen und verbessern.
