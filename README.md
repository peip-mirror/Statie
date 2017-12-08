# Statie - PHP Static Site Generator

[![Build Status](https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Statie)
[![Downloads](https://img.shields.io/packagist/dt/symplify/statie.svg?style=flat-square)](htptps://packagist.org/packages/symplify/statie)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fstatie)


Statie takes HTML, Markdown and Latte files and generates static HTML page.

## Install via Composer

```bash
composer require symplify/statie
```

## And via Node

```bash
npm install -g gulp gulp-watch
```

## Usage

### Generate content from `/source` to `/output` in HTML

```bash
vendor/bin/statie generate source
```

### See Generated web

```bash
php -S localhost:8000 -t output
```

And open [localhost:8000](http://localhost:8000) in browser.

### Live Rebuild

For live rebuild, just add `gulpfile.js`:

```javascript
var gulp = require('gulp');
var watch = require('gulp-watch');
var exec = require('child_process').exec;

gulp.task('default', function () {
    // Run local server, open localhost:8000 in your browser
    exec('php -S localhost:8000 -t output');

    return watch(['source/**/*', '!**/*___jb_tmp___'], { ignoreInitial: false })
        // For the second arg see: https://github.com/floatdrop/gulp-watch/issues/242#issuecomment-230209702
        .on('change', function() {
            exec('vendor/bin/statie generate source', function (err, stdout, stderr) {
                console.log(stdout);
                console.log(stderr);
            });
        });
});
```

And run:

```bash
gulp
```


## Detailed Documentation

- [Hook to Statie Application cycle with Events](/docs/HookToStatie.md)


### Push content of `/output` to Github pages

To push to e.g. [tomasvotruba/tomasvotruba.cz](https://github.com/TomasVotruba/tomasvotruba.cz) repository, setup repository slug:

```yaml
# statie.yml
parameters:
    github_repository_slug: "TomasVotruba/tomasvotruba.cz"
```

And push it with CLI command:

```
vendor/bin/statie push-to-github tomasvotruba/tomasvotruba.cz --token=${GH_TOKEN}
```

How to setup `${GH_TOKEN}`? Just check [this exemplary .travis.yml](https://github.com/TomasVotruba/tomasvotruba.cz/blob/fddcbe9298ae376145622d735e1408ece447ea09/.travis.yml#L9-L26).

 
## Configuration

### `statie.yml ` Config

This is basically `config.yml` Symfony Kernel that you know from Symfony apps. You can.

**1. [Add Parameters](https://symfony.com/doc/current/service_container/parameters.html)**

```yaml
# statie.yml
parameters:
    site_url: http://github.com

    socials:
        facebook: http://facebook.com/github
```

...that are available in every template:

```twig
# source/_layouts/default.latte

<p>Welcome to: {$site_url}</p>

<p>Checkout my FB page: {$socials['facebook']}</p>
```

**2. [Import other configs](http://symfony.com/doc/current/service_container/import.html)**

```yaml
# statie.yml
imports:
    - { resource: 'data/favorite_links.yml' }

parameters:
    site_url: http://github.com
    socials:
        facebook: http://facebook.com/github
```

...and split long configuration into more smaller files:

```yaml
# data/favorite_links.yml
parameters:
    favorite_links:
        blog:
            name: "Suis Marco"
            url: "http://ocramius.github.io/"
```

**3. And [Register Services](https://symfony.com/doc/current/service_container.html)**


### Show Related Posts

If you write a series, you can show related posts bellow.

Just use post ids and `related_items` section in post files like:

```yaml
---
id: 1
title: My first post
related_items: [2]
```


```yaml
---
id: 2
title: My second post
related_items: [1]
---
```

Then use in template:

```twig
{var $relatedPosts = ($post|relatedPosts)}

<div n:if="count($relatedPosts)">
    <strong>Continue Reading</strong>
    <ul>
        {foreach $relatedPosts as $relatedPost}
            <li>
                <a href="/{$relatedPost['relativeUrl']}">{$relatedPost['title']}</a>
            </li>
        {/foreach}
    </ul>
</div>
```


### Generator Elements

All items that **contain multiple records and need own html page** - e.g. posts - can be configured in `statie.yml`:  

```yml
parameters:
    generators:
        # key name, nice to have for more informative error reports
        posts:
            # required parameters
         
            # name of variable inside single such item
            variable: post
            # name of variable that contains all items
            varbiale_global: posts
            # directory, where to look for them
            path: '_posts' 
            # which layout to use
            layout: '_layouts/@post.latte' 
            # and url prefix, e.g. /blog/some-post.md
            route_prefix: 'blog'
             
            # optional parameters
             
            # an object that will wrap it's logic, you can add helper methods into it and use it in templates
            # Symplify\Statie\Renderable\File\File is used by default
            object: 'Symplify\Statie\Renderable\File\PostFile' 
```


### Enable Github-like Headline Anchors

Sharing long post to show specific paragraph is not a sci-fi anymore.

When your hover any headline, an anchor link to it will appear on the left. Click it & share it!

![Headline Anchors](docs/github-like-headline-anchors.png)
 
```yaml
# statie.yml
parameters:   
    markdown_headline_anchors: true 
```

You can use this sample css and modify it to your needs:

```css
/* anchors for post headlines */
.anchor {
    padding-right: .3em;
    float: left;
    margin-left: -.9em;
}

.anchor, .anchor:hover {
    text-decoration: none;
}

h1 .anchor .anchor-icon, h2 .anchor .anchor-icon, h3 .anchor .anchor-icon {
    visibility: hidden;
}

h1:hover .anchor-icon, h2:hover .anchor-icon, h3:hover .anchor-icon {
    visibility: inherit;
}

.anchor-icon {
    display: inline-block;
}
```

### Custom Output Path

Default output path for files is `<filename>/index.html`. That makes url nice and short.

In case you need a different path, use `outputPath` key in the configuration of the file.

E.g. running [Github Pages and 404 page](https://help.github.com/articles/creating-a-custom-404-page-for-your-github-pages-site/).

```html
---
layout: default
title: "Missing page, missing you"
outputPath: "404.html"
---

{block content}
    ...
{/block}
```


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
