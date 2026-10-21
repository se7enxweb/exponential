# Navigation der Administrationsoberfläche: der oberste Knoten, die Reiter und die Inhaltsauswahl

Diese Seite ist für Administratoren und Redakteure, die sich in der Administrationsoberfläche zurechtfinden. Zwischen dem
28. und 30. September 2026 lernte der Baum der Inhaltsstruktur, oben am ganzen Inhaltsbaum zu beginnen, die Reiter im
Kopfbereich erhielten klarere Namen, und mehrere kleine Fehler der Navigation wurden behoben. Wenn ein Menü nach einer
Aktualisierung anders aussieht, steht die Antwort wahrscheinlich hier.

## Der Baum der Inhaltsstruktur beginnt am obersten Knoten

Knoten 1 ist der virtuelle oberste Knoten der Inhaltsstruktur. Bisher begann das Menü bei Knoten 2 (der Inhaltswurzel),
sodass die übrigen Ordner der obersten Ebene einer neuen Installation (Users, Media, Setup, Design) nicht darin standen.
Jetzt beginnt der Baum bei Knoten 1, angezeigt als **Top Level Nodes** (in der deutschen Oberfläche „Inhalte“), der das
Dashboard öffnet.

Um das bisherige Verhalten zurückzubekommen, setzen Sie die Wurzel auf Knoten 2:

```ini
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
RootNodeID=2
```

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `settings/contentstructuremenu.ini` | `TreeMenu` | `RootNodeID` | `1` | Siteaccess | Knoten, bei dem der Baum beginnt. Setzen Sie `2` für das bisherige Verhalten oder jede andere Knoten-ID. |
| `settings/contentstructuremenu.ini` | `TreeMenu` | `MaxDepth` | `0` | Siteaccess | Unter der Wurzel jedes Baums gezeigte Ebenen; `0` bedeutet, dass sich jede Ebene öffnen lässt. Ein Knoten auf der letzten erlaubten Ebene hat kein Steuerelement zum Öffnen und Schließen. Der dynamische Baum berücksichtigt ihn auch. Er wird auf jeder Seite außerhalb des Template-Block-Caches gelesen, sodass eine Änderung sofort wirkt. |
| `settings/contentstructuremenu.ini` | `TreeMenu` | `AutoopenCurrentNode` | `enabled` | Siteaccess | Öffnet und hebt den aktuellen Knoten hervor. Die Einstellung erreichte die Seite schon vorher, aber das Baum-Skript las sie nie. |

Die Reiter Medien und Benutzer behalten ihre eigenen Bäume, mit den Wurzeln `[NodeSettings] MediaRootNode` und
`UserRootNode` aus `content.ini`. Auf Suchseiten, die auf einen Teilbaum beschränkt sind, wird der Wurzelknoten des Baums
zusammen mit seiner ID geholt, sodass der Klassenfilter dem gezeigten Teilbaum folgt.

### Top Level Nodes hat eine eigene Vollansicht

`content/view/full/1` zeigt jetzt, was der oberste Knoten hat, statt eines Platzhalterobjekts: seinen Namen, die
Knoten-ID, die Zeit der neuesten Änderung darunter, die Sprache, in der die Liste gezeigt wird, die Reiter **Ansicht**,
**Details** und **Sortierung** (der Kernel hält die Sortierung der Knoten der obersten Ebene am obersten Knoten) und die
übliche Unterelementliste mit Blättern und Sortieren.

Vorher zeigte die allgemeine Ansicht die Unix-Epoche als Änderungsdatum, eine leere Objekt-ID, eine Flagge einer
unbekannten Sprache und Aktionen zum Bearbeiten, Verschieben, Entfernen und für Versionen, die an einem Knoten ohne Objekt
nicht funktionieren können.

### Redakteure mit eingeschränktem Lesezugriff

Der oberste Knoten hat kein eigenes Objekt, daher wies die Prüfung des Lesezugriffs auf ihn jeden Redakteur ohne Zugriff
auf den ganzen Baum ab. Als das Menü bei Knoten 1 begann, war ihr Menü der Inhaltsstruktur leer. Diese Prüfung ist weg;
die Kinder werden beim Holen weiterhin nach dem Lesezugriff des Redakteurs gefiltert.

## Klarere Reiter im Kopfbereich

| Vorher | Jetzt | Wo |
|---|---|---|
| Media library | **Medien** (Media) | `Name=` im `menu.ini`-Block des Reiters |
| User accounts | **Benutzer** (Users) | ebenso |
| Webshop | **Shop** (Store) | ebenso; der Reiter öffnet das [Shop-Dashboard](shop-dashboard.md) |
| Name und Tooltip des Reiters nur auf Englisch | übersetzt | Blöcke `[TopAdminMenu]` |

Ein Reiter, der in `[TopAdminMenu] Tabs[]` zweimal aufgeführt ist, wird einmal gezeigt, an seiner ersten Stelle.
Extensions hängen ihren Reiter an diese Liste an, und ihre Einstellungen werden nach denen eines Siteaccess gelesen. Ein
Siteaccess, der die ganze Liste neu festlegte, um die Reiter in eigener Reihenfolge zu haben, erhielt daher früher jeden
Reiter einer Extension am Ende ein zweites Mal.

