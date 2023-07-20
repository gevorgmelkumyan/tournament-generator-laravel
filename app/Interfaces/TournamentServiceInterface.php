<?php

namespace App\Interfaces;

interface TournamentServiceInterface {

    function __construct(TeamServiceInterface $teamService);

    function create(): array;
}
