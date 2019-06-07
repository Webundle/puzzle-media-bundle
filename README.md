# Puzzle Media Bundle

Project based on Symfony project for managing media accounts and media security.

## **Installation**

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```yaml
composer require webundle/puzzle-media-bundle
```

## **Step 1: Enable**
Enable admin bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Puzzle\MediaBundle\MediaBundle(),
        );

        // ...
    }

    // ...
}
```

## **Step 2: Configure bundle security**
Configure security by adding it in the `app/config/security.yml` file of your project:

```yaml
security:
	...
    role_hierarchy:
        ...
        # Media
        ROLE_MEDIA: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_MEDIA]
        
	...
    access_control:
        ...
        # Media
        - {path: ^%admin_prefix%learning, host: "%admin_host%", roles: ROLE_MEDIA }

```

## **Step 3: Enable bundle routing**

Register default routes by adding it in the `app/config/routing.yml` file of your project:

```yaml
....
media:
    resource: "@MediaBundle/Resources/config/routing.yml"
    prefix:   /


_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.yaml"

```
See all learning routes by typing: `php bin/console debug:router | grep media`


## **Step 4: Define base web directory **

Register default routes by adding it in the `app/config/parameters.yml` file of your project:

```yaml
parameters:
	....
	media_base_dir: '%kernel.root_dir%/../web'

```

## **Step 5: Configure bundle**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:

```yaml
# Liip
liip_imagine :
    resolvers :
        default :
            web_path : ~
    filter_sets :
        cache : ~
        thumb :
            quality : 100
            filters :
                thumbnail  : { size : [95, 60], mode : outbound }

# Admin
admin:
    ...
    modules_available: '..,media'
    navigation:
        nodes:
            ...
            # Media
            media:
                label: 'media.title'
                description: 'media.description'
                translation_domain: 'media'
                attr:
                    class: 'fa fa-cloud'
                parent: ~
                user_roles: ['ROLE_MEDIA']
            media_file:
                label: 'media.file.navigation'
                description: 'media.file.description'
                translation_domain: 'media'
                path: 'puzzle_admin_media_file_list'
                parent: media
                user_roles: ['ROLE_MEDIA']
            media_folder:
                label: 'media.folder.navigation'
                description: 'media.folder.description'
                translation_domain: 'media'
                path: 'puzzle_admin_media_folder_list'
                sub_paths: ['puzzle_admin_media_folder_create', 'puzzle_admin_media_folder_update', 'puzzle_admin_media_folder_show']
                parent: media
                user_roles: ['ROLE_MEDIA']

```
