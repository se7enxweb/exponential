---
title: Standards der Webplattform
audience: developer, admin
category: standards
order: 50
summary: Exponential gemessen an den Standards der Webplattform, die es spricht: HTTP-Semantik, Caching und HTTP/2, TLS, HTML, CSS, ECMAScript, URLs, Medientypen, Cookies, Robots und Sitemaps, RSS- und Atom-Feeds sowie Open Graph.
keywords: Webstandards, Webplattform, HTTP, RFC 9110, RFC 9111, RFC 9112, RFC 9113, HTTP/2, TLS, RFC 8446, HTML, WHATWG, Doctype, CSS, Custom Properties, prefers-color-scheme, Dunkelmodus, ECMAScript, JavaScript, jQuery 4, URL, RFC 3986, MIME, Medientypen, Cookies, RFC 6265, SameSite, robots.txt, RFC 9309, Sitemaps, RSS 2.0, Atom, RFC 4287, Open Graph, Velocity, web standards, web platform, dark mode, media types
type: reference
---
# Standards der Webplattform

Diese Seite richtet sich an Entwickler und Administratoren, die wissen möchten, welchen Standards der Webplattform
Exponential 6.0.x folgt und wie weit. Für jeden Standard nennt sie die Version, den Code oder die Einstellung, die ihn
umsetzt, einen Status und die bekannten Lücken. Wo Exponential hinter einem anderen Webserver läuft (Apache, nginx,
FrankenPHP), gehört die Protokollschicht diesem Server; die Aussagen zu HTTP/2 und TLS unten gelten für Exponential
Velocity, den Webserver, der mit Exponential ausgeliefert wird.

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| HTTP-Semantik | RFC 9110 (2022) | **Teilweise erfüllt** | Statuscodes, `Content-Type` mit Zeichensatz, `Content-Language`, Bereichsanfragen für Downloads; veraltete Standards in Headern |
| Nachrichtensyntax von HTTP/1.1 | RFC 9112 (2022) | **Erfüllt** (Velocity) | Velocity lehnt mehrdeutige Rahmung ab (`Content-Length` mit `Transfer-Encoding`) |
| HTTP-Caching | RFC 9111 (2022) | **Teilweise erfüllt** | rollenbewusster Seiten-Cache, `Cache-Control: private` für persönliche Antworten, `Last-Modified`/304 für Feeds; veraltete Standards für `Pragma`/`Expires` |
| HTTP/2 | RFC 9113 (2022), HPACK RFC 7541 | **Erfüllt** (Velocity) | Aushandlung per ALPN, Prüfung von Frames und HPACK; siehe die Velocity-Spezifikation |
| TLS | TLS 1.2 (RFC 5246) / 1.3 (RFC 8446) | **Teilweise erfüllt** | Velocity verwaltet Zertifikate (ACME, eigene Dateien); Mindestversion für den PHP-Server **nicht verifiziert** |
| HTML | WHATWG HTML Living Standard | **Teilweise erfüllt** | `<!DOCTYPE html>` in jedem Kern-Pagelayout; keine HTML-Validierung in der CI |
| CSS | W3C CSS Snapshot 2024 (Custom Properties, Media Queries Level 5) | **Erfüllt** (admin4) | Tokens als Custom Properties, `prefers-color-scheme`, `forced-colors`, `prefers-reduced-motion`; kein Inline-CSS |
| ECMAScript | ECMA-262 (im Stil von ES5.1, läuft auf aktuellen Engines) | **Erfüllt** | Admin-Skripte in einfachem JavaScript im ES5-Stil mit jQuery 4.0.0; Syntax in der CI von Node.js geprüft |
| URI / URL | RFC 3986, WHATWG URL Standard | **Teilweise erfüllt** | URL-Aliase in sichere Zeichen umgewandelt; IRI-Modus optional |
| Medientypen | RFC 6838, IANA-Registry | **Teilweise erfüllt** | `settings/mime.ini`, Content Sniffing für Bilder, `nosniff`; Atom als `application/xml` ausgeliefert |
| HTTP-Cookies | RFC 6265 (+ `SameSite` aus dem Entwurf RFC 6265bis) | **Erfüllt** | `HttpOnly`, `Secure`, `SameSite=Lax` für das Session-Cookie |
| Robots Exclusion | RFC 9309 (2022) | **Erfüllt** | mitgelieferte `robots.txt` im Web-Wurzelverzeichnis |
| Sitemaps | Protokoll sitemaps.org 0.9 | **Erfüllt** (mit `bcgooglesitemaps`) | `xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"` |
| RSS / Atom | RSS 2.0, RSS 1.0 (RDF), Atom 1.0 (RFC 4287) | **Teilweise erfüllt** | `kernel/classes/ezrssexport.php` schreibt alle drei; Medientyp für Atom nicht `application/atom+xml` |
| Open Graph | Das Open Graph Protocol (ogp.me) | **Teilweise erfüllt** | `xrowmetadata` gibt `og:image` und seine Eigenschaften aus; `og:title`, `og:type`, `og:url` bleiben dem Design überlassen |

