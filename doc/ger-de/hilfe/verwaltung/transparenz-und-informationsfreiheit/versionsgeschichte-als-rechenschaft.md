---
title: Versionsgeschichte als Rechenschaft
audience: government, editor, admin, project-manager
category: public-sector
order: 140
summary: Wie die Versionsgeschichte von Exponential zeigt, was eine veröffentlichte Seite zu einem früheren Zeitpunkt sagte und wer jede Version gespeichert hat, wie Sie zwei Versionen vergleichen, wer sie sehen darf und wo die Geschichte aufhört, eine vollständige Aufzeichnung zu sein.
keywords: Versionsgeschichte, Versionen, Versionen vergleichen, Rechenschaft, was hat sich geändert, frühere Version, archivierte Version, content/history, versionread, Aufzeichnung, Audit, version history, compare versions, accountability, what changed, earlier version, archived version, record
type: help
---
# Versionsgeschichte als Rechenschaft

Diese Seite richtet sich an Redaktionen, Führungskräfte und Zuständige für Schriftgut, die zeigen müssen, was eine
Seite zu einem bestimmten Zeitpunkt sagte und wer sie geändert hat. Jedes Speichern eines Inhaltsobjekts in
Exponential ist eine Version; die Versionsseite listet sie auf und vergleicht jeweils zwei davon
([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)). Der Audit-Trail ergänzt auf Systemebene, wer was getan hat
(siehe „Der Audit-Trail für ‚wer hat was geändert‘“).

## Inhalt

1. [Was eine Version ist](#was-eine-version-ist)
2. [Die Geschichte öffnen](#die-geschichte-öffnen)
3. [Zwei Versionen vergleichen](#zwei-versionen-vergleichen)
4. [Wer die Geschichte sehen darf](#wer-die-geschichte-sehen-darf)
5. [Wo die Geschichte nicht vollständig ist](#wo-die-geschichte-nicht-vollständig-ist)
6. [Die Frage beantworten: „Was stand an jenem Tag auf der Seite?“](#die-frage-beantworten-was-stand-an-jenem-tag-auf-der-seite)

## Was eine Version ist

- Ein Entwurf wird beim Veröffentlichen zur **veröffentlichten** Version; die Version davor wird **archiviert**.
- Ein Freigabe-Workflow kann eine Version als **ausstehend** oder **abgelehnt** hinterlassen.
- Jede Version verzeichnet ihren Ersteller, wann sie erstellt und geändert wurde, ihre Sprache und alle ihre Felder,
  einschließlich der Dateien.
- Auch Übersetzungen werden versioniert: Das Veröffentlichen des deutschen Textes erzeugt eine Version auf Deutsch.

## Die Geschichte öffnen

- Im Editor: **Versionen verwalten** in der linken Spalte.
- Vom Eintrag aus: über sein Kontextmenü oder die Adresse `/content/history/<object ID>`.

Die Seite zeigt eine Übersicht (ID, erstellt, geändert, veröffentlichte Version), Filter und eine Karte pro Version mit
dem, was Sie damit tun dürfen. Filter und Sortierung werden in der Adresse gehalten, sodass ein Link dieselbe Liste
zeigt.

## Zwei Versionen vergleichen

Wählen Sie unter **Zwei Versionen vergleichen** die **Ältere Version**, die **Neuere Version** und die
**Übersetzung** und vergleichen Sie. Der Vergleich zeigt die Felder, die sich unterscheiden. Verwenden Sie ihn, um
genau zu zeigen, was sich bei einer Berichtigung geändert hat.

Um eine alte Version als Ganzes anzusehen, öffnen Sie sie (**Ansehen**, Richtlinie `content/versionread`).

## Wer die Geschichte sehen darf

Die Geschichte ist intern. Besucher sehen nie ältere Versionen. Mitarbeitende brauchen `content/versionread`; um eine
Version wiederherzustellen, erstellen sie einen **neuen Entwurf aus dieser Version** und veröffentlichen ihn, was eine
neue Version ergibt, sodass die Wiederherstellung selbst in der Geschichte steht.

## Wo die Geschichte nicht vollständig ist

| Grenze | Was zu tun ist |
|---|---|
| Die Anzahl der Versionen ist begrenzt: `content.ini [VersionManagement] DefaultVersionHistoryLimit=45`, bei Ordnern 75 | Für aktenrelevante Klassen anheben (siehe „Versionen länger aufbewahren“) |
| Mitarbeitende mit `content/versionremove` können archivierte Versionen entfernen | Geben Sie diese Richtlinie nur Administratoren |
| Das Entfernen der Übersetzung eines Objekts löscht diese Sprache in jeder Version | Beschränken Sie `content/remove` und das Entfernen von Übersetzungen |
| Das Entfernen des Objekts entfernt seine Geschichte (nachdem der Papierkorb geleert wurde) | Entfernen Sie keine aktenrelevanten Inhalte; verbergen oder zurücknehmen |
| Änderungen an Einstellungen und Struktur sind keine Versionen | Der Audit-Trail zeichnet sie auf |

Die Versionsgeschichte ist kein zertifiziertes Schriftgutsystem. Für langfristiges Schriftgut geben Sie Dokumente nach
Ihrer Schriftgutrichtlinie an Ihr Schriftgutsystem oder Archiv ab.

## Die Frage beantworten: „Was stand an jenem Tag auf der Seite?“

1. Öffnen Sie die Geschichte der Seite.
2. Suchen Sie die Version, die an jenem Tag veröffentlicht war: diejenige, die vor dem Datum veröffentlicht und erst
   danach ersetzt wurde.
3. Öffnen Sie sie und speichern oder drucken Sie sie für die Akte; notieren Sie Versionsnummer und Objekt-ID.
4. Wenn die Version wegen der Grenze nicht mehr vorhanden ist, suchen Sie in Datensicherungen und im Audit-Trail, wann
   sie veröffentlicht wurde.

## Verwandte Seiten

- [Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)
- [Versionen länger aufbewahren](versionen-laenger-aufbewahren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
