---
title: Standards
audience: developer, admin
category: standards
order: 1
summary: Die Standards, denen Exponential folgt, und wie weit es sie erfüllt (Barrierefreiheit, Sicherheit, Datenschutz, Web, Internationalisierung, PHP, Dokumentation, APIs, Datenbanken, Git, Programmierung und Tests), seine Architekturregeln und ein Katalog der Entwurfsmuster in seinem Code.
keywords: Standards, Konformität, Barrierefreiheit, Datenschutz, Sicherheit, Entwurfsmuster, Namenskonventionen, Abwärtskompatibilität, standards, compliance, conformance, WCAG, WAI-ARIA, EN 301 549, OWASP, ASVS, CSP, GDPR, DSGVO, HTTP, HTML, CSS, ECMAScript, Unicode, CLDR, BCP 47, RTL, PSR, PHP-FIG, Composer, semver, DITA, GFDL, EPUB, PDF, OAuth, OpenID, REST, WebDAV, SQL, Git, commit messages, phpcs, PHPUnit, CI, architecture, design patterns
featured: true
type: reference
---
# Standards

Dieser Abschnitt ist für Entwickler, die auf Exponential aufbauen, und für Administratoren und Entscheider, die sagen
müssen, welche Standards eine Website erfüllt. Er beantwortet drei Fragen: welchen veröffentlichten Standards
Exponential folgt und wie weit es jeden davon erfüllt, welchen Architekturregeln sein Code folgt und welche
Entwurfsmuster der Code verwendet und wo.

Jede Seite nennt den Code, die Einstellungen, Tests oder Jobs der kontinuierlichen Integration, die ihre Aussagen
stützen. Wo sich eine Aussage nicht prüfen ließ, sagt die Seite **Nicht geprüft**; wo Exponential etwas nicht erfüllt,
sagt die Seite das und nennt die Lücke. Die Konformität einer *Website* hängt auch von ihren eigenen Templates, Inhalten
und ihrem Betrieb ab: Die Software kann Konformität möglich machen, aber eine Website muss sie halten.

## Wie Sie den Status lesen

| Status | Bedeutung |
|---|---|
| **Erfüllt** | Die Anforderung wird von der Software im ausgelieferten Zustand erfüllt, und die Seite nennt den Nachweis. |
| **Teilweise erfüllt** | Einige Anforderungen werden erfüllt, andere nicht oder nur in manchen Teilen (zum Beispiel die Administrationsoberfläche, aber nicht jedes öffentliche Design). |
| **Nicht erfüllt** | Die Software erfüllt sie nicht; die Seite sagt, was fehlt. |
| **Nicht anwendbar** | Der Standard gilt nicht für das, was Exponential tut. |
| **Nicht geprüft** | Es wurde kein Nachweis in die eine oder andere Richtung gefunden; nichts wird behauptet. |

## Standards, denen Exponential folgt

