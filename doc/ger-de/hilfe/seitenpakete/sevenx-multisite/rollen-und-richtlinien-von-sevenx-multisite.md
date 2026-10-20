---
title: Rollen und Richtlinien von sevenx_multisite
audience: admin, developer
category: install-upgrade
order: 67
summary: Die Rollen, Richtlinien, Sektionen, Benutzergruppen und Rollenzuweisungen, die eine Installation von sevenx_multisite anlegt, und die Voreinstellungen des Administrators, die sie setzt, so wie sie im Installer stehen.
keywords: Rollen, Richtlinien, Anonymous, Member, Editor, Partner, Administrator, Sektion Restricted, Partners, Members, user/login, Einschränkung SiteAccess, siteRoles, sitePreferences, addPoliciesForRole, roles, policies, Restricted section, SiteAccess limitation
type: help
---
# Rollen und Richtlinien von sevenx_multisite

Diese Seite ist für Administratoren, die Benutzer auf einer Installation von `sevenx_multisite` verwalten und wissen wollen, was die
Installation jeder Rolle gegeben hat, und für Entwickler, die das ändern. Sie beantwortet: welche Rollen welche Richtlinien bekommen, welche
Sektionen und Benutzergruppen angelegt werden und wer wem zugewiesen ist. Die allgemeine Anleitung zu Rollen ist
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md).

## Inhalt

