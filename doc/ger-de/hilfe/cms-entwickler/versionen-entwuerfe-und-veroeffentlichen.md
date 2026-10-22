---
title: Versionen, Entwürfe und Veröffentlichen
audience: cms-developer
category: cms-development
order: 17
summary: Für CMS-Entwickler das Leben einer Version. Die Seite behandelt Entwürfe, Veröffentlichen und archivierte Versionen, was die Veröffentlichungsoperation für Sie erledigt, Grenzen und Bereinigung des Versionsverlaufs, automatisches Speichern, Vorschau, zeitgesteuertes Verbergen und Entfernen sowie asynchrones Veröffentlichen.
keywords: Versionen, Entwürfe, veröffentlichen, Veröffentlichungsoperation, Versionsstatus, archiviert, ausstehend, interner Entwurf, Grenze des Versionsverlaufs, cleanupversions, automatisches Speichern, ezautosave, Vorschau, zurückziehen, Datum zum Verbergen, asynchrones Veröffentlichen, Workflows, versions, drafts, publish, version history limit, autosave, preview, unpublish, asynchronous publishing
type: help
---
# Versionen, Entwürfe und Veröffentlichen

Diese Seite richtet sich an Entwickler, die wissen müssen, was zwischen dem Klick eines Redakteurs auf **Bearbeiten**
und der Änderung der Seite auf der Website geschieht, weil ihre Templates, Workflows, Importe und Caches davon abhängen.

## Inhalt

