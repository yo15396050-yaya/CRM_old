<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Instrumentation de diagnostic — inactive tant que PERF_LOG n'est pas a true.
 *
 * Pour chaque requete, journalise dans storage/logs/perf-AAAA-MM-JJ.log :
 *   - la duree totale et la duree passee en base ;
 *   - le nombre de requetes SQL ;
 *   - les requetes executees plusieurs fois a l'identique, qui trahissent
 *     un N+1 (la meme requete repetee dans une boucle).
 *
 * Aucun effet quand la mesure est desactivee : on ne branche meme pas
 * l'ecoute des requetes SQL.
 */
class MeasurePerformance
{
    /** Au-dela de ce nombre de requetes, la ligne est marquee comme a examiner. */
    private const SEUIL_REQUETES = 30;

    /** Au-dela de cette duree totale (ms), la ligne est marquee comme lente. */
    private const SEUIL_DUREE_MS = 800;

    public function handle(Request $request, Closure $next)
    {
        if (!config('app.perf_log')) {
            return $next($request);
        }

        $requetes = [];
        $debut = microtime(true);

        // L'instrumentation ne doit jamais empecher la page de s'afficher :
        // toute erreur ici est avalee, la requete suit son cours normalement.
        try {
            DB::listen(function ($query) use (&$requetes) {
                $requetes[] = [
                    'sql'  => $query->sql,
                    'time' => $query->time,
                ];
            });
        } catch (\Throwable $e) {
            return $next($request);
        }

        $response = $next($request);

        try {
            $this->journaliser($request, $requetes, (microtime(true) - $debut) * 1000);
        } catch (\Throwable $e) {
            // Un diagnostic qui echoue ne doit pas casser la reponse.
        }

        return $response;
    }

    private function journaliser(Request $request, array $requetes, float $dureeMs): void
    {
        $tempsSql = array_sum(array_column($requetes, 'time'));

        // Requetes identiques repetees : la signature d'un N+1.
        $repetees = [];

        foreach (array_count_values(array_column($requetes, 'sql')) as $sql => $n) {
            if ($n > 1) {
                $repetees[] = ['n' => $n, 'sql' => mb_substr($sql, 0, 160)];
            }
        }

        usort($repetees, fn ($a, $b) => $b['n'] <=> $a['n']);

        $signaux = [];

        if (count($requetes) > self::SEUIL_REQUETES) {
            $signaux[] = 'BEAUCOUP DE REQUETES';
        }

        if ($dureeMs > self::SEUIL_DUREE_MS) {
            $signaux[] = 'LENT';
        }

        if ($repetees) {
            $signaux[] = 'N+1 PROBABLE';
        }

        Log::channel('perf')->info(
            sprintf(
                '%s %s | %.0f ms total | %.0f ms SQL | %d requetes%s',
                $request->method(),
                $request->path(),
                $dureeMs,
                $tempsSql,
                count($requetes),
                $signaux ? ' | ' . implode(' + ', $signaux) : ''
            ),
            $repetees ? ['repetees' => array_slice($repetees, 0, 5)] : []
        );
    }
}
