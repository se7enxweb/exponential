# Writing content from PHP with expContentWriter

This guide is for PHP developers who create, update or remove content from code: a one-off script, an import from
another system, a migration that seeds content, an integration that keeps two systems in step. You follow it from top
to bottom. At the end you have created, updated, translated and removed content with a few lines each, and you have a
complete import script, a migration and an integration pattern to copy.

It needs Exponential 6.0.15 or later: the class `expContentWriter` is part of the kernel from that release on. The
feature page explains what the class does and why it exists:
[Content from plain values: expContentWriter](../features/6.0/content-writer.md).

## In short

- `expContentWriter::create( array( 'parentNodeID' => ..., 'classIdentifier' => ..., 'languageLocale' => ..., 'attributes' => array( ... ) ) )`
  creates and publishes an object.
- `expContentWriter::update( $object, array( ... ), 'eng-US' )` publishes a new version with the values that change.
- `expContentWriter::remove( $object )` removes the object with all its locations; `remove( $object, true )` moves it
  to the trash.
- Values are text by attribute identifier: rich text as HTML, a file as a public `https` address, everything else in
  the datatype's text form.
- Every call returns an array with `status` (`published`, `pending`, `invalid`, `failed`), `message`, `object` and
  `version`. Check `status`; nothing throws.
- The writer does not check permissions. Your code runs as a user you choose; the REST API checks for its callers.

## Contents

