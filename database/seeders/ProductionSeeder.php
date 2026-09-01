<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ContactUs\App\Models\ContactInfo;
use Modules\FileManager\App\Helpers\ImageHelper;
use Modules\Role\Database\Seeders\PermissionSeeder;
use Modules\Role\Database\Seeders\RoleSeeder;
use Modules\Setting\App\Models\AboutUs;
use Modules\Setting\App\Models\SiteDetail;
use Modules\User\App\Helpers\UserHelper;
use Modules\User\App\Models\User;
use RuntimeException;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        if (str_starts_with((string) config('user.admin_password'), 'CHANGE_ME')) {
            throw new RuntimeException('Set a strong ADMIN_PASSWORD before running the production seed.');
        }

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        if (! User::getFirstAdmin()) {
            UserHelper::firstOrCreateAdminUser();
        }

        $siteDetails = SiteDetail::query()->first();

        if (! $siteDetails) {
            $mainLogo = ImageHelper::createDefaultImage(altText: 'Logo sŁodziak', configPath: 'common.default_logo.file_path');
            $secondLogo = ImageHelper::createDefaultImage(altText: 'Logo sŁodziak', configPath: 'common.default_logo.file_path');
            $favicon = ImageHelper::createDefaultImage(altText: 'Ikona sŁodziak', configPath: 'common.default_favicon.file_path');

            $siteDetails = new SiteDetail([
                'main_logo_id' => $mainLogo->id,
                'second_logo_id' => $secondLogo->id,
                'favicon_id' => $favicon->id,
            ]);
        }

        $siteDetails->fill([
            'title' => 'sŁodziak — biuletyn blisko Łodzi',
            'description' => 'Lokalne wiadomości, sprawy mieszkańców, kultura i wydarzenia z Łodzi oraz regionu.',
            'keywords' => 'Łódź, wiadomości lokalne, biuletyn, wydarzenia, kultura, mieszkańcy',
            'footer_text' => 'sŁodziak to niezależny biuletyn tworzony blisko spraw Łodzi i jej mieszkańców.',
        ])->save();

        ContactInfo::query()->updateOrCreate(['id' => 1], [
            'title' => 'Kontakt z redakcją',
            'content' => 'Masz temat, sprostowanie albo informację dla redakcji? Napisz do nas.',
            'address' => env('CONTACT_ADDRESS', 'Łódź, Polska'),
            'email' => env('CONTACT_EMAIL', config('mail.from.address')),
            'phone' => env('CONTACT_PHONE'),
        ]);

        AboutUs::query()->updateOrCreate(['id' => 1], [
            'title' => 'O sŁodziaku',
            'content' => 'sŁodziak jest lokalnym biuletynem poświęconym Łodzi, jej mieszkańcom i sprawom, które wpływają na codzienne życie miasta.',
        ]);
    }
}
