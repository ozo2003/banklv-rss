<?php declare(strict_types = 1);

namespace App\Entity;

use App\Model;
use App\Utils\Doctrine\BaseFields;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RateReposirory")
 * @ORM\Table(name="rate")
 * @ORM\HasLifecycleCallbacks()
 */
class Rate extends Model\Rate
{
    use BaseFields;

    /**
     * @var Currency
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="rates", fetch="EAGER")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currency;

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     *
     * @return Rate
     */
    public function setCurrency(Currency $currency): Rate
    {
        $this->currency = $currency;

        return $this;
    }
}