1. [Zwei Stellen, die Rollen schreiben](#zwei-stellen-die-rollen-schreiben)
2. [Sektionen und Benutzergruppen](#sektionen-und-benutzergruppen)
3. [Anonymous](#anonymous)
4. [Member](#member)
5. [Editor](#editor)
6. [Partner](#partner)
7. [Rollenzuweisungen](#rollenzuweisungen)
8. [Voreinstellungen des Administrators](#voreinstellungen-des-administrators)

## Zwei Stellen, die Rollen schreiben

| Wo | Wann es läuft | Mechanismus |
|---|---|---|
| `siteRoles()`, aufgerufen über `eZSiteRoles()` in `roles.php` | während `CreateSites` die Rollen einrichtet | gibt Rollendefinitionen zurück, die das Setup anwendet |
| `initSteps()` Schritte 16 bis 31 | in der Nachinstallation | Kernel-Schritte `createContentSection`, `addPoliciesForRole`, `removePoliciesForRole`, `createContentObject`, `setSection`, `renameContentObject`, `assignUserToRole` |

`addPoliciesForRole` legt die Rolle an, wenn sie noch nicht existiert (`create_role` ist in `kernel/classes/ezsiteinstaller.php`
standardmäßig `true`), sodass die Rolle Partner durch ihre erste Richtlinie angelegt wird. Die Rolle Administrator ändert der Installer nicht;
sie hat bereits `*/*`.

Richtlinien werden durch Klasse und Sektion eingeschränkt. In der Liste der Schritte wird eine Klasse oder Sektion durch Bezeichner oder
Namen angegeben und aufgelöst, wenn der Schritt läuft (`classIDbyIdentifier`, `sectionIDbyName`), sodass die Richtlinien auf die IDs der
neuen Installation verweisen.

## Sektionen und Benutzergruppen

| Schritt | Ergebnis |
|---|---|
| `createContentSection` | eine Sektion `Restricted` mit dem Navigationsteil `ezcontentnavigationpart` |
| `createContentObject` | eine `user_group` `Partners` unter `users` |
| `setSection` | `users/partners` in der Sektion `Restricted` |
| `setSection` | `x_archives`, der oberste Archivordner der Basisdaten, in der Sektion `Restricted`; übersprungen, wenn er nicht existiert |
| `renameContentObject` | Objekt 11, `Guest accounts`, umbenannt in `Members` |

## Anonymous

| Modul / Funktion | Einschränkung | Quelle |
|---|---|---|
| `rss/feed` | | `siteRoles()`, den Gast- und anonymen Konten zugewiesen |
| `user/login` | `SiteAccess`: jeder ausgelieferte Siteaccess außer admin und adminui | `siteRoles()` |
| `content/read` | `Section` 3 (Media), damit in Artikeltexte eingebettete Bilder dargestellt werden | `siteRoles()` |
| `tags/view` | | `siteRoles()`: Tag-Links von Artikeln und Rezepten |
| `info-collection/read` | | `siteRoles()`: das modale Lead-Formular der Templates von Bold |
| `shop/buy` | | Nachinstallation |
| `content/create` | Klassen `comment` und `review`, Sektion Standard, Sprache die Hauptsprache | Nachinstallation |
| `ezjscore/call` | `FunctionList` `ezstarrating_rate`, `ezstarrating_user_has_rated`, `loadMore` | Nachinstallation |
| `content/read` | Klassen `image`, `banner`, `flash`, `real_video`, `windows_media`, `quicktime`, `video`, Sektion Media | Nachinstallation |

Die Richtlinie für die Anmeldung führt auch `bold` und `bold_ger` auf: Ohne sie verweigert die Website Bold den anonymen Zugriff, selbst
wenn die Siteaccesses registriert sind.

## Member

| Modul / Funktion | Einschränkung |
|---|---|
| `content/read` | `Section` 3 |
| `tags/view`, `info-collection/read` | |
| `content/create` | `forum_topic` unter `forum`, Sektion Standard |
| `content/create` | `forum_reply` unter `forum_topic`, Sektion Standard |
| `content/create` | `comment` unter `article`, `blog_post` oder `article_mainpage`, Sektion Standard |
| `content/edit` | `comment`, `forum_topic`, `forum_reply`, Sektion Standard, `Owner` selbst |
| `user/selfedit`, `user/password`, `notification/use`, `ezjscore/call` | |
| `user2fa/setup`, `user2fa/verify` | nur wenn `sevenx_authentication_2fa` installiert ist |

## Editor

Die Nachinstallation entfernt zuerst die Richtlinie `content/*` der Rolle Editor und fügt dann hinzu:

| Modul / Funktion | Einschränkung |
|---|---|
| `content/create` | 24 Klassen (`folder`, `link`, `file`, `product`, `feedback_form`, `frontpage`, die drei Artikelklassen, `blog`, `poll`, `multicalendar`, `documentation_page`, `infobox`, die Medienklassen, `gallery`, `forum`, `forums`, `event_calendar`, `banner`, `image`) unter einem `folder` |
| `content/create` | `blog_post` unter `blog`; `forum_topic` unter `forum`; `event` unter `event_calendar`; `image` unter `gallery` |
| `content/create` | `folder`, `link`, `feedback_form`, `frontpage`, `documentation_page`, `gallery`, `event_calendar`, `multicalendar`, `forums` unter einer `frontpage` |
| `websitetoolbar/use` | 24 Klassen |
| `content/edit` | |
| `content/read` | Sektionen Standard, Restricted und Media |
| `notification/use`, `content/manage_locations`, `ezodf/*`, `content/diff`, `content/versionread`, `content/versionremove`, `content/remove`, `content/translate`, `rss/feed`, `content/bookmark`, `content/pendinglist`, `content/dashboard`, `content/view_embed` | |
| `uiswitch/language`, `uiswitch/design` | die Wahl von Oberflächensprache und Design in der Toolbar der Administration |
| `ezjscore/call` | `FunctionList` `ezjscnode`, die Aufrufe von Knotendaten der Liste der Unterelemente und des Online-Editors |
| `user2fa/setup`, `user2fa/verify` | nur wenn `sevenx_authentication_2fa` installiert ist |

> **Hinweis**
> Die Einschränkungen von `content/create` der Rolle Editor nennen die Klassen von `sevenx_classes`, nicht die Klassen `ng_*` der
> Demo-Inhalte. Prüfen Sie die Rolle, bevor Sie Redakteuren die Inhalte von Fit & Healthy und Bold Agency zur Bearbeitung geben.

## Partner

| Modul / Funktion | Einschränkung |
|---|---|
| `content/read` | Sektion Restricted |
| `content/create` | `forum_topic` unter `forum`, `forum_reply` unter `forum_topic`, `comment` unter `article`, alle in der Sektion Restricted |
| `content/edit` | `comment`, `forum_topic`, `forum_reply`, Sektion Restricted, `Owner` selbst |
| `user/selfedit`, `notification/use` | |
| `user2fa/setup`, `user2fa/verify` | nur wenn `sevenx_authentication_2fa` installiert ist |

## Rollenzuweisungen

| Benutzergruppe | Rollen |
|---|---|
| `users/members` (die umbenannten `Guest accounts`) | Member |
| `users/partners` | Partner, Member, Anonymous |
| `users/editors` | Member |
| die Gast- und anonymen Konten | Anonymous (aus `siteRoles()`) |

## Voreinstellungen des Administrators

`sitePreferences()` setzt diese Voreinstellungen für das Administratorkonto (das Objekt mit der Remote-ID `1bb4fe25487f05527efa8bfd394cecc7`
in der Objektzuordnung des Setups):

| Voreinstellung | Wert |
|---|---|
| `admin_navigation_content` | `1` |
| `admin_navigation_roles` | `1` |
| `admin_navigation_policies` | `1` |
| `admin_list_limit` | `2` (laut Kommentar im Code ist die Grenze der Kinderliste standardmäßig 25) |
| `admin_treemenu` | `1` |
| `admin_bookmark_menu` | `1` |

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md)
- [Berechtigungen für Pakete](../admin-oberflaeche/berechtigungen-fuer-pakete.md)
- [Rollen und Richtlinien (Anleitung)](../../../anleitungen/rollen-und-richtlinien.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
