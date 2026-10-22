---
title: Der Schreibassistent
audience: individual, customer, editor
category: personal-sites
order: 1644
summary: Was Exponential Intelligence (expai) für eine Person leisten kann, die ein Blog, ein Portfolio oder eine Vereinsseite schreibt - nur Entwürfe, nie Veröffentlichung; was es braucht (einen Anbieterschlüssel oder ein lokales Modell, in den Einstellungen eingerichtet); wohin Ihr Text geht; Kosten und Grenzen; und wann Sie es nicht verwenden sollten, etwa bei privaten Familieninhalten.
keywords: Schreibassistent, Exponential Intelligence, expai, KI, Entwürfe, Anbieterschlüssel, API-Schlüssel, Anthropic, OpenAI-kompatibel, Ollama, lokales Modell, Alternativtext, Kosten, Grenzen, Datenschutz, Datenrichtlinie, Privatperson, writing assistant, AI, drafts, provider key, API key, local model, alt text, costs, limits, privacy, data policy
type: help
---
# Der Schreibassistent

Diese Seite ist für Privatpersonen, die sich fragen, ob der eingebaute Schreibassistent bei ihren Beiträgen,
Bildunterschriften und Seiten helfen könnte. Exponential Intelligence (`expai`, in der `composer.json` im
Wurzelverzeichnis) ist ein Schreibassistent für die Administrationsoberfläche: Sie fragen in einfacher Sprache, er
schreibt **Entwürfe**, als Sie und mit Ihren Berechtigungen. Nichts wird je von selbst veröffentlicht
([Exponential Intelligence](../../kunden/wachstum/exponential-intelligence.md),
[expai](../../../funktionen/6.0/erweiterungen/expai.md)).

## Inhalt

