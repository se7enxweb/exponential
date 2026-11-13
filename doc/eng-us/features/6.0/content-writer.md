# Content from plain values: expContentWriter

This page is for PHP developers who create, change or remove content from code: import scripts, migrations,
integrations, console commands and REST controllers. Since Exponential 6.0.15 the kernel does these writes itself
with one class, `expContentWriter` (`kernel/classes/contentwriter/expcontentwriter.php`). You give it plain values by
attribute identifier; it builds the object, the version and the attributes, and publishes through the same
`content/publish` operation as an edit in the administration. Added on 12 November 2026.

The guide with complete scripts, a migration, an import and an integration is
[Writing content from PHP with expContentWriter](../../guides/writing-content-from-php.md).

## Why it exists

Creating content from code has been a common need for as long as the kernel has existed: an import from another
system, a nightly sync, a form handler that files a record, an app that posts an article. Until 6.0.15 a developer
had three choices, each with a cost:

- **Build it by hand**: instantiate the class, store the object, add a node assignment, mark the version as a draft,
  loop over the attributes, call `fromString()` on each, run the publish operation and read its status. About forty
  lines that every project wrote again, and that are easy to get subtly wrong (a forgotten node assignment, a version
  left in the wrong status, a transaction left open after an exception).
- **`eZContentFunctions`** (in the kernel since the 4.x years): shorter, but it ignores attribute identifiers the
  class does not have, wants rich text as stored XML and files as server paths, and reports a failure only as `false`.
- **A third-party extension** such as `nxc_powercontent`: one more package to install, keep up to date and audit.
  The REST API needed it for its writes, so every installation that wanted REST writes had to carry it, and it could
  not update content through REST at all.

`expContentWriter` takes over what those extensions were installed for, in the kernel. The REST API
([ezprestapi](extensions/ezprestapi.md) 1.2.9) writes through it and therefore no longer needs `nxc_powercontent`, and
it can now update content.

What changes for a developer:

- **Less code to write and review**: a create is one call with an array of values, an update one call with the
  values that change. The answer says what happened in words a log or an API client can show.
- **Nothing extra to install**: the writes are part of the kernel and are covered by its tests and releases.
- **Less work at run time**: each write is one database transaction for the values and one run of the publish
  operation, and the REST API no longer loads a second extension for its writes.
- **The history is kept**: an update publishes a new version, so the administration shows it in the history and an
  editor can go back to the previous one (`nxc_powercontent` changed the current version in place).
- **The site stays consistent**: workflows, caches, the search index, URL aliases and notifications run as for an
  edit in the administration.

## Before and after

Creating an article below node 2 with a title, an introduction in HTML and an image from a web address.

Before, by hand (abbreviated; the HTML still had to be converted to XML and the image downloaded first):

```php
<?php
$class = eZContentClass::fetchByIdentifier( 'article' );
$parent = eZContentObjectTreeNode::fetch( 2 );
$db = eZDB::instance();
$db->begin();
$object = $class->instantiate( eZUser::currentUserID(), $parent->object()->attribute( 'section_id' ), false, 'eng-US' );
$object->store();
eZNodeAssignment::create( array( 'contentobject_id' => $object->attribute( 'id' ),
                                 'contentobject_version' => $object->attribute( 'current_version' ),
                                 'parent_node' => 2, 'is_main' => 1 ) )->store();
$version = $object->version( $object->attribute( 'current_version' ) );
$version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
$version->store();
foreach ( $version->contentObjectAttributes( 'eng-US' ) as $attribute )
{
    switch ( $attribute->attribute( 'contentclass_attribute_identifier' ) )
    {
        case 'title': $attribute->fromString( 'Opening of the new hall' ); break;
        case 'intro': $attribute->fromString( $xmlConvertedFromHtml ); break;   // your own HTML to XML step
        case 'image': $attribute->fromString( '/tmp/hall.jpg|The new hall' ); break; // your own download step
    }
    $attribute->store();
}
$db->commit();
$result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ),
                                                                     'version' => $object->attribute( 'current_version' ) ) );
// and then: was it published, or is a workflow holding it? what if an exception left the transaction open?
```

Since 6.0.15:

```php
<?php
$result = expContentWriter::create( array(
    'parentNodeID'    => 2,
    'classIdentifier' => 'article',
    'languageLocale'  => 'eng-US',
    'attributes'      => array(
        'title' => 'Opening of the new hall',
        'intro' => '<p>Doors open at <strong>ten</strong>.</p>',
        'image' => 'https://media.example.org/hall.jpg|The new hall',
    ),
) );
if ( $result['status'] !== 'published' && $result['status'] !== 'pending' )
    throw new RuntimeException( $result['message'] );
```

The HTML becomes rich text, the image is downloaded from its public address and stored, and the answer says whether
the object was published or is waiting for a workflow.

## What it does

