<?php

namespace App;

use App\GD\Draw;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

/**
 * App\Generate
 *
 * @property string $id
 * @property int $user_id
 * @property string $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Generate newModelQuery()
 * @method static Builder|Generate newQuery()
 * @method static Builder|Generate query()
 * @method static Builder|Generate uuid($uuid, $first = true)
 * @method static Builder|Generate whereCreatedAt($value)
 * @method static Builder|Generate whereData($value)
 * @method static Builder|Generate whereId($value)
 * @method static Builder|Generate whereUpdatedAt($value)
 * @method static Builder|Generate whereUserId($value)
 * @mixin Eloquent
 */
class Generate extends Model
{
    use Uuids;
    public $incrementing = false;

    protected static function boot()
    {
        static::creating(function ($model) {
            if (!$model->data) {
                $model->data =
                    @file_get_contents(resource_path('gd/default/'.app()->getLocale().'.json'))
                        ?:file_get_contents(resource_path('gd/default/main_zh-CN.json'));
            }
        });
        parent::boot();
    }
    /**
     * 生成png
     * @return false|string
     */
    public function generate()
    {
        return (new Draw($this, $this->data))->main();
    }
    public static function find($uuid)
    {
        return self::uuid($uuid);
    }
    public static function findOrFail($uuid)
    {
        $generate=self::find($uuid);
        if ($generate) {
            return $generate;
        } else {
            throw new ModelNotFoundException();
        }
    }
}