## HTTP-Semantik (RFC 9110) und Nachrichtensyntax (RFC 9112)

**Was es ist.** RFC 9110 "HTTP Semantics" und RFC 9112 "HTTP/1.1" (IETF, Juni 2022) legen Methoden, Statuscodes,
Header-Felder und das Übertragungsformat von HTTP/1.1 fest.

**Wie Exponential es erfüllt.**

- Jede Seite wird von `kernel/private/classes/ezpkernelweb.php` mit `Content-Type: text/html; charset=...` und
  `Content-Language` aus dem Siteaccess beantwortet, dazu die Sicherheitsheader aus
  `[HTTPHeaderSettings] SecurityHeaders[]`.
- Datei-Downloads (`lib/ezfile/classes/ezfile.php`) beantworten `Range`-Anfragen mit `206 Partial Content`,
  `Content-Range` und `Accept-Ranges: bytes`.
- Feeds beantworten bedingte Anfragen mit `304 Not Modified` (`kernel/private/classes/views/rss/feed.php`).
- Die Abmeldung mit einem Klick antwortet mit `200 text/plain`, ein defekter Link mit `400` (siehe
  [Datenschutzstandards](datenschutz.md)).
- **Velocity** lehnt eine Anfrage mit sowohl `Content-Length` als auch `Transfer-Encoding` oder mit zwei
  unterschiedlichen `Content-Length`-Werten mit `400` ab (RFC 9112 Abschnitt 6.1, Request Smuggling) und begrenzt die
  Größe von Kopf und Inhalt (`413`) ([Velocity: HTTP/2 und Sicherheit](../spezifikationen/6.0/velocity-http2-und-sicherheit.md)).

**Status.** **Teilweise erfüllt** (Exponential), **Erfüllt** (Rahmung in Velocity).

**Bekannte Lücken.**

- Die Standard-Header einer Seite tragen noch ein `Expires`-Datum aus dem Jahr 1997, `Pragma: no-cache` und ein
  `Last-Modified`, das auf den Zeitpunkt der Anfrage gesetzt ist, geerbt aus der Codebasis von eZ Publish 4;
  `Last-Modified` sagt daher nichts über den Inhalt aus. Überschreiben Sie das pro Pfad mit
  `[HTTPHeaderSettings] CustomHeader=enabled` und `HeaderList[]`.
- Der Download einer Datei sendet absichtlich ein leeres `Last-Modified` (Kompatibilität beim Fortsetzen).

## HTTP-Caching (RFC 9111)

**Was es ist.** RFC 9111 "HTTP Caching" (IETF, Juni 2022) legt fest, wie Browser und gemeinsam genutzte Caches
Antworten speichern und wiederverwenden (`Cache-Control`, Validatoren, `Vary`).

**Wie Exponential es erfüllt.**

