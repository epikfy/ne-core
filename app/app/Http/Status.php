<?php

namespace App\Http;

abstract class Status
{
    const IN_PROGRESS = 'IN_PROGRESS';
    const SUCCESS = 'SUCCESS';
    const PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
    const ERROR = 'ERROR';
}
