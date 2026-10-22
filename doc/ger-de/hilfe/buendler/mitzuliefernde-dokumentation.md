---
title: Mitzuliefernde Dokumentation eines Bundles
audience: bundler, partner
category: bundling
order: 150
summary: Welche Dokumentation ein Bundle von Exponential mitbringt - den Baum doc/ mit der eingebauten Hilfe, ihre Lizenz GNU FDL 1.3 und ihre Hinweise, Ihre eigenen Anleitungen - was die GFDL Ihnen mit der Hilfe erlaubt (kopieren, anpassen, übersetzen, verkaufen) und was sie dafür verlangt, sowie eine Liste von Seiten, auf die Sie die Empfänger hinweisen sollten.
keywords: Dokumentation, GFDL, Hilfe, Handbuch, anpassen, übersetzen, Bundle-Dokumentation, erste Schritte, documentation, GNU Free Documentation License, doc/, help, LICENSE-GFDL-1.3.txt, README-LICENSE.md, manual, adapt, translate, bundle documentation, first steps
type: help
---
# Mitzuliefernde Dokumentation eines Bundles

Diese Seite richtet sich an Bündler, die entscheiden, welche Dokumentation in ein Bundle kommt und was sie selbst
schreiben. Die Hilfe, die Sie gerade lesen, ist freie Dokumentation: Sie dürfen sie ausliefern, anpassen und Ihr
eigenes Handbuch darauf aufbauen.

## Inhalt

1. [Was Exponential bereits mitliefert](#was-exponential-bereits-mitliefert)
2. [Was die GFDL Ihnen erlaubt](#was-die-gfdl-ihnen-erlaubt)
3. [Was die GFDL verlangt](#was-die-gfdl-verlangt)
4. [Was Sie selbst schreiben sollten](#was-sie-selbst-schreiben-sollten)
5. [Seiten, auf die Sie die Empfänger hinweisen](#seiten-auf-die-sie-die-empfänger-hinweisen)

## Was Exponential bereits mitliefert

| Teil | Wo | Hinweise |
|---|---|---|
| Der Dokumentationsbaum | `doc/` (Englisch in `doc/eng-us/`, Deutsch in `doc/ger-de/`) | Installationsanleitung, Funktionen, Upgrade-Hinweise, Beispiele, diese Hilfe |
| Die eingebaute Hilfe | das Hilfe-Dashboard in der Administration | liest die Seiten aus `doc/`; "Nur von Git verfolgte Dateien unter doc/ ... sind Seiten" für das Kommandozeilenwerkzeug, und eine Installation ohne `.git` verwendet das Manifest `share/filelist.md5` als Liste |
| Die Lizenz | `doc/eng-us/LICENSE-GFDL-1.3.txt` | GNU Free Documentation License 1.3 |
| Urheberrecht und Ausnahmen | `doc/eng-us/README-LICENSE.md` | wer das Urheberrecht hält, ältere Seiten mit anderen Bedingungen |

Die Dateiliste der Hilfe stammt aus dem Git-Index, `git ls-files`, oder aus `share/filelist.md5` "für eine
Installation ohne .git" (`kernel/classes/help/exphelpallowlist.php`). Ein ohne `.git` gebautes Bundle braucht daher
ein aktuelles Manifest, damit die eingebaute Hilfe ihre Seiten auflistet ([Integritätsprüfungen](integritaetspruefungen.md)).

## Was die GFDL Ihnen erlaubt

Aus [Freie Dokumentation](../freie-dokumentation.md), in Kürze:

- die Seiten lesen, kopieren und weitergeben, "kostenlos oder gegen Gebühr, in jedem Medium";
- sie übersetzen;
- sie zu Ihrem eigenen Handbuch, Kursmaterial oder Kundenleitfaden umarbeiten.

## Was die GFDL verlangt

Wenn Sie Kopien weitergeben, behalten Sie bei jeder Kopie bei:

- die **Urheberrechtshinweise** der Seiten;
- den **Lizenzhinweis**;
- eine **Kopie der Lizenz** (`doc/eng-us/LICENSE-GFDL-1.3.txt`).

"Fügen Sie keine eigenen Bedingungen hinzu und verwenden Sie keine technischen Maßnahmen ..., die die Menschen, denen
Sie Kopien geben, daran hindern, sie zu lesen oder weiter zu kopieren." Mehr als 100 Kopien in einem schwer
bearbeitbaren Format brauchen zusätzlich eine bearbeitbare Kopie; eine angepasste Fassung wird wieder unter der GFDL
veröffentlicht. Die Einzelheiten stehen in der Lizenz und unter [Freie Dokumentation](../freie-dokumentation.md).
Keine Rechtsberatung.

## Was Sie selbst schreiben sollten

Das Projekt dokumentiert Exponential; Ihr Bundle können nur Sie dokumentieren:

| Dokument | Inhalt |
|---|---|
| Erste Schritte | wie das Bundle gestartet wird, wo die Website und die Administration sind, wie das erste Passwort den Empfänger erreicht |
| Was enthalten ist | die Versionen, die Erweiterungen, das Seitenpaket, die Laufzeitumgebung; Ihre Liste der Komponenten Dritter |
| Konfiguration | wo die Einstellungen Ihres Bundles liegen und welche der Empfänger ändern darf |
| Updates | wie der Empfänger aktualisiert und wie Sie Updates ankündigen ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)) |
| Support | was Sie unterstützen und wo man fragt ([Grenzen des Supports](grenzen-des-supports.md)) |
| Lizenzen | Ihre Hinweisdatei und das Quellcode-Angebot ([Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md)) |

Diese Dokumente auf dieser Hilfe aufzubauen ist willkommen; behalten Sie die Hinweise bei.

## Seiten, auf die Sie die Empfänger hinweisen

| Bedarf | Seite |
|---|---|
| Anmelden und das Passwort ändern | [Das Standard-Administratorpasswort](../standard-administratorpasswort.md), [Das Administratorpasswort ändern](../administratorpasswort-aendern.md) |
| Die Installation härten | [Sicherheitshärtung](../sicherheitshaertung.md), [Kapitel 13](../../installation/13-sicherheitshaertung.md) |
| Sichern und wiederherstellen | [Kapitel 10.9 Backups und Wiederherstellung](../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung) |
| Aktualisieren | [Mit Git aktualisieren](../mit-git-aktualisieren.md), [Kapitel 11: Upgrade](../../installation/11-aktualisieren.md) |
| Datenbanken | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Wenn etwas schiefgeht | [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md) |

## Verwandte Seiten

- [Die Lizenz dieser Dokumentation](../../README-LIZENZ.md)
- [Dokumentation und Übersetzungen beitragen](../partner/dokumentation-und-uebersetzungen-beitragen.md)
- [Mitzuliefernde Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
