# Wer `ezjscnode` aufruft und was das Verlangen einer Richtlinie ändert (L01)

Für **Administratoren**, die entscheiden, ob sie auf einer aktualisierten Website
`ezjscore.ini [ezjscServer_ezjscnode] RequirePolicy=enabled` einschalten
([L01 im Überblick](../lovestack.md#l01--knotendaten-aufrufe-können-eine-richtlinie-verlangen)). Neue Installationen haben es bereits
eingeschaltet.

## Die drei Aufrufe

| Aufruf | Was er zurückgibt oder tut | Seine eigene Prüfung |
|---|---|---|
| `ezjscnode::load::<node id or ezobject_<id>>` | Metadaten von Knoten und Objekt: Name, IDs, Remote-ID, Status, Besitzer, Sektion, Daten, Klasse, Übersetzungen, auf Wunsch die Data Map | `canRead()` des Objekts |
| `ezjscnode::subtree::<node>::<limit>::<offset>::<sort>` | eine Liste von Kindern mit denselben Metadaten | content/read über `subTree()` |
| `ezjscnode::updatepriority` (POST) | setzt Prioritäten | `canEdit()` des Elternknotens |

Mit `RequirePolicy=disabled` (dem Standard beim Upgrade) kann jeder, der einen Knoten lesen darf, auf diese Weise
dessen Metadaten lesen, anonyme Besucher eingeschlossen, zum Beispiel
`GET /ezjscore/call/ezjscnode::load::2?ContentType=json`. `ezjscore/call` selbst steht in `PolicyOmitList[]` (in der
eigenen `site.ini.append.php` von ezjscore), daher ist es `Functions[]=ezjscnode` mit `RequirePolicy=enabled`, das die
Prüfung der Richtlinie hinzufügt. Eine abgelehnte Anfrage wird dann mit 403 Forbidden beantwortet.

## Wer ihn in der Distribution aufruft

| Wo | Aufruf | Verwendet von | Anonym? |
|---|---|---|---|
| `design/admin/templates/children_detailed.tpl` mit `design/admin/javascript/ezajaxsubitems_expdatatable.js` | `ezjscnode::subtree` (die Ersatz-Datenquelle der Liste der Unterelemente) und `ezjscnode::updatepriority` (Bearbeitung der Priorität direkt in der Liste) | die Liste der Unterelemente der Administration | nein |
| `design/admin4/templates/children_detailed.tpl` mit `design/admin4/javascript/ezajaxsubitems_expdatatable.js` | dasselbe | die Liste der Unterelemente von admin4 | nein |
| `extension/exp_adminui/design/adminui/templates/children_detailed.tpl` | dasselbe | die Liste der Unterelemente von adminui | nein |
| `extension/ezoe/design/standard/templates/ezoe/tag_embed_images.tpl` | `ezjscnode::load::ezobject_<id>::0::<size>` | der Einbettungsdialog des Rich-Text-Editors: die Bildvorschau pro Größe | nein (Bearbeitung von Inhalten) |
| `kernel/classes/subitems/expsubitemsserverfunctions.php` | ruft ihn nicht auf; liest `[ezjscServer_ezjscnode] HardLimit` | `expsubitems::rows`, die eigene Datenquelle der Liste der Unterelemente | nein; hat eigene Prüfungen |
| `extension/expui` (`exp/io.js`, seine Tests und sein Buch) | nur als Beispiel für `Exp.io.call()` | Dokumentation und Tests | — |

Kein Aufruf wurde gefunden in ezflow, ezwt (es hat sein eigenes `ezwt::updatepriority`), ezstarrating, eztags, den
Erweiterungen von Exponential Layouts, den Designs media, simple, starter und ezwebin oder in `vendor/`. **Kein
ausgeliefertes Design ruft `ezjscnode` für anonyme Besucher auf.** Durchsuchen Sie Ihre eigenen Erweiterungen und
Designs, bevor Sie es einschalten:

```sh
grep -rn "ezjscnode" extension/myextension design/mydesign
```

## Was das Einschalten ändert

| Wer | Wirkung |
|---|---|
| anonyme Besucher | mit den ausgelieferten Designs nichts Sichtbares; das Lesen von Knoten-Metadaten über `ezjscnode` wird abgelehnt (403) |
| Rollen mit vollem Zugriff (Administrator) | nichts |
| Rollen, die *ezjscore / call* ohne Einschränkung auf Funktionen haben | nichts |
| eine Rolle ohne *ezjscore / call*, die in der Administration bearbeitet (oft **Editor**) | verliert die Bearbeitung der Priorität direkt in der Liste der Unterelemente, die Liste selbst, wo `expsubitems::rows` nicht verfügbar ist, und die Vorschau der Bildgrößen im Einbettungsdialog, **bis sie die Richtlinie erhält** |
| Ihr eigenes JavaScript, das `ezjscnode` für anonyme Besucher aufruft | braucht die Richtlinie für die Rolle Anonymous |

Andere Server-Funktionen, die mit einer Einschränkung auf Funktionen vergeben sind (zum Beispiel `ezstarrating_rate`
für die Rolle Anonymous), sind nicht betroffen.

## So schalten Sie es ein

1. Geben Sie *ezjscore / call* mit der Funktionsliste **ezjscnode** jeder Rolle, deren Benutzer ohne vollen Zugriff in
   der Administration arbeiten (auf den meisten Websites: Editor).
2. Fügen Sie zu `settings/override/ezjscore.ini.append.php` hinzu:

   ```ini
   [ezjscServer_ezjscnode]
   RequirePolicy=enabled
   ```

3. Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`); unter der Velocity-Engine starten Sie sie neu,
   damit ihre Worker die neue Einstellung lesen.
4. Prüfen Sie: Als anonymer Besucher antwortet `/ezjscore/call/ezjscnode::load::2?ContentType=json` mit 403; als
   Redakteur funktionieren die Liste der Unterelemente und ihre Spalte Priorität weiterhin.

Dank an Mugo Web, dessen LoveStack-Commit
[`aa307a7ff3`](https://github.com/mugoweb/ezpublish-legacy/commit/aa307a7ff3110121006b1b97f19fdcbdeab41190) (2024)
diese Aufrufe hinter das Berechtigungssystem gestellt hat.
