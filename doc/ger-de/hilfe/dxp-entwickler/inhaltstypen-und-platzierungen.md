---
title: Inhalte, Inhaltstypen und Platzierungen
audience: dxp-developer
category: dxp-development
order: 17
summary: Das Inhaltsmodell der DXP für Entwickler - Inhaltstypen und Felder, Inhaltselemente, Versionen, Übersetzungen, Platzierungen im Baum, Sektionen, Objektzustände, Benutzer, Rollen und Richtlinien - mit den Repository-Services, die jeden Teil behandeln, und den Begriffen aus Exponential 6 zum Vergleich.
keywords: Inhaltsmodell, Inhaltstyp, Felddefinition, Inhaltselement, Version, Entwurf, Übersetzung, Platzierung, Location, Baum, Sektion, Objektzustand, Rolle, Richtlinie, Repository, ContentService, LocationService, content model, content type, field definition, content item, draft, translation, location, tree, section, object state, role, policy
type: help
---
# Inhalte, Inhaltstypen und Platzierungen

Diese Seite richtet sich an Entwickler, die Inhalte auf der DXP modellieren. Sie benennt die Teile des Inhaltsmodells,
den Repository-Service, der jeden Teil in PHP behandelt, und den Begriff aus Exponential 6 für Leser, die diesen Kernel
kennen. Die aufgeführten Services sind die Schnittstellen in `Ibexa\Contracts\Core\Repository` des installierten Core
(`se7enxweb/exponential-platform-dxp-core`).

## Inhalt

1. [Die Teile des Modells](#die-teile-des-modells)
2. [Versionen und Übersetzungen](#versionen-und-übersetzungen)
3. [Der Baum](#der-baum)
4. [Zugriff](#zugriff)
5. [Mit dem Repository in PHP arbeiten](#mit-dem-repository-in-php-arbeiten)
6. [Ratschläge zur Modellierung](#ratschläge-zur-modellierung)

## Die Teile des Modells

| Teil | Was es ist | Service | In Exponential 6 |
|---|---|---|---|
| Inhaltstyp | eine Definition mit Feldern, gruppiert in Gruppen von Inhaltstypen | `ContentTypeService` | Inhaltsklasse |
| Felddefinition | ein Feld eines Inhaltstyps, mit einem Feldtyp und Einstellungen | `ContentTypeService` | Klassenattribut |
| Inhaltselement | ein Inhalt eines Inhaltstyps | `ContentService` | Inhaltsobjekt |
| Feld | ein Wert einer Felddefinition an einem Inhaltselement | `ContentService` | Objektattribut |
| Platzierung (Location) | ein Platz eines Inhaltselements im Baum | `LocationService` | Knoten |
| Sektion | eine Kennzeichnung für Zugriffs- und Designregeln | `SectionService` | Sektion |
| Objektzustand | ein Zustand in einer Zustandsgruppe (zum Beispiel gesperrt, entsperrt) | `ObjectStateService` | Objektzustand |
| URL-Alias | eine lesbare URL für eine Platzierung | `URLAliasService` | URL-Alias |

Die verfügbaren Feldtypen stehen unter [Die mitgelieferten Feldtypen](die-mitgelieferten-feldtypen.md).

## Versionen und Übersetzungen

Jede Änderung eines Inhaltselements erzeugt eine Version. Eine Version ist ein Entwurf, bis sie veröffentlicht wird;
die zuvor veröffentlichte Version wird archiviert. `exponential:content:cleanup-versions` räumt alte Entwürfe und
archivierte Versionen auf ([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)).

Jede Version enthält eine oder mehrere Übersetzungen. Die Sprachen des Repositorys werden mit `LanguageService`
verwaltet; welche Sprachen ein Siteaccess zeigt, ist seine Einstellung `languages`
([Siteaccesses](siteaccesses-site-und-admin.md)). Die Admin-Konfiguration in einem installierten Projekt vermerkt, dass
Inhalte und Inhaltstypen in `eng-GB` beginnen.

## Der Baum

Inhaltselemente werden in einem Baum von Platzierungen angeordnet. Ein Inhaltselement kann mehrere Platzierungen
haben; eine davon ist seine Hauptplatzierung. Verschieben, Kopieren, Verbergen und in den Papierkorb legen wirken auf
Platzierungen (`LocationService`, `TrashService`). `exponential:copy-subtree <source> <target>` kopiert einen Teilbaum
von der Kommandozeile.

## Zugriff

Benutzer und Benutzergruppen sind Inhaltselemente (`UserService`). Rollen sind Sätze von Richtlinien (Modul und
Funktion, mit Einschränkungen), die Benutzern oder Gruppen zugewiesen werden, optional auf einen Teilbaum oder eine
Sektion eingeschränkt (`RoleService`, `PermissionResolver`). Konsolenbefehle nehmen eine Option `--user` und handeln
mit den Berechtigungen dieses Benutzers.

## Mit dem Repository in PHP arbeiten

Injizieren Sie die Services in Ihren eigenen Service in `src/`; holen Sie sie nie von Hand aus dem Container:

```php
<?php

namespace App\Service;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;

final class ArticleReader
{
    public function __construct(
        private readonly ContentService $contentService,
        private readonly LocationService $locationService,
    ) {
    }

    public function titleOfLocation(int $locationId): string
    {
        $location = $this->locationService->loadLocation($locationId);
        $content = $this->contentService->loadContent($location->getContentId());

        return (string) $content->getFieldValue('title');
    }
}
```

Die Schnittstellen sind Upstream-API: Ihre vollständige Dokumentation ist die öffentliche API-Referenz der
Upstream-Linie, mit der die DXP kompatibel bleibt, weil die Forks die Namensräume behalten
([Die se7enxweb-Forks](die-se7enxweb-forks-und-replace.md#die-namensräume-bleiben-gleich)).

## Ratschläge zur Modellierung

- Modellieren Sie nach Bedeutung, nicht nach Seite: ein Typ "Event" mit einem Datumsfeld, kein "Kasten in der rechten
  Spalte".
- Bevorzugen Sie Relationen gegenüber Kopien: ein Relationsfeld auf ein Inhaltselement des Autors statt eines
  Textfeldes mit einem Namen.
- Halten Sie Bezeichner stabil; Templates, GraphQL-Typen und API-Clients verwenden sie. Das GraphQL-Schema wird aus
  Ihren Inhaltstypen erzeugt; erzeugen Sie es nach jeder Änderung neu ([GraphQL](graphql-und-graphiql.md)).
- Indexieren Sie nach einer Änderung eines Inhaltstyps neu, wenn die Suche die geänderten Felder verwendet
  ([Neu indexieren](neu-indexieren.md)).

## Verwandte Seiten

- [Die mitgelieferten Feldtypen](die-mitgelieferten-feldtypen.md)
- [Twig-Funktionen für den Alltag](twig-funktionen-fuer-den-alltag.md)
- [Inhaltsklassen für Website-Bauer (Exponential 6)](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
