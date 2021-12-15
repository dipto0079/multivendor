<?php

namespace App\Http\Controllers\Enum;


class MessageTypeEnum
{
    const SEPARATOR = ":: ";
    const SUCCESS = "Success" . MessageTypeEnum::SEPARATOR;
    const ERROR = "Error" . MessageTypeEnum::SEPARATOR;
    const WARNING = "Warning" . MessageTypeEnum::SEPARATOR;
    const INFO = "Info" . MessageTypeEnum::SEPARATOR;
}
