# Proprietary DXPs and SaaS

This page of [Exponential Competition](README.md) covers the products whose code the user may not freely run, study,
change and share: enterprise digital experience platforms (DXPs), headless content management sold as a service, and
hosted website builders and shops. For each product it gives what it is, its license and business model, what it does
well, where it fails a user without funding or with a long time horizon, and what Exponential does instead. The facts
were taken from each vendor's own pages in November 2026 (see [Sources](#sources)); plans and prices change, so check
them before deciding.

## Contents

- [1. Enterprise DXPs](#1-enterprise-dxps)
- [2. Headless CMS as a service](#2-headless-cms-as-a-service)
- [3. Website builders and hosted shops](#3-website-builders-and-hosted-shops)
- [4. What the whole group has in common](#4-what-the-whole-group-has-in-common)
- [Sources](#sources)

## 1. Enterprise DXPs

### Adobe Experience Manager (AEM)

- **What it is:** Adobe's content and digital asset management platform, part of Adobe Experience Cloud, offered as
  AEM as a Cloud Service and in earlier versions for own hosting.
- **License and model:** proprietary; licensed through Adobe, with pricing by quote.
- **Does well:** large-scale content, asset management and integration with Adobe's marketing products.
- **Where it fails the unfunded or long-term user:** there is no free production edition; running it needs an Adobe
  contract and specialist skills; the cloud service is operated by Adobe, so hosting and update timing follow the
  vendor.
- **Exponential instead:** free software with every feature included, on any PHP host the owner chooses, with the
  owner deciding when to update.

### Adobe ColdFusion

- **What it is:** Adobe's application server for the CFML language, often used to build custom websites and
  intranets.
- **License and model:** proprietary; sold per edition (Standard, Enterprise), with a free developer edition that is
  not for production.
- **Does well:** fast development of database-driven applications in CFML.
- **Where it fails:** production use needs a paid license; the language community is small, so skills and extensions
  are scarce; an open source CFML engine (Lucee) exists, see [Other languages](other-languages.md).
- **Exponential instead:** PHP, one of the most widely hosted web languages, with a complete CMS on top and no license
  fee.

### Sitecore

- **What it is:** a DXP on Microsoft .NET, sold as Experience Manager (XM), Experience Platform (XP) and
  cloud offerings, with commerce and content hub products.
- **License and model:** proprietary; pricing by quote.
- **Does well:** personalization and marketing features for large organizations on the Microsoft stack.
- **Where it fails:** there is no free production edition, so it is out of reach without a budget; it needs .NET
  expertise and a Sitecore partner or license holder to run.
- **Exponential instead:** no license, runs on Linux or Windows hosting, and keeps its sites upgradable within the line
  ([Updating Exponential 6.0](../UPDATE.md)).

### Optimizely

- **What it is:** the former Episerver, renamed after it acquired Optimizely in 2020; a DXP with content management
  (.NET), experimentation and commerce, including a SaaS CMS.
- **License and model:** proprietary; pricing by quote.
- **Does well:** combining content with A/B testing and experimentation.
- **Where it fails:** no free production use; the product names and packaging changed with the rebrand, and long-term
  plans depend on the vendor's roadmap.
- **Exponential instead:** a stable, documented product line that the community controls ([History of Exponential](../HISTORY.md)).

### Acquia

- **What it is:** a commercial company that sells hosting, tools and a DXP around Drupal.
- **License and model:** the services are proprietary subscriptions; Drupal itself is free software
  (see [PHP CMS and frameworks](php-cms-and-frameworks.md)).
- **Does well:** managed Drupal hosting and tooling for large Drupal estates.
- **Where it fails:** the convenience is a subscription; the added platform features stay with the vendor when the
  subscription ends.
- **Exponential instead:** the full product is free software; hosting is a choice, not a part of the product.

### Bloomreach Content

- **What it is:** the content product of Bloomreach, a commerce and marketing company. Its predecessor, Hippo CMS, was
  open source.
- **License and model:** proprietary SaaS; pricing by quote.
- **Does well:** content tied to Bloomreach's commerce search and personalization.
- **Where it fails:** no free production use; the product depends on the vendor's platform.
- **Exponential instead:** search, content and integrations the owner runs and keeps.

### Ibexa DXP

- **What it is:** the commercial digital experience platform of Ibexa AS (formerly eZ Systems AS), built on Symfony and
  the successor line of the eZ Platform products ([History of Exponential](../HISTORY.md)).
- **License and model:** the open source base, Ibexa OSS, is available under the GNU GPL version 2; the commercial
  editions and features are licensed under the Ibexa Business Use License with a subscription.
- **Does well:** a modern Symfony-based platform with commercial support, headless, experience and commerce editions.
- **Where it fails the unfunded user:** the features of the commercial editions require a subscription; an
  organization without a budget is limited to the open source base.
- **Exponential instead:** every feature in one free software product, and Exponential distributions that combine the
  open source platform with the legacy kernel ([Distributions of Exponential](../DISTRIBUTIONS.md)).

### Salesforce Experience Cloud

- **What it is:** Salesforce's product for portals, communities and sites on top of Salesforce data.
- **License and model:** proprietary SaaS, licensed per user or per login within Salesforce contracts.
- **Does well:** sites that work directly on Salesforce customer data.
- **Where it fails:** metered by users and logins; it presupposes a Salesforce subscription; the site cannot be run
  outside Salesforce.
- **Exponential instead:** unlimited users and visitors at no license cost; integrations with CRMs through its APIs
  ([Integrating With Exponential 6.0](../INTEGRATING.md)).

### Microsoft SharePoint

- **What it is:** Microsoft's collaboration and intranet platform, mainly offered as SharePoint in Microsoft 365.
- **License and model:** proprietary; part of Microsoft 365 subscriptions per user.
- **Does well:** document collaboration inside organizations that already use Microsoft 365.
- **Where it fails:** licensed per user; public websites are not its purpose; customization follows Microsoft's
  platform rules.
- **Exponential instead:** a public website and intranet CMS with roles, policies and sections, licensed for everyone.

## 2. Headless CMS as a service

These products store content in the vendor's cloud and deliver it through APIs to a front end the customer builds.

### Contentful

- **What it is:** a hosted headless CMS.
- **License and model:** proprietary SaaS; a free plan with usage limits, then paid and enterprise plans.
- **Does well:** a clean content API and content modeling for developer teams.
- **Where it fails:** limits on records, users, environments and API use decide when a project must pay; the content
  lives in the vendor's cloud; a separate front end has to be built and hosted.
- **Exponential instead:** the content repository, the editing interface and the website in one product, with GraphQL
  and REST for headless use when wanted ([GraphQL](../features/6.0/graphql.md)).

### Contentstack

- **What it is:** a hosted headless CMS for enterprises.
- **License and model:** proprietary SaaS, sold by subscription.
- **Does well:** enterprise workflows and integrations for omnichannel content.
- **Where it fails:** no self-hosting; costs grow with the plan; the content model is tied to the vendor's platform.
- **Exponential instead:** the same content modeling (content classes and attributes) on the owner's server.

### Sanity

- **What it is:** a hosted content platform ("Content Lake") with an editing application, Sanity Studio.
- **License and model:** Sanity Studio is open source (MIT); the Content Lake that stores the content is a hosted
  service with a free plan (for example up to 20 user seats and two public datasets) and paid plans.
- **Does well:** a highly customizable editing application and real-time collaboration.
- **Where it fails:** the content store is not open source and runs only in Sanity's cloud; growth moves a project into
  paid plans.
- **Exponential instead:** both the editing interface and the content store are free software on the owner's server.

### Storyblok

- **What it is:** a hosted headless CMS with a visual editor.
- **License and model:** proprietary SaaS; a free plan, then paid plans per feature set.
- **Does well:** visual editing of component-based pages.
- **Where it fails:** the content and the editor live in the vendor's cloud; features are unlocked by plan.
- **Exponential instead:** visual page building with Exponential Layouts and front-end editing on the site itself
  ([Distributions of Exponential](../DISTRIBUTIONS.md)).

### Kontent.ai

- **What it is:** a hosted headless CMS, formerly Kentico Kontent.
- **License and model:** proprietary SaaS; a time-limited free trial, then paid plans.
- **Does well:** governance and workflows for content teams.
- **Where it fails:** no lasting free production use; no self-hosting.
- **Exponential instead:** workflows, states and approval in the product, free.

### Hygraph

- **What it is:** a hosted GraphQL content platform, formerly GraphCMS.
- **License and model:** proprietary SaaS; a free plan, paid plans with limits on API calls and other usage.
- **Does well:** GraphQL content federation from several sources.
- **Where it fails:** metered usage and vendor hosting.
- **Exponential instead:** a GraphQL API on the owner's own installation ([GraphQL](../features/6.0/graphql.md)).

### Prismic

- **What it is:** a hosted headless CMS with "slices" for page building.
- **License and model:** proprietary SaaS; a free plan for personal sites and proofs of concept, paid plans per
  repository.
- **Does well:** a simple editing experience for marketing pages.
- **Where it fails:** content in the vendor's cloud; team and usage needs move projects to paid plans.
- **Exponential instead:** unlimited editors and repositories at no license cost.

## 3. Website builders and hosted shops

### Squarespace

- **What it is:** a hosted website builder.
- **License and model:** proprietary SaaS, subscription plans.
- **Does well:** polished templates and an easy start.
- **Where it fails:** Squarespace's own help says only certain content can be exported (to a WordPress XML file) and
  that many features rely on its platform, so some content does not export.
- **Exponential instead:** the whole site, content, structure and files, is the owner's, on the owner's server.

### Wix

- **What it is:** a hosted website builder.
- **License and model:** proprietary SaaS, free plan with Wix branding and paid plans.
- **Does well:** drag-and-drop page design for beginners.
- **Where it fails:** Wix's own help says a Wix site needs to be hosted and operated on Wix's servers to work; only the
  domain can be moved.
- **Exponential instead:** hosting anywhere, and a static copy for any static host if wanted
  ([Static publishing](../features/6.0/static-publishing.md)).

### Webflow

- **What it is:** a hosted visual web design and CMS tool.
- **License and model:** proprietary SaaS; a free starter plan and paid site and workspace plans.
- **Does well:** precise visual control of design for designers.
- **Where it fails:** the CMS runs only on Webflow's hosting; growth in items, pages or traffic moves sites into
  higher plans.
- **Exponential instead:** a CMS without item or traffic limits on the owner's hosting.

### Shopify

- **What it is:** a hosted e-commerce platform.
- **License and model:** proprietary SaaS; monthly plans, and Shopify's pricing page says third-party transaction fees
  may apply when a payment provider other than Shopify Payments is used.
- **Does well:** a fast, reliable start for online shops.
- **Where it fails:** recurring plan fees, app subscriptions and transaction fees grow with the business; the shop runs
  only on Shopify.
- **Exponential instead:** a shop in the product (products, basket, checkout and orders) with no fee per transaction
  from Exponential ([E-commerce FAQ](../help/e-commerce/e-commerce-faq.md)).

### HubSpot CMS

- **What it is:** HubSpot's website product (Content Hub), connected to its CRM and marketing tools.
- **License and model:** proprietary SaaS; free tools, then tiered paid plans.
- **Does well:** websites tied directly to CRM and marketing automation.
- **Where it fails:** key features sit in higher tiers; the site runs only on HubSpot.
- **Exponential instead:** a CMS that connects to a CRM through open APIs, without a tier system.

### Duda

- **What it is:** a hosted website builder sold mainly to agencies and SaaS companies.
- **License and model:** proprietary SaaS; subscription plans after a free trial.
- **Does well:** managing many client sites from one agency account.
- **Where it fails:** sites stay on Duda's platform; costs grow with the number of sites.
- **Exponential instead:** many sites from one installation at no extra license cost
  ([Multi-site settings](../features/6.0/multi-site-ini-overrides.md)).

## 4. What the whole group has in common

- **The owner rents, not owns.** The code is not free software, and with SaaS the content lives on the vendor's
  servers.
- **The price follows usage and the vendor's decisions.** Seats, records, API calls, sites or transactions decide the
  bill, and plans change.
- **Leaving costs.** Export is partial or needs a rebuild of the front end.
- **Exponential's answer** is the opposite on each point: free software under the GPL, no metering, the owner's own
  servers, and a documented exit ([Leaving Exponential](../install/migration/leaving-exponential.md)). See
  [Switching to Exponential](../help/businesses/choosing/switching-to-exponential.md) for the way in.

## Sources

Checked in November 2026.

- Adobe, [AEM as a Cloud Service: introduction](https://experienceleague.adobe.com/en/docs/experience-manager-cloud-service/content/overview/introduction);
  [Adobe Experience Manager](https://business.adobe.com/products/experience-manager/adobe-experience-manager.html);
  [ColdFusion family](https://www.adobe.com/products/coldfusion-family.html).
- Sitecore, [Products](https://www.sitecore.com/products).
- Optimizely, [Content management](https://www.optimizely.com/products/content-management/).
- Acquia, [Products](https://www.acquia.com/products).
- Bloomreach, [Bloomreach Content](https://www.bloomreach.com/en/products/content).
- Ibexa, [Products](https://www.ibexa.co/products); the license of
  [ibexa/oss](https://github.com/ibexa/oss/blob/main/LICENSE) (Ibexa Business Use License and GNU GPL version 2).
- Salesforce, [Experience Cloud](https://www.salesforce.com/products/experience-cloud/overview/).
- Microsoft, [SharePoint](https://www.microsoft.com/en-us/microsoft-365/sharepoint/collaboration).
- Contentful, [Pricing](https://www.contentful.com/pricing/); Contentstack, [Pricing](https://www.contentstack.com/pricing);
  Sanity, [Pricing](https://www.sanity.io/pricing) and [the Sanity Studio license (MIT)](https://github.com/sanity-io/sanity);
  Storyblok, [Pricing](https://www.storyblok.com/pricing); Kontent.ai, [Pricing](https://kontent.ai/pricing/);
  Hygraph, [Pricing](https://hygraph.com/pricing); Prismic, [Pricing](https://prismic.io/pricing).
- Squarespace, [Pricing](https://www.squarespace.com/pricing) and
  [Export your site content](https://support.squarespace.com/hc/en-us/articles/206566687-Exporting-your-site).
- Wix, [Plans](https://www.wix.com/upgrade/website) and
  [Exporting or embedding your Wix site elsewhere](https://support.wix.com/en/article/exporting-or-embedding-your-wix-site-elsewhere).
- Webflow, [Pricing](https://webflow.com/pricing); Shopify, [Pricing](https://www.shopify.com/pricing);
  HubSpot, [CMS pricing](https://www.hubspot.com/pricing/cms); Duda, [Pricing](https://www.duda.co/pricing).
