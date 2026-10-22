---
title: Sektionen, Rollen und Richtlinien
audience: cms-developer
category: cms-development
order: 19
summary: Für CMS-Entwickler, wie der Zugriff in Exponential funktioniert. Die Seite behandelt Rollen aus Richtlinien, Einschränkungen nach Klasse, Sektion, Teilbaum, Eigentümer, Sprache und Zustand, die Zuweisung von Rollen, Sektionen und Objektzustände als Hebel, die Rolle Anonymous, die Prüfung des Zugriffs in Templates und in der Shell sowie bewährte Entwürfe.
keywords: Rollen, Richtlinien, Berechtigungen, Zugriffskontrolle, Einschränkungen, Sektion, Teilbaum, Klasse, Eigentümer, Sprache, Objektzustand, content/read, content/create, content/edit, Rolle Anonymous, Rollenzuweisung, can_edit, has_policy, accesscheck, Mitgliederbereich, roles, policies, permissions, access control, limitations, role assignment, members area
type: help
---
# Sektionen, Rollen und Richtlinien

Diese Seite richtet sich an Entwickler, die entscheiden, wer was lesen und ändern darf. Die Zugriffskontrolle von
Exponential ist Teil des Inhaltsmodells: Regeln werden gegen Klassen, Sektionen, Teilbäume und Zustände geschrieben;
ein gutes Modell hält die Regeln also kurz, und jeder Fetch, jede View und jeder API-Aufruf prüft sie, ohne dass Ihr
Code etwas tun muss.

## Inhalt

