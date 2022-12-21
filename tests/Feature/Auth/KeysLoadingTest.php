<?php

use Tests\TestCase;

class KeysLoadingTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_signing_keys_can_be_loaded()
    {
        \Illuminate\Support\Facades\File::exists(app()->basePath(env("AUTH_PRIVATE_KEY_PATH")));
        \Illuminate\Support\Facades\File::exists(app()->basePath(env("AUTH_PUBLIC_KEY_PATH")));


    }
}
