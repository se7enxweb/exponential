---
title: Feste IDs und numerische Codes
audience: cms-developer
category: cms-development
order: 50
summary: Die Knoten-, Objekt- und Sektions-IDs, mit denen eine neue Installation beginnt, die Nummern der Sortierfelder und Sortierrichtungen sowie die Statuscodes von Objekten und Versionen, jeweils mit der Einstellung oder Konstante, aus der sie stammen.
keywords: Knoten-ID, Objekt-ID, Wurzelknoten, Knoten oberster Ebene, RootNode, UserRootNode, MediaRootNode, AnonymousUserID, Sektions-ID, sort_field, sort_order, Versionsstatus, Objektstatus, Konstanten, Referenz, node id, object id, root node, top-level nodes, section id, version status, object status, constants, reference
type: help
---
# Feste IDs und numerische Codes

Dies ist eine Nachschlageseite. Einstellungen, Fetch-Parameter, die Datenbank und Importe verwenden Zahlen, wo die
Administration Namen zeigt; die Tabellen unten übersetzen sie. Mit *Einstellung* gekennzeichnete Werte können je
Website abweichen; lesen Sie sie im Code also aus der Einstellung, statt die Zahl hineinzuschreiben.

## Die Spitze des Baums

Der Baum hat einen Wurzelknoten, ID 1, der kein Objekt hat und weder entfernt noch angezeigt werden kann. Darunter
liegen die Knoten oberster Ebene; jeder beginnt einen Zweig mit eigener Sektion und eigenem Reiter im oberen Menü.

| Knoten | Zweig | Einstellung (`content.ini [NodeSettings]`) | Sektion |
|---|---|---|---|
| 1 | die Wurzel, kein Objekt | keine | keine |
| 2 | Inhalte | `RootNode` | Standard (1) |
| 5 | Benutzer | `UserRootNode` | Users (2) |
| 43 | Medien | `MediaRootNode` | Media (3) |
| 48 | Setup | `SetupRootNode` | Setup (4) |
| 58 | Design | `DesignRootNode` | Design (5) |

Knoten oberster Ebene lassen sich weder entfernen noch verschieben: Die Berechtigungsprüfung verweigert beides für
jeden Knoten, dessen übergeordneter Knoten die Wurzel ist. Lesen Sie sie in Templates mit
`ezini( 'NodeSettings', 'RootNode', 'content.ini' )`, statt `2` zu schreiben.

## Benutzer und Benutzergruppen einer neuen Installation

Diese IDs stammen aus den Daten der sauberen Installation. Seitenpakete können Gruppen hinzufügen oder umbenennen;
prüfen Sie also Ihre eigene Website unter **Benutzer**.

| Objekt-ID | Knoten-ID | Was | Einstellung |
|---|---|---|---|
| 4 | 5 | die Gruppe Users, Spitze des Benutzerbaums | |
| 10 | 45 | der anonyme Benutzer, die Identität jedes Besuchers, der nicht angemeldet ist | `site.ini [UserSettings] AnonymousUserID` |
| 11 | 12 | die Gruppe, in der selbst registrierte Benutzer angelegt werden (Guest accounts in den sauberen Daten, oft in Members umbenannt) | `site.ini [UserSettings] DefaultUserPlacement` (die **Knoten**-ID) |
| 12 | 13 | Administrator users | |
| 13 | 14 | Editors | |
| 14 | 15 | das erste Administratorkonto | |
| 42 | 44 | Anonymous Users, die Gruppe des anonymen Benutzers | |

Der anonyme Benutzer und der erste Administrator dürfen nie entfernt werden: Die Website verwendet den ersten für jeden
Besucher, und der zweite ist unter Umständen das einzige Konto, das die Setup-Seiten erreicht.

Die Klassen dahinter sind `site.ini [UserSettings] UserClassID=4` (User) und `UserGroupClassID=3` (User group).

## Sektionen

| ID | Name | Bezeichner |
|---|---|---|
| 1 | Standard | `standard` |
| 2 | Users | `users` |
| 3 | Media | `media` |
| 4 | Setup | `setup` |
| 5 | Design | `design` |

Sektions-IDs werden nach dem Entfernen einer Sektion nie wiederverwendet. Override-Regeln können entweder auf die ID
(`Match[section]`) oder auf den Bezeichner (`Match[section_identifier]`) passen; der Bezeichner übersteht einen Umzug
in eine andere Installation, die ID unter Umständen nicht.

## Sortierfelder und Sortierrichtungen

