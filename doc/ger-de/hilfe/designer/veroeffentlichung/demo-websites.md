---
title: "Eine Demo-Website betreiben"
audience: designer, sales-marketing
category: theme-distribution
order: 330
summary: Menschen eine Live-Vorschau eines Themes geben - Möglichkeiten, eine Demo zu hosten, sie mit jedem Release aktuell zu halten, sie automatisch zurückzusetzen und sie sicher zu halten, wenn die Öffentlichkeit sie ansehen (oder sich anmelden) kann.
keywords: Demo-Website, Live-Demo, Vorschau, zurücksetzen, neu aufbauen, nur lesend, Sicherheit, Hosting, demo.exponential.earth, demo site, live demo, preview, reset, rebuild, read-only, security
type: help
---
# Eine Demo-Website betreiben

Diese Seite ist für Designer, die möchten, dass Menschen ein Theme ausprobieren, bevor sie es installieren. Eine
Live-Demo beantwortet die Frage „Wie fühlt es sich an?“ besser als jeder Screenshot.

## Beispiele aus dem Projekt

Das Projekt Exponential betreibt öffentliche Demonstrations-Websites, zum Beispiel `https://demo.exponential.earth/` und
`https://site.cjw.demo.exponential.earth/` (die CJW-Starter-Website, die das Design `cjw_themes_jumper` zeigt). Das
Projekt baut seine Demo-Installationen nach Zeitplan neu auf, sodass Besucher immer einen sauberen, aktuellen Stand
sehen.

## Möglichkeiten

| Möglichkeit | Aufwand | Passt zu |
|---|---|---|
| Nur Screenshots und ein kurzes Video | Gering | Einem ersten Release |
| Eine öffentliche, nur lesbare Website | Mittel | Den meisten Themes |
| Eine Website, die sich nach Zeitplan zurücksetzt, mit einer Anmeldung für einen Demo-Redakteur | Höher | Themes, die an Redakteure verkauft werden, die das Bearbeiten ausprobieren wollen |
| Eine Demo pro Kunde, auf Anfrage | Am höchsten | Individueller oder Premium-Arbeit |

## Schritt für Schritt: eine nur lesbare Demo

1. Installieren Sie Exponential auf einem Server oder Hosting-Konto, das Sie kontrollieren, mit dem Theme und
   Demo-Inhalten ([Demo-Inhalte für ein Theme](demo-inhalte.md)).
2. Geben Sie der anonymen Rolle nur Lesezugriff; halten Sie die Administration auf einem eigenen Siteaccess, der nicht
   verlinkt ist.
3. Installieren Sie jedes Release des Themes zuerst dort; das ist zugleich Ihr Upgrade-Test
   ([Upgrade-Test Schritt für Schritt](../lebenszyklus/upgrade-test-schritt-fuer-schritt.md)).
4. Verlinken Sie die Demo aus dem README, dem Projekteintrag und Ihrer Produktseite.

## Schritt für Schritt: eine Demo, die sich zurücksetzt

1. Skripten Sie eine frische Installation (Datenbank, Demo-Inhalte, Theme), die ohne Rückfragen läuft.
2. Führen Sie sie nach Zeitplan (zum Beispiel alle zwei Stunden) in ein neues Verzeichnis aus und schalten Sie dann das
   Web-Root um, sodass Besucher nie eine halb aufgebaute Website sehen.
3. Geben Sie einem Demo-Redakteurskonto nur Rechte zum Bearbeiten von Inhalten. Veröffentlichen Sie sein Passwort auf
   der Startseite der Demo, wenn es öffentlich sein soll; verwenden Sie es nirgends sonst.
4. Leiten Sie die Mails der Demo in Dateien oder ein Test-Postfach, nie an echte Adressen.

## Sicherheit

- Halten Sie Server und Exponential aktuell wie jede Produktiv-Website
  ([Sicherheitshärtung](../../sicherheitshaertung.md)).
- Legen Sie keine Kundendaten und kein privates Material auf eine Demo.

**Demo-Websites mit veröffentlichter Anmeldung.** Betreiben Sie sie im Demomodus (`EXP_DEMO_MODE=1`), damit Besucher das Administratorpasswort nicht ändern können. Ein Theme, das `user/forgotpassword.tpl` oder `user/password.tpl` überschreibt, sollte den Hinweis des Kernels anzeigen (`demo_mode_text`, `demo_mode_locked`, gelesen mit `first_set()`). [Demomodus für Entwickler](../../../../eng-us/help/extension-development/part-8-reference/demo-mode-for-developers.md).

## Verwandte Seiten

- [Demos, die verkaufen](../verkauf/demos-die-verkaufen.md)
- [Demo-Skripte](../../geschaeft/demo-drehbuecher.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
