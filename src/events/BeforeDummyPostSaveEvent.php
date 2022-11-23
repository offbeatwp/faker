<?php

namespace OffbeatWP\Faker\Events;

use OffbeatWP\Content\Post\PostModel;

final class BeforeDummyPostSaveEvent
{
    public PostModel $model;

    public function __construct(PostModel $model)
    {
        $this->model = $model;
    }
}