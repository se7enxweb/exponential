# Versionen eines Objekts: vergleichen, wiederherstellen, aufräumen

Diese Anleitung erklärt die **Versionsseite** eines Objekts in der Administration (`/content/history/<object id>`,
geöffnet mit **Versionen verwalten** im Editor oder aus dem Kontextmenü eines Elements): was eine Version ist, die
Übersicht, das Finden von Versionen nach Status, Übersetzung und Ersteller, das Vergleichen zweier Versionen, das
Anlegen eines neuen Entwurfs aus einer alten Version, das Entfernen von Versionen und wer was sieht.

Sie richtet sich an Redakteure und Administratoren. Jede Seite, jedes Feld und jede Regel unten wurde am 6. Oktober
2026 am Code dieses Repositorys geprüft; die Dateien sind unter [Quellen](#quellen) genannt.

[Anleitungen](README.md) · Siehe auch: [Bearbeitungszugriff auf nie veröffentlichte Objekte](../../en/us/bc/6.0/draft-edit-access.md) ·
[Blättern in Listen](../../en/us/features/6.0/admin-list-paging.md)

## Kurz gesagt

- Jedes Speichern eines Objekts ist eine **Version**. Ein Entwurf wird beim Veröffentlichen zur **veröffentlichten**
  Version, und die Version davor wird **archiviert**. Ein Freigabe-Workflow kann eine Version **ausstehend** oder
  **abgelehnt** lassen.
- Sie bearbeiten nur **Ihre eigenen Entwürfe** direkt. Um eine andere Version zu ändern, legen Sie **daraus einen
  neuen Entwurf** an: Der neue Entwurf gehört Ihnen, in der Übersetzung, die Sie wählen, und Sie bearbeiten und
  veröffentlichen ihn wie gewohnt.
- Die Seite zeigt eine Übersicht, Filter und eine Karte pro Version mit dem, was Sie damit tun dürfen; eine Aktion, die
  Sie nicht verwenden dürfen, sagt, warum.
- Filter, Reihenfolge und Seite werden in der Adresse festgehalten, sodass ein Link auf die Seite dieselbe Liste zeigt.
- Alles funktioniert ohne JavaScript. Mit JavaScript erhalten Sie zusätzlich **Alle auswählen**, die Anzahl der
  markierten Versionen und die Liste der Versionen in der Bestätigung des Entfernens.

## 1. Die Seite

| Teil | Was er zeigt |
| --- | --- |
| Einleitung | Was Versionen sind und wie aus einer Version ein neuer Entwurf angelegt wird |
| Hinweise | Was die letzte Aktion getan hat (entfernt, kopiert), was sie nicht getan hat und warum, und für einen Redakteur, der das Objekt bearbeiten, aber nicht lesen darf, was dieser hier tun kann |
| Kennzahlen | Alle Versionen, die Anzahl in jedem Status, die Übersetzungen, in denen sie angelegt wurden, Ihre Entwürfe. Eine Statuszahl filtert die Liste |
| Filter | **Status**, **Übersetzung** und **Ersteller** (angezeigt, wenn es mehr als einen gibt) und die **Reihenfolge**: neueste zuerst, älteste zuerst, zuletzt geändert |
| Versionen | Eine Karte pro Version: Nummer, Status, *Aktuelle Version*, *Ihre*, ihre Übersetzungen (die in dieser Version geänderte ist markiert), Ersteller, angelegt und geändert, und die Aktionen |
| Blättern | 10, 25 oder 50 Versionen pro Seite (`admininterface.ini [PaginationSettings]`, Schlüssel `content/history`; Ihre Wahl wird als Präferenz gespeichert) |
| Zwei Versionen vergleichen | Zwei Auswahlfelder (ältere, neuere) und die Übersetzung |
| Untere Leiste | **Zurück**, **Ausgewählte Versionen entfernen** (direkt an Ort und Stelle bestätigt) und ob Sie nach dem Anlegen eines neuen Entwurfs auf der Seite bleiben |

## 2. Die Aktionen einer Version

| Aktion | Angeboten, wenn | Andernfalls sagt die Karte |
| --- | --- | --- |
| **Ansehen** | `content/versionread` die Version erlaubt | Zum Ansehen braucht es die Richtlinie content/versionread |
| **Bearbeiten** | Es Ihr eigener Entwurf ist und Sie das Objekt bearbeiten dürfen | Nur Entwürfe werden bearbeitet, oder der Entwurf gehört jemand anderem: Legen Sie daraus einen neuen Entwurf an |
| **Vergleichen** | Sie die Version lesen dürfen und `content/diff` haben | (nicht angezeigt) |
| **Neuen Entwurf machen** | Sie das Objekt bearbeiten, die Version lesen und eine ihrer Übersetzungen bearbeiten dürfen | Sie dürfen sie nicht lesen, dürfen keine ihrer Übersetzungen bearbeiten, oder es ist ein unberührter Entwurf |
| Zum Entfernen auswählen | Entwurf, archiviert, abgelehnt oder unberührter Entwurf, mit `content/versionremove` | Die veröffentlichte Version und Versionen in einem laufenden Workflow werden nie entfernt |

**Vergleichen** auf einer Karte vergleicht diese Version mit der aktuellen (oder, bei der aktuellen, mit der neuesten
anderen Version, die Sie lesen dürfen). Die beiden Auswahlfelder vergleichen zwei beliebige Versionen. Die
Unterschiede werden über der Liste angezeigt: als Änderungen im Text, als Blockänderungen, als alte oder als neue
Version.

## 3. Ein neuer Entwurf aus einer alten Version

1. Suchen Sie die Version (filtern Sie nach dem Status *Archiviert* oder nach Übersetzung).
2. Wählen Sie auf ihrer Karte die Übersetzung, in der der neue Entwurf beginnt, und klicken Sie auf **Neuen Entwurf
   machen**.
3. Ist **Auf dieser Seite bleiben** markiert (die Voreinstellung), sagt die Seite, welcher Entwurf angelegt wurde;
   bearbeiten Sie ihn über seine Karte. Heben Sie die Markierung auf, um den neuen Entwurf sofort im Editor zu öffnen.
4. Veröffentlichen Sie den Entwurf wie gewohnt. Hat das Objekt bereits so viele Versionen, wie die Begrenzung der
   Versionshistorie (`content.ini [VersionManagement]`) erlaubt, wird die älteste archivierte Version entfernt, um
   Platz zu schaffen.

## 4. Versionen entfernen

Markieren Sie die Versionen, öffnen Sie **Ausgewählte Versionen entfernen**, prüfen Sie die Liste der genannten
Versionen und klicken Sie auf **Endgültig entfernen**. Die Seite sagt, welche Versionen entfernt wurden und welche
nicht (zum Beispiel eine ausstehende Version, die zu einem noch laufenden Workflow gehört).

## 5. Zurück

**Zurück** kehrt dorthin zurück, wo Sie die Seite geöffnet haben: zur Bearbeitung, die sie mit **Versionen verwalten**
geöffnet hat, oder zu der Seite, von der Sie kamen. Direkt geöffnet, führt es zum eigenen Ort des Objekts im
Inhaltsbaum, oder bei einem nie veröffentlichten Objekt zum Dashboard. Es öffnet nie eine Bearbeitung, die einen
Entwurf anlegen würde.

## 6. Wer was sieht

Wer das Objekt bearbeiten darf, öffnet seine Versionen, auch ohne eine Leserichtlinie dafür. Ein solcher Redakteur
sieht, was jede Version ist, und darf die veröffentlichte, die archivierten und die abgelehnten Versionen sowie seine
eigenen vergleichen und kopieren; der Entwurf oder die ausstehende Version eines anderen erfordert
`content/versionread`. Die vollständigen Regeln stehen unter [Bearbeitungszugriff auf nie veröffentlichte
Objekte](../../en/us/bc/6.0/draft-edit-access.md#the-versions-of-such-an-object-contenthistory).

## Probleme

| Symptom | Ursache und Lösung |
| --- | --- |
| Kein **Neuen Entwurf machen** bei einer Version | Sie dürfen sie nicht lesen (der Entwurf eines anderen), dürfen keine ihrer Übersetzungen bearbeiten, oder es ist ein unberührter Entwurf; die Karte sagt, was zutrifft |
| Kein **Ausgewählte Versionen entfernen** | Keine Version auf der Seite kann von Ihnen entfernt werden; die Seite sagt über der Liste, warum |
| Eine Version ist nach dem Anlegen eines neuen Entwurfs verschwunden | Die Begrenzung der Versionshistorie war erreicht, und die älteste archivierte Version hat Platz gemacht |
| „Keine Version wurde entfernt“ | Es war nichts markiert |

## Quellen

- View: `kernel/private/classes/views/content/history.php` (`History::canOpen()`, `canSeeVersionContent()`,
  `originURI()`); Liste, Filter und Aktionen: `kernel/classes/expcontenthistorylist.php`
- Templates: `design/admin4/templates/content/history.tpl` (dasselbe in `design/admin` und `design/admin3`),
  `content/history_access_messages.tpl`; das Aussehen steht in `stylesheets/modules/content.css`
- Tests: `tests/tests/kernel/classes/expContentHistoryListTest.php`, `eZContentHistoryAccessTest.php` (ohne
  Datenbank), `eZContentHistoryAccessLiveTest.php`