- Der eigene rollenbewusste Seiten-Cache des Kernels (`settings/httpcache.ini`, `kernel/private/classes/httpcache/`)
  wird von Apache, PHP-FPM und Velocity gemeinsam genutzt. Eine Antwort, die von einem Benutzer abhängt, wird mit
  `Cache-Control: private` markiert, sodass kein gemeinsam genutzter Cache sie behält; dieselbe Regel gilt, wenn das
  Session-Cookie mit jeder Antwort gesendet wird (`[Session] CookieAlwaysAddToHttpResponse`)
  ([HTTP-Cache](../../eng-us/bc/6.0/httpcache.md)).
- Purge-Tags für einen vorgeschalteten Cache (Varnish `xkey`, `Surrogate-Key`, `Cache-Tag`) mit `[HttpCacheSettings]
  TagHeader` (standardmäßig aus).
- `Cache-Control`, `Pragma` und `Expires` pro Pfad mit `[HTTPHeaderSettings] HeaderList[]`.
- Feeds verwenden `Last-Modified` / `If-Modified-Since`.

**Status.** **Teilweise erfüllt.**

**Bekannte Lücken.** Seiten tragen standardmäßig `Cache-Control: no-cache, must-revalidate` und kein `ETag`, sodass
Browser jede Seite ohne einen Validator zum Vergleichen neu validieren; `Pragma` ist durch RFC 9111 veraltet und wird
weiterhin gesendet; die Feed-Prüfung vergleicht die Zeichenkette `If-Modified-Since` exakt statt als Datum.

## HTTP/2 (RFC 9113)

**Was es ist.** RFC 9113 "HTTP/2" (IETF, Juni 2022), mit HPACK-Header-Kompression (RFC 7541).

**Wie Exponential es erfüllt.** Velocity handelt HTTP/2 per ALPN auf seinem TLS-Listener aus und beantwortet es aus
demselben Worker-Pool wie HTTP/1.1. HPACK wird gegen die Testvektoren der Spezifikation geprüft; Frames werden vor der
Weitergabe validiert (`PROTOCOL_ERROR`, `FRAME_SIZE_ERROR`); unbekannte Frame-Typen werden verworfen (RFC 9113
Abschnitt 4.1); Grenzen für Streams, die Größe des Header-Blocks und die dekodierte Header-Größe schützen vor
HPACK-Verstärkung ([Velocity: HTTP/2 und Sicherheit](../spezifikationen/6.0/velocity-http2-und-sicherheit.md)).

**Status.** **Erfüllt** für Velocity. Mit Apache, nginx oder FrankenPHP gehört HTTP/2 zu diesem Server.

**Bekannte Lücken.** Kein HTTP/3 (RFC 9114). Keine Interoperabilitäts-Testsuite (wie h2spec) ist Teil der CI des
Repositorys.

## TLS

**Was es ist.** Transport Layer Security 1.3 (RFC 8446, 2018) und 1.2 (RFC 5246); HTTP Strict Transport Security
(RFC 6797).

**Wie Exponential es erfüllt.**

- Velocity liefert HTTPS mit einer eigenen Zertifikatsverwaltung aus: eigene Dateien, Archive, PKCS#12 oder Let's
  Encrypt und andere ACME-CAs, vor der Verwendung geprüft und im Hintergrund erneuert; ein selbstsigniertes Zertifikat
  springt ein, bis ein echtes verwendbar ist ([Velocity: HTTPS und Zertifikate](../funktionen/6.0/velocity-https-zertifikate.md)).
- HSTS: `settings/velocity.ini [HTTPSSettings] HSTSMaxAge=300` (mit `HSTSIncludeSubDomains`, `HSTSPreload`) und
  `site.ini [HTTPHeaderSettings] SecurityHeaders[Strict-Transport-Security]` (standardmäßig leer) für jede Engine.
- Das Session-Cookie ist `Secure`, sobald die Anfrage über HTTPS kam (`[Session] CookieSecure=auto`).
- Der statische Dateiserver `uwebserver` von Velocity akzeptiert TLS 1.2 oder 1.3 (`--tls-min-version`, Standard
  `1.2`), nur mit AEAD-Chiffren mit Forward Secrecy ([uwebserver](../funktionen/6.0/velocity-uwebserver.md)).

