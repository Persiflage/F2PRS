<?php

/**
 * Calculate a player's EHP.
 *
 * @param $stats array The players stats structure
 * @param $mode int The players account mode
 * @param $array_flag boolean Specify whether you want the skills EHP calculated
 * @return float|array Either the players EHP or the edited $stats array
 */

function ehp($stats, $rates, $array_flag) {

    $ehp_rates = $rates;

    /**
     *
     * Some notes about EHP:
     *      Each rate (as defined in rates.php) consists of multiple pairs
     *      Each pair is made of a "threshold" and a "rate"
     *      The threshold specifies at what xp the new rate should take effect
     *      The rate specifies what the new efficient skill xp/h is
     *
     * This algorithm differs from Foot's in that I am counting progress COMPLETED.
     * His algorithm counts the progress REMAINING, and thus requires TWO function
     * calls to determine a players EHP: first to calculate the time to 200m all,
     * then to calculate the time to 200m all for an account. Then subtract the two.
     * (Time to 200m all could also be calculated and stored as a variable when EHP changes)
     *
     * Therefore, our implementations are reversely identical, but each are quicker in specific cases:
     *      Mine will be quicker to find a players EHP
     *      Foot's will be quicker to find time to 200m all
     * As they both only require one function call in these cases.
     *
     * Fun stuff! :~)
     *
     */

    $ehp = 0.0;

    /* Loop through each skill */
    foreach($rates as $skill => $skill_rates) {

        $skill_ehp = 0.0;

        /* Break early if skill is 0 time or has 0 xp */
        if(!$skill_rates || $stats[$skill]["xp"] == 0)
            continue;

        /* Count numbers of EHP thresholds */
        /* so we dont throw null exception */
        $num_thresholds = count($skill_rates);

        /* Loop through each "set" of EHP thresholds */
        for($n = 0; $n < $num_thresholds; $n = $n + 2) {

            $added = 0.0;

            /* Helper variables */
            $current_threshold = $skill_rates[$n];
            $current_rate = $skill_rates[$n + 1];
            $current_xp = $stats[$skill]["xp"];

            /* Want to access the next threshold ONLY if it exists */
            /* count() returns 1 based so we have to subtract 1    */
            $next_threshold = null;
            if($n + 2 <= $num_thresholds - 1)
                $next_threshold = $skill_rates[$n + 2];

            /* Current XP is past current threshold -- add the appropriate EHP */
            if($current_xp >= $current_threshold) {

                /* We are passed the next threshold too! Add the EHP in between */
                if($next_threshold && $current_xp >= $next_threshold)
                    $added = ($next_threshold - $current_threshold) / $current_rate;
                else
                    $added = ($current_xp - $current_threshold) / $current_rate;

            }

            $ehp += $added;
            $skill_ehp += $added;
        }

        $stats[$skill]["ehp"] = $skill_ehp;

    }

    $stats["total"]["ehp"] = $ehp;

    if($array_flag)
        return $stats;
    else
        return $ehp;

}

/**
 * This is a rewrite of Foot's EHP algorithm to use my player structures.
 * Efficient for calculating time to max / 120 all / 200m all
 *
 * @param $stats    The stats array for the account
 * @param $mode     The mode of the account (1 : normal, 2 : skiller, 3 : ironman, 4 : hardcore ironman)
 * @param $goal     The XP goal (usually 200,000,000)
 * @return float    The amount of hours remaining to reach goal XP
 */

function ehp_foot_rewrite($stats, $mode, $goal) {
    require_once 'rates.php';

    /* Select correct rates for account type */
    switch($mode) {
        case 1:
            $ehp_rates = $rates;
            break;
        case 2:
            $ehp_rates = $sk_rates;
            break;
        case 3:
        case 4:
            $ehp_rates = $im_rates;
            break;
        default:
            $ehp_rates = $rates;
            break;
    }

    $ehp_stats = $stats;
    $timeLeft = 0.0;

    foreach($ehp_rates as $skill => $skill_rates) {


        if($ehp_stats[$skill]["xp"] < $goal && $skill_rates) {

            echo "Skill: $skill XP: ".$ehp_stats[$skill]["xp"]."\n";

            for($n = 0; $n < count($skill_rates); $n = $n + 2) {

                if($ehp_stats[$skill]["xp"] >= $skill_rates[$n]) {
                    $prev_rate = count($skill_rates) - 2;

                    if($prev_rate == $n)
                        $target = $goal;
                    else
                        $target = min($skill_rates[$n + 2], $goal);

                    $rate = $skill_rates[$n + 1];

                    if($target > $ehp_stats[$skill]["xp"]) {
                        $added = ($goal - $ehp_stats[$skill]["xp"]) / $rate;
                        $timeLeft += $added;

                    }
                }
            }
        }
    }

    return $timeLeft;
}


?>
