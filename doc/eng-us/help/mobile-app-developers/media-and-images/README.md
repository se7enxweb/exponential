---
title: Media and images for apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 500
summary: The landing page of the media pages for app developers - what the REST API gives for images (the original file path), how image aliases give screen-sized versions through expimage services, and what is not possible (uploads from devices).
keywords: images, media, image alias, ezimage, original, expimage, files, uploads, screen sizes, mobile app
type: help
---
# Media and images for apps

This folder is for app developers who show images and files from Exponential, and for those who would like the app
to send photos to it. Images are usually the heaviest part of an app's traffic, so this is where screen sizes and
caching pay off most.

## Contents

1. [The situation in short](#the-situation-in-short)
2. [Pages in this folder](#pages-in-this-folder)

## The situation in short

| Need | Available | Read |
|---|---|---|
| The original image of an item | the REST field value of an `ezimage` field: the path of the original file | [Images in REST answers](images-in-rest-answers.md) |
| The image in a named size | `expimage::url` and `expimage::urls` of the remote services, generated on demand | [Image aliases for screen sizes](image-aliases-for-screen-sizes.md) |
| The list of named sizes | `expimage::aliases` (public) | same |
| Alternative text | `expimage::alt`, `expimage::info` | [Images in REST answers](images-in-rest-answers.md) |
| Downloading files | the `expfile` services | [Files and uploads](files-and-uploads.md) |
| Uploading a photo or file from a device | no documented route | same |

## Pages in this folder

| Page | Answers |
|---|---|
| [Images in REST answers](images-in-rest-answers.md) | What the `ezimage` value is and how to turn it into a URL |
| [Image aliases for screen sizes](image-aliases-for-screen-sizes.md) | Named sizes, adding sizes for the app, density and caching |
| [Files and uploads](files-and-uploads.md) | Downloads, and why uploads need a custom endpoint |

## Related pages

- [Images and image variations](../../creators/website-creators/images-and-image-variations.md)
- [Headless Exponential: rich text, images and links](../../creators/app-creators/headless-exponential.md#rich-text-images-and-links)
- [Media and uploads](../../media-and-uploads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