1. [Was er für Sie tun kann](#was-er-für-sie-tun-kann)
2. [Was er braucht](#was-er-braucht)
3. [Ihn einschalten](#ihn-einschalten)
4. [Wohin Ihr Text geht](#wohin-ihr-text-geht)
5. [Kosten und Grenzen](#kosten-und-grenzen)
6. [Wann Sie ihn nicht verwenden sollten](#wann-sie-ihn-nicht-verwenden-sollten)
7. [Ihn gut nutzen](#ihn-gut-nutzen)

## Was er für Sie tun kann

| Aufgabe | Wo |
|---|---|
| Einen Beitrag oder eine Seite aus einer kurzen Vorgabe entwerfen | Der Reiter **Intelligence** eines Ordners oder Blogs |
| Ein einzelnes Feld umschreiben, kürzen, übersetzen oder Vorschläge machen | Feldaktionen und das Menü **Improve** (Verbessern) im Bearbeitungsformular |
| Titel, Zusammenfassung, Meta-Beschreibung, Schlüsselwörter ausfüllen | Voreinstellungen pro Klasse im Bearbeitungsformular |
| Alternativtext für Bilder vorschlagen | Über ein bildfähiges Modell; ein Bericht „Missing alt text" (fehlender Alternativtext) |

Alles landet als Entwurf oder als Vorschlag im Formular, damit Sie es prüfen.

## Was er braucht

Laut Funktionsseite: Exponential 6.0.14 oder neuer, PHP 8.0+ mit `curl`, `dom`, `json` und `mbstring`, `ezjscore`
und `ezformtoken`, `expqueue` für Arbeit in der Warteschlange und **einen Anbieterschlüssel oder ein lokales Modell**.

| Anbietertyp | In der Dokumentation genannte Beispiele | Hinweis |
|---|---|---|
| Anthropic | Der eigene Dienst von Anthropic | Ein Schlüssel vom Anbieter |
| Jeder OpenAI-kompatible Dienst | Melious.ai (EU), Hugging Face, OpenRouter, Mistral | Ein Schlüssel vom Anbieter |
| Ein lokales Open-Weight-Modell | Über Ollama | Kein externer Anbieter; braucht einen Rechner, der stark genug ist, es auszuführen |

Nichts funktioniert, bis Sie eines davon mitbringen. Das Projekt stellt keinen Schlüssel bereit.

## Ihn einschalten

Auf Ihrer eigenen Website tun Sie das einmal ([expai, Einschalten](../../../funktionen/6.0/erweiterungen/expai.md#einschalten)):

1. Aktivieren Sie `expai` für den Siteaccess der Administration.
2. Tragen Sie den Schlüssel nur in `settings/override/expai.ini.append.php` ein (Dateimodus 0640, nie in der
   Versionsverwaltung): `[AISettings] Enabled=true` und `ApiKey=<your provider key>`.
3. Legen Sie seine Tabellen an mit `php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>`.
4. Geben Sie Ihrer Rolle `expai/write` und `ezjscore/call` (Funktionsliste `expai`).

Wenn Ihnen das zu weit geht, ist es eine kurze, klar umrissene Aufgabe für einen Partner
([Wann Sie sich Hilfe holen sollten](wann-sie-sich-hilfe-holen-sollten.md)).

## Wohin Ihr Text geht

Text, zu dem Sie fragen, wird an den eingerichteten Anbieter geschickt. Mit `expai` wählen Sie pro Anbieter eine
Region (`us`, `eu`, `local`) und die Aufbewahrung, legen pro Sektion und Klasse fest, was gesendet werden darf, und
es hält pro Anfrage fest, wohin sie ging; Prompts, Antworten und Schlüssel werden nicht im Audit-Protokoll
festgehalten. Ein lokales Modell behält alles auf Ihrem Rechner. **Setup > EI: Summary** zeigt Region, Aufbewahrung
und Schlüsselquelle jedes Anbieters auf einer Seite.

## Kosten und Grenzen

Anbieter rechnen pro Nutzung ab. `expai` zeigt geschätzte Kosten pro Anfrage und lässt Sie Grenzen pro Benutzer,
Tag, Monat und Website setzen, damit ein Fehler keine hohe Rechnung verursacht
([Kosten und Grenzen](../../kunden/wachstum/exponential-intelligence.md#kosten-und-grenzen)). Setzen Sie zuerst eine
niedrige Monatsgrenze und erhöhen Sie sie, wenn Sie ihn nützlich finden.

## Wann Sie ihn nicht verwenden sollten

- **Private Familieninhalte**: Namen, Kinder, Gesundheit, Adressen. Schicken Sie sie nicht an einen externen
  Anbieter. Schließen Sie die private Sektion in der Datenrichtlinie aus oder verwenden Sie ein lokales Modell.
- **Mitgliederdaten** auf einer Vereinsseite.
- **Ihre eigene Stimme**: Wenn Leser wegen *Ihrer* Art zu schreiben kommen, nutzen Sie ihn für Routinearbeit
  (Zusammenfassungen, Alternativtexte, Meta-Beschreibungen), nicht für die Beiträge.
- **Fakten, die Sie nicht prüfen können.** Er kann sich selbstsicher irren; jede Tatsache in einem Entwurf müssen
  Sie selbst prüfen.

## Ihn gut nutzen

- Geben Sie ihm eine kurze, konkrete Vorgabe: Zielgruppe, Länge, Ton, die Punkte, um die es geht.
- Behandeln Sie jeden Entwurf als ersten Entwurf; schreiben Sie ihn in Ihren eigenen Worten um.
- Prüfen Sie Namen, Daten, Zahlen und Links.
- Sagen Sie auf Ihrer Website, wenn Sie KI-Hilfe nutzen, wo Ihre Leser das wissen möchten.

Siehe [KI-Entwürfe verantwortungsvoll nutzen](../../kunden/wachstum/ki-entwuerfe-verantwortungsvoll-nutzen.md).

## Verwandte Seiten

- [Allein betreiben](README.md)
- [Exponential Intelligence](../../kunden/wachstum/exponential-intelligence.md)
- [expai](../../../funktionen/6.0/erweiterungen/expai.md)
- [Für die eigene Website schreiben](../persoenliche-seiten/fuer-die-eigene-website-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