| Method | Does | Arguments |
|---|---|---|
| `expContentWriter::create( array $params )` | creates an object below a node and publishes it | `parentNodeID` (int), `classIdentifier` (string), `languageLocale` (string, for example `eng-US`), `attributes` (identifier => text); optional `ownerID` (default: the current user), `sectionID` (default: the parent's section), `remoteID` |
| `expContentWriter::update( eZContentObject $object, array $values, $languageCode = false )` | publishes a new version with the values given; attributes left out keep their values; a language the object does not have yet adds that translation | the object, identifier => text, a locale (default: the object's main language) |
| `expContentWriter::remove( eZContentObject $object, $moveToTrash = false )` | removes the object with all its locations and everything below them, as `content/removeobject` does after its checks | the object, `true` to move it to the trash instead of deleting it |

Every method returns an array:

| Key | Value |
|---|---|
| `status` | `published` (done; also for a removal), `pending` (a workflow holds the new version), `invalid` (nothing was written: see the message), `failed` (an error during the write; the transaction was rolled back) |
| `message` | `Created`, `Updated`, `Removed`, `Waiting for a workflow`, or the reason, for example `The class article has no attribute teaser.` |
| `object` | the `eZContentObject` (fetched again after the publish), or `null` |
| `version` | the version number that was written, `0` for a removal or a refusal |
| `languageLocale` | after an update, the language written (an empty string after a create) |

## Values

Values are text, by attribute identifier:

- **Rich text** (`ezxmltext`): HTML. With the Online Editor (`ezoe`) active it is converted by the Online Editor's
  input parser, so links and embeds of other objects become relations of the object; without it, by the simplified XML
  input parser.
- **Files** (`ezimage`, `ezbinaryfile`, `ezmedia`): `<URL>` or `<URL>|<text>`, where the text is the alternative text
  of an image or the file name of a file. The file is downloaded from a public `http` or `https` address. A path on
  the server, other schemes (`file://`, `ftp://`, `php://` ...), credentials in the URL, and addresses that resolve to
  private, loopback, link-local or reserved networks (IPv4, IPv6 and IPv4-mapped IPv6) are refused; redirects are not
  followed; a file may be at most 50 MB (`MAX_FILE_BYTES`). The download needs PHP's `curl` extension. An empty file
  value leaves the file as it is.
- **Every other datatype**: its text form, exactly as `eZContentObjectAttribute::fromString()` reads it (`1` or `0`
  for a checkbox, a timestamp for a date, `<object id>-<object id>` for a relation list, and so on).

A value that is not text (an array, an object) and an identifier the class does not have are refused before anything
is written, with all such identifiers named in the message. Numbers, booleans and `null` are accepted.

## What runs during a write

- The values are written in one database transaction; an exception rolls it back, the downloaded temporary files are
  removed, the error is written to the debug log and the method answers `failed`.
- The version is then published through the `content/publish` operation, the same one the administration runs. Workflows
  (approval, for example), the view cache, the search index, URL aliases and notifications follow as for an edit by
  hand. A version a workflow holds is answered as `pending` and written to `var/log/contentwriter.log`.
- Outside the content module (a REST request, a console script) the module repositories may not be known yet; the
  writer sets them before it runs the operation; without them the operation handler found no `content/publish` and
  did nothing.
- `remove()` removes every location of the object and what is below them; with `$moveToTrash = true` they go to the
  trash. An object without locations is purged.

## What it does not do

- **It does not check permissions.** The caller decides who may write. The REST API checks with the kernel's
  `expRestContentPermission` before it calls the writer; a script runs as the user it sets (see the
  [guide](../../guides/writing-content-from-php.md#the-user-a-script-runs-as)).
- It does not move, hide or add locations, and it does not change the section or the owner of an existing object; use
  the kernel's calls for those ([Content from PHP](../../help/cms-developers/content-from-php.md#moving-hiding-and-removing)).
- It does not publish without notifications; `eZContentFunctions` keeps its `$notify` argument for that.

## Where it is used

- The REST API, `ezprestapi` 1.2.9 and later: `POST /api/ezp/v2/content/node/create`, `POST` or `PUT
  /api/ezp/v2/content/node/<id>` and `DELETE /api/ezp/v2/content/node/delete/<id>`
  ([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md#21-updating-and-the-values-of-a-write)).
- Your scripts, migrations and integrations: [Writing content from PHP with expContentWriter](../../guides/writing-content-from-php.md).

## Tests

`tests/tests/kernel/classes/contentwriter/expContentWriterTest.php` runs without a database: unknown attributes and
values that are not text are refused with the attribute named (CW-01); a file is fetched only from `http` or `https`
(CW-02); private, loopback, link-local and reserved addresses are refused, also when one of several addresses is
(CW-03); a host that does not resolve and a URL with credentials are refused (CW-04); the file value format (CW-05);
the file datatypes (CW-06).

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/contentwriter/
```

## Related pages

- [Writing content from PHP with expContentWriter](../../guides/writing-content-from-php.md): the guide
- [Upgrade notes: the REST writes through the kernel, nxc_powercontent no longer needed](../../bc/6.0/content-writer-and-rest-writes.md)
- [New in 6.0.15: the REST API updates content](../../help/whats-new-in-6-0-15-rest-api.md)
- [ezprestapi](extensions/ezprestapi.md), [nxc_powercontent](extensions/nxc_powercontent.md)
- [Content from PHP](../../help/cms-developers/content-from-php.md)
- [Creating, publishing and changing content from PHP](../../help/extension-development/part-8-reference/content-functions-from-php.md)
- [Migrations in the kernel](migrations.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
