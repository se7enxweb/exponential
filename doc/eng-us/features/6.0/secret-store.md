# The secret store: passwords and keys of the tools, encrypted, by name (6.0.15)

Read this page if you write a tool that needs a password, a token or an API key (a database connection, an import
source, a remote server), or if you administer the secrets of an installation. The
[data table connections](data-tables.md) are the first users.

## In short

| | |
|---|---|
| What it is | One store in the kernel for every secret a tool needs: the [data table connections](data-tables.md), the import sources of 7x SQLI Import, the remote conversion server of ezodf and any other tool. A tool keeps the **name** of a secret, never its value, and asks the store for the value when it needs it. |
| Where the values are | The table `exp_secret`, each value encrypted with XChaCha20-Poly1305 (PHP's sodium extension). |
| The key | The environment variable `EXP_SECRET_KEY`, or a key file the store makes on first use (`var/secrets/exp_secret.key`). **The key is not in the database**: a backup of the database alone cannot decrypt the secrets. |
| Where | **Setup > Secrets** (module `secrets`, policy `secrets/manage`): names, the tool each belongs to, whether this server can decrypt it, the key, rotation. A value is written there and never shown again. |
| Classes | `expSecrets` (the store), `expSecretKey` (the key), `expSecret` (a row) |
| Settings | `secrets.ini [SecretSettings]`: `KeyFile`, `Secrets[]` |

## How a name is resolved

`expSecrets::get( $name )` looks in this order:

| Name | Value |
|---|---|
| `env:<VARIABLE>` | the environment variable of the PHP process (the PHP-FPM pool, the Velocity environment) |
| `<name>` | a row of `exp_secret` of that name, decrypted with the store's key |
| `<name>` | `secrets.ini [SecretSettings] Secrets[<name>]`, set in the site's own settings (never in a repository) |
| `<name>` | the tool's own list of secrets, when it passes one (`get( $name, $fallback )`), so the secrets a tool kept in its own settings before 6.0.15 keep working |

A name is letters, digits and `_ . : -`, at most 128 characters. A name that is none of these gives `null`.

## The key

The key is 32 bytes, from:

1. the environment variable **`EXP_SECRET_KEY`** when it is set (base64, with or without padding, or 64 hex
   characters); or
2. the **key file** of `secrets.ini [SecretSettings] KeyFile` (default `var/secrets/exp_secret.key`, relative to the
   installation, or absolute), made when the first secret is stored: 32 random bytes, base64, mode 0600. Its
   directory gets mode 0700 and a `.htaccess` that refuses every request, and it must not be in a path the web server
   serves (`var/<site>/storage`, `var/cache/public` are). Written by a process running as root (a command, Velocity),
   the file and its directory get the owner of the installation's `var/` directory, so the web server's user can read
   them. Two requests that store the first secret at the same moment end with one key, never one each.

**Keep a copy of the key with every backup**, in a safe place of its own. The key is not in the database, so a backup
of the database alone cannot decrypt the secrets: restored without the key, every secret has to be set again. Setup >
Secrets says where the key is and repeats this warning.

## Encryption

Each value is encrypted on its own with XChaCha20-Poly1305 (`sodium_crypto_aead_xchacha20poly1305_ietf_encrypt`) and a
random 24-byte nonce. The stored text is base64 of a version byte, the nonce and the ciphertext. The secret's name and
the version byte are the associated data: a value moved to another name in the database, or a changed byte, no longer
decrypts. Each row also keeps the id of its key (16 hex characters of a hash of the key, never the key), so the store
knows which key to use and Setup > Secrets can say how many secrets are still under an older key. A row no key of
this server decrypts is listed as "cannot be decrypted here" and gives `null`.

## Rotating the key

**Key file** (Setup > Secrets, **Rotate the key**, confirmed with a tick):

1. A new key is written beside the current one (`exp_secret.key.next`).
2. Every secret is decrypted and encrypted again with the new key, in one transaction.
3. The current key is kept as `exp_secret.key.previous`, and the new key takes the place of `exp_secret.key`.

Until the next rotation the previous key still decrypts what it encrypted; a rotation that stopped half way loses
nothing, because a row is always decrypted with the key its id names: the current one, the previous one, or the
`.next` one of a rotation in progress. Back up the new key file afterwards.

**Environment key**: set the new key as `EXP_SECRET_KEY` and the old one as `EXP_SECRET_KEY_PREVIOUS`, then
**Encrypt every secret again with the current key** on Setup > Secrets. When no secret is left under the old key
(the page says how many are), remove `EXP_SECRET_KEY_PREVIOUS`.

## Setup > Secrets

| | |
|---|---|
| The key | where it comes from (the environment or the key file, with its path), its id, the previous key, the encryption, the backup warning, how many secrets are under another key; **Rotate the key** (file key) and **Encrypt every secret again with the current key** |
| Names | every secret of the store and of `secrets.ini`: name, tool (a link that filters the list to that tool), description, state (encrypted, `secrets.ini`, or cannot be decrypted here), when it was changed; **Remove** with a tick beside it |
| Set a secret | name, tool (`datatable`, `sqliimport`, `ezodf` ...), value, description. A new name adds a secret, an existing one replaces its value; with an empty value only the description changes. The value field is a password field and is empty when the page comes back. |

While the table `exp_secret` is missing the page says so and offers **Create the table**; until then only the
secrets of `secrets.ini` and environment variables can be used. The policy is `secrets/manage`; only the Administrator
role has it in a new installation.

## Using it in a tool

```php
// the name the tool keeps (in its own row, its settings, its import definition)
$name = $connection['secret'];

$password = expSecrets::get( $name );                      // null when there is none of that name
$password = expSecrets::required( $name, 'Archive database' ); // a RuntimeException naming the secret, never a value
if ( expSecrets::has( $name ) ) { ... }

// storing a value the user typed in (write-only: it is never read back to a page)
expSecrets::set( 'archive_db', $typed, 'datatable', 'The archive database' );
expSecrets::describe( 'archive_db', 'The archive database (read only)' );
expSecrets::remove( 'archive_db' );

// a driver's error message may quote the password: mask it before it reaches a page or a log
$message = expSecrets::mask( $e->getMessage(), array( $name ), array( $typed ) );

expSecrets::names( 'datatable' );   // name => name, source, scope, description, readable, key_id, modified
expSecrets::reencryptAll();         // after EXP_SECRET_KEY changed
expSecrets::rotate();               // a file key
```

`set()` refuses a name starting with `env:`, an empty value, and works only when the table exists and the key is
available. A tool that offers a password field stores it with `set()` under a name the tool then keeps; the data table
connections store it as `datatable.<identifier>` unless another name is given.

A tool never:

- stores a value anywhere but through `expSecrets::set()` (not in its own table, its settings, a job's parameters, a
  file it exports);
- writes a value to a page, a log line, the debug output, a run's history or an error message (`mask()` first);
- shows a stored value again, even to the user who typed it in.

## Tests

`tests/tests/kernel/classes/secrets/` (no installation needed): encryption and decryption, the name and the key bound
in, a changed text refused; the key from the environment and from a file made on first use with mode 0600, an
existing key file kept, the key id, rotation and the previous key; `env:` names, valid names and masking. The table
`exp_secret` is checked to be the same in `share/db_schema.dba`, the persistent class, the three kernel schemas and the
three update files.