1. [Rollen, Richtlinien und Einschränkungen](#rollen-richtlinien-und-einschränkungen)
2. [Die Inhaltsrichtlinien, die Sie am häufigsten verwenden](#die-inhaltsrichtlinien-die-sie-am-häufigsten-verwenden)
3. [Einschränkungen](#einschränkungen)
4. [Rollen zuweisen](#rollen-zuweisen)
5. [Sektionen: der wichtigste Hebel](#sektionen-der-wichtigste-hebel)
6. [Objektzustände: Stufen mit eigenen Regeln](#objektzustände-stufen-mit-eigenen-regeln)
7. [Zugriff in Templates](#zugriff-in-templates)
8. [Zugriff prüfen und Fehler suchen](#zugriff-prüfen-und-fehler-suchen)
9. [Entwürfe, die funktionieren](#entwürfe-die-funktionieren)

## Rollen, Richtlinien und Einschränkungen

| Teil | Ist | Beispiel |
|---|---|---|
| Richtlinie | eine Berechtigung für ein Modul und eine Funktion (oder alle) | `content/read`, `content/create`, `user/login` |
| Einschränkung | eine Bedingung an einer Richtlinie | Klasse = Article; Sektion = Members |
| Rolle | ein benannter Satz von Richtlinien | *Editor*, *Member*, *Anonymous* |
| Zuweisung | eine Rolle, die einem Benutzer oder einer Benutzergruppe gegeben wird, optional auf einen Teilbaum oder eine Sektion eingeschränkt | *Editor* für die Gruppe *News team*, auf dem Teilbaum *News* |

Berechtigungen addieren sich nur: Es gibt keine Richtlinie zum "Verbieten". Ein Benutzer darf etwas tun, wenn eine
seiner Rollen eine Richtlinie hat, die es erlaubt. Rollen werden unter **Benutzer** > **Rollen und Richtlinien**
verwaltet ([Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)).

## Die Inhaltsrichtlinien, die Sie am häufigsten verwenden

Zu den Funktionen des Moduls `content` (`kernel/content/module.php`) gehören:

| Funktion | Erlaubt |
|---|---|
| `read` | Inhalte sehen: Views, Fetches, Suchergebnisse, die REST-Schnittstelle |
| `create` | Objekte einer Klasse unter einem übergeordneten Knoten anlegen |
| `edit` | neue Versionen vorhandener Objekte anlegen |
| `publish` | einen Entwurf veröffentlichen |
| `remove`, `move`, `hide`, `manage_locations` | den Baum ändern |
| `translate` | an Übersetzungen arbeiten |
| `versionread`, `versionremove`, `diff` | alte Versionen lesen, entfernen und vergleichen |
| `view_embed` | ein Objekt eingebettet in anderen Inhalten zeigen, ohne vollen Lesezugriff |
| `pdf`, `bookmark`, `dashboard`, `restore`, `cleantrash`, `jobs` | die jeweils genannten Funktionen |

Andere Module haben eigene Funktionen: `user/login` (Anmelden an einem Siteaccess), `user/selfedit`, `setup/*`,
`apikey/create` und so weiter. Ihre eigenen Module deklarieren ihre
([Richtlinienfunktionen und Zugriff](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md)).

## Einschränkungen

Die Einschränkungen der Inhaltsfunktionen sind `Class`, `Section`, `Owner`, `Group`, `Node`, `Subtree`, `Language`,
auf Seite des übergeordneten Knotens `ParentClass`, `ParentOwner`, `ParentGroup`, `Status` für Versionen und eine
Einschränkung je Gruppe von Objektzuständen. Innerhalb einer Richtlinie müssen alle Einschränkungen passen; über
Richtlinien hinweg genügt eine, die es erlaubt.

| Bedarf | Richtlinie |
|---|---|
| Redakteure legen Artikel nur in News an | `content/create` mit Klasse = Article und Teilbaum = News (oder Knoten = der News-Ordner) |
| Autoren ändern nur ihre eigenen Elemente | `content/edit` mit Eigentümer = selbst |
| Ein Übersetzer arbeitet nur auf Deutsch | `content/edit` und `content/translate` mit Sprache = Deutsch |
| Prüfer bearbeiten nur Elemente in Prüfung | `content/edit` mit der Zustandseinschränkung Ihrer Prüfgruppe = In review |

## Rollen zuweisen

Weisen Sie Rollen **Benutzergruppen** zu, nicht einzelnen Benutzern, damit ein neues Teammitglied den richtigen
Zugriff erhält, indem es in die Gruppe kommt. Eine Zuweisung kann selbst auf einen Teilbaum oder eine Sektion
eingeschränkt sein, sodass eine Rolle *Editor* mehreren Abteilungen dient, jede ihrem eigenen Teilbaum zugewiesen.
Benutzer sind Inhalte in Gruppen: [Benutzer und Registrierung](benutzer-und-registrierung.md).

## Sektionen: der wichtigste Hebel

Eine Sektion ist eine Kennzeichnung je Objekt. Eine neue Installation hat fünf (Standard, Users, Media, Setup,
Design), und die Rolle **Anonymous** liest nur Standard: Diese eine Einschränkung hält Benutzerkonten und andere
Sektionen von Besuchern fern ([Sektionen, Abschnitt 2](../../anleitungen/sektionen.md#2-die-sektionen-einer-neuen-installation)).
Für einen Mitgliederbereich legen Sie eine Sektion *Members* an, weisen ihr den Teilbaum zu und geben einer Rolle
*Member* `content/read`, eingeschränkt auf diese Sektion. Neue Inhalte übernehmen die Sektion ihres übergeordneten
Knotens. Sektionen wählen auch Templates (`Match[section]` in Override-Regeln) und Navigationsbereiche der
Administration ([Sektionen](../../anleitungen/sektionen.md)).

## Objektzustände: Stufen mit eigenen Regeln

Objektzustände sind eigene Kennzeichnungen in Gruppen (zum Beispiel *Review*: Entwurf, in Prüfung, freigegeben).
Richtlinien können nach Zustand eingeschränkt werden, und Zustände ändern sich von Hand, durch Workflows oder durch
Code. Verwenden Sie sie für redaktionelle Stufen, die unterschiedliche Rechte brauchen; verwenden Sie Sektionen für
Bereiche der Website ([Objektzustände](../../anleitungen/objektzustaende.md)).

## Zugriff in Templates

Fetches und Views lassen bereits weg, was der Benutzer nicht lesen darf. Für Schaltflächen und Links fragen Sie den
Knoten:

```smarty
{if $node.can_edit}
    <a href={concat( 'content/edit/', $node.contentobject_id )|ezurl}>{'Edit'|i18n( 'extension/exp_mysite' )}</a>
{/if}
```

Knoten beantworten `can_read`, `can_create`, `can_edit`, `can_hide`, `can_remove`, `can_move` und mehr
([Referenz der Knotenattribute](../template-sprache/referenz-der-knotenattribute.md)). Die Operatoren `has_role`,
`has_policy` und ihre Varianten fragen stattdessen nach dem Benutzer
([Template-Operatoren für Rollen und Richtlinien](../../funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md)). Verwenden
Sie sie nie, um Inhalte zu verbergen, die der Benutzer ohnehin lesen darf: Das ist Gestaltung, keine Sicherheit.

## Zugriff prüfen und Fehler suchen

```bash
./console exp:accesscheck --help
```

beantwortet, was ein Benutzer mit einem Knoten oder Objekt tun darf und welche Einschränkung es verweigert
([Sektionen, Rollen und Zugriff für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/sektionen-rollen-und-zugriff-fuer-builder.md#den-zugriff-eines-benutzers-prüfen)).
Testen Sie mit einem eigenen Testkonto (Adresse unter `example.invalid`), nie durch Ausprobieren als Administrator.

## Entwürfe, die funktionieren

- **Wenige Rollen, viele Zuweisungen.** Eine Rolle *Editor*, je Teilbaum zugewiesen, ist besser als eine Rolle je
  Abteilung.
- **Sektionen für Zielgruppen, Zustände für Stufen, Klassen für Arten von Inhalten.** Bilden Sie nicht das eine im
  anderen ab.
- **Geringste Rechte.** Beginnen Sie bei nichts und fügen Sie hinzu; ein Importbenutzer erhält genau die Rechte zum
  Anlegen und Bearbeiten, die er braucht.
- **Halten Sie die Rolle Anonymous klein.** `content/read` auf Standard, `user/login` auf dem öffentlichen Siteaccess
  und nur, was die Formulare der Website brauchen.

## Verwandte Seiten

- [Sektionen](../../anleitungen/sektionen.md): jede Seite der Sektionsverwaltung
- [Sicherheits-Checkliste](sicherheits-checkliste.md)
- [eZUser und Zugriff](../erweiterungsentwicklung/teil-8-referenz/ezuser-und-zugriff.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
