---
title: Über Sicherheitskorrekturen berichten
audience: press
category: press-media
order: 110
summary: Wie Sie verantwortungsvoll über Sicherheitskorrekturen in Exponential berichten - der dokumentierte Sicherheitsprozess, wo Korrekturen festgehalten werden, wie Sie sie ohne Übertreibung oder Alarm beschreiben, welchen Rat Sie Lesern geben können und was Sie nie veröffentlichen.
keywords: Sicherheit, Sicherheitskorrektur, Sicherheitslücke, Schwachstelle, Härtung, Patch, SECURITY.md, 6.0.13, August 2026, verantwortungsvolle Berichterstattung, Upgrade, Offenlegung, security, security fix, vulnerability, hardening, patch, responsible reporting, upgrade, disclosure
type: help
---
# Über Sicherheitskorrekturen berichten

Diese Seite ist für Reporter, Sicherheitsautoren und Creator, die über Sicherheitskorrekturen in Exponential berichten.
Sicherheitsbeiträge werden von Menschen gelesen, die entscheiden müssen, ob sie handeln, deshalb zählt Genauigkeit mehr
als Dramatik. Berichten Sie, was die Korrektur bewirkt, welche Versionen sie betrifft und was Leser tun sollten, mit den
Dokumenten des Projekts als Quelle.

## Inhalt

