<?php

namespace App\Helpers;

class ResponseMessage {

    const ERROR_CANT_RUN_DIVISION_GAMES = 'You can\'t run division games without creating teams first';

    const ERROR_CANT_RUN_PLAYOFFS = 'You can\'t run playoffs without running division games first';

    const ERROR_CANT_RUN_SEMI_FINALS = 'You can\'t run semi-finals without running playoffs first';

    const ERROR_CANT_RUN_FINALS = 'You can\'t run finals without running semi-finals first';

    const ERROR_SOMETHING_WENT_WRONG = 'Something went wrong, please try again';
}
