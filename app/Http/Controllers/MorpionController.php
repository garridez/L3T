<?php

namespace App\Http\Controllers;

use App\Models\User_play;


class MorpionController extends Controller
{

    private static $morpion;
    private static $x;
    private static $y;

    /**
     * Test si il y a 3 pions alignés en ligne sur la par rapport au pion de coordonées x,y
     *
     * @return boolean
     */
    private static function check_line(): bool {
        $new_x = self::$x;
        $new_y = self::$y;

        $points = 0;

        while($new_y > 0 && self::$morpion[$new_x][$new_y] === self::$morpion[$new_x][$new_y-1]) {
            $new_y--;
            $points++;
        }

        $new_x = self::$x;
        $new_y = self::$y;

        while($new_y < 2 && self::$morpion[$new_x][$new_y] === self::$morpion[$new_x][$new_y+1]) {
            $new_y++;
            $points++;
        }

        return $points === 2;
    }


    /**
     * Test si il y a trois pions alignés en colonne par rapport au pion de coordonées x,y
     *
     * @return boolean
     */
    private static function check_col(): bool {
        $new_x = self::$x;
        $new_y = self::$y;

        $points = 0;

        while($new_x > 0 && self::$morpion[$new_x-1][$new_y] === self::$morpion[$new_x][$new_y]) {
            $new_x--;
            $points++;
        }

        $new_x = self::$x;
        $new_y = self::$y;

        while($new_x < 2 && self::$morpion[$new_x][$new_y] === self::$morpion[$new_x+1][$new_y]) {
            $new_x++;
            $points++;
        }

        return $points === 2;
    }


    /**
     * Check si il y a trois pions alignés en diagonale (gauche droite) par rapport au pion de coordonées
     * x,y
     *
     * @return boolean
     */
    private static function check_diagonale_gd(): bool {
        $new_x = self::$x;
        $new_y = self::$y;

        $points = 0;

        while($new_x > 0 && $new_y > 0 && self::$morpion[$new_x-1][$new_y-1] === self::$morpion[$new_x][$new_y]) {
            $new_x--;
            $new_y--;
            $points++;
        }

        $new_x = self::$x;
        $new_y = self::$y;

        while($new_x < 2 && $new_y < 2 && self::$morpion[$new_x+1][$new_y+1] === self::$morpion[$new_x][$new_y]) {
            $new_x++;
            $new_y++;
            $points++;
        }

        return $points === 2;
    }


    /**
     * Check si il y a 3 pions alignés en diagonale (droite gauche) par rapport au pion de coordonées x,y
     *
     * @return boolean
     */
    private static function check_diagonale_dg(): bool {
        $new_x = self::$x;
        $new_y = self::$y;
        
        $points = 0;

        while($new_x > 0 && $new_y < 2 && self::$morpion[$new_x-1][$new_y+1] === self::$morpion[$new_x][$new_y]) {
            $new_x--;
            $new_y++;
            $points++;
        }
        
        $new_x = self::$x;
        $new_y = self::$y;
        
        while($new_x < 2 && $new_y > 0 && self::$morpion[$new_x+1][$new_y-1] === self::$morpion[$new_x][$new_y]) {
            $new_x++;
            $new_y--;
            $points++;
        }
        
        return $points === 2;
    }


    /**
     * Recupère tout les records liés à un morpion à partir de l'id unique de la game qui lui 
     * est associé afin de construire un tableau 2D représentant le morpion
     *
     * @param string $id        L'ID unique de la game associé
     * 
     * @return array            Un morpion sous forme d'un tableau 2D
     */
    public static function get_morpion(string $id): array {
        $coups = User_play::where("gameid", $id) -> get();
        $morpion = [["", "", ""],["", "", ""],["", "", ""]];

        foreach($coups as $coup) {
            $pos = $coup -> position;
            
            $morpion[floor($pos/3)][$pos%3] = $coup -> symbol;
        }
        
        return $morpion;
    }


    /**
     * Test si le pion placé en position x,y permet la victoire en ligne, colone ou diagonale du joueur.
     *
     * @param array $morpion        Le morpion sous forme d'un tableau 2D
     * @param integer $x            La coordonée en abscisse
     * @param integer $y            La coordonée en ordonnée 
     * 
     * @return boolean              True si c'est un coup gagnant, false sinon
     */
    public static function check_win(array $morpion, int $x, int $y): bool {
        self::$morpion = $morpion;
        self::$x = $x;
        self::$y = $y;

        if($morpion[$x][$y] === "") return false;

        return 
            self::check_line() || self::check_col() || self::check_diagonale_dg() || self::check_diagonale_gd();
    }
}

