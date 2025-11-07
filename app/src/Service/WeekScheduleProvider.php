<?php

namespace App\Service;

final class WeekScheduleProvider
{
    /**
     * Retourne le planning hebdo sous forme d’un tableau associatif
     * { day: mon|tue|..., start: "HH:MM", end: "HH:MM", title: "...", type: "..." }
     */
    public function get(): array
    {
        return [
            // LUNDI
            ['day'=>'mon','start'=>'10:00','end'=>'11:00','title'=>'GYM SÉNIOR','type'=>'senior'],
            ['day'=>'mon','start'=>'10:00','end'=>'11:00','title'=>'SOUPLESSE','type'=>'flex'],
            ['day'=>'mon','start'=>'11:00','end'=>'12:00','title'=>'FUNCTIONAL TRAINING','type'=>'functional'],
            ['day'=>'mon','start'=>'12:00','end'=>'12:45','title'=>'SANGLES','type'=>'straps'],
            ['day'=>'mon','start'=>'18:00','end'=>'18:30','title'=>'ABDOS FLASH','type'=>'abs'],
            ['day'=>'mon','start'=>'18:30','end'=>'19:30','title'=>'HYBRID TRAINING','type'=>'hybrid'],
            ['day'=>'mon','start'=>'19:30','end'=>'20:15','title'=>'FIT DANCE','type'=>'dance'],
            ['day'=>'mon','start'=>'20:15','end'=>'20:45','title'=>'YOGA','type'=>'yoga'],
            ['day'=>'mon','start'=>'20:45','end'=>'21:45','title'=>'HEELS','type'=>'heels'],

            // MARDI
            ['day'=>'tue','start'=>'17:30','end'=>'18:15','title'=>'JUMP','type'=>'jump'],
            ['day'=>'tue','start'=>'18:15','end'=>'19:00','title'=>'FUNCTIONAL TRAINING','type'=>'functional'],
            ['day'=>'tue','start'=>'19:00','end'=>'19:45','title'=>'CARDIOLAND','type'=>'cardio'],
            ['day'=>'tue','start'=>'19:45','end'=>'20:30','title'=>'SOUPLESSE YOGA PILATE','type'=>'flex'],
            ['day'=>'tue','start'=>'20:30','end'=>'21:15','title'=>'SANGLES','type'=>'straps'],

            // MERCREDI
            ['day'=>'wed','start'=>'10:00','end'=>'10:45','title'=>'SANGLES','type'=>'straps'],
            ['day'=>'wed','start'=>'10:45','end'=>'11:30','title'=>'FUNCTIONAL TRAINING','type'=>'functional'],
            ['day'=>'wed','start'=>'11:30','end'=>'12:15','title'=>'GYM SÉNIOR','type'=>'senior'],
            ['day'=>'wed','start'=>'11:30','end'=>'12:15','title'=>'SOUPLESSE','type'=>'flex'],
            ['day'=>'wed','start'=>'12:15','end'=>'13:00','title'=>'STEP DÉBUTANT','type'=>'step'],
            ['day'=>'wed','start'=>'18:00','end'=>'18:30','title'=>'JUMP','type'=>'jump'],
            ['day'=>'wed','start'=>'18:30','end'=>'19:15','title'=>'YOGA','type'=>'yoga'],
            ['day'=>'wed','start'=>'19:15','end'=>'20:00','title'=>"MOVE N'DANCE",'type'=>'dance'],
            ['day'=>'wed','start'=>'20:00','end'=>'21:00','title'=>'HYBRID TRAINING','type'=>'hybrid'],

            // JEUDI
            ['day'=>'thu','start'=>'18:00','end'=>'18:45','title'=>'FLYING POLE','type'=>'pole'],
            ['day'=>'thu','start'=>'18:00','end'=>'18:45','title'=>'SOUPLESSE','type'=>'flex'],
            ['day'=>'thu','start'=>'18:45','end'=>'19:30','title'=>'JUMP','type'=>'jump'],
            ['day'=>'thu','start'=>'19:30','end'=>'20:15','title'=>'FUNCTIONAL TRAINING','type'=>'functional'],
            ['day'=>'thu','start'=>'20:15','end'=>'21:15','title'=>'HYBRID TRAINING','type'=>'hybrid'],

            // VENDREDI
            ['day'=>'fri','start'=>'11:45','end'=>'12:30','title'=>'GYM SÉNIOR','type'=>'senior'],
            ['day'=>'fri','start'=>'11:45','end'=>'12:30','title'=>'SOUPLESSE','type'=>'flex'],
            ['day'=>'fri','start'=>'12:30','end'=>'13:15','title'=>'FUNCTIONAL TRAINING','type'=>'functional'],
            ['day'=>'fri','start'=>'18:15','end'=>'19:00','title'=>'STEP','type'=>'step'],
            ['day'=>'fri','start'=>'19:00','end'=>'19:45','title'=>'HIIT','type'=>'hiit'],
            ['day'=>'fri','start'=>'19:45','end'=>'20:30','title'=>'FUNCTIONAL TRAINING','type'=>'functional'],

            // SAMEDI
            ['day'=>'sat','start'=>'10:00','end'=>'10:45','title'=>'FLYING POLE','type'=>'pole'],
            ['day'=>'sat','start'=>'10:00','end'=>'10:45','title'=>'SOUPLESSE','type'=>'flex'],
            ['day'=>'sat','start'=>'10:45','end'=>'11:30','title'=>'FUNCTIONAL TRAINING','type'=>'functional'],
            ['day'=>'sat','start'=>'11:30','end'=>'13:00','title'=>'HYBRID TRAINING','type'=>'hybrid'],
        ];
    }

    public function startHour(): int { return 9; }
    public function endHour(): int { return 22; }
}
