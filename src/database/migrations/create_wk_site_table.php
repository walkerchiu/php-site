<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkSiteTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.site.sites'), function (Blueprint $table) {
            $table->uuid('id');
            $table->string('type')->nullable();
            $table->string('serial')->nullable();
            $table->string('vat')->nullable();
            $table->string('identifier');
            $table->string('language', 5)->default(config('wk-core.language'));
            $table->json('language_supported')->nullable();
            $table->string('timezone')->default(config('wk-core.timezone'));
            $table->json('area_supported')->nullable();
            $table->uuid('currency_id')->nullable();
            $table->json('currency_supported')->nullable();
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->text('email_theme')->nullable();
            $table->text('layout_theme')->nullable();
            $table->string('view_template')->nullable();
            $table->string('email_template')->nullable();
            $table->string('skin')->nullable();
            $table->text('script_head')->nullable();
            $table->text('script_footer')->nullable();
            $table->json('options')->nullable();
            $table->json('images')->nullable();
            $table->boolean('can_guestOrder')->default(1);
            $table->boolean('can_guestComment')->default(1);
            $table->boolean('is_main')->default(0);
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->primary('id');
            $table->index('type');
            $table->index('serial');
            $table->index('identifier');
            $table->index('language');
            $table->index('view_template');
            $table->index('email_template');
            $table->index('can_guestOrder');
            $table->index('is_main');
            $table->index('is_enabled');
        });
        if (
            config('wk-site.onoff.currency')
            && Schema::hasTable(config('wk-core.table.currency.currencies'))
        ) {
            Schema::table(config('wk-core.table.site.sites'), function (Blueprint $table) {
                $table->foreign('currency_id')->references('id')
                      ->on(config('wk-core.table.currency.currencies'))
                      ->onDelete('set null')
                      ->onUpdate('cascade');
            });
        }
        if (!config('wk-site.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.site.sites_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }

        Schema::create(config('wk-core.table.site.layouts'), function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('site_id');
            $table->string('type', 10);
            $table->string('serial')->nullable();
            $table->string('identifier');
            $table->text('script_head')->nullable();
            $table->text('script_footer')->nullable();
            $table->json('options')->nullable();
            $table->unsignedBigInteger('order')->nullable();
            $table->boolean('is_highlighted')->default(0);
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('site_id')->references('id')
                  ->on(config('wk-core.table.site.sites'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');


            $table->primary('id');
            $table->index('serial');
            $table->index('identifier');
            $table->index('is_highlighted');
            $table->index('is_enabled');
        });
        if (!config('wk-site.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.site.layouts_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }

        Schema::create(config('wk-core.table.site.emails'), function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('site_id');
            $table->string('type', 20);
            $table->string('serial')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('site_id')->references('id')
                  ->on(config('wk-core.table.site.sites'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->primary('id');
            $table->index('serial');
            $table->index('is_enabled');
        });
        if (!config('wk-site.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.site.emails_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.site.emails_lang'));
        Schema::dropIfExists(config('wk-core.table.site.emails'));
        Schema::dropIfExists(config('wk-core.table.site.layouts_lang'));
        Schema::dropIfExists(config('wk-core.table.site.layouts'));
        Schema::dropIfExists(config('wk-core.table.site.sites_lang'));
        Schema::dropIfExists(config('wk-core.table.site.sites'));
    }
}