**Status.** **Teilweise erfüllt.** Die TLS-Mindestversion und die Chiffrenliste des PHP-Anwendungsservers von Velocity
sind in diesem Repository nicht dokumentiert: **Nicht verifiziert**. Mit Apache oder nginx ist die TLS-Konfiguration
Sache des Betreibers.

## HTML (WHATWG Living Standard)

**Was es ist.** Der HTML Living Standard, gepflegt von der WHATWG, seit 2019 die einzige HTML-Spezifikation.

**Wie Exponential es erfüllt.** Jedes Kern-Pagelayout beginnt mit `<!DOCTYPE html>`: `design/standard`,
`design/base`, `design/plain`, `design/mysite`, `design/admin`, `design/admin3`, `design/admin4`, `design/admin4l` und
die Portal-Designs von `extension/expservices`. Seiten tragen `lang` und bei Sprachen mit Schreibrichtung von rechts
nach links `dir="rtl"` am `<html>` ([Sprachen mit Schreibrichtung von rechts nach links](../funktionen/6.0/rechts-nach-links.md));
Landmarks und Formularsemantik werden getestet ([Standards zur Barrierefreiheit](barrierefreiheit.md)).

**Status.** **Teilweise erfüllt.**

**Bekannte Lücken.** In der CI läuft kein HTML-Validator (wie der Nu HTML Checker); das E-Mail-Layout
`design/standard/templates/mail/layout.tpl` verwendet noch einen XHTML-Doctype (für E-Mail-Programme akzeptabel, aber
nicht der Living Standard); Site-Designs aus Paketen wurden nicht geprüft.

## CSS

**Was es ist.** Die CSS-Spezifikationen der W3C CSS Working Group, zusammengefasst im jährlichen CSS Snapshot; hier
insbesondere Custom Properties for Cascading Variables, Media Queries Level 5 (`prefers-color-scheme`,
`prefers-reduced-motion`, `forced-colors`) und Selectors Level 4 (`:focus-visible`, `:has()`).

**Wie Exponential es erfüllt.**

- Die Design-Tokens von admin4 sind CSS Custom Properties (`--a4-*` in `design/admin4/stylesheets/components.css`),
  jedes Farb-Token mit einem dunklen Wert unter `html[data-a4-theme="dark"]`.
- Der Dunkelmodus folgt dem System: Der Modus "System" liest `prefers-color-scheme`
  (`design/admin4/javascript/a4_head.js`), getestet von `tests/tests/design/AdminThemeSwitchTest.php`.
- Regeln für `@media (forced-colors: active)` und `@media (prefers-reduced-motion: reduce)` in
  `design/admin4/stylesheets/admin4.css`.
- Keine `<style>`-Blöcke oder `style=""`-Attribute in Admin-Templates; das gesamte CSS liegt in Stylesheet-Dateien,
  gebündelt vom ezjscore-Packer. Durchgesetzt von `tests/tests/design/AdminStylingRulesTest.php` (auch fest kodierte
  Farben und Radien in Modul-Stylesheets schlagen fehl) und verlangt durch eine durchgesetzte CSP `style-src 'self'`
  ([Styling von admin4](../../eng-us/bc/6.0/admin4-styling.md)).

**Status.** **Erfüllt** für admin4 und admin4l. Andere Designs: **Nicht verifiziert**.

## ECMAScript

**Was es ist.** ECMA-262, die ECMAScript Language Specification, jährlich von Ecma International (TC39)
veröffentlicht.

**Wie Exponential es erfüllt.**

- Die Skripte der Administration sind einfaches Browser-JavaScript ohne Build-Schritt. In
  `design/admin4/javascript/` verwenden 15 von 16 Dateien nur Syntax im ES5-Stil (`var`, Funktionsausdrücke); eine
  verwendet Pfeilfunktionen und blockweise gültige Deklarationen (ES2015). Sie laufen in jeder aktuellen
  Browser-Engine.
