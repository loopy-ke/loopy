<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Application extends Model
{
    public static function newSecret()
    {
        return Str::random(64);
    }

    public static function newKey()
    {
        return Str::random(16);
    }

    /**
     * @param $key
     * @return Application
     */
    public static function fromKey($key)
    {
        return static::query()->where('key', $key)->first();
    }

    public function checkSign($token, $url)
    {
        return sha1($this->key . $url . $this->secret) == $token;
    }

    public function save(array $options = [])
    {
        if (!$this->key) {
            $this->key = self::newKey();
        }

        if (!$this->user_id) {
            $this->user_id = auth()->user()->id;
        }

        if (!$this->secret) {
            $this->secret = self::newSecret();
        }

        return parent::save($options);
    }
}
