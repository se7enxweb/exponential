---
title: How do I work with content as files over WebDAV?
audience: editor
category: manual
order: 498
summary: Where an administrator has switched WebDAV on, connect your file manager to the site's WebDAV address with your usual account; folders are items, dropping files creates items, saving a file of the same name makes a new version, and deleting sends items to the trash.
keywords: WebDAV, network drive, file manager, upload many files, drag and drop, index_webdav.php, webdav.php, Content, Media, siteaccess, new version, trash, mount
type: help
---
# How do I work with content as files over WebDAV?

WebDAV lets your computer's file manager open the content tree like a network drive. You can drag in a pile of
images, pull files out, or keep documents up to date by saving over them. Behind the scenes every file is an item,
created and changed with your normal rights.

WebDAV is off on a new installation. An administrator has to switch it on and tell you the address.

## Who can do this

Any user with an account on the site; the usual roles decide what you may read, create, change and remove.

## Steps

1. Get the WebDAV address from your administrator. It is either a host of its own, such as
   `https://webdav.example.com/<site>/`, or the site's address followed by `/index_webdav.php/<site>/`. An address
   with `/webdav.php/` still works, but that old name will be removed in a later major version: ask for the
   `index_webdav.php` address and connect again
   ([why](../../bc/6.0/webdav-entry-script-index-webdav.md)).
2. Connect your file manager to it and sign in with your user name and password:
   - Windows: **This PC** > **Map network drive**, or **Add a network location**;
   - macOS: Finder > **Go** > **Connect to Server**;
   - Linux: the file manager's **Connect to Server** or **Other Locations**, with a `davs://` address.
3. Open the folder of a site. Each site of the installation is a top folder; inside it are **Content** and **Media**,
   the two branches of the tree.
4. Work with the files:
   - **Open or copy out** a file to read it; images come in the size they were uploaded in.
   - **Drop files in** a folder to create items. The kind of file decides the class, for example an image becomes an
     *Image*, other files usually a *File*.
   - **Save a file over an existing one** with the same name to make a new version of that item.
   - **Make a new folder** to create a *Folder* item.
   - **Rename or move** an item to rename or move it in the tree; **copy** copies it with everything below it.
   - **Delete** an item to send it to the trash.

## What can go wrong

- **The address does not answer.** WebDAV is not switched on, or the web server does not pass the address to
  Exponential: since 6.0.15 its rules must name `index_webdav.php`, see
  [The WebDAV entry script is now index_webdav.php](../../bc/6.0/webdav-entry-script-index-webdav.md). Ask an
  administrator.
- **New items are in the wrong language.** Items made over WebDAV get the language of the site folder you used.
  Choose the folder of the site in the language you want.
- **Some entries are empty files.** Items that have no file of their own, such as an article, show as empty entries.
  Edit those in the administration.
- **A renamed file lost its type.** Keep the file extension when you rename, or the item can no longer be opened by the
  right program.
- **Deleting removed only one place.** If the item has several locations, deleting one keeps the others. Extra
  locations cannot be added over WebDAV.
- **Drafts are not touched.** WebDAV works on published content only; a draft someone left open stays as it is.

## Related pages

- [WebDAV and SOAP](../integrators/webdav-and-soap.md), how to switch WebDAV on
- [Images, media and uploads](../media-and-uploads.md), the upload page in the browser
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