1. [Before you start](#before-you-start)
2. [The user a script runs as](#the-user-a-script-runs-as)
3. [Your first create](#your-first-create)
4. [Reading the answer](#reading-the-answer)
5. [Updating and translating](#updating-and-translating)
6. [Removing](#removing)
7. [Rich text, files and other values](#rich-text-files-and-other-values)
8. [An import that can run again](#an-import-that-can-run-again)
9. [Content in a migration](#content-in-a-migration)
10. [An integration that keeps two systems in step](#an-integration-that-keeps-two-systems-in-step)
11. [Checking permissions in your own code](#checking-permissions-in-your-own-code)
12. [Which tool for which job](#which-tool-for-which-job)
13. [Troubleshooting](#troubleshooting)

## Before you start

Check that the class is there. From the root of the installation:

```bash
grep -c expContentWriter autoload/ezp_kernel.php
```

Expected: `1`. A `0` means the kernel is older than 6.0.15; update it first ([Upgrading](upgrading.md)).

The examples are PHP files that run with the kernel loaded. The quickest way is `bin/php/ezexec.php`, which does the
bootstrap and runs the file:

```bash
php bin/php/ezexec.php extension/exp_mysite/scripts/first_create.php
```

Keep scripts in an extension of your own (here `exp_mysite`), never in the kernel's folders. For a script with options
and help output, write a console command instead ([Write a console command](../help/php-developers/write-a-console-command.md)).

Run everything on a copy of the site first.

## The user a script runs as

A script on the command line runs as the anonymous user until it sets another one, and the new objects are owned by
the current user. Make a dedicated account for imports (for example `exp_importer`) with exactly the roles the work
needs, and sign it in at the top of every script:

```php
<?php
$user = eZUser::fetchByName( 'exp_importer' );
if ( !$user instanceof eZUser )
{
    eZCLI::instance()->error( 'The user exp_importer does not exist' );
    return;
}
eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
```

`expContentWriter` does not ask the permission system: it writes what your code tells it to. That is right for a
script you trust, and wrong for anything that acts on behalf of somebody else; see
[Checking permissions in your own code](#checking-permissions-in-your-own-code).

## Your first create

`extension/exp_mysite/scripts/first_create.php` creates a folder below the content root (node 2):

```php
<?php
$cli = eZCLI::instance();
$user = eZUser::fetchByName( 'exp_importer' );
eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );

$result = expContentWriter::create( array(
    'parentNodeID'    => 2,
    'classIdentifier' => 'folder',
    'languageLocale'  => 'eng-US',
    'remoteID'        => 'exp-mysite-press-folder',
    'attributes'      => array(
        'name'              => 'Press',
        'short_description' => '<p>News for <em>journalists</em>.</p>',
    ),
) );

$cli->output( $result['status'] . ': ' . $result['message'] );
if ( $result['object'] instanceof eZContentObject )
    $cli->output( 'Object ' . $result['object']->attribute( 'id' ) . ', node ' . $result['object']->attribute( 'main_node_id' ) );
```

Run it:

```bash
php bin/php/ezexec.php extension/exp_mysite/scripts/first_create.php
```

Expected output:

```text
published: Created
Object <object id>, node <node id>
```

Open the content tree in the administration: the folder **Press** is there, published, owned by `exp_importer`, in the
section of node 2. Run the script a second time and it answers
`invalid: An object with the remote ID exp-mysite-press-folder exists already.` and writes nothing: the remote id is
what makes a script safe to run again.

The parameters of `create()`:

| Parameter | Required | Meaning |
|---|---|---|
| `parentNodeID` | yes | the node to create below |
| `classIdentifier` | yes | the class, for example `folder`, `article` |
| `languageLocale` | yes | a language of the installation, for example `eng-US`, `ger-DE` |
| `attributes` | no | attribute identifier => text; attributes left out keep the class's defaults |
| `remoteID` | no | your key for the object; a second create with the same key is refused |
| `ownerID` | no | the owner's object id; default: the current user |
| `sectionID` | no | default: the section of the parent's object |

## Reading the answer

Every call returns the same array, so one helper handles all of them:

```php
<?php
function exp_mysite_written( array $result, eZCLI $cli )
{
    switch ( $result['status'] )
    {
        case 'published':
            return true;
        case 'pending':
            // a workflow (an approval, for example) holds the version; it is published when the workflow ends
            $cli->warning( $result['message'] . ' (version ' . $result['version'] . ')' );
            return true;
        case 'invalid':
            // nothing was written: an unknown class, attribute, language or parent, a value that is not text,
            // a file that is not a public address, a remote id that exists already
            $cli->error( 'Refused: ' . $result['message'] );
            return false;
        default: // failed
            // the write failed half way and was rolled back; the reason is in the debug log
            $cli->error( 'Failed: ' . $result['message'] );
            return false;
    }
}
```

| Key | Value |
|---|---|
| `status` | `published`, `pending`, `invalid` or `failed` |
| `message` | `Created`, `Updated`, `Removed`, `Waiting for a workflow`, or why nothing was written |
| `object` | the `eZContentObject`, fetched again after the publish, or `null` |
| `version` | the version number written (`0` for a removal or a refusal) |
| `languageLocale` | after an update, the language written |

`invalid` means nothing changed, so it is safe to fix the input and try again. A `pending` version is also written to
`var/log/contentwriter.log`.

## Updating and translating

`update()` takes the object, the values that change and a language. It publishes a **new version**: the old one stays
in the history, where an editor can compare or restore it.

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'exp-mysite-press-folder' );
if ( !$object instanceof eZContentObject )
    return;

// change one attribute; the others keep their values
$result = expContentWriter::update( $object, array( 'name' => 'Press and media' ), 'eng-US' );

// add a German translation: a language the object does not have yet becomes a new translation
$result = expContentWriter::update( $object, array(
    'name'              => 'Presse und Medien',
    'short_description' => '<p>Neuigkeiten für die <em>Presse</em>.</p>',
), 'ger-DE' );
```

Without the third argument the object's main language is written. An empty array of values is refused
(`No attribute values were given; the content is unchanged.`), so a sync that has nothing to change writes nothing.

## Removing

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'exp-mysite-press-folder' );
if ( $object instanceof eZContentObject )
{
    $result = expContentWriter::remove( $object, true );   // true: to the trash; false (default): deleted
}
```

`remove()` removes **every location** of the object and **everything below them**, like **Remove** in the
administration with all locations selected. Before you remove a folder from a script, look at what is below it:
`eZContentObjectTreeNode::subtreeRemovalInformation( array( $nodeID ) )` lists it without changing anything. For large
subtrees, run the removal as a content job in the background instead ([Content jobs](../bc/6.0/content-jobs.md)).

## Rich text, files and other values

| Datatype | Give | Example |
|---|---|---|
| `ezxmltext` (rich text) | HTML | `<p>Doors open at <a href="https://example.org">ten</a>.</p>` |
| `ezimage` | `<public URL>` or `<public URL>\|<alternative text>` | `https://media.example.org/hall.jpg\|The new hall` |
| `ezbinaryfile`, `ezmedia` | `<public URL>` or `<public URL>\|<file name>` | `https://files.example.org/r/7.pdf\|Annual report 2026.pdf` |
| `ezstring`, `eztext` | the text | `Opening of the new hall` |
| `ezboolean` | `1` or `0` | `1` |
| `ezinteger`, `ezfloat` | the number | `42` |
| `ezdate`, `ezdatetime` | a Unix timestamp | `(string)mktime( 10, 0, 0, 11, 2, 2026 )` |
| `ezobjectrelationlist` | object ids joined with `-` | `57-58-91` |
| `ezkeyword` | keywords separated by commas | `press, opening` |
| any other | the text that the datatype's `fromString()` reads | see [The eZDataType API](../help/extension-development/part-8-reference/ezdatatype-api.md) |

What the writer does with them:

- **HTML** is converted by the Online Editor's input parser when `ezoe` is active; a link or an embed of another object
  (`<a href="ezobject://57">`, `<a href="eznode://12">`) becomes a relation of the object. Without `ezoe` the simplified
  XML input parser converts it.
- **Files** are downloaded from a public `http` or `https` address, at most 50 MB, without following redirects, and
  stored like an upload. A path on the server is refused, so is any address that resolves to a private, loopback,
  link-local or reserved network: a value from outside can never make the server read its own files or call an
  internal service. If your files are on the server already, publish them under a public address first, or write the
  attribute with `eZContentFunctions` and `storage_dir`, which reads server paths ([Which tool for which job](#which-tool-for-which-job)).
  The download needs PHP's `curl` extension.
- An empty file value leaves the file as it is; an empty value of any other datatype empties it.
- An identifier the class does not have, and a value that is an array or an object, are refused before anything is
  written.

## An import that can run again

The pattern every import should follow: one remote id per record, create when it is new, update when it exists,
report what happened, offer a dry run, keep memory flat.

`extension/exp_mysite/scripts/import_events.php` reads `events.csv` (columns `id,title,intro_html,start,image_url`)
and keeps the folder Events in step with it:

```php
<?php
$cli = eZCLI::instance();
$dryRun = in_array( '--dry-run', $_SERVER['argv'], true );
$file = 'var/import/events.csv';
$parentNodeID = 2; // replace with the node id of your folder Events

$user = eZUser::fetchByName( 'exp_importer' );
eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );

$handle = fopen( $file, 'r' );
if ( !$handle )
{
    $cli->error( "Cannot read $file" );
    return;
}
fgetcsv( $handle, 0, ',', '"', '\\' ); // the header line
$counts = array( 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'refused' => 0 );
$row = 0;
while ( ( $line = fgetcsv( $handle, 0, ',', '"', '\\' ) ) !== false )
{
    ++$row;
    list( $id, $title, $introHtml, $start, $imageUrl ) = $line;
    $remoteID = 'exp-mysite-event-' . $id;
    $values = array(
        'title'      => $title,
        'intro'      => $introHtml,
        'start_date' => (string)strtotime( $start ),
        'image'      => $imageUrl !== '' ? $imageUrl . '|' . $title : '',
    );

    $object = eZContentObject::fetchByRemoteID( $remoteID );
    if ( $dryRun )
    {
        $cli->output( ( $object ? 'would update ' : 'would create ' ) . $remoteID );
        continue;
    }
    if ( $object instanceof eZContentObject )
    {
        // write only what changed, so an unchanged record makes no new version
        $dataMap = $object->dataMap();
        $changed = array();
        foreach ( array( 'title', 'start_date' ) as $identifier )
            if ( $dataMap[$identifier]->toString() !== $values[$identifier] )
                $changed[$identifier] = $values[$identifier];
        if ( !$changed )
        {
            ++$counts['unchanged'];
            continue;
        }
        $result = expContentWriter::update( $object, $changed, 'eng-US' );
        $key = 'updated';
    }
    else
    {
        $result = expContentWriter::create( array( 'parentNodeID' => $parentNodeID, 'classIdentifier' => 'exp_event',
                                                   'languageLocale' => 'eng-US', 'remoteID' => $remoteID,
                                                   'attributes' => $values ) );
        $key = 'created';
    }
    if ( $result['status'] === 'published' || $result['status'] === 'pending' )
        ++$counts[$key];
    else
    {
        ++$counts['refused'];
        $cli->error( "Row $row ($remoteID): " . $result['message'] );
    }
    if ( $row % 100 === 0 )
        eZContentObject::clearCache(); // keeps memory flat on long runs
}
fclose( $handle );
$cli->output( json_encode( $counts ) );
```

```bash
php bin/php/ezexec.php extension/exp_mysite/scripts/import_events.php --dry-run   # what it would do
php bin/php/ezexec.php extension/exp_mysite/scripts/import_events.php             # the import
```

Expected at the end of a first run: `{"created":<rows>,"updated":0,"unchanged":0,"refused":0}`; a second run with the
same file: `{"created":0,"updated":0,"unchanged":<rows>,"refused":0}`.

The comparison writes only what changed, so a nightly run adds no versions for records that did not change. It
compares the plain attributes here; compare the rich text too if your source changes it. A refused row (an unknown
attribute, an image address on a private network) is reported with its row number, and the other rows go on.

For very large imports, also read [Importing content](../help/cms-developers/importing-content.md) (the import tools
that exist already, `sqliimport` among them) and [Large data migrations](../help/migrations/recipes/large-data-migrations.md).

## Content in a migration

A migration is the right place for content every installation of a site must have: a folder the templates expect, a
landing page, a set of categories. The run's user is signed in during `up()` and `down()`
([PHP migrations](migrations-php.md#the-context)), so no user code is needed:

```php
<?php
// description: The folder Press releases below the content root
namespace Site\Migrations;

final class PressReleasesFolder20261112100000 extends \expMigrationPhp
{
    const REMOTE_ID = 'site-press-releases-folder';

    public function plan( $context )
    {
        return array( 'publish the folder Press releases below node 2 (remote id ' . self::REMOTE_ID . ')' );
    }

    public function detect( $context )
    {
        return \eZContentObject::fetchByRemoteID( self::REMOTE_ID ) instanceof \eZContentObject ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $result = \expContentWriter::create( array(
            'parentNodeID'    => 2,
            'classIdentifier' => 'folder',
            'languageLocale'  => 'eng-US',
            'remoteID'        => self::REMOTE_ID,
            'attributes'      => array( 'name' => 'Press releases' ),
        ) );
        if ( $result['status'] !== 'published' )
            throw new \RuntimeException( 'the folder Press releases was not published: ' . $result['message'] );
        $context->setReference( 'press_releases_node', (int)$result['object']->attribute( 'main_node_id' ) );
        return array( 'published the folder Press releases (object ' . $result['object']->attribute( 'id' ) . ')' );
    }

    public function down( $context )
    {
        $object = \eZContentObject::fetchByRemoteID( self::REMOTE_ID );
        if ( !$object instanceof \eZContentObject )
            return array( 'the folder Press releases was already gone' );
        \expContentWriter::remove( $object, true );
        return array( 'moved the folder Press releases to the trash' );
    }
}
```

Throwing when the status is not `published` makes the runner roll the migration back and report it. A migration
expects the content to be there when it ends, so a `pending` answer counts as a failure here.

Migrations can also create content without PHP, with the YAML step `content`
([The YAML steps](migrations-yaml-steps.md)). Use the YAML step for plain content and PHP when the values are computed.

## An integration that keeps two systems in step

An integration usually receives a record from another system (a product feed, a list of events from a booking system)
and has to make the site match it. Keep the mapping in one class, so the code that receives the data (a cronjob part,
a console command, a module view) stays small:

```php
<?php
class expMySiteEventSync
{
    /**
     * @param array $record the other system's record: id, title, description (HTML), cancelled
     * @return array the expContentWriter answer
     */
    public static function apply( array $record, $parentNodeID )
    {
        $remoteID = 'booking-event-' . (int)$record['id'];
        $object = eZContentObject::fetchByRemoteID( $remoteID );
        if ( !empty( $record['cancelled'] ) )
        {
            return $object instanceof eZContentObject
                ? expContentWriter::remove( $object, true )
                : array( 'status' => 'published', 'message' => 'Nothing to remove', 'object' => null, 'version' => 0 );
        }
        $values = array( 'title' => (string)$record['title'], 'intro' => (string)$record['description'] );
        if ( $object instanceof eZContentObject )
            return expContentWriter::update( $object, $values );
        return expContentWriter::create( array( 'parentNodeID' => (int)$parentNodeID, 'classIdentifier' => 'exp_event',
                                                'languageLocale' => 'eng-US', 'remoteID' => $remoteID,
                                                'attributes' => $values ) );
    }
}
```

Put the class in `extension/exp_mysite/classes/` and regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`).

**When the other system calls you** rather than you calling it, you need not write PHP at all: the REST API does the
same writes over HTTP with a personal API key or an OAuth2 token, and checks the caller's permissions:

```bash
curl -s -X PUT -H "Authorization: Bearer $EXP_API_KEY" -H "Content-Type: application/json" \
     -d '{"languageLocale":"eng-US","title":"Opening moved to eleven"}' \
     https://www.example.com/api/ezp/v2/content/node/<node id>
# {"message":"Updated","objectId":<object id>,"nodeId":<node id>,"version":<version>,"languageLocale":"eng-US"}
```

See [REST API v1 and v2](rest-api-v1-and-v2.md#21-updating-and-the-values-of-a-write) and
[New in 6.0.15: the REST API updates content](../help/whats-new-in-6-0-15-rest-api.md).

## Checking permissions in your own code

When your code writes on behalf of a signed-in user (a module view, an ezjscore function, a REST provider of your
own), ask the permission system first, the way the content module and the REST API do. The kernel's
`expRestContentPermission` has the checks:

```php
<?php
$parent = eZContentObjectTreeNode::fetch( $parentNodeID );
$class = eZContentClass::fetchByIdentifier( 'exp_event' );
if ( !$parent || !$class || !expRestContentPermission::createAllowed( $parent, $class, 'eng-US' ) )
    return array( 'error' => 'You may not create an event here.' );

$result = expContentWriter::create( array( 'parentNodeID' => $parentNodeID, 'classIdentifier' => 'exp_event',
                                           'languageLocale' => 'eng-US', 'attributes' => $values ) );
```

| Before | Check |
|---|---|
| `create()` | `expRestContentPermission::createAllowed( $parentNode, $class, $locale )` |
| `update()` | `expRestContentPermission::editAllowed( $node, $locale )` |
| `remove()` | `expRestContentPermission::removeAllowed( $node )` for every location |

A module view that changes content also takes POST only and checks the form token
([CSRF and ezformtoken](../help/php-developers/csrf-and-ezformtoken.md)).

## Which tool for which job

| You want to | Use |
|---|---|
| Create, update or remove content from values in PHP | `expContentWriter` (this guide) |
| Publish **without** notifications, or read files from paths on the server | `eZContentFunctions::createAndPublishObject()` / `updateAndPublishObject()` with `$notify = false` and `storage_dir` ([reference](../help/extension-development/part-8-reference/content-functions-from-php.md)) |
| Move, hide, add locations, change section or owner | the kernel's calls ([Content from PHP](../help/cms-developers/content-from-php.md#moving-hiding-and-removing)) |
| Content every installation of a site must have | a migration, YAML `content` step or PHP ([Migrations](migrations.md)) |
| Let another system write over HTTP | the REST API ([REST API v1 and v2](rest-api-v1-and-v2.md)) or GraphQL mutations ([GraphQL](../features/6.0/graphql.md)) |
| Import a large set from a known format | the existing tools first ([Importing content](../help/cms-developers/importing-content.md)) |

## Troubleshooting

| Message | Cause | What to do |
|---|---|---|
| `The parent node <id> does not exist.` | wrong `parentNodeID` | find the node by remote id rather than by number; ids differ between installations |
| `The class <x> does not exist.` | wrong identifier | **Setup** > **Classes** shows the identifiers |
| `The language <x> is not a language of this installation.` | a locale not added | add it under **Setup** > **Languages**, or use one that exists |
| `The class <x> has no attribute <a>, <b>.` | identifiers that are not in the class | compare with the class definition; nothing was written |
| `The value of <a> must be text.` | an array or object as value | turn it into the datatype's text form |
| `An object with the remote ID <x> exists already.` | a create for a record that exists | fetch it and `update()` instead |
| `The value of <a> must be a public http or https address of the file.` | a server path, another scheme, credentials, or an address on a private network | give a public `https` address |
| `The file of <a> could not be downloaded (at most 50 MB).` | not reachable, a redirect, too large, or no `curl` | check the address with `curl -I`; give the final address, not one that redirects |
| `Waiting for a workflow` (`pending`) | a workflow holds the version | it is published when the workflow ends; see **Setup** > **Workflows** and `var/log/contentwriter.log` |
| `The content could not be created.` / `updated.` (`failed`) | an error during the write | the reason is in the debug log (`var/log/error.log`); the write was rolled back |
| New objects are owned by Anonymous, or the publish is refused | no user signed in | sign in the importer account ([The user a script runs as](#the-user-a-script-runs-as)) |

## Related pages

- [Content from plain values: expContentWriter](../features/6.0/content-writer.md): the feature page
- [Upgrade notes: the REST writes through the kernel, nxc_powercontent no longer needed](../bc/6.0/content-writer-and-rest-writes.md)
- [Content from PHP](../help/cms-developers/content-from-php.md): reading content, lists and the other kernel calls
- [Writing an import script](../help/integrators/writing-an-import-script.md)
- [PHP migrations](migrations-php.md), [Migrations](migrations.md)
- [REST API v1 and v2](rest-api-v1-and-v2.md), [Personal API keys](api-keys.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
