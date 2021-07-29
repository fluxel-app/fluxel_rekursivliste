<?php declare(strict_types=1);

namespace Plugin\fluxel_rekursivliste;

use JTL\Events\Dispatcher;
use JTL\Plugin\Bootstrapper;
use JTL\Shop;


require_once(__DIR__ . "/class/class.Fluxel.Rekursivliste.php");

/**
 * Class Bootstrap
 * @package Plugin\fluxel_rekursivliste
 */
class Bootstrap extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function boot(Dispatcher $dispatcher): void
    {
        parent::boot($dispatcher);
        $dispatcher->listen('shop.hook.' . \HOOK_PRODUCTFILTER_GET_BASE_QUERY, static function (array $args) {
			$kKategorie = preg_replace("/.*\.kKategorie \= ([0-9]*)/i", "$1", $args["conditions"][0]);
			if(!$kKategorie || !is_numeric($kKategorie))
				return;
			
			$rekursivliste = new \Fluxel\Rekursivliste();
			$kats = $rekursivliste->getRecursiveCategoryIds($kKategorie);
			
			$args["conditions"][0] = "tkategorieartikel.kKategorie IN (" . implode(",", $rekursivliste->getRecursiveCategoryIds($kKategorie)) . ")";
        });
    }
}