Jeder Knoten speichert, wie seine Unterelemente sortiert werden: ein Feld (`sort_field`) und eine Richtung
(`sort_order`). Die Zahlen sind Konstanten von `eZContentObjectTreeNode`; `$node.sort_array` wandelt sie in die Form
um, die ein Fetch annimmt.

| `sort_field` | Konstante | Sortiert nach | Name in `sort_by` eines Fetch |
|---|---|---|---|
| 1 | `SORT_FIELD_PATH` | Platz im Baum | `path` |
| 2 | `SORT_FIELD_PUBLISHED` | Datum der ersten Veröffentlichung | `published` |
| 3 | `SORT_FIELD_MODIFIED` | letzter Änderung | `modified` |
| 4 | `SORT_FIELD_SECTION` | Sektion | `section` |
| 5 | `SORT_FIELD_DEPTH` | Tiefe im Baum | `depth` |
| 6 | `SORT_FIELD_CLASS_IDENTIFIER` | Klassenbezeichner | `class_identifier` |
| 7 | `SORT_FIELD_CLASS_NAME` | Klassenname | `class_name` |
| 8 | `SORT_FIELD_PRIORITY` | der Priorität, die Redakteure von Hand setzen | `priority` |
| 9 | `SORT_FIELD_NAME` | Objektname | `name` |
| 10 | `SORT_FIELD_MODIFIED_SUBNODE` | letzter Änderung irgendwo unter dem Knoten | `modified_subnode` |
| 11 | `SORT_FIELD_NODE_ID` | Knoten-ID | `node_id` |
| 12 | `SORT_FIELD_CONTENTOBJECT_ID` | Objekt-ID | `contentobject_id` |
| 13 | `SORT_FIELD_VISIBILITY` | verborgen oder sichtbar | `visibility` (das `sort_array` eines Knotens nennt es `is_invisible`, was der Fetch nicht annimmt: übergeben Sie `visibility` selbst) |

| `sort_order` | Konstante | Bedeutung | In einem Fetch |
|---|---|---|---|
| 0 | `SORT_ORDER_DESC` | absteigend | `false()` |
| 1 | `SORT_ORDER_ASC` | aufsteigend | `true()` |

Eine Liste, die der von den Redakteuren gewählten Reihenfolge folgt:

```
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                  'sort_by', $node.sort_array ) )}
```

## Statuscodes

Ein Objekt (`ezcontentobject.status`, Konstanten von `eZContentObject`):

| Status | Konstante | Bedeutung |
|---|---|---|
| 0 | `STATUS_DRAFT` | angelegt, nie veröffentlicht |
| 1 | `STATUS_PUBLISHED` | hat eine veröffentlichte Version |
| 2 | `STATUS_ARCHIVED` | im Papierkorb |

Eine Version (`ezcontentobject_version.status`, Konstanten von `eZContentObjectVersion`):

| Status | Konstante | Bedeutung |
|---|---|---|
| 0 | `STATUS_DRAFT` | wird bearbeitet; nur ihr Ersteller darf sie bearbeiten |
| 1 | `STATUS_PUBLISHED` | die Version, die Leser sehen; eine je Objekt |
| 2 | `STATUS_PENDING` | zur Veröffentlichung geschickt, von einem Workflow zurückgehalten (Freigabe, ein Datum) |
| 3 | `STATUS_ARCHIVED` | eine früher veröffentlichte Version, im Verlauf aufbewahrt |
| 4 | `STATUS_REJECTED` | abgelehnt; von den mitgelieferten Workflows nicht gesetzt, die eine abgelehnte Version in den Entwurf zurückgeben |
| 5 | `STATUS_INTERNAL_DRAFT` | ein geöffneter, aber noch nicht gespeicherter Entwurf; alte werden von einem Cronjob entfernt |
| 6 | `STATUS_REPEAT` | ein Workflow-Event hat verlangt, sein Formular erneut zu zeigen, damit die Version noch einmal bearbeitet werden kann |
| 7 | `STATUS_QUEUED` | wartet in der Warteschlange des asynchronen Veröffentlichens |

Nur veröffentlichte Objekte haben Knoten. Ein Entwurf eines neuen Objekts, eine ausstehende erste Version und ein
Objekt im Papierkorb sind nicht im Baum; ein Fetch von Knoten gibt sie also nie zurück.

## Verwandte Seiten

- [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md)
- [Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md)
- [Referenz der Knotenattribute](../template-sprache/referenz-der-knotenattribute.md)
- [Sektionen](../../anleitungen/sektionen.md)
- [Glossar für CMS-Entwickler](glossar-fuer-cms-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
