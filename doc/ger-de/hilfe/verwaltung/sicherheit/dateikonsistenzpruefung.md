---
title: Dateikonsistenzprüfung
audience: government, admin, hosting
category: public-sector
order: 240
summary: Für öffentliche Stellen, die unbefugte Änderungen an ihrer Webanwendung erkennen müssen - wie die Dateikonsistenzprüfung von Exponential jede Datei mit der Prüfsummenliste des Release vergleicht, in der Administration und auf der Kommandozeile, wann Sie sie ausführen und was mit einem Befund zu tun ist.
keywords: Dateikonsistenz, Integritätsprüfung, Prüfsummen, Manifest, Upgrade-Prüfung, Manipulation, Defacement, Aktualisierung, Sicherheitsvorfall, Änderungserkennung, öffentlicher Sektor, file consistency, integrity check, filelist.md5, manifest, checksums, Upgrade check, verifyfiles.sh, checkmanifest.php, tampering, defacement, upgrade, incident, change detection, public sector
type: help
---
# Dateikonsistenzprüfung

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams. Jedes Release von Exponential
liefert `share/filelist.md5` mit, eine Liste der Prüfsummen seiner Dateien. Der Vergleich der Installation mit ihr
zeigt, ob eine Datei geändert, entfernt oder zurückgelassen wurde, sei es durch ein misslungenes Upgrade oder durch
einen Eindringling ([Dateikonsistenzprüfung](../../../../eng-us/features/6.0/file-consistency-check.md)).

## Inhalt

1. [Die Prüfung ausführen](#die-prüfung-ausführen)
2. [Wann Sie sie ausführen](#wann-sie-sie-ausführen)
3. [Die Befunde lesen](#die-befunde-lesen)
4. [Grenzen](#grenzen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Prüfung ausführen

**In der Administration**: **Setup > Upgrade-Prüfung** (`/setup/systemupgrade`), **Überprüfe Dateikonsistenz**. Sie
liest `share/filelist.md5` und das Manifest jeder aktiven Erweiterung, die eines mitbringt, gruppiert die Befunde
(geändert, fehlend, nicht lesbar; verfolgte, aber nicht gelistete Dateien; fehlerhafte Zeilen), zeigt die Daten der
Manifeste an und bietet das Ergebnis als CSV oder Text an ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
**Überprüfe Datenbankkonsistenz** vergleicht das Schema.

**Auf der Kommandozeile**, aus dem Document-Root, den der Webserver verwendet:

```bash
bash bin/shell/verifyfiles.sh            # summary and the files that differ
bash bin/shell/verifyfiles.sh --quiet    # exit status only: 0 = all files match
php bin/php/checkmanifest.php --all --extensions           # same class as the admin page
php bin/php/checkmanifest.php --all --extensions --csv     # findings as CSV
```

## Wann Sie sie ausführen

| Zeitpunkt | Warum |
|---|---|
| nach jedem Update | Zurückgelassenes oder von Hand Bearbeitetes fällt sofort auf |
| wöchentlich aus der Überwachung (`--quiet`, Alarm bei Exit-Status 1) | unbemerkte Änderungen |
| nach einem Sicherheitsvorfall oder einem verdächtigen Alarm | um zu sehen, welche Dateien ein Angreifer angefasst hat |
| vor einem Penetrationstest oder einem Audit | um von einem bekannten Zustand auszugehen |

## Die Befunde lesen

- **Geänderte Kernel-Datei, die Sie nicht geändert haben**: Behandeln Sie das als mögliche Kompromittierung, bis es
  erklärt ist. Vergleichen Sie mit dem Release (dem Tag auf GitHub) und sehen Sie sich das Audit-Protokoll um den
  Änderungszeitpunkt der Datei herum an.
- **Geänderte Datei, die Sie selbst geändert haben** (ein lokaler Patch): Verlagern Sie die Änderung in eine
  Erweiterung oder einen Override; gepatchte Kernel-Dateien gehen beim nächsten Update verloren.
- **Fehlende Datei**: ein unvollständiges Update oder eine unvollständige Bereitstellung.
- **Dateien, die git verfolgt, die das Manifest aber nicht listet** (ein Hinweis, in einer Installation, die ein
  Git-Checkout ist): Dateien, die nach der Erstellung des Manifests hinzugekommen sind; eine Datei, die niemand erklären
  kann, verdient einen Blick.

Releases sind unveränderliche Tags: Eine Korrektur kommt als nächste Version, nie als geänderte Kopie, sodass das
Manifest einer Version immer dieselben Dateien beschreibt
([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).

## Grenzen

- Sie umfasst die Dateien, die die Manifeste listen: den Kernel und Erweiterungen, die ein Manifest mitliefern. Ihre
  eigenen Erweiterungen, `settings/override` und `var/` sind nicht abgedeckt.
- Ein Angreifer mit Schreibzugriff könnte auch das Manifest ändern; bewahren Sie eine Kopie des Manifests des Release
  außerhalb des Servers auf oder vergleichen Sie mit dem Tag.

## Verwandte Seiten

- [Dateikonsistenzprüfung](../../../../eng-us/features/6.0/file-consistency-check.md)
- [Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)
- [Patch-Rhythmus](patch-rhythmus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
