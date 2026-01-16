<?php

namespace App\Contracts;

interface ActorExtractorInterface
{
    /**
     * @return array{first_name:?string,last_name:?string,address:?string,height_cm:?int,weight_kg:?int,gender:?string,age:?int}
     */
    public function extract(string $description): array;

    /**
     * @return array{first_name:?string,last_name:?string,address:?string,height_cm:?int,weight_kg:?int,gender:?string,age:?int}
     */
    public function extractRequired(string $description): array;
}

