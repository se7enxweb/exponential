---
title: Von Report-only zu einer durchgesetzten CSP
audience: government, admin, developer
category: public-sector
order: 130
summary: Ein gestufter Plan für öffentliche Stellen, die Content Security Policy der Administration und später der öffentlichen Siteaccesses von Report-only auf Durchsetzen umzustellen - Prüfung der Templates, Sammeln von Berichten, zuerst ein Siteaccess, nur das Nötige lockern, Rückfall.
keywords: CSP durchsetzen, Umstellungsplan, Report-only, gestufte Einführung, Rückfall, Änderungsprozess, öffentlicher Sektor, CSP enforce plan, report-only, enforce, Mode, AdminContentSecurityPolicyTest, CSP_ALL_EXTENSIONS, ReportEndpoint, csp.log, rollback, staged rollout, unsafe-inline, TinyMCE 3, public sector
type: help
---
# Von Report-only zu einer durchgesetzten CSP

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Entwicklerinnen und Entwickler, die die
Content Security Policy von einem Berichtswerkzeug zu einem Schutz machen. Das Durchsetzen ist eine Entscheidung je
Siteaccess, getroffen nach einer Prüfung; das schrittweise Vorgehen des Projekts steht unter
[Schritt für Schritt auf enforce umstellen](../../../funktionen/6.0/content-security-policy.md#schritt-für-schritt-auf-enforce-umstellen).
Diese Seite macht daraus einen Plan mit Meilensteinen, der zum Änderungsprozess einer öffentlichen Stelle passt.

## Inhalt

1. [Phase 1: Die Templates prüfen](#phase-1-die-templates-prüfen)
2. [Phase 2: Echte Berichte sammeln](#phase-2-echte-berichte-sammeln)
3. [Phase 3: Einen Siteaccess durchsetzen](#phase-3-einen-siteaccess-durchsetzen)
4. [Phase 4: Die Administration durchsetzen](#phase-4-die-administration-durchsetzen)
5. [Phase 5: Öffentliche Siteaccesses](#phase-5-öffentliche-siteaccesses)
6. [Rückfall](#rückfall)
7. [Verwandte Seiten](#verwandte-seiten)

## Phase 1: Die Templates prüfen

Führen Sie den Test des Projekts mit jeder Erweiterung der Installation aus, so wie die Administration sie darstellt:

```bash
CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
```

Jede Fehlerzeile ist ein Template mit Inline-Code, den eine durchgesetzte Richtlinie unbrauchbar machen würde. Beheben
Sie Erweiterungen in ihren eigenen Repositorys: Skripte in Dateien, die mit `ezscript` geladen werden, Werte in
`data-*`-Attribute, Handler in Listener, Menüeinträge in `data-menu-*`.

**Meilenstein**: die Liste der verbleibenden Befunde, jeder mit einer verantwortlichen Person.

## Phase 2: Echte Berichte sammeln

Lassen Sie die Berichte an den eigenen Sammeldienst der Website gehen, in der `site.ini.append.php` des
Administrations-Siteaccess:

```ini
[ContentSecurityPolicy]
ReportEndpoint=enabled
```

Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und laden Sie PHP neu (starten Sie Velocity neu).
Arbeiten Sie einige Tage mit der Administration: die Seiten jeder Erweiterung, die Editoren, Dialoge und Kontextmenüs
(Handler-Attribute werden nur gemeldet, wenn sie ausgeführt werden). Lesen Sie **Setup > Logdateien** > „Berichte der
Content Security Policy“ (`csp.log`).

**Meilenstein**: kein Bericht für die Seiten, die Ihre Beschäftigten verwenden, oder eine Entscheidung für jede Quelle.

## Phase 3: Einen Siteaccess durchsetzen

Setzen Sie die Richtlinie in einem zweiten Administrations-Siteaccess durch, den nur das Webteam verwendet, mit
denselben Designs und Erweiterungen:

```ini
# settings/siteaccess/<test admin siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
Mode=enforce
ReportEndpoint=enabled
```

Arbeiten Sie eine Woche darin. Wo etwas bleiben muss, lockern Sie nur diese Direktive für diesen Siteaccess, zum
Beispiel mit `AdditionalSources[]=script-src https://maps.example.org` oder, für TinyMCE 3, mit `'unsafe-inline'` in
`script-src` und `style-src`.

**Meilenstein**: eine Woche normaler Arbeit ohne Ausfälle.

## Phase 4: Die Administration durchsetzen

Setzen Sie `Mode=enforce` im Haupt-Siteaccess der Administration, leeren Sie den INI-Cache, laden Sie PHP-FPM neu und
starten Sie Velocity neu (`./console exp:velocity deploy --allow-root-user` erledigt alles). Klassische Designs
(`ReportOnlyDesigns[]`) bleiben für Benutzer, die sie wählen, Report-only. Lassen Sie `ReportEndpoint=enabled` stehen und
lesen Sie die Berichte wöchentlich.

## Phase 5: Öffentliche Siteaccesses

Öffentliche Designs und die Ausgabe von Rich Text (Stile aus Inhalten, eingebettete Player) prüft der Test des Projekts
nicht. Beginnen Sie jeden öffentlichen Siteaccess mit `Mode=report-only` und `ReportEndpoint=enabled`, beheben Sie Ihr
Design, ergänzen Sie die Hosts der Einbettungen, die Sie behalten, und setzen Sie durch, wenn die Berichte ruhig sind.
Eine strengere Begleitrichtlinie kann den nächsten Schritt messen: `ReportOnlyDirectives[style-src]='self'` neben einer
durchgesetzten Richtlinie, die Inline-Stile noch erlaubt.

## Rückfall

Setzen Sie im Siteaccess `Mode=report-only`, leeren Sie den INI-Cache, laden Sie PHP-FPM neu und starten Sie Velocity
neu. Sonst ändert sich nichts
([Durchsetzen: Rückfall](../../../funktionen/6.0/content-security-policy.md#durchsetzen-nonce-weitere-quellen-ein-strengerer-begleiter-rückkehr)).
Schreiben Sie den Rückfall in Ihren Änderungsantrag, bevor Sie durchsetzen.

## Verwandte Seiten

- [Content Security Policy](../../../funktionen/6.0/content-security-policy.md)
- [Content Security Policy für öffentliche Websites](content-security-policy.md)
- [Routine zur Protokollprüfung](routine-zur-protokollpruefung.md) (das CSP-Protokoll gehört dazu)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
