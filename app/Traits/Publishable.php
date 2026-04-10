<?php

namespace App\Traits;

use App\Enums\PublishEnum;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

trait Publishable
{
    public bool $publishable = true;

    public string $publish_filter = '%';

    public function check_table($table_name)
    {
        $columns = Schema::getColumnListing($table_name);
        if(!in_array('publish_status', $columns)) {
            Schema::table($table_name, function (Blueprint $table) {
                $table->string('publish_status', 20)->default(PublishEnum::DRAFT->value);
            });
        }

        if(!in_array('publisher_id', $columns)) {
            Schema::table($table_name, function (Blueprint $table) {
                $table->unsignedBigInteger('publisher_id')->index()->nullable();
            });
        }
    }

    public function publish($item): void
    {
        $user = auth()->user();
        $this->check_table($this->table_name);
        if($user->hasPermissionTo($this->model_name . '_publish')){
            DB::update("update ".$this->table_name." set publisher_id = ".Auth::user()->id." where id=".$item['id']);
        }else{
            DB::update("update ".$this->table_name." set publisher_id = ".Auth::user()->id." , publish_status = '".PublishEnum::PUBLISHED->value."' where id=".$item['id']);
        }
    }

}