| Bereich | Seite | Behandelte Standards |
|---|---|---|
| Barrierefreiheit | [Barrierefreiheit](barrierefreiheit.md) | WCAG 2.2 AA, WAI-ARIA 1.2, EN 301 549 |
| Sicherheit | [Sicherheit](sicherheit.md) | OWASP Top 10, OWASP ASVS, Content Security Policy Level 3, OWASP Cheat Sheets |
| Datenschutz | [Datenschutz](datenschutz.md) | DSGVO, ePrivacy (Cookies), Einwilligung und Abmeldung |
| Die Webplattform | [Webplattform](webplattform.md) | HTTP-Semantik und Caching, HTTP/2, TLS, HTML, CSS, ECMAScript, URLs, Cookies, Feeds, Sitemaps |
| Sprachen | [Internationalisierung](internationalisierung.md) | Unicode und UTF-8, CLDR und ICU, BCP 47 und ISO-Sprachcodes, Rechts-nach-links und Bidi, Zeitzonen |
| PHP | [PHP und Composer](php-und-composer.md) | Unterstützte PHP-Versionen, PSRs von PHP-FIG, Composer, Semantic Versioning |
| Codestil | [Programmierstandards](programmierstandards.md) | Das Regelwerk von phpcs, PHPStan, Dateiköpfe, Benennung, Regeln für CSS und JavaScript |
| Code-Präfixe | [Namenspräfixe: sevenx und exp](namenspraefixe-sevenx-und-exp.md) | `sevenx` als offizielles öffentliches PHP-Code-Präfix von 7x, `exp` für Arbeit, die dem Projekt Exponential und der Exponential Foundation zugeordnet ist |
| Tests | [Tests und kontinuierliche Integration](tests-und-ci.md) | PHPUnit, die Testkarte, die CI-Workflows und die Prüfungen vor einem Push |
| Historie | [Versionsverwaltung und Releases](versionsverwaltung-und-releases.md) | Git-Konventionen, Commit-Nachrichten, Changelogs, Tags und Versionsnummern |
| Dokumentation | [Dokumentation und Veröffentlichung](dokumentation-und-veroeffentlichung.md) | DITA 1.3, CommonMark und GFM, GNU FDL 1.3, PDF, EPUB 3 und EPUB Accessibility |
| Schnittstellen | [APIs und Protokolle](apis-und-protokolle.md) | OAuth 2.0 und OpenID Connect, REST und JSON, WebDAV, SOAP, Feeds, Mail, LDAP |
| Datenbanken | [Datenbanken und SQL](datenbanken-und-sql.md) | SQL und der Dialekt jeder unterstützten Datenbank, die Datenbankabstraktion |

## Architektur

| Seite | Was sie behandelt |
|---|---|
| [Kernel-Architektur](architektur/kernel-architektur.md) | Der Lebenszyklus einer Anfrage, Module, Ansichten und Funktionen, Richtlinien, Datentypen, persistente Objekte, Einstellungen und Siteaccesses, Designs und Überschreibungen, Templates, Extensions, Caches, Workflows, Cronjobs, die Datenbankschicht und der Anwendungsserver Velocity |
| [Regeln zu Schichten und Benennung](architektur/schichten-und-benennung.md) | Welche Schicht welche verwenden darf und wie neue Klassen, Module, Templates, Einstellungen und Befehle benannt werden |
| [Kompatibilität, Releases und Deployment](architektur/kompatibilitaet-releases-deployment.md) | Die Richtlinie zur Abwärtskompatibilität, Versionsnummern und Releases und wie eine Änderung ausgebracht wird |

## Entwurfsmuster

Der [Katalog der Entwurfsmuster](muster/README.md) führt jede Abstraktion des Stacks von Exponential 6 auf: Fabriken,
Singletons, Registries, Handler und Strategien, abstrakte Basisklassen, Beobachter und Ereignis-Hooks, Adapter und
Fassaden, persistente Objekte, Datentypen, Template-Operatoren, Fetch-Funktionen, Modulansichten,
Richtlinienfunktionen, Workflow-Ereignisse, Cronjob-Teile und die INI-Einstellungen, über die Klassen eingesteckt
werden, jeweils mit Datei, Klasse, Beschreibung und Erweiterungspunkt. Er wird aus dem Quellcode erzeugt und lässt sich
jederzeit mit `php bin/php/exppatterncatalogue.php --write` neu erstellen.

## Verwandt

- [Vorlage für den Kopf von Quelldateien (übernommen, 4.1)](../../eng-us/standards/header.txt): der Lizenzkopf, mit dem jede PHP-Datei der
  Codebasis 4.x begann, als Historie aufbewahrt. Neue Dateien verwenden den in
  [Programmierstandards](programmierstandards.md) beschriebenen Kopf.
- [Checklisten zu Barrierefreiheit, Sicherheit und Konformität](../hilfe/fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md) für Projektleiter und Einkäufer
- [Die Lizenz dieser Dokumentation](../README-LIZENZ.md)
- [Sicherheitshärtung](../hilfe/sicherheitshaertung.md), [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md) und
  [Unterstützung von Betriebssystemen](../hilfe/betriebssystemunterstuetzung.md) für die Sicht des Betreibers auf dieselben Themen.
