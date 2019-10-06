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
     * @var Rate[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Rate", mappedBy="currency", fetch="LAZY")
     */
    private $rates;

    /**
     * @return Rate[]|ArrayCollection
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * @param Rate[]|ArrayCollection $rates
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
        $this->rates[] = $rate;

        return $this;
    }
}
