<?php

declare(strict_types=1);

use AcMarche\Sepulture\Security\SecurityRoles;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('locale', 'fr');
    $parameters->set('acmarche_sepulture_email', '%env(SEPULTURE_EMAIL)%');
    $parameters->set('acmarche_sepulture_nom_ville', '%env(SEPULTURE_VILLE)%');
    $parameters->set('acmarche_sepulture_captcha_site_key', '%env(RECAPTCHA_SITE_KEY)%');
    $parameters->set('acmarche_sepulture_captcha_secret_key', '%env(RECAPTCHA_SECRET_KEY)%');
    $parameters->set('acmarche_sepulture_upload_sepulture_directory', '%kernel.project_dir%/public/uploads/sepultures');
    $parameters->set('acmarche_sepulture_download_sepulture_directory', '/uploads/sepultures');
    $parameters->set('acmarche_sepulture_upload_cimetiere_directory', '%kernel.project_dir%/public/uploads/cimetieres');
    $parameters->set('acmarche_sepulture_download_cimetiere_directory', '/uploads/cimetieres');

    $services = $containerConfigurator->services();
    $services = $services
        ->defaults()
        ->autowire()
        ->autoconfigure();
       // ->bind('to', '%acmarche_volontariat_email_to%')
      //  ->bind('from', '%acmarche_volontariat_email_from%')
     //   ->bind('rootUploadPath', '%acmarche_volontariat_upload_directory%')
    //    ->bind('rootDownloadPath', '%acmarche_volontariat_download_directory%');

    $services->load('AcMarche\Sepulture\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Migrations,Tests,Kernel.php,DataFixtures}']);
};
