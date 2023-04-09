<?php

namespace FluxEcoType;

enum FluxEcoHttpStatusCode: int
{
    case OK = 200;
    case BAD_REQUEST = 400;
    case FORBIDDEN = 403;

}