1. [Eine Version wird nach dem Veröffentlichen nie bearbeitet](#eine-version-wird-nach-dem-veröffentlichen-nie-bearbeitet)
2. [Versionsstatus](#versionsstatus)
3. [Was das Veröffentlichen tut](#was-das-veröffentlichen-tut)
4. [Wie viele Versionen aufbewahrt werden](#wie-viele-versionen-aufbewahrt-werden)
5. [Entwürfe, automatisches Speichern und verwaiste Entwürfe](#entwürfe-automatisches-speichern-und-verwaiste-entwürfe)
6. [Vorschau](#vorschau)
7. [Veröffentlichen und Verbergen zu einem Datum](#veröffentlichen-und-verbergen-zu-einem-datum)
8. [Asynchrones Veröffentlichen](#asynchrones-veröffentlichen)
9. [Für Ihren Code](#für-ihren-code)

## Eine Version wird nach dem Veröffentlichen nie bearbeitet

Jedes **Bearbeiten** erzeugt eine neue Entwurfsversion mit einer Kopie jeder Übersetzung. Redakteure ändern den
Entwurf; die veröffentlichte Version bleibt auf der Website, bis der Entwurf veröffentlicht wird. Das Veröffentlichen
macht den Entwurf zur aktuellen Version und archiviert die vorherige. Redakteure finden ihre offenen Entwürfe unter
**Meine Entwürfe** (`/content/draft`) und vergleichen oder stellen alte Versionen im Versionsverlauf eines Objekts
wieder her ([Versionen eines Objekts](../../anleitungen/inhaltsverlauf.md)).

## Versionsstatus

Die Status sind Konstanten von `eZContentObjectVersion` (`kernel/classes/ezcontentobjectversion.php`):

| Status | Wert | Bedeutung |
|---|---|---|
| `STATUS_DRAFT` | 0 | wird bearbeitet |
| `STATUS_PUBLISHED` | 1 | die aktuelle Version |
| `STATUS_PENDING` | 2 | wartet, zum Beispiel auf einen Freigabe-Workflow |
| `STATUS_ARCHIVED` | 3 | eine früher veröffentlichte Version |
| `STATUS_REJECTED` | 4 | von einem Freigebenden abgelehnt |
| `STATUS_INTERNAL_DRAFT` | 5 | ein vom System angelegter Entwurf, den der Redakteur noch nicht angefasst hat |
| `STATUS_REPEAT` | 6 | von Workflows verwendet, die erneut laufen |
| `STATUS_QUEUED` | 7 | wartet auf asynchrones Veröffentlichen |

## Was das Veröffentlichen tut

Das Veröffentlichen führt die Veröffentlichungsoperation des Kernels aus (`content/publish`). Unter anderem:

- führt sie die Workflows aus, die mit den Auslösern für das Veröffentlichen verbunden sind und das Veröffentlichen
  anhalten oder verzögern können ([Workflows und Auslöser](workflows-und-ausloeser.md));
- macht sie die Version zur aktuellen, setzt den Namen des Objekts aus dem Namensmuster und aktualisiert die URL-Aliase;
- aktualisiert sie den Suchindex, sofort oder über den Indexierungs-Cronjob, wenn verzögertes Indexieren eingeschaltet
  ist ([Suche](suche.md));
- leert sie die View-Caches der Knoten des Objekts und verwandter Knoten gemäß `viewcache.ini`
  ([Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md));
- sendet sie Benachrichtigungen an Abonnenten, sofern ein Redakteur nicht ohne Benachrichtigung veröffentlicht hat
  ([Ohne Benachrichtigung veröffentlichen](../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md)).

Deshalb muss Code über den Kernel veröffentlichen und darf nie selbst Versionszeilen schreiben
([Inhalte aus PHP](inhalte-aus-php.md)).

## Wie viele Versionen aufbewahrt werden

`settings/content.ini [VersionManagement]`:

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `DefaultVersionHistoryLimit` | `45` | aufbewahrte Versionen je Objekt; die älteste archivierte Version entfällt, wenn die Grenze erreicht ist |
| `VersionHistoryClass[<class id>]` | `[1]=75` | eine andere Grenze für eine Klasse (Klasse 1 ist der Ordner) |
| `DraftsDuration[]`, `DraftsCleanUpLimit` | 90 Tage, 100 | das Alter und die Anzahl je Lauf alter Entwürfe, die der Cronjob `old_drafts_cleanup.php` entfernt; dieser Cronjob gehört im Auslieferungszustand zu keinem Teil |
| `InternalDraftsDuration[]`, `InternalDraftsCleanUpLimit` | 1 Tag, 100 | Entfernen unberührter interner Entwürfe durch den Cronjob `internal_drafts_cleanup.php` |

Um alte Versionen sofort zu entfernen, lesen Sie zuerst die Hilfe; ohne `--help` entfernt der Befehl Versionen:

```bash
./console exp:cleanupversions --help --allow-root-user
```

## Entwürfe, automatisches Speichern und verwaiste Entwürfe

Ist die Erweiterung `ezautosave` aktiv, speichert das Bearbeitungsformular den Entwurf alle 180 Sekunden im Hintergrund
(`autosave.ini [AutosaveSettings] Interval`), sodass ein Redakteur, der den Browser-Tab verliert, den Entwurf unter
**Meine Entwürfe** wiederfindet. Entwürfe, die niemand veröffentlicht, bleiben, bis sie entfernt werden; nehmen Sie
`old_drafts_cleanup.php` in einen Cronjob-Teil auf oder bitten Sie die Redakteure, sie zu entfernen
([Meine Entwürfe und meine ausstehenden Elemente](../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).

## Vorschau

Redakteure sehen einen Entwurf vor dem Veröffentlichen in der Vorschau an. `content.ini [VersionView]` legt fest,
welche Designs eine Vorschau verwenden kann (`AvailableSiteDesignList[]`) und welche Ansichtsmodi (`ViewModes[]`,
mitgeliefert `full`); eine Vorschau zeigt den Entwurf mit den Templates der Website, testen Sie Ihre
Full-View-Templates also auch mit Entwürfen
([Ansichtsmodi der Versionsvorschau](../../funktionen/6.0/ansichtsmodi-der-versionsvorschau.md)).

## Veröffentlichen und Verbergen zu einem Datum

Zwei Cronjobs des Standardteils reagieren auf Daten, die in Inhalten gespeichert sind (`settings/cronjob.ini`):

| Cronjob | Einstellung | Was er tut |
|---|---|---|
| `unpublish.php` | `content.ini [UnpublishSettings] RootNodeList[]`, `ClassList[]` | entfernt Objekte der aufgeführten Klassen unter den aufgeführten Knoten, sobald ihr Attribut `unpublish_date` in der Vergangenheit liegt |
| `hide.php` | `content.ini [HideSettings] RootNodeList[]`, `HideDateAttributeList[<class>]=<attribute>` | verbirgt Knoten, deren Datumsattribut in der Vergangenheit liegt |

Beide Listen sind im Auslieferungszustand leer; es geschieht also nichts, bis Sie sie konfigurieren und Ihren Klassen
das Datumsattribut hinzufügen. Eine Freigabe vor dem Veröffentlichen ist ein Workflow, kein Datum
([Workflows und Auslöser](workflows-und-ausloeser.md)).

## Asynchrones Veröffentlichen

`content.ini [PublishingSettings] AsynchronousPublishing=disabled` ist der mitgelieferte Wert. Ist es eingeschaltet,
reiht das Veröffentlichen die Version ein (Status `STATUS_QUEUED`), und ein Daemon, `bin/php/ezasynchronouspublisher.php`,
veröffentlicht sie; die Composer-Metadaten vermerken, dass dafür die PHP-Erweiterung `pcntl` nötig ist. Verwenden Sie
es nur, wenn Redakteure beim Veröffentlichen großer Objekte warten müssen, und betreiben Sie den Daemon unter
Überwachung.

## Für Ihren Code

- In Templates liefern `$node.object.current_version` und `$node.contentobject_version` die Nummer der
  veröffentlichten Version; `$node.object.published` und `$node.object.modified` liefern die Daten.
- Erzeugen Sie in PHP Versionen über `eZContentFunctions::updateAndPublishObject()`, das die Version anlegt, die
  Attribute setzt und die Veröffentlichungsoperation ausführt ([Inhalte aus PHP](inhalte-aus-php.md)).
- Workflow-Events sehen die Version, die veröffentlicht wird, und können sie anhalten
  ([Workflow-Event-Typen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/workflow-ereignistypen.md)).

## Verwandte Seiten

- [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md): die Sicht der Redakteure
- [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md)
- [Das Inhaltsmodell](das-inhaltsmodell.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