- jQuery 4.0.0 mit jQuery Migrate 4.0.2 und jQuery UI 1.14.2 werden lokal ausgeliefert
  (`extension/ezjscore/design/standard/javascript/`, `ezjscore.ini LocalScripts[jquery]=jquery-4.0.0.min.js`); YUI
  wurde entfernt ([jQuery 4 und die Entfernung von YUI](../funktionen/6.0/jquery4-und-entfernung-von-yui.md)).
- Die CI prüft die Syntax jeder geänderten `.js`-Datei mit `node --check` auf Node.js 22
  (`.github/workflows/quality.yml`).
- Skripte laufen in admin4 und admin4l unter einer CSP ohne `'unsafe-inline'` (siehe
  [Sicherheitsstandards](sicherheit.md)).

**Status.** **Erfüllt.**

**Bekannte Lücken.** Weder ein Linter noch ein ECMAScript-Ziel ist konfiguriert; das Sprachniveau ist eine Konvention,
keine Prüfung. Der Editor TinyMCE 3 und ältere Skripte von Extensions sind Altcode.

## URLs (RFC 3986 und der WHATWG URL Standard)

**Was es ist.** RFC 3986 "Uniform Resource Identifier (URI): Generic Syntax" (IETF, 2005), RFC 3987 für IRIs und der
WHATWG URL Standard, den Browser umsetzen.

**Wie Exponential es erfüllt.** Knoten-URLs sind URL-Aliase, die aus dem Inhaltsnamen über die Umwandlungsgruppe von
`settings/site.ini [URLTranslator] TransformationGroup=urlalias` mit `WordSeparator=dash` gebildet werden, die Namen in
einfache Pfadsegmente umwandelt; `urlalias_iri` behält Unicode-Buchstaben (IRI), und `urlalias_compat` liefert das
älteste Verhalten. Weiterleitungsziele, die von außerhalb des Codes kommen, prüft `eZRedirectManager`
([Sichere Weiterleitungen](../funktionen/6.0/sichere-weiterleitungen.md)).

**Status.** **Teilweise erfüllt.** Die Regeln für Aliase sind in [URLs und Aliase](../anleitungen/urls-und-aliase.md)
dokumentiert; kein Test prüft erzeugte URLs als solche gegen RFC 3986: darüber hinaus **nicht verifiziert**.

## Medientypen (RFC 6838)

**Was es ist.** RFC 6838 legt die Registrierung von Medientypen fest; die IANA-Registry listet die Typen auf.

**Wie Exponential es erfüllt.**

- `settings/mime.ini` ordnet Dateiendungen Typen zu (zum Beispiel `[svg] Types[]=image/svg+xml`, `[webp]
  Types[]=image/webp`, `[mp4] Types[]=video/mp4`).
- Hochgeladene Bilder werden nach ihrem Inhalt mit fileinfo von PHP typisiert und gegen `settings/image.ini
  [ImageUploadSettings] AllowedMIMETypes[]` geprüft (JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG).
- `X-Content-Type-Options: nosniff` auf jeder Seite.
- Feeds: RSS als `application/rss+xml`, OPML als `text/x-opml`.

**Status.** **Teilweise erfüllt.**

**Bekannte Lücken.** Atom-Feeds werden als `application/xml` statt als `application/atom+xml` gesendet (RFC 4287
Abschnitt 7); `settings/mime.ini` hat keine Einträge für `avif`, `json` oder `woff2`.

## HTTP-Cookies (RFC 6265)

**Was es ist.** RFC 6265 "HTTP State Management Mechanism" (IETF, 2011). Das Attribut `SameSite` stammt aus seiner
Überarbeitung, dem Entwurf RFC 6265bis, den alle großen Browser umsetzen.