1. [Der dokumentierte Sicherheitsprozess](#der-dokumentierte-sicherheitsprozess)
2. [Wo Korrekturen festgehalten werden](#wo-korrekturen-festgehalten-werden)
3. [Eine Korrektur genau beschreiben](#eine-korrektur-genau-beschreiben)
4. [Rat, den Sie Lesern geben können](#rat-den-sie-lesern-geben-können)
5. [Was Sie nie veröffentlichen](#was-sie-nie-veröffentlichen)

## Der dokumentierte Sicherheitsprozess

Alles Folgende stammt aus [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

| Punkt | Was SECURITY.md sagt |
|---|---|
| Gepflegte Linie | "Exponential 6.0 is the maintained line." |
| Wo Korrekturen entstehen | "Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`) and published with the next 6.0.x release." |
| Ältere Tags | Die veröffentlichten Tags `v6.0.0` bis `v6.0.14` werden "fixed by upgrading to the newest 6.0.x" |
| Nicht unterstützt | "5.x, 4.x, 3.x and older" |
| Meldung | "privately by e-mail to security@se7enx.com, not in the public issue tracker or the discussions" |
| Erste Antwort | "within one business day" |
| Nach einer Meldung | Der Meldende wird "told whether the report is accepted or declined, and kept informed while a fix is prepared" |

SECURITY.md beschreibt keine Advisories, Schweregrade, CVE-Kennungen und keinen Offenlegungskalender. Wenn Sie diese für
einen Beitrag brauchen, fragen Sie 7x schriftlich ([Ein Interview anfragen](ein-interview-anfragen.md)) und drucken Sie
„das Projekt veröffentlicht das nicht“, wenn die Antwort nein lautet.

## Wo Korrekturen festgehalten werden

| Nachweis | Beispiel |
|---|---|
| Das Changelog des Releases | [6.0.13](../../../changelogs/6.0/6.0.13.md): "Security hardening (seven findings fixed)", die Befunde SEC-01 bis SEC-06 und weitere |
| Eine Spezifikationsseite pro Härtungsrunde | [Die Härtung von 6.0.13](../../spezifikationen/6.0/sicherheitshaertung-6.0.13.md), [Sicherheitspatches vom August 2026](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md), [Sicherheitsvorgaben vom September 2026](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md) |
| Die Monatsseite der Geschichte | [August 2026](../../../eng-us/history/2026/2026-08.md): "Six security patches and jQuery 3.7.1 landed on 16 August" |
| Die Entwicklungslinie | Das [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) führt spätere Korrekturen nach Datum auf; sie sind nicht veröffentlicht, bis das Tag 6.0.15 existiert |

Beachten Sie den Status: Die Patches vom August 2026 liegen auf der Entwicklungslinie 6.0.15. Ein Beitrag darf nicht
sagen, sie seien in einer veröffentlichten Version, bis ein Tag sie enthält ([Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md)).

## Eine Korrektur genau beschreiben

| Statt | Schreiben Sie |
|---|---|
| „Kritische Lücke in Exponential gefunden“ | Nur, was die Quelle sagt; die Quellen bewerten keinen Schweregrad |
| „Exponential wurde gehackt“ | Nichts dergleichen, es sei denn, eine genannte Quelle dokumentiert einen Vorfall |
| „Jetzt vollständig sicher“ | „Version X behebt die Probleme, die ihr Changelog aufführt“ |
| „Exponential hatte nie eine Sicherheitslücke“ | Nicht wahr und nicht behauptbar ([Aussagen, die Sie nicht machen dürfen](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md#sicherheit-und-compliance)) |
| „Ein gepatchtes 6.0.14 ist verfügbar“ | „Die Korrektur kommt mit dem nächsten 6.0.x-Release“ ([Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md)) |

Verwenden Sie für das, was behoben wurde, die Worte des Changelogs; die Monatsseite August 2026 nennt zum Beispiel
"unpredictable REST/OAuth tokens", "header injection in the After the Deadline RPC" und "shell escaping in the PDF and
Word text extractors". Zitieren Sie sie; ergänzen Sie keine Angriffsszenarien, die die Quellen nicht beschreiben.

## Rat, den Sie Lesern geben können

```text
Wenn Sie Exponential betreiben: Das Projekt behebt Sicherheitsprobleme auf seinem main-Branch und
veröffentlicht die Korrekturen mit dem nächsten 6.0.x-Release. Versionen 5.x und älter werden nicht
unterstützt. Lesen Sie vor einem Upgrade das Changelog und die Upgrade-Hinweise, und folgen Sie für
Produktivsysteme dem Leitfaden zur Härtung:
- SECURITY.md: https://github.com/se7enxweb/exponential/blob/main/SECURITY.md
- Upgrade: <Link zum Kapitel über das Upgrade>
- Härtung: <Link zu Kapitel 13 des Installationshandbuchs>
```

Die Links: [Upgrade](../../installation/11-aktualisieren.md), [Sicherheitshärtung, Kapitel 13](../../installation/13-sicherheitshaertung.md)
und die kürzere Hilfeseite [Sicherheitshärtung](../sicherheitshaertung.md).

## Was Sie nie veröffentlichen

- **Einzelheiten einer nicht behobenen Sicherheitslücke**, auch keine Schritte zur Reproduktion, keinen
  Proof-of-Concept-Code und nicht die betroffene Datei und den Parameter. Wenn Sie von einer erfahren, folgen Sie
  [Wenn Sie eine Sicherheitslücke finden](wenn-sie-eine-sicherheitsluecke-finden.md).
- **Namen von Websites, die Sie für verwundbar halten.** Sie können deren Version oder Konfiguration nicht kennen, und
  die Nennung gefährdet deren Nutzer.
- **Passwörter, Tokens oder Schlüssel** aus Screenshots, Logs oder Demos ([Sicherheitsmeldungen bleiben vertraulich](../fuersprecher/sicherheitsmeldungen-bleiben-vertraulich.md)).
- **Den Namen eines Meldenden**, der nicht zugestimmt hat, genannt zu werden.

## Verwandte Seiten

- [Wenn Sie eine Sicherheitslücke finden](wenn-sie-eine-sicherheitsluecke-finden.md)
- [Gesprächspunkte: Sicherheit](../fuersprecher/gespraechspunkte-sicherheit.md)
- [Häufige Fehler in Berichten](haeufige-fehler-in-berichten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
