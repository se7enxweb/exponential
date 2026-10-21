---
title: Rollen und Rechte für Übersetzer
audience: government, admin
category: public-sector
order: 190
summary: Wie Sie Übersetzern einer öffentlichen Stelle in Exponential genau die Rechte geben, die sie brauchen - eine Rolle mit content/read, content/edit und content/translate, eingeschränkt nach Sprache (und Sektion), warum content/create zählt und wie Sie prüfen, was ein Übersetzer tun kann.
keywords: Übersetzerrolle, content/translate, content/edit, Einschränkung Sprache, Rollen und Richtlinien, Rechte, Übersetzer, Sprachdienst, externe Übersetzer, translator role, Language limitation, roles and policies, permissions, translators, language services, external translators
type: help
---
# Rollen und Rechte für Übersetzer

Diese Seite richtet sich an Administratoren, die Konten für Übersetzer einrichten, für Mitarbeitende eines
Sprachdienstes oder beauftragte Übersetzer. Die Richtlinien von Exponential lassen sich nach **Sprache** einschränken,
sodass ein Übersetzer eine Sprache bearbeiten darf und sonst nichts
([Eine mehrsprachige Website bauen, Übersetzer](../../../../eng-us/help/creators/website-creators/multilingual-sites.md#translators),
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

## Inhalt

1. [Die Rolle](#die-rolle)
2. [Sie anlegen](#sie-anlegen)
3. [Externe Übersetzer](#externe-übersetzer)
4. [Prüfen](#prüfen)
5. [Fallstricke](#fallstricke)

## Die Rolle

| Modul / Funktion | Einschränkung | Warum |
|---|---|---|
| `content/read` | die Sektionen, die sie übersetzen | um die Quelle zu sehen |
| `content/edit` | Sprache: ihre Sprache; Sektion nach Bedarf | um ihre Übersetzung zu bearbeiten |
| `content/translate` | Sprache: ihre Sprache | um eine neue Übersetzung hinzuzufügen |
| `content/versionread` | Sektion nach Bedarf | um Versionen zu vergleichen |
| `user/login` | der Siteaccess der Administration | um sich anzumelden |

Redakteure brauchen `content/edit` (oder `content/translate`) und `content/create` für eine Sprache, um sie einem
Objekt hinzuzufügen, und alle drei lassen sich nach **Sprache** einschränken
([Inhaltssprachen, Abschnitt 7](../../../anleitungen/inhaltssprachen.md#7-inhalte-im-editor-übersetzen)). Geben Sie
`content/create` nur, wenn Übersetzer neue Objekte anlegen müssen.

## Sie anlegen

1. **Benutzer > Rollen und Richtlinien** (`/role/list`): Legen Sie eine Rolle an, zum Beispiel „Übersetzer
   Französisch“.
2. Fügen Sie die Richtlinien der Tabelle hinzu; setzen Sie bei jeder die Einschränkung **Sprache** auf die Sprache.
3. Legen Sie eine Benutzergruppe „Übersetzer Französisch“ an und weisen Sie ihr die Rolle zu.
4. Verschieben Sie die Konten der Übersetzer in diese Gruppe.

## Externe Übersetzer

- Persönliche Konten, nie geteilt; ein Enddatum in Ihren Unterlagen vermerkt. Exponential kennt kein Ablaufdatum für
  Konten: Deaktivieren Sie Konten, wenn der Vertrag endet, und nutzen Sie **Benutzer > Benutzerkonten prüfen**, um
  Konten zu finden, die eine Weile nicht genutzt wurden ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).
- Zweistufige Anmeldung, wo Ihre Richtlinien sie verlangen ([Anmeldung mit zwei Faktoren](../../../anleitungen/zwei-faktor-anmeldung.md)).
- Ein Freigabeschritt, bevor Übersetzungen online gehen, wenn der Übersetzer nicht die letzte prüfende Instanz ist
  (siehe „Übersetzungsablauf und Freigabe“).

## Prüfen

Melden Sie sich als Test-Übersetzer an (nicht mit dem Konto einer realen Person) und prüfen Sie: Die Liste der
Bearbeitungssprachen bietet nur ihre Sprache an; das Bearbeiten einer anderen Sprache wird verweigert. Fehlt die
Einschränkung **Sprache**, bietet der Editor eine neu hinzugefügte Sprache nicht an
([Inhaltssprachen, Abschnitt 12](../../../anleitungen/inhaltssprachen.md#12-fehlerbehebung)).

## Fallstricke

- Übersetzer mit `content/remove` könnten ganze Objekte entfernen; geben Sie dieses Recht nicht.
- Das Entfernen einer Übersetzung löscht sie in jeder Version; lassen Sie dieses Recht bei den Administratoren (siehe
  „Eine Übersetzung sicher entfernen“).
- Rollenzuweisungen werden im Kanal `access` des Prüfprotokolls festgehalten.

## Verwandte Seiten

- [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)
- [Übersetzungsablauf und Freigabe](uebersetzungsablauf-und-freigabe.md)
- [Einen Eintrag Schritt für Schritt übersetzen](einen-eintrag-schritt-fuer-schritt-uebersetzen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