**Wie Exponential es erfüllt.** Das Session-Cookie wird von `lib/ezsession/classes/ezsession.php` aus
`settings/site.ini [Session]` gesetzt: `CookieHttponly` (Standard true), `CookieSecure` (Standard `auto`),
`CookieSameSite` (Standard `Lax`; `None` nur zusammen mit `Secure`), `CookiePath`, `CookieDomain`, `CookieTimeout`.
Das Cookie `is_logged_in` verwendet dieselben Optionen (`eZSession::siteCookieOptions()`). Die Cookies, die
Exponential setzt, sind in den [Datenschutzstandards](datenschutz.md) aufgeführt.

**Status.** **Erfüllt.**

## Robots Exclusion (RFC 9309) und Sitemaps

**Was es ist.** RFC 9309 "Robots Exclusion Protocol" (IETF, 2022); das Sitemaps-XML-Protokoll 0.9 (sitemaps.org), von
den großen Suchmaschinen unterstützt.

**Wie Exponential es erfüllt.** Eine neue Installation liefert eine `robots.txt` im Web-Wurzelverzeichnis aus, die
Crawler von Seiten ohne Nutzen fernhält und auskommentierte `Sitemap:`-Zeilen zum Ausfüllen enthält
([robots.txt](../funktionen/6.0/robots-txt.md)). Die Extension `bcgooglesitemaps` (von `composer.json` installiert, von
der Site aktiviert) schreibt Sitemap-Dateien mit dem Namensraum `http://www.sitemaps.org/schemas/sitemap/0.9`, auch pro
Sprache (`classes/runnable/cronjobs/generate.php`, `generatemultilingual.php` in der Extension).

**Status.** **Erfüllt** (Sitemaps mit aktivem `bcgooglesitemaps`).

## RSS- und Atom-Feeds

**Was es ist.** RSS 2.0 (Spezifikation des RSS Advisory Board), RSS 1.0 (RDF Site Summary) und das Atom Syndication
Format 1.0 (RFC 4287, IETF, 2005).

**Wie Exponential es erfüllt.** RSS-Exporte (**Setup > RSS**) werden von `kernel/classes/ezrssexport.php` als RSS 1.0,
RSS 2.0, Atom 1.0, Apple Podcasts (RSS 2.0 mit dem iTunes-Namensraum) oder OPML 2.0 geschrieben. Die Ausgabe für Apple
Podcasts trägt ein `atom:link rel="self"`. Tests: `tests/tests/kernel/classes/eZRSSExportTest.php`,
`tests/tests/kernel/classes/security/eZRSSSecurityTest.php`
([RSS, Podcasts und die Feed-Liste](../funktionen/6.0/rss-podcast-und-feed-liste.md), [OPML-Exporte](../../eng-us/bc/6.0/opml.md)).

**Status.** **Teilweise erfüllt** (Formate geschrieben; Medientyp für Atom wie oben vermerkt).

## Open Graph

**Was es ist.** Das Open Graph Protocol (ogp.me), von Facebook begonnen und von sozialen Netzwerken und Messengern zum
Erstellen von Linkvorschauen verwendet.

**Wie Exponential es erfüllt.** Die Extension `xrowmetadata`, standardmäßig aktiv (`settings/site.ini
[ExtensionSettings] ActiveExtensions[]=xrowmetadata`), gibt `og:image` mit `og:image:secure_url`, `og:image:type`,
`og:image:width`, `og:image:height` und `og:image:alt` sowie `twitter:image` aus, aus einem Bild pro Objekt oder dem
Standard der Klasse; ihr Beispiel-Seitenkopf gibt das Meta-Tag `description` aus.

**Status.** **Teilweise erfüllt.** `og:title`, `og:type` und `og:url`, die das Protokoll auf jeder Seite verlangt,
werden von den Templates der Extension nicht ausgegeben; ein Site-Design muss sie hinzufügen.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](barrierefreiheit.md), [Sicherheitsstandards](sicherheit.md), [Datenschutzstandards](datenschutz.md)
- [Velocity: HTTP/2 und Sicherheit](../spezifikationen/6.0/velocity-http2-und-sicherheit.md)
- [HTTP-Cache](../../eng-us/bc/6.0/httpcache.md)
- [Styling von admin4](../../eng-us/bc/6.0/admin4-styling.md)
