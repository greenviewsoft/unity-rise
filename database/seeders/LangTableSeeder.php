<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class LangTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('langs')->insert([
            'language_name' => 'English',
            'language_code' => 'en',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'English',
            'language_code' => 'en',
        ]);


        DB::table('langs')->insert([
            'language_name' => 'Indonesia',
            'language_code' => 'id',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'Tiếng Việt',
            'language_code' => 'vi',
        ]);

        DB::table('langs')->insert([
            'language_name' => '日本語',
            'language_code' => 'ja',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'Português',
            'language_code' => 'pt',
        ]);


        DB::table('langs')->insert([
            'language_name' => 'عربي',
            'language_code' => 'ar',
        ]);


        DB::table('langs')->insert([
            'language_name' => 'ไทย',
            'language_code' => 'th',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'Español',
            'language_code' => 'es',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'Deutsch',
            'language_code' => 'de',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'Français',
            'language_code' => 'fr',
        ]);
        
        DB::table('langs')->insert([
            'language_name' => 'Italiano',
            'language_code' => 'it',
        ]);

        DB::table('langs')->insert([
            'language_name' => '한국어',
            'language_code' => 'ko',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'Türk',
            'language_code' => 'tr',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'Pусский',
            'language_code' => 'ru',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'فارسی',
            'language_code' => 'fa',
        ]);

        DB::table('langs')->insert([
            'language_name' => 'Melayu',
            'language_code' => 'ms',
        ]);


        DB::table('langs')->insert([
            'language_name' => 'বাংলা',
            'language_code' => 'bn',
        ]);


        DB::table('langs')->insert([
            'language_name' => 'Azərbaycan',
            'language_code' => 'az',
        ]);

        DB::table('langs')->insert([
            'language_name' => '简体中文',
            'language_code' => 'zh-CN',
        ]);

        DB::table('langs')->insert([
            'language_name' => '繁體中文',
            'language_code' => 'zh-TW',
        ]);

    }
}
