<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RedisCacheTest extends TestCase
{
    /** @test */
    public function it_can_cache_data_with_redis()
    {
        // Defina a chave e o valor do cache
        $key = 'redis_test_key';
        $value = 'This is a test value';

        // Armazene o valor no cache Redis por 60 segundos
        Cache::store('redis')->put($key, $value, 60);

        // Recupere o valor do cache
        $cachedValue = Cache::store('redis')->get($key);

        // Verifique se o valor recuperado é igual ao valor original
        $this->assertEquals($value, $cachedValue);
    }

    /** @test */
    public function it_expires_cached_data()
    {
        // Defina a chave e o valor do cache
        $key = 'redis_test_key_expires';
        $value = 'This value will expire';

        // Armazene o valor no cache Redis por 1 segundo
        Cache::store('redis')->put($key, $value, 1);

        // Recupere o valor do cache imediatamente
        $this->assertEquals($value, Cache::store('redis')->get($key));

        // Aguarde 2 segundos para garantir que o cache expirou
        sleep(2);

        // Verifique se o valor não está mais disponível no cache
        $this->assertNull(Cache::store('redis')->get($key));
    }
}