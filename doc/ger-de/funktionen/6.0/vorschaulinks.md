# Vorschaulinks: ein Entwurf für einen Prüfer ohne Konto (6.0.15)

Lesen Sie diese Seite, wenn jemand außerhalb der Installation (ein Kunde, eine Anwältin, ein Übersetzer) einen Entwurf
sehen soll, bevor er veröffentlicht wird, oder wenn Sie ein Werkzeug schreiben, das solche Links erzeugt. Die
[Website-Seitenleiste](https://github.com/se7enxweb/expwt) bietet sie in ihrem Export-Panel an.

## Was ein Link ist

Ein Vorschaulink zeigt **eine Version eines Objekts in einer Sprache**, im Design der Site, jedem, der den Link hat,
angemeldet oder nicht. Über dem Inhalt sagt ein Hinweis, dass es sich um einen Entwurf handelt, der nicht veröffentlicht
ist, und bis wann der Link funktioniert.

```
https://www.example.com/content/sharedpreview/3f9c0a1b2c3d4e5f60718293.Yp0w...43 characters
```

- **Signiert.** Das Token ist die ID des Links und ein HMAC-SHA256 seines Objekts, seiner Version, seiner Sprache und
  seines Ablaufs. Der Schlüssel ist das Geheimnis `content.previewlink.key` im
  [Geheimnisspeicher](geheimnisspeicher.md), erzeugt mit dem ersten Link. Wird es unter Setup > Secrets entfernt, enden
  alle Links auf einmal.
- **Ablaufend.** Ein Tag, sofern sein Ersteller nichts anderes wählt, nie länger als `MaxLifetime` (sieben Tage).
- **Widerrufbar.** Jeder Link ist eine Zeile von `ezsite_data` (`exp_previewlink_<id>`) mit dem, der ihn erzeugt hat,
  wann, seinem Ablauf und wie oft er geöffnet wurde. Ein widerrufener Link funktioniert sofort nicht mehr. Beendete Links
  werden noch sieben Tage gelistet und dann entfernt.
- **Nur solange es ein Entwurf ist.** Sobald die Version veröffentlicht, verworfen oder archiviert ist oder das Konto
  ihres Erstellers entfernt oder deaktiviert wird, antwortet der Link mit 410 und sagt das.
- **Protokolliert.** `content.previewlink.create`, `content.previewlink.revoke` und `content.previewlink.open`
  ([Audit-Katalog](../../bc/6.0/audit.md)). Das Token selbst wird nie aufgezeichnet.

## Wer einen erzeugen darf

Ein Benutzer, der die Version lesen (`content/versionread`) und ihr Objekt bearbeiten (`content/edit`) darf. Die
Website-Seitenleiste fragt außerdem zuerst "Verify it's you", in der Gruppe `export` von `site.ini [ReauthSettings]`:
Ein Link übergibt unveröffentlichten Inhalt an jemanden außerhalb.

## Was der Prüfer bekommt und was nicht

- Die Ansicht `content/sharedpreview/<token>` braucht keine Anmeldung (`site.ini [RoleSettings] PolicyOmitList[]`). Das
  Token ist die Berechtigung, nur für diese Version; Menüs, verwandte Inhalte und alles andere auf der Seite sind das,
  was ein anonymer Besucher ohnehin sehen darf.
- Die Seite wird nie in einem Cache gespeichert (dem rollenbewussten Seiten-Cache, dem Antwort-Cache von Velocity,
  Proxys: `Cache-Control: private, no-store`), nie indexiert (`X-Robots-Tag: noindex, nofollow`), und die Adresse wird
  nicht als Referrer weitergegeben (`Referrer-Policy: no-referrer`).
- Ein Token, das kein Link ist oder dessen Signatur nicht passt, antwortet mit 404 und einer Seite, die sagt, dass der
  Link nicht funktioniert; ein abgelaufenes, widerrufenes oder beendetes antwortet mit 410.

## Einstellungen

`content.ini`:

```ini
[PreviewLinkSettings]
Enabled=true
DefaultLifetime=86400
MaxLifetime=604800
MaxActivePerVersion=20
```

## Für Entwickler

```php
$made = expContentPreviewLink::create( $version, 'eng-GB', 3 * 86400, 'For the client' );
// array( 'link' => ..., 'token' => '...', 'path' => 'content/sharedpreview/<token>' ), or array( 'error' => 'not_allowed' )
$links = expContentPreviewLink::listFor( $objectID );   // newest first, each with state active, expired or revoked
expContentPreviewLink::revoke( $links[0]['id'] );
```

`expContentPreviewLink::available()` sagt, ob Links erzeugt werden können (eingeschaltet, ein Geheimnisspeicher, der den
Schlüssel hält). Das Signieren, die Form des Tokens, die Lebensdauer und der Zustand sind reine Funktionen, getestet in
`tests/tests/kernel/classes/expContentPreviewLinkTest.php`.
