---
title: Joomla und Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 42
summary: Eine Site mit Joomla 4 oder 5 nach Exponential 6 umziehen und zurück – die Web Services API, die Datenbank, die Abbildung von Beiträgen, Kategorien, Schlagwörtern, Menüs, eigenen Feldern, Medien und Benutzern, SEF-URLs und Weiterleitungen sowie der Rückweg mit dem Massenimport der Weiterleitungen von Joomla.
keywords: Joomla, Joomla 4, Joomla 5, Web Services API, api/index.php/v1, X-Joomla-Token, Beiträge, Kategorien, Schlagwörter, Menüeinträge, eigene Felder, SEF-URLs, Weiterleitungen, Migration, articles, categories, tags, menu items, custom fields, SEF URLs, redirects, migration
type: install
---
# Joomla und Exponential

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Abbildung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## Inhalt

- [1. Was Joomla exportieren kann](#1-was-joomla-exportieren-kann)
- [2. Joomla auf Exponential abbilden](#2-joomla-auf-exponential-abbilden)
- [3. Von Joomla zu Exponential, Schritt für Schritt](#3-von-joomla-zu-exponential-schritt-für-schritt)
- [4. Adressen und Weiterleitungen](#4-adressen-und-weiterleitungen)
- [5. Von Exponential zu Joomla](#5-von-exponential-zu-joomla)
- [6. Risiken und Checkliste](#6-risiken-und-checkliste)
- [Quellen](#quellen)

## 1. Was Joomla exportieren kann

| Quelle | Was Sie erhalten | Hinweise |
|---|---|---|
| **Web Services API** (Joomla 4 und 5) | JSON unter `/api/index.php/v1/...`, zum Beispiel `/api/index.php/v1/content/articles`, Medientyp `application/vnd.api+json`, Methoden GET, POST, PATCH, DELETE | ein Token im Header `X-Joomla-Token` (Plugins „API Authentication - Web Services Joomla Token“ und „User - Joomla API Token“); der Benutzer braucht Super User oder `core.login.api` plus `core.login.site` |
| Die Datenbank | Beiträge (`#__content`), Kategorien (`#__categories`), Schlagwörter, Menüeinträge (`#__menu`), eigene Felder und ihre Werte, Benutzer | das Tabellenpräfix ist das Ihrer Installation |
| Die Verzeichnisse `images/` und `media/` | die Mediendateien | kopieren |

Eine Kernfunktion „die Inhalte der Site exportieren“ wurde in der Dokumentation von Joomla nicht gefunden (in keiner Richtung geprüft). Die
Paginierungsparameter der Web Services API wurden nicht geprüft; lesen Sie die `links` jeder Antwort.

## 2. Joomla auf Exponential abbilden

| Joomla | Exponential 6 |
|---|---|
| Beitrag | `article`-Objekt |
| `introtext` / `fulltext` (getrennt durch die Weiterlesen-Linie) | `intro` und `body` (`ezxmltext`), HTML umgewandelt |
| Kategorie (verschachtelt) | Ordner; die Kategorie des Beitrags ist sein Hauptort |
| Schlagwörter | `eztags` oder `ezkeyword` |
| Eigene Felder (`com_fields`) | Attribute der Klasse |
| Einleitungs- und Beitragsbilder (JSON-Spalte `images`) | `ezimage`-Attribute oder Relationen zu Bildobjekten |
| Menüeinträge | der Inhaltsbaum und die Sortierung; Menüeinträge, die auf Komponenten zeigen, brauchen jeweils eine eigene Entscheidung |
| Module (Seitenblöcke) | Layout-Blöcke (Exponential Layouts) oder Template-Teile |
| Benutzer und Benutzergruppen, Zugriffsebenen | Benutzerobjekte in Gruppen; Sektionen und Rollen für die Zugriffsebenen. Die Passwort-Hashes von Joomla: Testen Sie, ob es Standardwerte von PHPs `password_hash()` sind (dann funktionieren sie als Typ 7, [Abbildung, 6](inhaltsmodell-zuordnen.md#6-benutzer-gruppen-rollen-und-passwörter)); andernfalls planen Sie das Zurücksetzen ein |
| `metadesc`, `metakey`, Seitentitel | `xrowmetadata` |
| Mehrsprachige Verknüpfungen | Übersetzungen desselben Objekts (verbinden Sie die verknüpften Beiträge vor dem Import) |
| Inhalte von Erweiterungen (K2, Formulare, Shops) | jeweils eine eigene Entscheidung |

## 3. Von Joomla zu Exponential, Schritt für Schritt

1. **Bestandsaufnahme**: Beiträge pro Kategorie und Sprache, eigene Felder, Menüs, Module, installierte Erweiterungen, die
   Inhalte enthalten, die SEF-Einstellungen (Global Configuration > SEO).
2. **Legen Sie die Klassen** und den Ordnerbaum an.
3. **Export**: über die Web Services API (ein API-Benutzer und ein Token auf einer Kopie der Site) oder per SQL auf einer Kopie
   der Datenbank; schreiben Sie JSON-Zeilen mit den Remote-IDs `joomla-article-<id>`, dem Kategoriepfad, dem umgewandelten HTML und der
   alten SEF-Adresse. Kopieren Sie `images/`.
4. **Importieren Sie** die Medien, dann die Kategorien als Ordner, dann die Beiträge, mit dem Importer von xrowextract oder dem
   [Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst).
5. **Weiterleitungen** ([4](#4-adressen-und-weiterleitungen)), SEO-Felder, Suche, testen, proben, umstellen.

## 4. Adressen und Weiterleitungen

Die Adressen von Joomla hängen von den SEF-Einstellungen und vom Menü ab: `/index.php/blog/12-my-article`,
`/blog/my-article` mit URL-Rewriting, `/index.php?option=com_content&view=article&id=12`. Erstellen Sie die Weiterleitungstabelle
aus der Live-Site (crawlen Sie sie und nehmen Sie das Server-Log hinzu), nicht allein aus der Datenbank, denn das Menü bestimmt den
Pfad. Formen mit Query-String gehen an den Webserver; Pfadformen werden zu weiterleitenden eigenen URL-Aliasen. Die eigene Liste
**System > Redirects** von Joomla lässt sich aus ihrer Tabelle exportieren und derselben Weiterleitungstabelle hinzufügen.

## 5. Von Exponential zu Joomla

Exportieren Sie mit xrowextract ([Exponential verlassen](exponential-verlassen.md)), wandeln Sie XmlText in HTML um und legen Sie die
Beiträge über die Web Services API (`POST /api/index.php/v1/content/articles`) oder ein Joomla-Importwerkzeug Ihrer
Wahl an. **System > Redirects** von Joomla hat einen **Bulk Import** (Standardtrennzeichen `|`; die neue URL muss eine vollständige
URL sein), der die Weiterleitungstabelle aus den URL-Aliasen von Exponential übernimmt.

## 6. Risiken und Checkliste

| Risiko | Gegenmaßnahme |
|---|---|
| Vom Menü bestimmte Adressen | die Live-Site für die Adressliste crawlen |
| Weiterlesen-Trennung | `introtext`/`fulltext` ausdrücklich abbilden |
| Erweiterungen von Drittanbietern | früh erfassen und jede einzeln entscheiden |

- [ ] Beiträge, Kategorien, Felder, Menüs, Erweiterungen erfasst.
- [ ] API-Token oder Datenbankkopie bereit; Export vollständig; Bilder kopiert.
- [ ] Import mit Remote-IDs; Kategorien als Ordner; Übersetzungen verknüpft.
- [ ] Weiterleitungstabelle aus dem Crawl; getestet; SEO-Felder abgebildet.

## Quellen

- Joomla Web Services: <https://manual.joomla.org/docs/general-concepts/webservices/>
- Weiterleitungen (Bulk Import): <https://docs.joomla.org/Help5.x:Redirects:_Links>

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
