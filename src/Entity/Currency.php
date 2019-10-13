<?php declare(strict_types = 1);

namespace App\Entity;

use App\Model;
use App\Utils\Doctrine\BaseFields;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="currency")
 * @ORM\HasLifecycleCallbacks()
 */
class Currency extends Model\Currency
{
    use BaseFields;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Rate", mappedBy="currency", fetch="LAZY")
     */
    private $rates;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getRates(): ArrayCollection
    {
        return $this->rates;
    }

    /**
     * @param ArrayCollection $rates
     *
     * @return Currency
     */
    public function setRates($rates): Currency
    {
        $this->rates = $rates;

        return $this;
    }

    /**
     * @param Rate $rate
     *
     * @return Currency
     */
    public function addRate(Rate $rate): Currency
    {
        $this->rates->add($rate);

        return $this;
    }
}
