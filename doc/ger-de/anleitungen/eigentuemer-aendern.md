# Den Eigentümer von Inhalten ändern

Jedes Inhaltsobjekt hat einen Eigentümer, den Benutzer, dem es gehört. Rollen können einschränken, was jemand mit
seinen eigenen Inhalten tun darf (die Einschränkung `Owner` von `content/read`, `content/edit` und `content/remove`).
Wenn jemand ausscheidet oder Inhalte unter dem falschen Konto angelegt wurden, muss der Eigentümer geändert werden.
Die Extension **ezownerchange** (se7enxweb/ezownerchange) erledigt das für ein einzelnes Objekt oder für einen ganzen
Teilbaum. In neuen Installationen ist sie eingeschaltet.

Am Ende dieser Anleitung haben Sie den Eigentümer eines Objekts und eines Teilbaums in der Administration geändert,
zuvor die Vorschau gesehen und dasselbe auf der Kommandozeile mit `--dry-run` getan.

## 1. In der Administration

1. Öffnen Sie das Kontextmenü eines Knotens und wählen Sie **Eigentümer ändern**. Der Eintrag erscheint für Benutzer
   mit der Richtlinie `owner/change` (die Rolle Administrator hat sie).
2. Wählen Sie den neuen Eigentümer im Inhaltsbrowser und klicken Sie auf **Auswählen**. Noch hat sich nichts geändert.
3. Die Vorschau zeigt jedes Objekt, das die Änderung erreicht, und das Ergebnis für jedes. Wählen Sie **Nur dieses
   Objekt** oder **Dieses Objekt und alles darunter** (dafür ist `owner/subtree` nötig), schränken Sie die Änderung
   optional auf die Objekte des aktuellen Eigentümers ein und klicken Sie auf **Vorschau aktualisieren**.
4. Klicken Sie auf **Eigentümer von N Objekten ändern**. Die Seite zeigt, wie viele Eigentümer geändert wurden.

Jedes Objekt wird nur dort geändert, wo Sie es auch bearbeiten dürfen (`content/edit`). Jede Änderung wird im Audit
als `content.ezownerchange.change` festgehalten.

## 2. Auf der Kommandozeile

Für Teilbäume mit mehr als 500 Objekten oder für skriptgesteuerte Übergaben sehen Sie sich zuerst die Vorschau an und
führen dann die Änderung aus. Führen Sie sie als Benutzer des Webservers aus:

```bash
php extension/ezownerchange/bin/php/expownerchange.php --to-owner=<new owner id> --node=<node id> --from-owner=<old owner id> --dry-run --list
php extension/ezownerchange/bin/php/expownerchange.php --to-owner=<new owner id> --node=<node id> --from-owner=<old owner id>
```

Die [Anleitung zur Kommandozeile](https://github.com/se7enxweb/ezownerchange/blob/master/doc/cli.md) der Extension enthält jede Option und echte Ausgaben.

## Die Dokumentation der Extension

Die eigenen Anleitungen der Extension stehen im
[Repository ezownerchange](https://github.com/se7enxweb/ezownerchange/tree/master/doc) und in jeder Installation
unter `extension/ezownerchange/doc/`:

- [Verwendung in der Administration](https://github.com/se7enxweb/ezownerchange/blob/master/doc/usage.md)
- [Das Kommandozeilenskript](https://github.com/se7enxweb/ezownerchange/blob/master/doc/cli.md)
- [Konfiguration](https://github.com/se7enxweb/ezownerchange/blob/master/doc/configuration.md)
- [Berechtigungen und Rollen-Setups](https://github.com/se7enxweb/ezownerchange/blob/master/doc/permissions.md)
- [Was das Audit festhält](https://github.com/se7enxweb/ezownerchange/blob/master/doc/audit.md)
- [Upgrade von Releases bis 1.3](https://github.com/se7enxweb/ezownerchange/blob/master/doc/upgrading.md)
- [Fehlerbehebung](https://github.com/se7enxweb/ezownerchange/blob/master/doc/troubleshooting.md)

## Verwandte Seiten

- [Rollen, Richtlinien und nicht aktivierte Benutzer](rollen-und-richtlinien.md)
- [Sicherheit und Audit](sicherheit-und-audit.md)
