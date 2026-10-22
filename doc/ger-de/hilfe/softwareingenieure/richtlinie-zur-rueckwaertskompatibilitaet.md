---
title: Richtlinie zur Rückwärtskompatibilität
audience: software-engineer, developer
category: software-engineering
order: 640
summary: Was in Exponential als brechende Änderung zählt - Einstellungen, Templates, PHP-API, Datenbank, Kommandozeile, Verhalten und Dateien -, die Regeln, die damit einhergehen (Upgrade-Hinweise, optionales Verhalten, sichere Standards, keine umbenannten Bezeichner, behaltene Einstiegspunkte), wie Abkündigungen funktionieren, die bekannten Ausnahmen bei Sicherheitsstandards und wie Sie das Risiko eines Upgrades bewerten.
keywords: Rückwärtskompatibilität, BC, brechende Änderung, Abkündigung, optional, sicherer Standard, Upgrade-Hinweise, Bezeichner, Einstiegspunkte, Sicherheitsstandards, Kompatibilitätsversprechen, Softwareingenieur, backward compatibility, breaking change, deprecation, opt-in, safe default, upgrade notes, identifiers, entry points, security defaults
type: help
---
# Richtlinie zur Rückwärtskompatibilität

Diese Seite richtet sich an Ingenieure, die Exponential oder eine Erweiterung ändern, und an jene, die Upgrades planen und
wissen müssen, was brechen kann. Sie fasst
[Kompatibilität, Releases und Bereitstellung](../../standards/architektur/kompatibilitaet-releases-deployment.md#richtlinie-zur-abwärtskompatibilität)
zusammen.

## Inhalt

1. [Was als Bruch zählt](#was-als-bruch-zählt)
2. [Die Regeln](#die-regeln)
3. [Abkündigung](#abkündigung)
4. [Bekannte Ausnahmen](#bekannte-ausnahmen)
5. [Für die aktualisierende Seite](#für-die-aktualisierende-seite)

## Was als Bruch zählt

Eine Änderung bricht die Kompatibilität, wenn eine Installation oder Erweiterung, die vorher funktionierte, eine
Handlung braucht, um weiter zu funktionieren, oder sich ungefragt anders verhält:

| Art | Beispiele |
|---|---|
| Einstellungen | eine Einstellung entfernt oder umbenannt, ihre Bedeutung oder ihr Standard geändert, ein Abschnitt in eine andere Datei verschoben |
| Templates | ein Template entfernt oder verschoben, eine Variable nicht mehr übergeben, ein `Source`-Pfad eines Overrides geändert |
| PHP-API | eine öffentliche Klasse, Methode oder Funktion entfernt oder umbenannt, eine Signatur oder ein Rückgabetyp geändert |
| Datenbank | eine Tabelle oder Spalte hinzugefügt, entfernt oder geändert (ein Upgrade braucht SQL) |
| Kommandozeile | ein Befehl, eine Option, ein Ausgabeformat oder ein Exit-Code geändert |
| Verhalten | ein Standard geändert (Sicherheits-Header, Cookies, Zugriffsprüfungen), eine Berechtigung neu verlangt |
| Dateien | eine Datei, die Websites kopieren oder einbinden, in ihrer Form geändert |

## Die Regeln

- **Jeder Bruch hat Upgrade-Hinweise** in `doc/eng-us/bc/<series>/`, beginnend mit der Tabelle In short
  ([Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md)).
- **Neues Verhalten ist optional; Fehlerkorrekturen müssen es nicht sein.** Eine Funktion kommt ausgeschaltet oder mit
  einem Standard, der das alte Ergebnis behält.
- **Neue Einstellungen haben einen sicheren Standard** in der mitgelieferten Datei, einen Kommentar, einen Abschnitt auf
  der Funktionsseite und eine Zeile im Changelog.
- **Bezeichner werden nicht umbenannt.** Klassen, Tabellen, Einstellungen und Pfade `eZ*` und `ezp*` bleiben; die
  Umbenennung des Produkts hat nur Text geändert ([Das Produkt heißt Exponential](../../funktionen/6.0/umbenennung-in-exponential.md)).
- **Verschobener Code behält seinen Einstiegspunkt.** Als Views, Skripte und Cronjob-Teile in 6.0.15 zu Klassen wurden,
  behielt jede Datei ihren Pfad als einzeiligen Aufruf.

## Abkündigung

- Eine ersetzte API bleibt verfügbar und wird mit `@deprecated` und ihrem Ersatz gekennzeichnet; einige schreiben bei
  Verwendung einen Hinweis im strikten Modus über `eZDebug::writeStrict()`.
- Ein umbenannter Befehl behält seinen alten Namen als abgekündigten Alias, der weiter funktioniert
  ([Forks der Plattformpakete und Umbenennungen von Befehlen](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)).
- Eine Entfernung wird im Abschnitt Removed des Changelogs und in einem Upgrade-Hinweis angekündigt.
- Aus der alten Codebasis geerbte abgekündigte APIs haben kein Entfernungsdatum; für die Linie 6.0 ist keines angekündigt.

## Bekannte Ausnahmen

- **Sicherheitsstandards wurden** im September und Oktober 2026 **ohne Opt-in geändert**: Sicherheits-Header,
  Session-Cookies mit `HttpOnly` und `SameSite`, eine strengere Upload-Prüfung, neue Zugriffsanforderungen für einige
  entfernte Dienste. Sicherheitskorrekturen werden wie Fehlerkorrekturen behandelt: standardmäßig an, dokumentiert mit dem
  Weg, sie bewusst rückgängig zu machen.
- Konten ohne Zeile in `ezuser_setting` können sich seit August 2026 nicht anmelden, und Override-Blöcke folgen seit
  Juli 2026 `Priority` ([Verhaltensänderungen von Juli und August 2026](../../bc/6.0/verhaltensaenderungen-2026-07-08.md)).

## Für die aktualisierende Seite

- Lesen Sie die Checkliste jedes Zeitraums, den Sie durchlaufen, der Reihe nach.
- Durchsuchen Sie Ihre Erweiterungen nach überschriebenen Kernel-Klassen und kopierten Kernel-Templates: Sie erhalten
  keine Korrekturen und können still brechen.
- `./console exp:checkclasses` meldet Klassen, die PHP nicht laden will; die Upgrade-Prüfung vergleicht Dateien und
  Tabellen mit dem Release.
- Testen Sie mit Ihren eigenen Inhalten und Erweiterungen vor dem Produktions-Upgrade
  ([Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md)).

## Verwandte Seiten

- [Aktualisieren](../../anleitungen/aktualisieren.md)
- [Die Upgrade-Checkliste für Erweiterungen](../erweiterungsentwicklung/teil-4-aktualisieren/die-upgrade-checkliste.md)
- [Release- und Upgrade-Planung](../fuehrung/release-und-upgrade-planung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
