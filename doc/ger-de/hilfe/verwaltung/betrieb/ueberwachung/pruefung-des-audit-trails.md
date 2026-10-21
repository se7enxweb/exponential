---
title: Prüfung des Audit-Trails
audience: government, admin, project-manager
category: public-sector
order: 80
summary: Wie Exponential belegt, dass sein Audit-Trail weder bearbeitet noch gekürzt wurde - die Hash-Kette, signierte Manifeste der Archive, exp:audit verify und Jetzt prüfen - wie Sie die Prüfung in die Überwachung aufnehmen und was zu tun ist, wenn eine Kette gebrochen ist.
keywords: Audit-Prüfung, Hash-Kette, manipulationserkennend, exp:audit verify, Jetzt prüfen, Integrität, Beweise, Manifeste, Archive, gebrochene Kette, chain_broken, Forensik, audit verification, hash chain, tamper-evident, Verify now, integrity, evidence, manifests, archives, broken chain, forensic
type: help
---
# Prüfung des Audit-Trails

Diese Seite richtet sich an die Administration, Informationssicherheitsbeauftragte und Prüfende, die sich auf den
Audit-Trail als Beweismittel stützen. Der Audit-Trail macht Manipulationen erkennbar: Jeder Eintrag trägt den Hash des
vorherigen, Tagesdateien werden archiviert und in signierten Manifesten aufgeführt, und eine Prüfung zeigt Ihnen, ob
etwas bearbeitet oder gekürzt wurde ([Audit-Trail](../../../../../eng-us/features/6.0/audit-trail.md);
[Audit-Leitfaden, Abschnitt 4.4](../../../../bc/6.0/audit.md#44-prüfen-und-wiederherstellen)).

## Inhalt
1. [Was die Prüfung belegt](#was-die-prüfung-belegt)
2. [Ausführen](#ausführen)
3. [In der Überwachung](#in-der-überwachung)
4. [Wenn eine Kette gebrochen ist](#wenn-eine-kette-gebrochen-ist)
5. [Grenzen](#grenzen)
6. [Verwandte Seiten](#verwandte-seiten)

## Was die Prüfung belegt

- Kein Eintrag innerhalb einer Datei wurde geändert (der Hash jedes Eintrags umfasst den vorherigen).
- Kein Eintrag wurde aus der Mitte oder vom Ende einer laufenden Datei entfernt, ohne dass die Kette es zeigt.
- Archive stimmen mit dem Manifest überein, das bei der Archivierung geschrieben wurde (signiert mit einem HMAC-Schlüssel
  aus `settings/override/audit.ini.append.php`).

## Ausführen

```bash
./console exp:audit status --allow-root-user            # every channel and its chain state
./console exp:audit verify --allow-root-user            # live files; exit 0 intact, 1 broken, 2 error
./console exp:audit verify --archives --allow-root-user # everything, archives included
./console exp:audit verify --channel=access --date=2026-10-01 --allow-root-user
```

In der Administration: Reiter **Audit**, **Jetzt prüfen** im Dashboard oder in der Konsole (erfordert `audit/manage`);
einfache Seitenansichten zeigen den gespeicherten Zustand. Der Cronjob-Teil `audit` prüft täglich im Rahmen von
Rotation und Archivierung.

## In der Überwachung

```bash
./console exp:audit verify -q --allow-root-user || <your alert command>
```

Die Regel `chain_broken` sendet außerdem eine Warnung (Syslog und E-Mail), wenn die Prüfung einen Bruch findet. Prüfen
Sie, bevor Sie Einträge an Prüfende übergeben, und nach jedem Vorfall.

## Wenn eine Kette gebrochen ist

Laut Audit-Leitfaden: Bearbeiten oder entfernen Sie nichts. Notieren Sie Datei, Zeile und Art des Bruchs; kopieren Sie
das Audit-Verzeichnis an einen sicheren Ort; vergleichen Sie mit dem, was den Server verlassen hat: Kopien in Syslog oder
Journal, der Webhook-Empfänger, von den Senken gesendete Prüfpunkte, Archive. Eine gebrochene Datei bleibt eine
Aufzeichnung: Die Konsole zeigt sie weiterhin an und markiert sie. Behandeln Sie es als möglichen Vorfall
([Verdacht auf ein kompromittiertes Konto](../vorfaelle/verdacht-auf-kompromittiertes-konto.md)), denn jemand mit Zugriff auf
die Dateien des Servers könnte am Werk gewesen sein. Beachten Sie auch, dass ein Absturz oder eine volle Festplatte die
letzte Zeile einer Datei beschädigen kann; Abschnitt 5.3 des Leitfadens erklärt jede Art.

## Grenzen

- Manipulationen erkennbar zu machen heißt nicht, sie zu verhindern: Jemand mit Shell-Zugang kann die Dateien löschen.
  Erst eine externe Kopie (Syslog an einen zentralen Log-Server, die Webhook-Senke) macht das Löschen sichtbar und
  rückgängig machbar.
- Die Signaturschlüssel liegen in `settings/override/audit.ini.append.php`; sichern Sie sie getrennt und halten Sie sie
  von Personen fern, die die Dateien verändern könnten
  ([Schlüssel und Geheimnisse in Datensicherungen](../datensicherung-und-wiederherstellung/schluessel-und-geheimnisse-in-datensicherungen.md)).
- `OnWriteFailure=continue` (Standard) lässt die Website weiterlaufen, wenn das Audit nicht schreiben kann; `refuse`
  verweigert sicherheitsrelevante Aktionen, solange in ihren Kanal nicht geschrieben werden kann. Wählen Sie nach Ihrer
  Risikobereitschaft.

## Verwandte Seiten
- [Den Audit-Trail prüfen](../../../../../eng-us/help/government/tasks/operations/verify-the-audit-trail.md)
- [Audit-Warnungen](audit-warnungen.md)
- [Audit-Trail-Archive und Datensicherungen](../datensicherung-und-wiederherstellung/audit-trail-archive-und-datensicherungen.md)
- [Rollenentwurf: Prüfende](../personen-und-berechtigungen/rollenentwurf-pruefende.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
