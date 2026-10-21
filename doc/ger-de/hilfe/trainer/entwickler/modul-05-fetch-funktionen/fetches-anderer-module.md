---
title: "Lektion 5.3: Fetches anderer Module"
audience: trainer, developer
category: training
order: 53
summary: Die Teilnehmenden finden die Fetch-Funktionen, die jedes Kernel-Modul anbietet, verwenden die Fetches für Benutzer, Sektionen und Beziehungen, prüfen Zugriffsrechte mit has_access_to und lesen Website-Daten aus einem Template. 40 Minuten.
keywords: entwickler, fetch, user current_user, has_access_to, section list, related_objects, reverse_related_objects, content object, site_data, website-daten, berechtigungen, beziehungen, function_definition.php, kernel-module, developers course, kernel modules, site data
type: help
---
# Lektion 5.3: Fetches anderer Module

Inhalte sind nicht das Einzige, was Templates holen. Das Benutzermodul sagt einem Template, wer angemeldet ist und
was diese Person tun darf, das Sektionsmodul listet Sektionen auf, und das Inhaltsmodul kennt auch Beziehungen,
Lesezeichen und Entwürfe. In dieser Lektion finden die Teilnehmenden die Fetch-Funktionen jedes Moduls und verwenden
diejenigen, die eine Website am häufigsten braucht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 5: Fetch-Funktionen](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Entwickler |
| Voraussetzungen | [Lektion 5.2](filter-sortierung-und-blaettern.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Module auflisten, die Fetch-Funktionen anbieten, und die Funktionen eines Moduls;
- den angemeldeten Benutzer mit `fetch( 'user', 'current_user' )` anzeigen;
- eine Berechtigung mit `fetch( 'user', 'has_access_to', ... )` prüfen;
- die verknüpften und rückwärts verknüpften Objekte eines Elements auflisten;
- eine freigegebene Zeile der Website-Daten mit `fetch( 'content', 'site_data', ... )` lesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/*/function_definition.php` und
[Templates und Design, Teil 9](../../../../anleitungen/templates-und-design.md#9-site-daten-in-templates).

1. **Welche Module** (3 Minuten). `ls kernel/*/function_definition.php` listet sie auf: `content`, `user`,
   `section`, `class`, `search`, `shop`, `url`, `workflow` und weitere. Erweiterungen fügen auf dieselbe Weise eigene
   hinzu (Lektion 5.4).
2. **Das Benutzermodul** (4 Minuten). `current_user`, `is_logged_in`, `member_of`, `user_role`, `has_access_to`,
   `logged_in_count` und weitere. `has_access_to` mit `module` und `function` beantwortet die Frage „Darf der aktuelle
   Benutzer das tun?“, und so blenden Templates Schaltflächen aus.
3. **Beziehungen** (3 Minuten). `related_objects` und `reverse_related_objects` mit `object_id`; die Varianten mit
   `_count` zählen.
4. **Website-Daten** (3 Minuten). `fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )` gibt eine Zeile aus
   **Setup** > **Site-Daten** zurück, aber nur für Namen, die in `site.ini [SiteDataSettings] TemplateReadable[]`
   aufgeführt sind; die Liste ist standardmäßig leer.
5. **Den Cache im Blick behalten** (2 Minuten). Eine Seite im View-Cache ist für alle mit denselben Berechtigungen
   gleich: Wer in einer gecachten Vollansicht den Namen des Benutzers holt, zeigt ihn anderen. Benutzerbezogene Teile
   gehören ins Pagelayout oder in nicht gecachte Teile.

### Häufige Fragen

**Wie verberge ich einen Bearbeiten-Link vor Besuchern?** Verwenden Sie `$node.can_edit` am Knoten oder
`has_access_to` für Rechte auf Modulebene;
[Template-Operatoren für Rollen und Richtlinien](../../../../../eng-us/features/6.0/role-and-policy-template-operators.md) fügt
Operatoren wie `has_policy` hinzu.

**Was gibt `current_user` für einen Besucher zurück?** Den anonymen Benutzer mit dem Login `anonymous`.

**Gibt es einen Such-Fetch?** Ja, `fetch( 'content', 'search', ... )` und die eigenen Fetches des Moduls `search`;
für eine echte Suchseite verwenden Sie die Views des Suchmoduls.

### Stolperfallen

- **Benutzerbezogene Daten in gecachten Views.** Siehe Punkt 5; testen Sie mit zwei verschiedenen angemeldeten
  Benutzern.
- **Verbergen ist nicht Schützen.** Eine ausgeblendete Schaltfläche verhindert keine Anfrage; die Modul-View muss die
  Berechtigungen selbst prüfen (Modul 6).
- **Namen der Website-Daten.** Ein Name, der nicht in `TemplateReadable[]` steht, gibt `false` zurück und hinterlässt
  einen Hinweis in der Debug-Ausgabe.

## Praktische Übung

Verwenden Sie den Template-Spielplatz und führen Sie ihn nach jedem Schritt aus. Der Spielplatz läuft als anonymer
Benutzer.

1. Listen Sie die Module mit Fetch-Funktionen auf: `ls kernel/*/function_definition.php`.
2. Listen Sie die Funktionen des Benutzermoduls auf: `grep -o "FunctionList\['[a-z_]*'\]" kernel/user/function_definition.php`.
3. Geben Sie den Login des aktuellen Benutzers aus: `{fetch( 'user', 'current_user' ).login}`.
4. Prüfen Sie eine Berechtigung:

   ```
   {if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'read' ) )}may read{else}may not read{/if}
   {if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'setup' ) )}may use setup{else}no setup{/if}
   ```

5. Zählen Sie die Sektionen: `{fetch( 'section', 'list' )|count}`.
6. Zählen Sie die Beziehungen des Objekts von Knoten 2:
   `{fetch( 'content', 'related_objects_count', hash( 'object_id', $node.contentobject_id ) )}`.
7. Probieren Sie einen Namen der Website-Daten, der nicht freigegeben ist:
   `{fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )|choose( 'not readable', 'readable' )}`.
8. Fügen Sie auf der Website die Benutzerprüfung aus Schritt 3 in das Override-Template aus Lektion 3.2 ein, melden
   Sie sich mit einem Teilnehmerkonto an der Website an und vergleichen Sie mit dem abgemeldeten Zustand. Entfernen
   Sie sie danach wieder: Sie gehört nicht in eine gecachte View.
9. Committen Sie das Spielplatz-Template.

## Erwartetes Ergebnis

- Schritt 3 gibt im Spielplatz `anonymous` aus.
- Schritt 4 gibt für den anonymen Benutzer `may read` und `no setup` aus.
- Schritt 5 gibt die Anzahl der Sektionen aus, die unter **Setup** > **Sektionen** angezeigt werden.
- Schritt 7 gibt `not readable` aus.
- Schritt 8: Je nach View-Cache zeigt die Seite der anderen Sitzung womöglich den falschen Benutzer, und genau darum
  geht es in diesem Schritt.
- Der Trainer fragt, warum Schritt 8 wieder entfernt werden muss.

## Quiz

1. Welcher Fetch sagt einem Template, wer angemeldet ist?
2. Welcher Fetch beantwortet die Frage „Darf der aktuelle Benutzer diese Modulfunktion verwenden?“
   A. `user/member_of` B. `user/has_access_to` C. `content/access` D. `section/roles`
3. Warum ist der Name des Benutzers in einer gecachten Vollansicht eine schlechte Wahl für die Ausgabe?
4. Was muss eingestellt sein, bevor ein Template eine Zeile der Website-Daten lesen kann?
5. Wo finden Sie alle Fetch-Funktionen des Sektionsmoduls?

<details>
<summary>Antworten</summary>

1. `fetch( 'user', 'current_user' )`.
2. **B.** `fetch( 'user', 'has_access_to', hash( 'module', ..., 'function', ... ) )`.
3. Die gecachte Seite wird womöglich anderen Besuchern mit denselben Berechtigungen gezeigt.
4. Der Name muss in `site.ini [SiteDataSettings] TemplateReadable[]` aufgeführt sein.
5. In `kernel/section/function_definition.php`.

</details>

## Weiterführende Literatur

- [Templates und Design, Teil 9](../../../../anleitungen/templates-und-design.md#9-site-daten-in-templates): Website-Daten in
  Templates.
- [Template-Operatoren für Rollen und Richtlinien](../../../../../eng-us/features/6.0/role-and-policy-template-operators.md).
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md): die Berechtigungen, die die Fetches beachten.

## Verwandte Seiten

- [Modul 5: Fetch-Funktionen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