Die Einträge Maintenance, Cronjobs, Preload Sites und oAuth admin des Setup-Menüs sind ebenfalls übersetzt (Deutsch:
Wartung, Cronjobs, Seiten vorladen, oAuth-Verwaltung).

## Der Link zur Website und das Logo

- **Link zur Website.** Der Link neben dem Logo wird aus den Einstellungen ermittelt (`ezpSiteAccessURL::front()`):
  `site.ini [SiteSettings] FrontSiteAccess` des Admin-Siteaccess, sonst `DefaultAccess` (eine Installation, die mehrere
  Websites aus einem Baum bedient, nennt dort die Website jedes Admins); das Schema dieser Anfrage; dieser Host, wenn der
  Siteaccess auf ihm über die URI erreichbar ist, oder sonst ein Host, den `HostMatchMapItems` ihm gibt, oder seine
  `SiteURL`; der Port des Besuchers, wenn der Host von dieser Installation bedient wird; und der Pfad des Siteaccess,
  sofern `RemoveSiteAccessIfDefaultAccess` ihn nicht weglässt. Vorher war es `https://` plus der Host ohne `edit.`,
  sodass er weder den Siteaccess noch den Port nannte (eine auf 443 und 8080 ausgelieferte Installation führte immer zu
  443). Der Template-Operator ist `siteaccess_url()`. Die beiden Cache-Blöcke des Kopfbereichs von admin3 tragen die
  Adresse in ihren Schlüsseln, sodass ein auf einem Port ausgegebener Kopfbereich nie auf dem anderen ausgeliefert wird.
  In admin4 und admin4l führt die Weltkugel außerdem die anderen Websites auf, bei denen sich der Benutzer anmelden darf
  (`site.ini [AdminSiteAccessMenu]`, Administrationen nur mit `ListAdministrations=enabled`; siehe
  [admin4](admin4-design.md#einstellungen)).
- **Logo.** Das Logo verlinkt über `ezurl` auf die Inhaltswurzel, `content.ini [NodeSettings] RootNode`. Auf einem über
  die URI zugeordneten Admin bleibt es im Admin (`/admin/content/view/full/2`); auf einem über den Host zugeordneten
  Admin ist es `/content/view/full/2`.

## Inhaltsauswahl: den aktuellen Knoten wählen

In einem Auswahldialog wählt eine Zeile den Knoten vor, in dem Sie sind, sodass ein Klick auf **Auswählen** ihn nimmt.
Diese Zeile hatte Fehler:

- Sie prüfte ignorierte Knoten gegen eine Variable, die es nicht gab, sodass ein Knoten, der nicht gewählt werden darf
  (der aktuelle übergeordnete Knoten beim Verschieben oder Hinzufügen einer Platzierung), angeboten und sogar vorgewählt
  wurde;
- sie sendete immer die Knoten-ID, auch wenn die Auswahl Objekt-IDs zurückgibt (das Zuweisen einer Rolle an die Gruppe
  Users sendete das falsche Objekt);
- mit einer Klassenbeschränkung schrieb sie ein nacktes Eingabefeld in die Tabelle.

Sie liegt jetzt im Template `content/browse_current_node.tpl` (mitgeliefert in den Designs `admin3` und `admin4`), mit
denselben Prüfungen wie aufgeführte Elemente: Berechtigung, ignorierte Knoten und Teilbäume, Klassenbeschränkungen,
Container für Verschieben, Kopieren und Platzierung hinzufügen sowie Kompatibilität beim Tauschen. Sie sendet die ID, nach
der die Auswahl fragt, wird deaktiviert gezeigt, wenn der Knoten nicht gewählt werden darf, und für Suchergebnisse und die
oberste Ebene ausgelassen. Die Vorschaubildanzeige des Designs admin3 bietet den aktuellen Knoten ebenfalls als erstes,
vorgewähltes Vorschaubild an.

## Kleinere Korrekturen im selben Zug

- Der Umschalter links der Reiter der Knotenansicht verbirgt den Inhalt der Reiter wieder.
- **Ausgewählte bearbeiten** und **Mehrere neu anlegen** in der Unterelementliste gehen zur Administrationsoberfläche,
  nicht zum öffentlichen Siteaccess.
- Die URL-Liste der Administrationsoberfläche zählt und listet die URLs veröffentlichter Inhalte in Millisekunden (ein
  Test mit `EXISTS` statt eines Joins mit `count(DISTINCT)`, der auf SQLite bei 4.615 URLs über zwei Minuten dauerte).
- Das Festlegen der Sortierung eines Knotens verlangt die Berechtigung, ihn zu bearbeiten, und nur ein bekanntes
  Sortierfeld und eine bekannte Reihenfolge werden gespeichert.
- Fehlerseiten der Administrationsoberfläche zeigen ihren eigenen Fehler im Seitentitel.
- Eine Zählung eines Teilbaums mit einer Sprache findet einen Startknoten, der in dieser Sprache keine Übersetzung hat,
  und lässt diese Sprache nicht mehr gesetzt, wenn der Knoten nicht existiert (Inhalte „sahen fehlend aus“ für den Rest
  der Anfrage).

## Verwandte Seiten

- [Installer-Protokolle und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md) für die neuen Ordner der obersten Ebene
- [Ausgeblendete Admin-Reiter](ausgeblendete-admin-reiter.md), [Shop-Dashboard](shop-dashboard.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
