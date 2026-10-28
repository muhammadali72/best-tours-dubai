# Best Tours Dubai

Website design and development by **Muhammad Ali — Ali Dev Solutions**.

[Visit website](https://besttoursdubai.com) · [Developer portfolio](https://alidevsolutions.com)

![Best Tours Dubai website preview](preview.png)

## Project overview

This repository showcases the custom PHP/SQLite implementation prepared for Best Tours Dubai. It includes the available local application source, styles, scripts and website assets. It is a source snapshot, not a synchronized export of the live CMS. Later content edits on the live server may differ.

## Features

- Tour and attraction listings with dedicated detail pages.
- Booking forms with packages, pricing and optional extras.
- Custom content dashboard, image management and booking status tools.
- SEO metadata, redirects, sitemap and optimized responsive imagery.

## Technology

PHP 8.2+, SQLite through PDO, HTML, CSS and JavaScript. Apache/LiteSpeed rewrite rules are included. PHP extensions include PDO SQLite, mbstring, DOM, fileinfo, GD/WebP, OpenSSL and cURL as used by each feature. Bundled third-party libraries retain their own license files.

## Setup

After cloning, extract every `assets-*.zip` archive into the repository root. These archives contain the original media and larger public template/seed files, with their folder paths preserved. Application code remains browsable in the repository.

```sh
for archive in assets-*.zip; do unzip -n "$archive"; done
```

See [SETUP.md](SETUP.md) for folder layout and configuration. Production databases, account credentials, mail settings, payment keys, customer records and uploaded runtime files are intentionally excluded. Configure your own origin, private data location and administrator account before use.

GitHub hosts the source; the PHP application needs a PHP-capable server and does not run on GitHub Pages. This upload does not redeploy the live website.

## Author

**Muhammad Ali** — [Ali Dev Solutions](https://alidevsolutions.com)

## Rights

Published as a portfolio and source reference. No general open-source license is granted for original project code or branded content. Third-party libraries, fonts, images and client materials remain subject to their respective rights and licenses.
